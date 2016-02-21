<?php

require_once __DIR__ . '/../vendor/autoload.php';

if (!isset($_REQUEST['ReturnTo'])) {
    die('Missing ReturnTo parameter.');
}

$returnTo = \SimpleSAML\Utils\HTTP::checkURLAllowed($_REQUEST['ReturnTo']);

$comm = new \BankId\Merchant\Library\Communicator();

//if ($_SERVER['REQUEST_METHOD'] === 'POST') {
//    \SimpleSAML\Utils\HTTP::redirectTrustedURL($returnTo);
//}
?>

<!DOCTYPE html>
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
<title>idin login page</title>
</head>
<body>
<h1>idin login page</h1>

<p>Directory response: <?php echo $comm->getDirectory()?></p>

<!--
<form method="post" action="?">

<p>
Choose your bank:
</p>

<input type="hidden" name="ReturnTo" value="<?php echo htmlspecialchars($returnTo); ?>">
<p><input type="submit" value="Log in"></p>
</form>
-->
</body>
</html>
