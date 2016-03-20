<?php

$stateID = sspmod_idin_Auth_Source_iDIN::getSession()->getData(sspmod_idin_Auth_Source_iDIN::AUTHID, 'stateID');
assert('!empty($stateID)');
$state = SimpleSAML_Auth_State::loadState($stateID, sspmod_idin_Auth_Source_iDIN::STAGE_INIT);

assert('array_key_exists(sspmod_idin_Auth_Source_iDIN::AUTHID, $state)');
if (!array_key_exists(sspmod_idin_Auth_Source_iDIN::AUTHID, $state)) {
    sspmod_idin_Exception::fromString($state, 'State information has AuthId mismatch.');
}
assert('array_key_exists(sspmod_idin_Auth_Source_iDIN::DIRECTORY_RESPONSE, $state)');
if (!array_key_exists(sspmod_idin_Auth_Source_iDIN::DIRECTORY_RESPONSE, $state)) {
    sspmod_idin_Exception::fromString($state, 'State information has Directory missing.');
}

$response = $state[sspmod_idin_Auth_Source_iDIN::DIRECTORY_RESPONSE];

if ($response->getIsError()) {
    sspmod_idin_Exception::fromErrorResponse($state, $response->getErrorResponse());
}
?>

<!doctype html>
<html lang="en">
<head>
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>iDin Bank</title>
    <link rel="stylesheet" href="css/default.css">
    <link href='https://fonts.googleapis.com/css?family=Roboto:300,400,700' rel='stylesheet' type='text/css'>
    <script type="text/javascript">
        function validate() {
            var x = document.forms[0]["issuerID"].value;
            if (x == null || x == "") {
                alert("Please select a bank.");
                return false;
            }
            
            return true;
        }
    </script>
</head>
<body>
    <div id="wrapper">
        <div id="header">
            <div id="logo-surf">
                <a href="#">
                    <img class="logo-surf" src="images/SURFconext.png" />
                </a>
            </div>
        </div>
        <div id="content">
            <div id="logo">
                <a href="#">
                    <img class="logo-img" src="images/iDIN logo.png" />
                    <span class="page-title">iDIN Authentication</span>
                </a>
            </div>
            <p>You can use your bank credentials to authenticate to this website. Some attributes of your account will be shared with this website. Select your bank to continue the authentication process.</p>
            <div id="select-area">
                <form method="POST" action="runauth.php">
                    <div class="select" name="issuerID">
                        <span class="arr"></span>
                            <select name="issuerID">
                                <option value="">Please, select your bank</option>
                                <?php
                                    foreach ($response->getIssuersByCountry() as $countryName => $issuers) {
                                        echo '<optgroup label="' . $countryName . '">';
                                        foreach ($issuers as $issuer) {
                                            echo '<option value="' .  $issuer->getID() . '">' . $issuer->getName() . '</option>';
                                        }
                                    }
                                ?>
                            </select>
                    </div>
                    <input type="submit" value="NEXT" />
                </form>
            </div>
        </div>
        <footer id="footer">
            <div>
                <p>
                <small>Copyright &copy; 2016 iDIN Authentication</small>
                </p>
            </div>
        </footer>
    </div>
</body>
</html>