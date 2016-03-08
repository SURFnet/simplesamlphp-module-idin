<?php

require_once(dirname(dirname(dirname(dirname(__FILE__)))) . '/lib/_autoload.php');

if (!array_key_exists('stateID', $_REQUEST)) {
    throw sspmod_idin_Exception::fromString('Lost OAuth Client State');
}
$state = SimpleSAML_Auth_State::loadState($_REQUEST['stateID'], sspmod_idin_Auth_Source_iDIN::STAGE_INIT);

assert('array_key_exists(sspmod_idin_Auth_Source_iDIN::AUTHID, $state)');
if (!array_key_exists(sspmod_idin_Auth_Source_iDIN::AUTHID, $state)) {
    throw sspmod_idin_Exception::fromString('State information has AuthId mismatch');
}
assert('array_key_exists(sspmod_idin_Auth_Source_iDIN::DIRECTORY_RESPONSE, $state)');
if (!array_key_exists(sspmod_idin_Auth_Source_iDIN::DIRECTORY_RESPONSE, $state)) {
    throw sspmod_idin_Exception::fromString('State information has Directory missing');
}

$response = $state[sspmod_idin_Auth_Source_iDIN::DIRECTORY_RESPONSE];

if ($response->getIsError()) {
    throw sspmod_idin_Exception::fromErrorResponse($response->getErrorResponse());
}
?>

<!doctype html>
<html>
<head>
  <meta charset="utf-8">
  <meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <meta name="HandheldFriendly" content="True">

  <title>iDIN Authentication</title>

  <link rel="stylesheet" type="text/css" media="screen" href="css/concise.min.css" />
  <link rel="stylesheet" type="text/css" media="screen" href="css/narrow.css" />
</head>

<body>
  <div container>
    <header row class="siteHeader">
      <div column=4>
        <h1 class="logo">iDIN Authentication</h1>
      </div>
    </header>
    <div class="feature">
      <h1 class="feature-title">iDIN Authentication</h1>
      <p>You can use your bank credentials to authenticate to this website. Choose your bank to continue.</p>
    </div>

    <main class="siteContent">
      <div row>
        <div column=8>
          <form method="POST" action="runauth.php">
              <p>
                  <select name="issuerID">
                      <?php foreach ($response->getIssuers() as $issuer) { ?>
                          <option value="<?php echo $issuer->getID(); ?>"><?php echo $issuer->getName() . ' (' . $issuer->getCountry() . ')'; ?></option>
                      <?php } ?>
                  </select>
              </p>
              <input name="stateID" type="hidden" value="<?php echo $_REQUEST['stateID']; ?>" />
              <input type="submit" value="Next" />
          </form>
        </div>
      </div>
    </main>
    <footer class="siteFooter">
      <p>Copyright &copy; 2016</p>
    </footer>
  </div>
</body>
</html>