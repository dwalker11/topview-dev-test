<?php
  // Application logic
  try {
    $sourceUrl  = "https://blockchain.info/ticker";
    $tickerData = json_decode(file_get_contents($sourceUrl), true);
    $tickerData = $tickerData ?: [];
    $tickerHeadings = current($tickerData);
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
        <table>
          <thead>
            <tr>
              <th></th>
              <?php foreach ($tickerHeadings as $heading => $data): ?>
                <th><?php echo $heading; ?></th>
              <?php endforeach; ?>
            </tr>
          </thead>
          <tbody>
            <?php foreach ($tickerData as $denom => $ticker): ?>
              <tr>
                <td><?php echo $denom; ?></td>
                <?php foreach ($ticker as $data): ?>
                  <td><?php echo $data; ?></td>
                <?php endforeach; ?>
              </tr>
            <?php endforeach; ?>
          </tbody>
        </table>
      </div>
    </div>


    <!-- Begin JavaScript
    ================================================== -->
    <script src="scripts/main.js"></script>
  </body>
</html>
