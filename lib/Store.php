<?php

abstract class sspmod_idin_Store {
    protected function __construct(&$config) {
        assert('is_array($config)');
    }
    
    abstract public function getLastDirectoryTimestamp();
    abstract public function getDirectory();
    abstract public function saveDirectory($dirRes);

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
