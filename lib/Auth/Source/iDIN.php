<?php

$baseDir = dirname(dirname(dirname(dirname(dirname(dirname(__FILE__))))));
require_once($baseDir . '/lib/_autoload.php');

class sspmod_idin_Auth_Source_iDIN extends SimpleSAML_Auth_Source {

    const STAGE_INIT = 'idin:init';
    const AUTHID = 'idin:AuthID';
    
    const DIRECTORY_RESPONSE = 'idin:DirectoryResponse';
    const TRANSACTION_RESPONSE = 'idin:TransactionResponse';
    const STATUS_RESPONSE = 'idin:StatusResponse';
    const ISSUER_ID = 'idin:IssuerId';
    const TRANSACTION_ID = 'idin:TransactionId';
    
    const SURFCONEXT_UID = 'urn:mace:dir:attribute-def:uid';
    
    private $interface;
    private $attributes;

    private function debug($message)
    {
        if ($message != NULL) {
            SimpleSAML_Logger::debug('idin - ' . $message);
        }
    }

    public function __construct($info, $config) {
        $this->debug(__METHOD__);
        assert('is_array($info)');
        assert('is_array($config)');
        parent::__construct($info, $config);
        
        $this->interface = new sspmod_idin_Interface();
        $this->interface->initialize();
    }

    private function getUser() {
        $this->debug(__METHOD__);
        if (!session_id()) {
            $this->debug("starting session");
            session_start();
        }
        if (!isset($this->attributes)) {
            $this->debug("nameID is null");
            return NULL;
        }
        
        $processedAttributes = array();
        foreach ($this->attributes as $key => $value) {
            $processedAttributes[$key] = array ( $value );
        }
        
        return $processedAttributes;
    }

    public function authenticate(&$state) {
        $this->debug(__METHOD__);

        assert('is_array($state)');

        $attr = $this->getUser();
        if ($attr !== NULL) {
            $state['Attributes'] = $attr;
            return;
        }
        
        $response = sspmod_idin_Interface::sendDirectoryRequest();

        $state[self::AUTHID] = $this->authId;
        $state[self::DIRECTORY_RESPONSE] = $response;

        $stateID = SimpleSAML_Auth_State::saveState($state, self::STAGE_INIT);

        \SimpleSAML\Utils\HTTP::redirectTrustedURL(SimpleSAML_Module::getModuleURL('idin/beginauth.php'), array(
            'stateID' => $stateID
        ));

        assert('FALSE');
    }
    
    public function redirectToBank(&$state) {
        $this->debug(__METHOD__);

        assert('is_array($state)');
        assert('array_key_exists(sspmod_idin_Auth_Source_iDIN::ISSUER_ID, $state)');

        $attr = $this->getUser();
        if ($attr !== NULL) {
            $state['Attributes'] = $attr;
            return;
        }
        
        $response = sspmod_idin_Interface::sendAuthenticationRequest(array(
            'entranceCode' => 'test',
            'issuerID' => $state[self::ISSUER_ID]
        ));
        
        $state[self::TRANSACTION_RESPONSE] = $response;
        
        if ($response->getIsError()) {
            throw sspmod_idin_Exception::fromErrorResponse($response->getErrorResponse());
        }
        
        $stateID = SimpleSAML_Auth_State::saveState($state, self::STAGE_INIT);
        \SimpleSAML\Utils\HTTP::redirectTrustedURL($response->getIssuerAuthenticationUrl(), array(
            'stateID' => $stateID
        ));
    }
    
    public function resume(&$state) {
        $this->debug(__METHOD__);

        assert('is_array($state)');
        assert('array_key_exists(sspmod_idin_Auth_Source_iDIN::TRANSACTION_ID, $state)');
        
        $attr = $this->getUser();
        if ($attr !== NULL) {
            $state['Attributes'] = $attr;
            return;
        }
        
        $response = sspmod_idin_Interface::sendStatusRequest(array(
            'transactionID' => $state[self::TRANSACTION_ID]
        ));
        
        $state[self::STATUS_RESPONSE] = $response;
        
        if ($response->getIsError()) {
            throw sspmod_idin_Exception::fromErrorResponse($response->getErrorResponse());
        }
        
        $this->attributes =
            $response->getSamlResponse()->getAttributes();
        
        $state['Attributes'] = $this->getUser();
        SimpleSAML_Auth_Source::completeAuth($state);
    }

    public function logout(&$state) {
        $this->debug(__METHOD__);
        
        assert('is_array($state)');

        if (!session_id()) {
            session_start();
        }

        unset($_SESSION['uid']);
    }
}
