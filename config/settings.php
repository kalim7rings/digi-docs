<?php
return [
    'settings' => [
        'displayErrorDetails' => false, // set to false in production
        'addContentLengthHeader' => false, // Allow the web server to send the content-length header

        'app' => [
            'baseUrl' => HTTP_HOST.BASE_PATH,
            'uploadsPath' => __DIR__ . '/../storage/uploads',
            'webservice_url' => 'https://dmzws.hdfc.com/onlineloans_live_v1/instaservice.asmx/',
            'perfios_endpoint' => 'https://www.perfios.com/KuberaVault/insights/',
            'perfios_return_url' => HTTP_HOST.BASE_PATH.'perfios-response',
            'perfios_encryption_cmd' => '"C:\\Program Files\\Java\\jdk1.8.0_131\\bin\\java.exe" -classpath E:\\apps\\javaapps\\perfios\\bcprov-ext-jdk15-1.46.jar;E:\\apps\\javaapps\\perfios\\ com.perfios.production.OnlineSampleHdfc',
        ],

        // Renderer settings
        'view' => [
            'template_path' => __DIR__ . '/../assets/views',
            'cache_path' => __DIR__ . '/../storage/views',
        ],

        // Monolog settings
        'logger' => [
            'name' => 'slim-app',
            'path' => isset($_ENV['docker']) ? 'php://stdout' : __DIR__ . '/../storage/logs/app.log',
            'level' => \Monolog\Logger::DEBUG,
        ],
    ],
];
