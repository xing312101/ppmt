<?php
/// IPMT/PPMT方式
/// actor: 李俞興 (Lee, Yu-Xing)
// created date: 2016/9/30
// last updated date: 2018/01/29
  class XingPpmtLoanMoney{
    private $amount = 0;
    private $year_rate = 0;
    private $period = 0;
    private $month_rate = 0;

    private $pmt = 0;                     //每期償還
    private $arr_amount = Array();        //本金
    private $arr_ipmt = Array();          //本期利息
    private $arr_ppmt = Array();          //本期償還本金
    private $arr_remain_amount = Array(); //償還後金額
    private $total_interest = 0;
    private $start_date;

    public function __construct($amount, $year_rate, $period, $start_date = '0000-00-00'){
      $this->amount = $amount;
      $this->year_rate = $year_rate / 100;
      $this->period = $period;
      $this->month_rate = $this->year_rate / 12;
      $this->pmt = self::pmt_calculate();
      $this->start_date = date("Y-m-d", strtotime($start_date));
    }

    public function __destruct(){
      $this->amount = null;
      $this->year_rate = null;
      $this->period = null;
      $this->month_rate = null;
      $this->pmt = null;
      $this->arr_amount = null;
      $this->arr_ipmt = null;
      $this->arr_ppmt = null;
      $this->arr_remain_amount = null;
    }

    public function get_amount(){
      return $this->amount;
    }

    public function get_period(){
      return $this->period;
    }

    public function get_year_rate(){
      return $this->year_rate;
    }

    public function get_month_rate(){
      return $this->month_rate;
    }

    public function get_start_date(){
      return $this->start_date;
    }

    public function get_pmt(){
      return $this->pmt;
    }

    public function get_total_interest(){
      return $this->total_interest;
    }

    public function get_target_amount($target_period){
      //償還前本金
      if (!isset($this->arr_amount[$target_period]) || !isset($this->arr_ipmt[$target_period]) || !isset($this->arr_ppmt[$target_period]) || !isset($this->arr_remain_amount[$target_period]))
      {
        $this->all_period_calculate($target_period);
      }

      return ($this->arr_amount[$target_period]);
    }

    public function get_target_ipmt($target_period){
      // 償還利息
      if (!isset($this->arr_amount[$target_period]) || !isset($this->arr_ipmt[$target_period]) || !isset($this->arr_ppmt[$target_period]) || !isset($this->arr_remain_amount[$target_period]))
      {
        $this->all_period_calculate($target_period);
      }

      return ($this->arr_ipmt[$target_period]);
    }

    public function get_target_ppmt($target_period){
      // 償還本金
      if (!isset($this->arr_amount[$target_period]) || !isset($this->arr_ipmt[$target_period]) || !isset($this->arr_ppmt[$target_period]) || !isset($this->arr_remain_amount[$target_period]))
      {
        $this->all_period_calculate($target_period);
      }

      return ($this->arr_ppmt[$target_period]);
    }

    public function get_target_remain_amount($target_period){
      //償還後餘額
      if (!isset($this->arr_amount[$target_period]) || !isset($this->arr_ipmt[$target_period]) || !isset($this->arr_ppmt[$target_period]) || !isset($this->arr_remain_amount[$target_period]))
      {
        $this->all_period_calculate($target_period);
      }

      return ($this->arr_remain_amount[$target_period]);
    }

    public function get_target_date($target_period){
      //償還日期
      /*
      $date = new DateTime($this->start_date);
      $date->setDate($date->format('Y'), $date->format('m'), 1);

      return (date('Y-m', strtotime( $date->format('Y-m-d') . "+$target_period month")));
      */

      return (date('Y-m-d', strtotime( $this->start_date . "+$target_period month")));
    }

    public function get_all_period_data(){
      //回傳array, 各時間點的資料
      $data = array();

      for ($arr_index = 0; $arr_index <= $this->period; $arr_index++)
      {
        array_push($data, array(
          "amount" => $this->arr_amount[$arr_index],
          "ipmt" => $this->arr_ipmt[$arr_index],
          "ppmt" => $this->arr_ppmt[$arr_index],
          "remain" => $this->arr_remain_amount[$arr_index],
          "date" => $this->get_target_date($arr_index)
        ));
      }

      return $data;
    }

    private function pmt_calculate(){
      // 每期付款金額
      return ($this->amount * (($this->month_rate * pow((1 + $this->month_rate), $this->period)) / (pow((1 + $this->month_rate), $this->period) -1)));
    }

    private function all_period_calculate($target_period){
      for ($arr_index = 0; $arr_index <= $target_period; $arr_index++)
      {
        if (!isset($this->arr_amount[$arr_index]) || !isset($this->arr_ipmt[$arr_index]) || !isset($this->arr_ppmt[$arr_index]))
        {
          if (0 == $arr_index)
          {
            $this->arr_amount[$arr_index] = $this->amount;
            $this->arr_ipmt[$arr_index] = 0;
            $this->arr_ppmt[$arr_index] = 0;
          }
          else
          {
            $this->arr_amount[$arr_index] = $this->arr_remain_amount[$arr_index - 1];
            $this->arr_ipmt[$arr_index] = ($this->arr_amount[$arr_index] * $this->month_rate);
            $this->arr_ppmt[$arr_index] = ($this->pmt - ($this->arr_amount[$arr_index] * $this->month_rate));
          }

          $this->arr_remain_amount[$arr_index] = $this->arr_amount[$arr_index] - $this->arr_ppmt[$arr_index];
        }
      }

      $this->total_interest = array_sum($this->arr_ipmt);
    }
  }
?>