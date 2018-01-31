<?php
/// IPMT/PPMT方式
/// actor: 李俞興 (Lee, Yu-Xing)
// created date: 2016/9/30
// last updated date: 2018/01/31

require_once('class/xing_ppmt.php');
?>
<html>
  <head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8">

    <link rel="stylesheet" href="css/index.css">
    <script src="https://code.jquery.com/jquery-3.2.1.slim.min.js" integrity="sha384-KJ3o2DKtIkvYIK3UENzmM7KCkRr/rE9/Qpg6aAZGJwFDMVNA/GpGFF93hXpG5KkN" crossorigin="anonymous"></script>

    <!-- bootstrap -->
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/css/bootstrap.min.css" integrity="sha384-Gn5384xqQ1aoWXA+058RXPxPg6fy4IWvTNh0E263XmFcJlSAwiGgFAW/dAiS6JXm" crossorigin="anonymous">
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/js/bootstrap.min.js" integrity="sha384-JZR6Spejh4U02d8jOt6vLEHfe/JQGiRRSQQxSfFWpi1MquVdAyjUar5+76PVCmYl" crossorigin="anonymous"></script>
  </head>
  <body>
    <div class="container" style="margin-top: 50px;">
      <form class="form-inline" action="index.php" method="post">
        <div class="form-group col-md-3">
          <label for="money" class="col-sm-4 col-form-label">金額:</label>
          <input type="text" id="money" name="money" class="form-control col-sm-8" value="<?php echo isset($_POST["money"])? $_POST["money"] : ''; ?>" />
        </div>
        <div class="form-group col-md-4" style="white-space:nowrap;">
          <label for="rate" class="col-sm-4 col-form-label">年利率:</label>
          <div class="col-sm-8">
            <input type="text" id="rate" name="rate" class="form-control" value="<?php echo isset($_POST["rate"])? $_POST["rate"] : ''; ?>" />%
          </div>
        </div>
        <div class="form-group col-md-3">
          <label for="period" class="col-sm-4 col-form-label">期數:</label>
          <input type="text" id="period" name="period" class="form-control col-sm-8" value="<?php echo isset($_POST["period"])? $_POST["period"] : ''; ?>" />
        </div>
        <input type="submit" class="btn btn-primary col-md-2" />
      </form>

<?php
    $money = isset($_POST["money"])? $_POST["money"] : null;
    $year_rate = isset($_POST["rate"])? $_POST["rate"] : null;
    $period = isset($_POST["period"])? $_POST["period"] : null;

    if(!empty($money) && !empty($year_rate) && !empty($period))
    {
      $ppmt_loan_money = new XingPpmtLoanMoney($money, $year_rate, $period);
?>
      <div class="alert alert-primary">
        <label class="col-md-3">總金額: <?php echo $ppmt_loan_money->get_amount(); ?></label>
        <label class="col-md-3">期數: <?php echo $ppmt_loan_money->get_period(); ?></label>
        <label class="col-md-4">每期償還總金額: <?php echo $ppmt_loan_money->get_pmt(); ?></label>
      </div>
      <div class="alert alert-info">
        <label class="col-md-3">年利率: <?php echo $ppmt_loan_money->get_year_rate(); ?></label>
        <label class="col-md-3">月利率: <?php echo $ppmt_loan_money->get_month_rate(); ?></label>
      </div>

      <div class="table-responsive">
        <table class="table">
          <thead>
            <tr>
              <th>期數</th>
              <th>償還前本金</th>
              <th>本期償還本金</th>
              <th>本期償還利息</th>
              <th>應償還總金額</th>
              <th>償還後本金餘額</th>
            </tr>
          </thead>
          <tbody>
<?php
          for ($i_row = 0; $i_row <= $period; $i_row++)
          {
?>
            <tr>
              <td><?php echo $i_row; ?></td>
              <td><?php echo round($ppmt_loan_money->get_target_amount($i_row)); ?></td>
              <td><?php echo round($ppmt_loan_money->get_target_ppmt($i_row)); ?></td>
              <td><?php echo round($ppmt_loan_money->get_target_ipmt($i_row)); ?></td>
              <td><?php echo $i_row > 0 ? round($ppmt_loan_money->get_pmt()) : 0; ?></td>
              <td><?php echo round($ppmt_loan_money->get_target_remain_amount($i_row)); ?></td>
            </tr>
<?php
          }
?>
          </tbody>
        </table>
      </div>
<?php
  }
?>

    </div>
  </body>
</html>




