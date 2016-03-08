<?php

require_once __DIR__ . '/../../../vendor/autoload.php';

class sspmod_idin_iDIN_Store_Memcache extends sspmod_idin_Store {
    private $mc;
    private $key;

    public function __construct($config) {
        parent::__construct($config);
        
        if (!array_key_exists('servers', $config)) {
            throw sspmod_idin_Exception::fromString('Missing required option \'dsn\'.');
        }

        $this->mc = new Memcache();
        $this->key = $config['key'];
        
        $servers = $config['servers'];
        foreach ($servers as $key => $value) {
            $port = (int) $value['port'];
            $this->mc->addServer($value['hostname'], $port, true);
        }
    }
    
    public function getLastDirectoryTimestamp() {
        try {
            $use_errors = libxml_use_internal_errors(true);
            $response = new SimpleXMLElement($this->getDirectory());
            $lastTimestamp = (string)$response->createDateTimestamp;
            libxml_clear_errors();
            libxml_use_internal_errors($use_errors);
            return $lastTimestamp;
        }
        catch (Exception $e) {
            return NULL;
        }
    }
    
    public function getDirectory() {
        $value = @$this->mc->get($this->key);
        return $value;
    }
    
    public function saveDirectory($dirRes) {
        $this->mc->set($this->key, $dirRes->getRawMessage());
    }
}
