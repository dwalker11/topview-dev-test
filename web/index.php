<?php
  // Application logic goes here
  try {
    $sourceUrl  = "https://blockchain.info/ticker";
    $tickerData = json_decode(file_get_contents($sourceUrl), true);
    $tickerData = $tickerData ?: [];
  } catch (Exception $e) {
    echo "Oh no, something went wrong.";
    die();
  }

?>

<!DOCTYPE html>
<html lang="en">
  <head>
    <meta charset="utf-8">
    <title>TopView Blockchain Ticker</title>

    <!-- Custom styles for this page -->
    <link href="styles/main.css" rel="stylesheet"/>
  </head>

  <body>

    <!-- Begin page content -->
    <div>
      <div>
        <h1>Ticker Data</h1>
      </div>
      <div>
        <?php foreach ($tickerData as $ticker): ?>
          <p><?php print_r($ticker); ?></p>
        <?php endforeach; ?>
      </div>
    </div>


    <!-- Begin JavaScript
    ================================================== -->
    <script src="scripts/main.js"></script>
  </body>
</html>
