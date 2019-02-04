<?php
return [
    'settings' => [
        'displayErrorDetails' => true, // set to false in production
        'addContentLengthHeader' => false, // Allow the web server to send the content-length header

        'app' => [
            //'baseUrl' => 'https://applicationsuat.hdfc.com/pendency/',
            'baseUrl' => 'http://perfios/',
            'uploadsPath' => __DIR__ . '/../storage/uploads',
            //'webservice_url' => 'http://192.168.10.220/instahomeloan_uat5_v1/instaservice.asmx/',
            //'webservice_url' => 'https://vmapiuat.hdfc.com/PENDENCY_WS_DMZ/Service.asmx/',
            'webservice_url' => 'https://vmapiuat.hdfc.com/PENDANCY_WS_DMZ_DBILPSUAT/Service.asmx/',
            'perfios_endpoint' => 'https://demo.perfios.com/KuberaVault/insights/',
            //'perfios_return_url' => 'https://applicationsuat.hdfc.com/pendency/perfios-response',
            'perfios_return_url' => 'http://perfios/perfios-response',
            'perfios_encryption_cmd' => '"C:\\Program Files\\Java\\jdk1.8.0_131\\bin\\java.exe" -classpath D:\\apps\\javaapps\\perfios\\bcprov-ext-jdk15-1.46.jar;D:\\apps\\javaapps\\perfios\\ com.perfios.demo.OnlineSampleHdfc',
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
