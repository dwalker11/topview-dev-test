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

    <!-- Bootstrap styles -->
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0-beta/css/bootstrap.min.css" integrity="sha384-/Y6pD6FV/Vv2HJnA6t+vslU6fwYXjCFtcEpHbNJ0lyAFsXTsjBbfaDjzALeQsN6M" crossorigin="anonymous">

    <!-- Custom styles for this page -->
    <link href="styles/main.css" rel="stylesheet"/>
  </head>

  <body>

    <!-- Begin page content -->
    <div class="container">
      <div class="mt-1">
        <h1>Ticker Data</h1>
      </div>

      <div class="col-10">
        <form class="form-inline form-options-js">
          <div class="form-group">
            <label>
              Order
              <select class="form-control" id="exampleFormControlSelect1">
                <option>15m</option>
                <option>last</option>
                <option>buy</option>
                <option>sell</option>
                <option>symbol</option>
              </select>
            </label>
          </div>
          <div class="form-group">
            <label>
              Limit
              <input class="form-control" id="" placeholder="10">
            </label>
          </div>
          <button type="submit" class="btn btn-primary">Refresh</button>
        </form>
      </div>

      <br>

      <div class="col-10">
        <div>
          <table class="table table-striped table-bordered">
            <thead>
              <tr>
                <th></th>
                <?php foreach ($tickerHeadings as $heading => $data): ?>
                  <th><a href="#" class="order-js"><?php echo $heading; ?></a></th>
                <?php endforeach; ?>
              </tr>
            </thead>
            <tbody>
              <?php foreach ($tickerData as $denom => $ticker): ?>
                <tr>
                  <td><?php echo $denom; ?></td>
                  <?php foreach ($ticker as $data): ?>
                    <td><?php echo (is_numeric($data)) ? '$'.number_format($data, 2, '.', ',') : $data; ?></td>
                  <?php endforeach; ?>
                </tr>
              <?php endforeach; ?>
            </tbody>
          </table>
        </div>
      </div>
    </div>


    <!-- Begin JavaScript
    ================================================== -->
    <script src="https://code.jquery.com/jquery-3.2.1.slim.min.js" integrity="sha384-KJ3o2DKtIkvYIK3UENzmM7KCkRr/rE9/Qpg6aAZGJwFDMVNA/GpGFF93hXpG5KkN" crossorigin="anonymous"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.11.0/umd/popper.min.js" integrity="sha384-b/U6ypiBEHpOf/4+1nzFpr53nxSS+GLCkfwBdFNTxtclqqenISfwAzpKaMNFNmj4" crossorigin="anonymous"></script>
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0-beta/js/bootstrap.min.js" integrity="sha384-h0AbiXch4ZDo7tp9hKZ4TsHbi047NrKGLO3SEJAg45jXxnGIfYzk4Si90RDIqNm1" crossorigin="anonymous"></script>
    <script>
      function updateTable(order, limit){
        // get data
        $.get();

        // redraw table
      }

      $(function(){
        $('.order-js').on('click', function(){
          // grab the elements value
          updateTable();
        });

        $('.form-options-js').on('click', function(e){
          e.preventDefault();
          // grab the selected options
          updateTable();
        });
      });
    </script>
  </body>
</html>
