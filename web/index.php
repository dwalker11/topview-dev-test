<?php
  // Remove later
  // ini_set('display_errors', true);
  // error_reporting(E_ALL);

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

  if (isset($_GET['json'])) {
    header('Content-Type: application/json');
    echo json_encode($tickerData);
    exit(0);
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

      <hr>

      <div class="col-10">
        <form class="form-inline form-options-js">
          <label>Order</label>&nbsp;
          <select class="form-control order-input-js" id="exampleFormControlSelect1">
            <option>15m</option>
            <option>last</option>
            <option>buy</option>
            <option>sell</option>
            <option>symbol</option>
          </select>
          &nbsp;&nbsp;
          <label>Limit</label>&nbsp;
          <input class="form-control limit-input-js" id="" placeholder="10">
          &nbsp;&nbsp;
          <button type="submit" class="btn btn-primary">Refresh</button>
        </form>
      </div>

      <hr>

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
            <tbody class="ticker-body-js">
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
    </div>


    <!-- Begin JavaScript
    ================================================== -->
    <script src="https://code.jquery.com/jquery-3.2.1.min.js" integrity="sha256-hwg4gsxgFZhOsEEamdOYGBf13FyQuiTwlAQgxVSNgt4=" crossorigin="anonymous"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.11.0/umd/popper.min.js" integrity="sha384-b/U6ypiBEHpOf/4+1nzFpr53nxSS+GLCkfwBdFNTxtclqqenISfwAzpKaMNFNmj4" crossorigin="anonymous"></script>
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0-beta/js/bootstrap.min.js" integrity="sha384-h0AbiXch4ZDo7tp9hKZ4TsHbi047NrKGLO3SEJAg45jXxnGIfYzk4Si90RDIqNm1" crossorigin="anonymous"></script>
    <script>
      $(function(){
        var order, limit;

        function updateTable(orderParam, limitParam){
          $.get('index.php', {order: orderParam, limit: limitParam, json: true}, null, 'json')
          .done(function(results){
            // rerender the table
            var row;

            $.each(results, function(key, val){
              row += "<tr><td>" + key + "</td>";

              for (var i in val) {
                row += "<td>" + val[i] + "</td>";
              }

              row += "</tr>";
            });

            $(".ticker-body-js").empty().append(row);
          });
        }

        $('.order-js').on('click', function(){
          order = $(this).text();

          updateTable(order, limit);
        });

        $('.form-options-js').on('submit', function(e){
          e.preventDefault();

          order = $(".order-input-js").val();
          limit = $(".limit-input-js").val();

          updateTable(order, limit);
        });
      });
    </script>
  </body>
</html>
