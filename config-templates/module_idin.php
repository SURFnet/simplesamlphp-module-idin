<?php
/* 
 * Configuration for the iDIN module.
 */

$config = array(
    'merchantID' => '000000000',
    'subID' => '0',
    'returnUrl' => 'https://localhost/simplesaml/module.php/idin/return.php',
    'directoryUrl' => 'https://my.bank/directory',
    'transactionUrl' => 'https://my.bank/transaction',
    'statusUrl' => 'https://my.bank/status',
    
    'merchant:certificate:file' => 'my-certificate.p12',
    'merchant:certificate:password' => 'my-password',
    'routingservice:certificate' => 'routing-service.cer',
    'saml:certificate:file' => 'my-certificate.p12',
    'saml:certificate:password' => 'my-password',
    'servicelogs:enabled' => true,
    'servicelogs:location' => 'C:\MyWebshop\idin\ServiceLogs',
    'servicelogs:pattern' => '%Y-%M-%D\%h%m%s.%f-%a.xml',
    
    /*
    'store' => array(
        'idin:Database',
        'dsn' => 'mysql:host=localhost;dbname=simplesaml',
        'username' => 'username',
        'password' => 'password',
        'table' => 'module.idin'
    ),
    */
    
    /*
    'store' => array(
        'idin:Memcache',
        'servers' => array(
            array(
                'hostname' => 'localhost',
                'port' => '11211'
            ),
        ),
        'key' => 'simplesamlphp.module.idin.directory'
    ),
    */
    
    'store' => array(
        'idin:File',
        'filename' => 'C:\MyWebshop\idin\Directory.xml'
    )
);
