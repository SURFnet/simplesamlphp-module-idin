<?php

require_once __DIR__ . '/../../../vendor/autoload.php';

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
    private $nameID;

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
        if (!isset($this->nameID)) {
            $this->debug("nameID is null");
            return NULL;
        }
        $attributes = array(
            self::SURFCONEXT_UID => array($this->nameID)
        );
        $this->debug("nameID = " . $this->nameID);
        return $attributes;
    }

    public function authenticate(&$state) {
        $this->debug(__METHOD__);

        assert('is_array($state)');

        $attributes = $this->getUser();
        if ($attributes !== NULL) {
            $state['Attributes'] = $attributes;
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

        $attributes = $this->getUser();
        if ($attributes !== NULL) {
            $state['Attributes'] = $attributes;
            return;
        }
        
        $response = sspmod_idin_Interface::sendAuthenticationRequest(array(
            'entranceCode' => 'test',
            'issuerID' => $state[self::ISSUER_ID]
        ));
        
        $state[self::TRANSACTION_RESPONSE] = $response;
        
        $stateID = SimpleSAML_Auth_State::saveState($state, self::STAGE_INIT);
        \SimpleSAML\Utils\HTTP::redirectTrustedURL($response->getIssuerAuthenticationUrl(), array(
            'stateID' => $stateID
        ));
    }
    
    public function getStatus(&$state) {
        $this->debug(__METHOD__);

        assert('is_array($state)');
        assert('array_key_exists(sspmod_idin_Auth_Source_iDIN::TRANSACTION_ID, $state)');
        
        $attributes = $this->getUser();
        if ($attributes !== NULL) {
            $state['Attributes'] = $attributes;
            return;
        }
        
        $response = sspmod_idin_Interface::sendStatusRequest(array(
            'transactionID' => $state[self::TRANSACTION_ID]
        ));
        
        $state[self::STATUS_RESPONSE] = $response;
        
        
        $this->nameID =
            $response->getSamlResponse()->getAttributes()['urn:nl:bvn:bankid:1.0:consumer.bin'];
        
        $state['Attributes'] = $this->getUser();
        SimpleSAML_Auth_Source::completeAuth($state);
    }

    public static function resume() {
        $attributes = $source->getUser();
        if ($attributes === NULL) {
            throw new SimpleSAML_Error_Exception('User not authenticated after login page.');
        }

        $state['Attributes'] = $attributes;
        SimpleSAML_Auth_Source::completeAuth($state);

        assert('FALSE');
    }


    /**
     * This function is called when the user start a logout operation, for example
     * by logging out of a SP that supports single logout.
     *
     * @param array &$state  The logout state array.
     */
    public function logout(&$state) {
        $this->debug(__METHOD__);
        
        assert('is_array($state)');

        if (!session_id()) {
            /* session_start not called before. Do it here. */
            session_start();
        }

        /*
         * In this example we simply remove the 'uid' from the session.
         */
        unset($_SESSION['uid']);

        /*
         * If we need to do a redirect to a different page, we could do this
         * here, but in this example we don't need to do this.
         */
    }

}
