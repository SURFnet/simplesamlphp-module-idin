<?php

$stateID = sspmod_idin_Auth_Source_iDIN::getSession()->getData(sspmod_idin_Auth_Source_iDIN::AUTHID, 'stateID');
assert('!empty($stateID)');
$state = SimpleSAML_Auth_State::loadState($stateID, sspmod_idin_Auth_Source_iDIN::STAGE_INIT);

if (!array_key_exists('trxid', $_REQUEST) || empty($_REQUEST['trxid'])) {
    throw new Exception('No trxid specified');
}

if (!array_key_exists(sspmod_idin_Auth_Source_iDIN::AUTHID, $state)) {
    throw new Exception('State information has AuthId mismatch');
}

$state[sspmod_idin_Auth_Source_iDIN::TRANSACTION_ID] = $_REQUEST['trxid'];

$sourceId = $state[sspmod_idin_Auth_Source_iDIN::AUTHID];
$source = SimpleSAML_Auth_Source::getById($sourceId);
if ($source === NULL) {
    throw new Exception('Could not find authentication source with id ' . $sourceId);
}

$source->resume($state);
