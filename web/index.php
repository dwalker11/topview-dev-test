<?php
  // Remove later
  ini_set('display_errors', true);
  error_reporting(E_ALL);

  // fetch ticker data
  try {
    $sourceUrl  = "https://blockchain.info/ticker";
    $tickerData = json_decode(file_get_contents($sourceUrl), true);
    $tickerData = $tickerData ?: [];
    $tickerHeadings = current($tickerData);
  } catch (Exception $e) {
    $tickerHeadings = $tickerData = [];
  }

  // fetch, sanitize, & validate user options
  if (isset($_GET['order'])) {
    $order = filter_input(INPUT_GET, 'order', FILTER_SANITIZE_STRING);
    $order = rtrim(strtolower($order));

    if (in_array($order, ['15m', 'last', 'buy', 'sell', 'symbol'])) {
      uasort($tickerData, function ($a, $b) use ($order) {
        if ($a[$order] == $b[$order]) {
          return 0;
        }

        return ($a[$order] < $b[$order]) ? -1 : 1;
      });
    }

  }

  if (isset($_GET['limit'])) {
    $limit = filter_var(
      filter_input(INPUT_GET, 'limit', FILTER_SANITIZE_STRING),
      FILTER_VALIDATE_INT,
      ['options' => ['default' => 0]]);

    $tickerData = array_slice($tickerData, 0, ($limit ?: null));
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
                  <td><?php echo (is_numeric($data)) ? number_format($data, 2, '.', ',') : $data; ?></td>
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
