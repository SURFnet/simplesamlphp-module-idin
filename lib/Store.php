<?php

abstract class sspmod_idin_Store {
    protected function __construct(&$config) {
        assert('is_array($config)');
    }
    
    abstract public function getDirectory();
    abstract public function saveDirectory($dirRes);
    
    public function getLastDirectoryTimestamp() {
        try {
            $use_errors = libxml_use_internal_errors(true);
            $response = new SimpleXMLElement($this->getDirectory());
            $response = $response->children('http://www.betaalvereniging.nl/iDx/messages/Merchant-Acquirer/1.0.0');
            $lastTimestamp = (string)$response->createDateTimestamp;
            libxml_clear_errors();
            libxml_use_internal_errors($use_errors);
            return $lastTimestamp;
        }
        catch (Exception $e) {
            return NULL;
        }
    }
    
    public static function getFromConfig($config) {
        $configStore = $config->getValue('store');
        
        if ($configStore == NULL) {
            throw sspmod_idin_Exception::fromString('No store specified.');
        }
        
        $className = SimpleSAML_Module::resolveClass(
            $configStore[0],
            'iDIN_Store',
            'sspmod_idin_Store'
        );
        
        return new $className($configStore);
    }
}
