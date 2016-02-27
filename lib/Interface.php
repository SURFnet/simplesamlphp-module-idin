<?php

require_once __DIR__ . '/../../../vendor/autoload.php';

class sspmod_idin_Interface {
    private static $_bankid_config;
    private static $_bankid_communicator;
    
    private static function getCertificatePath($name) {
        $certificateFullPath =
            SimpleSAML_Utilities::resolvePath(
                $name,
                SimpleSAML_Configuration::getInstance()->getPathValue('certdir', 'cert/')
            );
        return $certificateFullPath;
    }
    
    public static function initialize() {
        $config = SimpleSAML_Configuration::getConfig('module_idin.php');

        if (self::$_bankid_config == NULL)
        {
            self::$_bankid_config = new \BankId\Merchant\Library\Configuration();
            self::$_bankid_config->MerchantID = $config->getValue('merchantID');
            self::$_bankid_config->MerchantSubID = $config->getValue('subID');
            
            self::$_bankid_config->AcquirerDirectoryUrl = $config->getValue('directoryUrl');
            self::$_bankid_config->AcquirerTransactionUrl = $config->getValue('transactionUrl');
            self::$_bankid_config->AcquirerStatusUrl = $config->getValue('statusUrl');
            self::$_bankid_config->MerchantReturnUrl = $config->getValue('returnUrl');;
            
            self::$_bankid_config->MerchantCertificateFile = self::getCertificatePath($config->getValue('merchant:certificate:file'));
            self::$_bankid_config->MerchantCertificatePassword = $config->getValue('merchant:certificate:password');
            
            self::$_bankid_config->RoutingServiceCertificateFile = self::getCertificatePath($config->getValue('routingservice:certificate'));
            
            self::$_bankid_config->SamlCertificateFile = self::getCertificatePath($config->getValue('saml:certificate:file'));
            self::$_bankid_config->SamlCertificatePassword = $config->getValue('saml:certificate:password');
            
            self::$_bankid_config->ServiceLogsEnabled = $config->getValue('servicelogs:enabled');
            self::$_bankid_config->ServiceLogsLocation = $config->getValue('servicelogs:location');
            self::$_bankid_config->ServiceLogsPattern = $config->getValue('servicelogs:pattern');
            
            \BankId\Merchant\Library\Configuration::setup(self::$_bankid_config);
        }
        
        if (self::$_bankid_communicator == NULL) {
            self::$_bankid_communicator = new \BankId\Merchant\Library\Communicator();
        }
    }
    
    public static function sendDirectoryRequest() {
        $dirRes = self::$_bankid_communicator->getDirectory();
        return $dirRes;
    }
    
    public static function sendAuthenticationRequest($params) {
        $trxReq = new \BankId\Merchant\Library\AuthenticationRequest();
        $trxReq->setEntranceCode($params['entranceCode']);
        $trxReq->setLanguage('en');
        $trxReq->setIssuerID($params['issuerID']);
        $trxReq->setMerchantReference(\BankId\Merchant\Library\AuthenticationRequest::generateMerchantReference());
        $trxReq->setAssuranceLevel(\BankId\Merchant\Library\AssuranceLevel::$Loa2);
        $trxReq->setRequestedServiceID(1);
        
        $trxRes = self::$_bankid_communicator->newAuthenticationRequest($trxReq);
        return $trxRes;
    }
    
    public static function sendStatusRequest($params) {
        $stsReq = new \BankId\Merchant\Library\StatusRequest();
        $stsReq->setTransactionID($params['transactionID']);
        
        $stsRes = self::$_bankid_communicator->getResponse($stsReq);
        return $stsRes;
    }
}