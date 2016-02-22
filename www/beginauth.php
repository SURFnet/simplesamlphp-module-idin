<?php

require __DIR__ . '/../vendor/autoload.php';

sspmod_idin_Interface::initialize();

$response = sspmod_idin_Interface::sendDirectoryRequest();
//var_dump($Model);
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
      <p class="feature-description">You can use your bank credentials to authenticate to this website. Choose your bank to continue.</p>
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
