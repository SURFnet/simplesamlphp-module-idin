<?php

if (!array_key_exists('stateID', $_REQUEST)) {
    throw sspmod_idin_Exception::fromString('Lost OAuth Client State');
}
if (!array_key_exists('issuerID', $_REQUEST)) {
    throw sspmod_idin_Exception::fromString('No IssuerID specified');
}

$state = SimpleSAML_Auth_State::loadState($_REQUEST['stateID'], sspmod_idin_Auth_Source_iDIN::STAGE_INIT);

if (!array_key_exists(sspmod_idin_Auth_Source_iDIN::AUTHID, $state)) {
    throw sspmod_idin_Exception::fromString('State information has AuthId mismatch');
}

$state[sspmod_idin_Auth_Source_iDIN::ISSUER_ID] = $_REQUEST['issuerID'];

SimpleSAML_Auth_State::saveState($state, sspmod_idin_Auth_Source_iDIN::STAGE_INIT);

$sourceId = $state[sspmod_idin_Auth_Source_iDIN::AUTHID];
$source = SimpleSAML_Auth_Source::getById($sourceId);
if ($source === NULL) {
    throw sspmod_idin_Exception::fromString('Could not find authentication source with id ' . $sourceId);
}

$source->redirectToBank($state);
