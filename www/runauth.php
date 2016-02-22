<?php

require __DIR__ . '/../vendor/autoload.php';

sspmod_idin_Interface::initialize();
$response = sspmod_idin_Interface::sendAuthenticationRequest($_REQUEST['issuerID']);

//var_dump($response);

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
    <main class="siteContent">
      <div row>
        You will now be redirected to your bank's website. You will need to enter your credentials and allow the authentication to take place. Once you approve this operation, you will be redirected back to this website and you will be logged in.
      </div>
      <div row>
        <a href="<?php echo $response->getIssuerAuthenticationURL(); ?>"><button>Click here to continue...</button></a>
      </div>
    </main>
    <footer class="siteFooter">
      <p>Copyright &copy; 2016</p>
    </footer>
  </div>
</body>
</html>
