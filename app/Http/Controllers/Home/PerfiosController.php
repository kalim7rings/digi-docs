<?php

namespace App\Http\Controllers\Home;

use App\Http\Controllers\BaseController;
use Interop\Container\ContainerInterface;
use Slim\Http\Request;
use Slim\Http\Response;

class PerfiosController extends BaseController {

    public function __construct(ContainerInterface $container)
    {
        parent::__construct($container);
    }

    function request(Request $request, Response $response, array $args)
    {
        $requestParams = $request->getParsedBody();

        $request = $request->withAttributes([
            'doc_sr_no'   => $requestParams['doc_sr_no'],
            'doc_ref_tab' => $requestParams['doc_ref_tab'],
        ]);
        $this->addRequest($request, $response, $args);

        $payload1 = "<payload><vendorId>hdfc</vendorId><txnId>" . $this->session->get('perfiosRequestSrNo') . "</txnId><emailId>";

        $payload2 = "</emailId><destination>netbankingFetch</destination><returnUrl>" . $this->settings['perfios_return_url'] . "</returnUrl></payload>";

        exec($this->settings['perfios_encryption_cmd'] . " encrypt edgarr@hdfc.com 2>&1", $output);

        $payloadtring = trim($payload1 . trim($output[0]) . $payload2);

        exec($this->settings['perfios_encryption_cmd'] . ' signature "' . $payloadtring . '" 2>&1', $output2);

        $args['payload'] = trim($payloadtring);
        $args['signature'] = trim($output2[0]);
        $args['perfios_endpoint'] = $this->settings['perfios_endpoint'];

        $this->view->render($response, 'perfios.request', $args);
    }

    function addRequest(Request $request, Response $response, array $args)
    {
        $params = [
            'SERIAL_NO'   => $this->session->get('file_no'),
            'USER_ID'     => $this->session->get('userid'),
            'STEP_NO'     => $this->session->get('step_no', '3'),
            'DOC_REF_NO'  => $request->getAttribute('doc_sr_no'),
            'DOC_REF_TAB' => $request->getAttribute('doc_ref_tab'),
        ];

        try {
            $result = $this->guzzle->request('POST', $this->settings['webservice_url'] . 'AddPerfiosRequest', ['form_params' => $params, 'verify' => false]);

            $result = soap_response($result->getBody());

            $result = $result['OBJECT'][0]['TABLE'][0];

            $this->session->set('perfiosRequestSrNo', $result['SRNO']);

            return $response->withJson($result);

        } catch (ServerException | ClientException $e) {

            return $response->withJson([
                'status'  => false,
                'message' => $e->getMessage(),
            ], $e->getCode());

        } catch (Exception $e) {
            return $response->withJson([
                'status'  => false,
                'message' => $e->getMessage(),
            ], $e->getCode());
        }
    }

    function response(Request $request, Response $response, array $args)
    {
        $request = $request->withAttributes([
            'status'             => 'RETURNED',
            'perfios_trxn_no'    => '',
            'perfios_status'     => '',
            'perfios_error_code' => '',
            'perfios_reason'     => '',
        ]);
        $this->updateRequest($request, $response, $args);

        $theXml = '<payload><apiVersion>2.0</apiVersion><txnId>' . $this->session->get('perfiosRequestSrNo') . '</txnId><vendorId>hdfc</vendorId></payload>';

        exec($this->settings['perfios_encryption_cmd'] . ' signature "' . $theXml . '" 2>&1', $output);

        $result = $this->guzzle->request('POST',
            $this->settings['perfios_endpoint'] . 'txnstatus',
            [
                'verify'      => false,
                'form_params' => ['payload' => trim($theXml), 'signature' => trim($output[0])],
            ]
        );

        $result = parse_xml_response($result->getBody());

        if ($result === false) {

            $request = $request->withAttributes([
                'status'             => 'INVALID_XML',
                'perfios_trxn_no'    => '',
                'perfios_status'     => '',
                'perfios_error_code' => '',
                'perfios_reason'     => '',
            ]);

            $this->updateRequest($request, $response, $args);

        } else {

            if ($this->session->get('perfiosRequestSrNo') != $result['txnId']) {
                $request = $request->withAttributes([
                    'status'             => 'INVALID_TRAXN_REQ',
                    'perfios_trxn_no'    => '',
                    'perfios_status'     => '',
                    'perfios_error_code' => '',
                    'perfios_reason'     => '',
                ]);
                $this->updateRequest($request, $response, $args);
            } else {
                if ($result['parts'] > 1) {
                    for ($i = 0; $i < $result['parts']; $i ++) {
                        $args['perfios_trxn_no'] = $result['Part'][$i]['perfiosTransactionId'];
                        $args['perfios_status'] = $result['Part'][$i]['status'];
                        $args['perfios_error_code'] = $result['Part'][$i]['errorCode'];
                        $args['perfios_reason'] = $result['Part'][$i]['reason'];
                    }
                } else {
                    $args['perfios_trxn_no'] = $result['Part']['perfiosTransactionId'];
                    $args['perfios_status'] = $result['Part']['status'];
                    $args['perfios_error_code'] = $result['Part']['errorCode'];
                    $args['perfios_reason'] = $result['Part']['reason'];
                }

                $request = $request->withAttributes([
                    'status'             => '',
                    'perfios_trxn_no'    => $args['perfios_trxn_no'],
                    'perfios_status'     => $args['perfios_status'],
                    'perfios_error_code' => $args['perfios_error_code'],
                    'perfios_reason'     => $args['perfios_reason'],
                ]);

                $this->updateRequest($request, $response, $args);
            }
        }

        $this->view->render($response, 'perfios.response', $args);
    }

    function updateRequest(Request $request, Response $response, array $args)
    {
        $params = [
            'SR_NO'              => $this->session->get('perfiosRequestSrNo'),
            'STATUS'             => $request->getAttribute('status'),
            'PERFIOS_TRNS_NO'    => $request->getAttribute('perfios_trxn_no'),
            'PERFIOS_STATUS'     => $request->getAttribute('perfios_status'),
            'PERFIOS_ERROR_CODE' => $request->getAttribute('perfios_error_code'),
            'PERFIOS_REASON'     => $request->getAttribute('perfios_reason'),
        ];

        try {
            $result = $this->guzzle->request('POST', $this->settings['webservice_url'] . 'UpdatePerfiosRequest', ['form_params' => $params, 'verify' => false]);

            $result = soap_response($result->getBody());

            $result = $result['OBJECT'][0]['UPDATE_PERFIOS_REQUEST'][0];

            return $response->withJson($result);

        } catch (ServerException | ClientException $e) {

            return $response->withJson([
                'status'  => false,
                'message' => $e->getMessage(),
            ], $e->getCode());

        } catch (Exception $e) {
            return $response->withJson([
                'status'  => false,
                'message' => $e->getMessage(),
            ], $e->getCode());
        }
    }

    function download(Request $request, Response $response, array $args)
    {
        $requestParams = $request->getParsedBody();

        $payload = "<payload><apiVersion>2.0</apiVersion><perfiosTransactionId>" . $requestParams['perfios_trxn_no'] . "</perfiosTransactionId><reportType>pdf</reportType><txnId>" . $this->session->get('perfiosRequestSrNo') . "</txnId><vendorId>hdfc</vendorId></payload>";

        exec($this->settings['perfios_encryption_cmd'] . ' signature "' . $payload . '" 2>&1', $output);

        try {
            $result = $this->guzzle->request('POST', $this->settings['perfios_endpoint'] . 'retrieve',
                [
                    'verify'      => false,
                    'form_params' => ['payload' => trim($payload), 'signature' => trim($output[0])],
                ]
            );

            $request = $request->withAttributes([
                'status'             => 'DOWNLOADED',
                'perfios_trxn_no'    => '',
                'perfios_status'     => '',
                'perfios_error_code' => '',
                'perfios_reason'     => '',
            ]);
            $this->updateRequest($request, $response, $args);

            $filename = $this->session->get('file_no') . '-' . $this->session->get('perfiosRequestSrNo') . ".pdf";

            $params[] = [
                'name'     => 'files',
                'contents' => $result->getBody(),
                'filename' => basename($filename),
            ];

            foreach ($requestParams as $key => $value) {
                $params[] = [
                    'name'     => strtoupper($key),
                    'contents' => $value,
                ];
            }

            $result = $this->guzzle->request('POST', $this->settings['webservice_url'] . 'UploadDocuments_V2', ['multipart' => $params, 'verify' => false]);
            $result = soap_response($result->getBody());
            $result = $result['OBJECT'][0];

            if ($result) {
                return $response->withJson($result);
            }

            return $response->withJson([
                'status'  => false,
                'message' => 'Error While uploading Document',
            ], 417);
        } catch (ServerException | ClientException $e) {
            return $response->withJson([
                'status'  => false,
                'message' => $e->getMessage(),
            ], $e->getCode());
        }
    }
}