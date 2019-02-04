<?php
// Routes
use App\Http\Controllers\Home\HomeController;
use App\Http\Controllers\Home\PerfiosController;
use App\Http\Controllers\Home\UploadDocumentController;

$app->get('/home', HomeController::class . ':home')->setName('home');

//$app->post('/home', HomeController::class . ':store')->setName('home');

$app->post('/validate-otp', HomeController::class . ':validateOTP')->setName('validate-otp');

$app->post('/resend-otp', HomeController::class . ':resendOTP')->setName('resend-otp');

$app->post('/uploadDocument', UploadDocumentController::class . ':upload')->setName('uploadDocument');

$app->post('/view-document', UploadDocumentController::class . ':viewDocument')->setName('viewDocument');

$app->post('/check-password', UploadDocumentController::class . ':readPassword')->setName('readPassword');

$app->post('/doc-list', UploadDocumentController::class . ':getDocList')->setName('docList');

$app->post('/generate-uploader', UploadDocumentController::class . ':generateFileUploader')->setName('generate-uploader');

$app->post('/perfios-request', PerfiosController::class . ':request')->setName('perfios-request');

$app->any('/perfios-response', PerfiosController::class . ':response')->setName('perfios-response');

$app->post('/download', PerfiosController::class . ':download')->setName('downloadDocument');

$app->get('/logout', HomeController::class . ':logout')->setName('logout');

$app->get('/{token}', HomeController::class . ':index')->setName('token');
