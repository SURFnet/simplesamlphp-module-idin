<?php

if (!array_key_exists('stateID', $_REQUEST)) {
    throw new Exception('Lost OAuth Client State');
}
if (!array_key_exists('trxid', $_REQUEST)) {
    throw new Exception('No trxid specified');
}

$state = SimpleSAML_Auth_State::loadState($_REQUEST['stateID'], sspmod_idin_Auth_Source_iDIN::STAGE_INIT);

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
