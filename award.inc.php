<?php
/**
 * @file award.inc.php
 * This file contains the award class.
 * @author Jeremy Streich
 **/

/**
 * @class award
 * This class represents an award or honor.
 **/
class award
{
  public $name;
  public $org;
  public $year;
  public $month;
  public $day;

  /**
   * Constructor for the award class
   * @param string $name The name of award.
   * @param string $org The name of the orginization that bestowed the award.
   * @param string $year The year it was awarded.
   * @param string $month The month it was awarded.
   * @param string $day The day it was awarded.
   **/
  public function __construct($name,$org,$year = '',$month = '',$day = '')
  {
    $this->name = trim($name);
    $this->org = trim($org);
    $this->year = trim($year);
    $this->month = trim($month);
    $this->day = trim($day);
  }


  /**
   * Display the award.
   **/
  public function display($limit = 0)
  {
    $cite = '<div class="award"><span class="name">' . $this->name . '</span> ';
    if($this->year)
    {
      $cite .= '(' . $this->year;
      if($this->month)
      {
        $cite .= ', ' . $this->month;
        if($this->day)
        {
          $cite .= ' ' . $this->day;
        }
      }
      $cite .= ') ';
    }
    $cite .= '<span class="org">' . $this->org . '</span>.</div>';
    return $cite;
  }


}

?>