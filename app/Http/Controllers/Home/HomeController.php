<?php

namespace App\Http\Controllers\Home;

use App\Http\Controllers\BaseController;
use Interop\Container\ContainerInterface;
use Prophecy\Exception\Exception;
use Slim\Http\Request;
use Slim\Http\Response;
use GuzzleHttp\Exception\ClientException;
use GuzzleHttp\Exception\ServerException;
use Respect\Validation\Validator as Validator;

class HomeController extends BaseController {

    public function __construct(ContainerInterface $container)
    {
        parent::__construct($container);
    }

    public function index(Request $request, Response $response, array $args)
    {
        $randomKey = $request->getAttribute('route')->getArgument('token');
        $headers = $request->getHeaders();
        foreach ($headers as $name => $values) {
            $this->logger->info("Header " . $name . ": " . implode(", ", $values));
        }

        try {

            /*if(!isset($this->session->email_id))
            {*/
            $result = $this->sendOtp($randomKey);

            if ($result['RETURN_CODE'] == "0") {
                $this->session->set('lrNo', $result['LRNO']);
                $this->session->set('contactType', $result['CONTACT_TYPE']);
                $this->session->set('contactDetails', $result['CONTACT_DET']);
                $this->session->set('contactSrNo', $result['CONTACT_SRNO']);
                $this->session->set('sessionKey', $result['SESSION_KEY']);
                $this->session->set('customerName', $result['CUSTNAME']);
                $this->session->set('randomKey', $randomKey);
            }
            if ($result['RETURN_CODE'] == "-10") {
                return $this->view->render($response, 'error', ['status' => '', 'message' => 'Request Already Completed']);
            }

            if ($result['RETURN_CODE'] == "-15") {
                return $this->view->render($response, 'error', ['status' => '', 'message' => $result['RETURN_MESSAGE']]);
            }

            /*}*/

            if ($randomKey === $this->session->get('randomKey')) {
                $args['csrfNameKey'] = $this->csrf->getTokenNameKey();
                $args['csrfValueKey'] = $this->csrf->getTokenValueKey();
                $args['csrfName'] = $request->getAttribute($args['csrfNameKey']);
                $args['csrfValue'] = $request->getAttribute($args['csrfValueKey']);

                return $this->view->render($response, 'index', $args);
            }

        } catch (ServerException | ClientException $e) {

        }

        return $this->view->render($response, 'error', ['status' => 417, 'message' => 'Invalid Token']);
    }

    public function sendOtp($randomKey)
    {
        $response = $this->guzzle->request('POST', $this->settings['webservice_url'] . 'LR_GET_RANDOM_KEY_DET', [
            'form_params' => [
                'RANDOM_KEY'      => $randomKey,
                'CLIENT_IP'       => $_SERVER['REMOTE_ADDR'],
                'BROWSER_INFO'    => $_SERVER['HTTP_USER_AGENT'],
                'BROWSER_VERSION' => '',
            ],
            'verify'      => false,
        ]);       

        $responseJson = soap_response($response->getBody());
        
        return $responseJson['OBJECT'][0]['GET_RANDOM_KEY_DET'][0];
    }

    public function validateOTP(Request $request, Response $response, array $args)
    {
        $otp = $request->getParsedBody()['otp'];

        try {
            $result = $this->guzzle->request('POST', $this->settings['webservice_url'] . 'LR_VALIDATE_OTP', [
                'form_params' => [
                    'RANDOM_KEY'      => $this->session->get('randomKey'),
                    'SESSION_KEY'     => $this->session->get('sessionKey'),
                    'OTP'             => $otp,
                    'CLIENT_IP'       => $_SERVER['REMOTE_ADDR'],
                    'BROWSER_INFO'    => $_SERVER['HTTP_USER_AGENT'],
                    'BROWSER_VERSION' => '',
                ],
                'verify'      => false,
            ]);

            $result = soap_response($result->getBody());

            $result = $result['OBJECT'][0]['VALIDATE_OTP'][0];

            if ($result['RETURN_CODE'] == 0) {
                $this->session->set('isUserLoggedIn', true);
            }

           /* if ($result['RETURN_CODE'] === '0') {
                $this->session->set('doc_srno', $result['DIGITAL_DOC_SRNO']);
                $this->session->set('session_key', $result['SESSION_KEY']);
            }*/

            return json_encode($result);

        } catch (ServerException | ClientException $e) {
            $this->logger->info($e->getMessage());

            return $response->withJson([
                'errors' => $e->getMessage(),
            ], 422);
        }
    }

    public function resendOTP(Request $request, Response $response, array $args)
    {
        try {
            if ($this->session->get('resend_attempts') < 4) {
                $this->session->resend_attempts += 1;

                $result = $this->sendOtp($this->session->get('randomKey'));
            }

            $result['NO'] = $this->session->get('resend_attempts');

            if($result['RETURN_CODE'] == 0) {
                 $this->session->set('sessionKey', $result['SESSION_KEY']);
            }

            return json_encode($result);

        } catch (ServerException | ClientException $e) {
            $this->logger->info($e->getMessage());

            return $response->withJson([
                'errors' => $e->getMessage(),
            ], 422);
        }

    }

    public function home(Request $request, Response $response, array $args)
    {

        try {
            $result = $this->guzzle->request('POST', $this->settings['webservice_url'] . 'GET_LR_CONTACT_DOC_LIST', [
                'form_params' => [
                    'CONTACT_SRNO' 			=> $this->session->get('contactSrNo'),
                    'LR_NO' 				=> $this->session->get('lrNo'),
                    'SESSION_KEY'           => $this->session->get('sessionKey'),
                ],
                'verify'      => false,
            ]);

            $result = soap_response($result->getBody());

            if ($result === null) {
                $this->session->set('sessionKey', '');
                $args['invalidSession'] = 'Something went wrong.';
                $args['randomKey'] = $this->session->get('randomKey');
            }


            if ($result) {

                $args['allDocList'] = $result['OBJECT'][0]['GET_LR_CONTACT_DOC_LIST'];

                    if(isset($args['allDocList'][0]['RETURN_CODE']) && $args['allDocList'][0]['RETURN_CODE'] == '-20'){

                            $this->session->set('sessionKey', '');
                            $args['invalidSession'] = $args['allDocList'][0]['RETURN_MESSAGE'];
                            $args['randomKey'] = $this->session->get('randomKey');

                    }else{

                            $args['allDocList'] = $this->getAllList($args['allDocList']);
                            $args['receivedDocList'] = $args['allDocList']['ALL'] ?? [];
                            $args['passwordDocList'] = $args['allDocList']['PASSWORD'] ?? [];

                            /*//$args['cust_details'] = $result['OBJECT'][0]['DIGITAL_DOC_REQUEST'][0] ?? $result['OBJECT'][0]['DIGITAL_DOC_REQUEST'];
                            $args['allDocList'] = $this->getAllList($result['OBJECT'][1]['DIGITAL_DOC_REQ_DTLS']);
                            $args['pendingDocList'] = $args['allDocList']['PENDING'] ?? [];
                            $args['receivedDocList'] = $args['allDocList']['RECEIVED'] ?? [];
                            $args['passwordDocList'] = $args['allDocList']['PASSWORD'] ?? [];

                            array_walk($args['cust_details'], function ($v, $k) {
                                $this->session->set(strtolower($k), $v);
                            });*/
                    }                
            }

        } catch (ServerException | ClientException | \Exception | \Throwable $e) {
            $this->logger->info($e->getMessage());
        }

        return $this->view->render($response, 'home', $args);
    }

    public function getAllList(array $docs)
    {
        $doc_list_arr = [];
        foreach ($docs as $key => $value) {
           /* if ($value['STATUS'] == 'PENDING') {
                $doc_list_arr['PENDING'][$value['APPL_CUST_NUMBER']][] = $value;
            }*/

            if ($value['PASSWORD_FLAG'] == 'Y') {
                $doc_list_arr['PASSWORD'][] = $value;
            }else{
                 $doc_list_arr['ALL'][] = $value;
            }

           /* if (($value['STATUS'] == 'RECEIVED' || $value['STATUS'] == 'UPLOADED') && $value['PASSWORD_FLAG'] != 'Y') {
                $doc_list_arr['RECEIVED'][] = $value;
            }*/
        }

        return $doc_list_arr;
    }

    public function logout(Request $request, Response $response, array $args)
    {
        $this->session::destroy();

        return $this->view->render($response, 'logout', $args);
    }
}