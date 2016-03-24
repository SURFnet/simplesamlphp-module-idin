<?php

class sspmod_idin_Exception {
    public static function fromErrorResponse($state, $errorRes) {
        $s = '';
        if ($errorRes->getErrorCode() != '') {
            $s = $s . 'Error code: ' . $errorRes->getErrorCode() . '\r\n';
        }
        if ($errorRes->getErrorMessage() != '') {
            $s = $s . 'Error message: ' . $errorRes->getErrorMessage() . '\r\n';
        }
        if ($errorRes->getErrorDetails() != '') {
            $s = $s . 'Error details: ' . $errorRes->getErrorDetails() . '\r\n';
        }
        if ($errorRes->getConsumerMessage() != '') {
            $s = $s . 'Consumer message: ' . $errorRes->getConsumerMessage() . '\r\n';
        }
        if ($errorRes->getSuggestedAction() != '') {
            $s = $s . 'Suggested action: ' . $errorRes->getSuggestedAction() . '\r\n';
        }
        if ($errorRes->getAdditionalInformation() != NULL)
        {
            if ($errorRes->getAdditionalInformation()->getStatusMessage() != '') {
                $s = $s . 'SAML status message: ' . $errorRes->getAdditionalInformation()->getStatusMessage() . '\r\n';
            }
            if ($errorRes->getAdditionalInformation()->getStatusCodeFirstLevel() != '') {
                $s = $s . 'SAML status code 1st level: ' . $errorRes->getAdditionalInformation()->getStatusCodeFirstLevel() . '\r\n';
            }
            if ($errorRes->getAdditionalInformation()->getStatusCodeSecondLevel() != '') {
                $s = $s . 'SAML status code 2nd level: ' . $errorRes->getAdditionalInformation()->getStatusCodeSecondLevel() . '\r\n';
            }
        }
        
        SimpleSAML_Auth_State::throwException($state, new SimpleSAML_Error_Exception($s));
    }
    
    public static function fromString($state, $str) {
        SimpleSAML_Auth_State::throwException($state, new SimpleSAML_Error_Exception($str));
    }
}