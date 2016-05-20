<?php
/**
 * @file degree.inc.php
 * This file contains the degree class.
 * @author Jeremy Streich
 **/


/**
 * @class degree
 * This object represents a degree a faculty memeber has earned.
 **/
class degree
{
  private $level;
  private $subject;
  private $university;
  private $location;
  private $year_started;
  private $year_completed;

  /**
   * Constructor for the degree class.
   * @param string $level the level or type of the degree (BA,BS,Ph D).
   * @param string $subject the major or subject area the degree is in.
   * @param string $university the name of the university the degree is from.
   * @param string $location the city and state where the degree was earned.
   * @param string $year_started the year the course work for the degree began, if the year_started is more than 4 chars, it will get truncated.
   * @param string $year_completed the year the degree was confered.
   **/
  public function __construct($level,$subject,$university,$location,$year_started,$year_completed)
  {
    $this->level = trim($level);
    $this->subject = trim($subject);
    $this->university = trim($university);
    $this->location = trim($location);
    $this->year_started = substr($year_started,0,4);
    $this->year_completed = substr($year_completed,0,4);
  }

  /**
   * Inspector for the year the degree was completed .
   * @return year_completed
   * @see ed_cmp()
   **/
  public function get_completed()
  {
    return $this->year_completed;
  }

  /**
   * Display this degree as HTML
   * @param bool $show_location Weather to show the location or not, default false.
   * @param mixed $show_dates if true show completion date, if 'range' show date range, if false don't show
   * @return HTML of this degree.
   ***/
  public function display($show_location = false,$show_dates = false)
  {
    $output = '<div class="degree">';
    $output .= '<span class="degree_level">' . htmlentities($this->level) . '</span> ';
    $output .= '<span class="subject">' . htmlentities($this->subject) . '</span> ';
    $output .= '<span class="university' . (($show_location && $show_location != 'false')|| ($show_dates && $show_dates != 'false')? ' withafter' : '') . '">' . htmlentities($this->university) . '</span> ';

    if($show_location)
    {
      $output .= '<span class="location' . ($show_dates && $show_dates != 'false'? ' withafter' : '') . '">' . htmlentities($this->location) . '</span> ';
    }
    if($show_dates && $show_dates != 'false')
    {
      if($show_dates == 'range')
      {
        $output .= '<span class="dates">' . htmlentities($this->year_started) . '&nbsp;&ndash;&nbsp;' . htmlentities($this->year_completed) . '</span> ';
      }
      else
      {
        $output .= '<span class="dates">' . htmlentities($this->year_completed) . '</span> ';
      }
    }
    $output .= '</div>';
    return $output;
  }
}
?>