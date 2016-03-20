<?php

$stateID = sspmod_idin_Auth_Source_iDIN::getSession()->getData(sspmod_idin_Auth_Source_iDIN::AUTHID, 'stateID');
assert('!empty($stateID)');
$state = SimpleSAML_Auth_State::loadState($stateID, sspmod_idin_Auth_Source_iDIN::STAGE_INIT);

if (!array_key_exists('trxid', $_REQUEST) || empty($_REQUEST['trxid'])) {
    sspmod_idin_Exception::fromString($state, 'No transaction id specified. Request must have a trxid parameter.');
}

if (!array_key_exists(sspmod_idin_Auth_Source_iDIN::AUTHID, $state)) {
    sspmod_idin_Exception::fromString($state, 'State has AuthId mismatch.');
}

$state[sspmod_idin_Auth_Source_iDIN::TRANSACTION_ID] = $_REQUEST['trxid'];

$sourceId = $state[sspmod_idin_Auth_Source_iDIN::AUTHID];
$source = SimpleSAML_Auth_Source::getById($sourceId);
if ($source === NULL) {
    sspmod_idin_Exception::fromString('Could not find authentication source with id ' . $sourceId);
}

$source->resume($state);
