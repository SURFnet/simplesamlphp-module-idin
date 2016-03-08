<?php

class sspmod_idin_iDIN_Store_File extends sspmod_idin_Store {
    private $filename;

    public function __construct($config) {
        parent::__construct($config);
        
        if (!array_key_exists('filename', $config)) {
            throw sspmod_idin_Exception::fromString('Missing required option \'filename\'.');
        }

        $this->filename = $config['filename'];
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
        $value = @file_get_contents($this->filename);
        return $value;
    }
    
    public function saveDirectory($dirRes) {
        file_put_contents($this->filename, $dirRes->getRawMessage());
    }
}
