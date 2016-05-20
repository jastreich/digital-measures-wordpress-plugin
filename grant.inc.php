<?php
/**
 * @file grant.inc.php
 * Contains the grant class.
 **/


/**
 * @class grant
 * Grant object for digitial measures, represents a research grant, teaching grant or businesss contract.
 **/
class grant
{
  private $sponsor;
  private $title;
  private $type;
  private $investigators;

  /**
   * Constructor for grant object.
   * @param string $title The title of the grant.
   * @param string $sponsor The sponsor org for the grant.
   * @param string $type The type of grant or contract.
   * @param array $investigators Who the grant or contract is awwarded to.
   **/
  public function __construct($title,$sponsor,$type,$investigators)
  {
    $this->title = trim($title);
    $this->sponsor = trim($sponsor);
    $this->type = trim($type);
    $this->investigators = $investigators;
  }


  /**
   * Display the grant.
   * @return the HTML of the grant.
   **/
  public function display()
  {
    $ret = '<div class="grant">';
    $ret .= '<span class="grant-title">' . $this->title . '</span> ';
    $ret .= '<span class="grant-sponsor">' . $this->sponsor . '</span> ';
    $ret .= '<span class="grant-type">' . $this->type . '</span> ';
    if(is_array($this->investigators))
    {
      $i = count($this->investigators);
      foreach($this->investigators as $inv)
      {
        $ret .= '<span class="grant-investigator">' . $inv . ($i > 1 ? ',' : '') . '</span> ' ;
        $i--;
      }
    }
    $ret .= '.</div>';
    return $ret;
  }
}

?>