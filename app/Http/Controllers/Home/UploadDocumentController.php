<?php

namespace App\Http\Controllers\Home;

use App\Http\Controllers\BaseController;
use Interop\Container\ContainerInterface;
use Slim\Http\Request;
use Slim\Http\Response;
use Psr\Http\Message\ResponseInterface;

class UploadDocumentController extends BaseController {

    public function __construct(ContainerInterface $container)
    {
        parent::__construct($container);
    }

    public function upload(Request $request, Response $response, array $args)
    {
        foreach ($request->getUploadedFiles()['files'] as $key => $value) {
            $params[] = [
                'name'     => $key,
                'contents' => @fopen($value->file, 'r'),
                'filename' => basename($value->getClientFilename()),
            ];
        }


        foreach ($request->getParsedBody() as $key => $value) {
            $params[] = [
                'name'     => strtoupper($key),
                'contents' => $value,
            ];
        }       

        try {
            $result = $this->guzzle->request('POST', $this->settings['webservice_url'] . 'Upload_LR_Documents', ['multipart' => array_change_key_case($params, CASE_UPPER), 'verify' => false]);
            $result = soap_response($result->getBody());           

            $result = $result['OBJECT'][0];            


            if (!empty($result['UPLOADDOCUMENT'][0]['RETURN_CODE']) && $result['UPLOADDOCUMENT'][0]['RETURN_CODE'] == '15')
            {
                return $response->withJson($result, 422);
            }

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


    public function readPassword(Request $request, Response $response, array $args)
    {
        $password = $request->getParams()['password'];
        $dtlSrno = $request->getParams()['srno'];

        try {
            $result = $this->guzzle->request('POST', $this->settings['webservice_url'] . 'Read_Password_protected_file', [
                'form_params' => [
                    'DIGI_LRDOC_SRNO' => $dtlSrno,
                    'PASSWORD'        => $password,
                ],
                'verify'      => false,
            ]);

            $result = soap_response($result->getBody());

            $result = $result['OBJECT'][0]['GET_FILE_DATA'][0];

            return $response->withJson($result);

        } catch (ServerException | ClientException $e) {
            $this->logger->info($e->getMessage());
        }
    }

    public function generateFileUploader(Request $request, Response $response, array $args)
    {
        $args['csrfNameKey'] = $this->csrf->getTokenNameKey();
        $args['csrfValueKey'] = $this->csrf->getTokenValueKey();
        $args['csrfName'] = $request->getAttribute($args['csrfNameKey']);
        $args['csrfValue'] = $request->getAttribute($args['csrfValueKey']);
        $args['doc_type'] = $request->getParams()['doc_type'];

        return $this->view->render($response, 'form_uploader', $args);
    }

    public function getDocList(Request $request, Response $response, array $args)
    {
        $docType = $request->getParams()['docType'];

        if( !empty($this->session->get('temp_doc_type')) && $this->session->get('temp_doc_type') == $docType && !empty($this->session->get('sess_doc_list')) )
        {
            return $response->withJson([
                'status' => true,
                'data'   => $this->session->get('sess_doc_list'),
            ]);
        }

        try {

            $result = $this->guzzle->request('POST', $this->settings['webservice_url'] . 'getDocumentAddlnInfoList', [
                'form_params' => [
                    'USER_ID'    => '',
                    'SERIAL_NO'  => '',
                    'CUST_NO'    => '',
                    'DOC_TYPE'   => $docType,
                    'KYC_ID'     => '',
                    'DOC_REF_NO' => '',
                    'SESSION_ID' => '',
                ],
                'verify'      => false,
            ]);

            $result = soap_response($result->getBody());

            $result = $result['OBJECT'][0]['DOC_ADDLINFO_LIST_DET'];

            if ($result)
            {
                $result = $result[0]['JSON_CONTROL_VALUES']['OBJECT'][0]['CONTROL_VALUES_DET'];
                $this->session->set('temp_doc_type', $docType);
                $this->session->set('sess_doc_list', $result);

                return $response->withJson([
                    'status' => true,
                    'data'   => $result,
                ]);
            }

            return $response->withJson([
                'status'  => false,
                'message' => 'Doc list not available',
            ], 200);

        } catch (ServerException | ClientException $e) {
            return $response->withJson([
                'status'  => false,
                'message' => $e->getMessage(),
            ], $e->getCode());
        }
    }

    public function viewDocument(Request $request, ResponseInterface $response, array $args)
    {
        $lrNo = $request->getParams()['lr_no'];
        $docsSrNo = $request->getParams()['docs_srno'];

        try {

            $result = $this->guzzle->request('POST', $this->settings['webservice_url'] . 'View_LeadDocument_LR', [
                'form_params' => [
                    'LR_NO' => $lrNo,
                    'DIGIDOC_LR_DOCS_SRNO' => $docsSrNo,
                ],
                'verify'      => false,
            ]);

            $result = soap_response($result->getBody());

            $result = $result['OBJECT'][0]['MESSAGE'][0];

            if ($result['RETURN_CODE'] == '0') {

                $data = $result['FILE_DATA'];

                $data = base64_decode($data);

                $ext = strtolower(pathinfo($result['FILE_NAME'], PATHINFO_EXTENSION));

                $mimes = new \Mimey\MimeTypes;

                $contentType = $mimes->getMimeType($ext);   //get content type

                $disposition = 'attachment';

                if(str_contains(strtolower($contentType), 'image'))
                {
                  $disposition = 'inline';
                }

                if(str_contains(strtolower($contentType), 'pdf'))
                {
                 $disposition = 'inline';
                }

                $response = $response
                    ->write($data)
                    ->withHeader('Content-Type', $contentType)
                    ->withHeader('Content-Length', strlen($data))
                    ->withHeader('Content-Disposition', $disposition.'; filename="' . $result['FILE_NAME'] . '"')
                    ->withStatus(200);

                return $response;
            }

        } catch (ServerException | ClientException | \Exception | \Throwable $e) {

            return $response->withJson([
                'status'  => false,
                'message' => $e->getMessage(),
            ], $e->getCode());
        }
    }

}