<?php

class sspmod_idin_Exception extends SimpleSAML_Error_Exception {
    public function __construct($message, $code = 0, Exception $cause = null) {
        parent::__construct($message, $code, $cause);
    }
    
    public static function fromErrorResponse($errorResponse) {
        return new sspmod_idin_Exception($errorResponse->getErrorMessage());
    }
    
    public static function fromString($str) {
        return new sspmod_idin_Exception($str);
    }
}