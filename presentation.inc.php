<?php
/**
 * @file presentation.inc.php
 * This file contains the presentation class
 * @author Jeremy Streich
 **/

class presentation
{
  private $conference;
  private $authors;
  private $title;
  private $location;
  public $month;
  public $year;
  public $profile;

  /**
   * Comstrictor for the presntation class.
   * @param string $conference the conference the presentation was given.
   * @param array $authors the array of contributors who gave the presentation.
   * @param string $title the title of the presentation
   * @param string $location the location the presntation was held.
   * @param string $month the month the presentation was given.
   * @param string $year the year the presentation was given. 
   * @param bool $profile Weather or not to show this on the profile page.
   **/
  public function __construct($conference,$authors,$title,$location,$month,$year,$profile)
  {
    $this->conference = trim($conference);
    $this->authors = $authors;
    $this->title = trim($title);
    $this->location = trim($location);
    $this->month = trim($month);
    $this->year = trim($year);
    $this->profile = trim($profile);
  }

  /**
   * Out puts this presentation's citation in MLA format in either text or HTML.
   * @param string $type the type of output, either 'html' or 'text'. Option, default is 'text'.
   * @param int $authors the number of authors to show.
   * @return the mla citation of this presentation.
   **/
  public function mla($type = 'text',$authors = null)
  {
    $cite = mla_contribs($this->authors,$type,$authors);
    if('html' == $type)
    {
      $cite = htmlentities($cite);
    }

    // title
    if('html' == $type)
    {
      $cite .= '&ldquo;' . htmlentities(str_replace('"',"'",$this->title)) . '.&rdquo; ';
    }
    else
    {
      $cite .= '"' . str_replace('"',"'",$this->title) . '." ';
    }

    // conference
    if('html' == $type)
    {
      $cite .= htmlentities($this->conference);
    }
    else
    {
      $cite .= $conference;
    }
    $cite .= '. ';

    // location
    if('html' == $type)
    {
      $cite .= htmlentities($this->location);
    }
    else
    {
      $cite .= $this->location;
    }
    $cite .= '. ';

    // date
    $cite .= ($this->month != '' ? $this->month . ' ' : '') . $this->year . '.';
    return $cite;
  }

  public function apa($type = 'text',$authors = null)
  {
    $cite = apa_contribs($this->authors,$type,$authors) . ' ';
    $cite .= ($this->year != '' ? '(' . ($this->month != '' ? $this->month . ' ' : '') . $this->year . '). ' : '. ');
    $cite .= ($type == 'html' ? htmlentities($this->title) : $this->title) . '. ';
    $cite .= ($type == 'html' ? htmlentities($this->conference . ', ' . $this->location) : $this->conference . ', ' . $this->location) . '.';
    return $cite;
  }


  /**
   * Returns weather or not the publication should be listed in the profile.
   * @return true if the Faculty has agreed to list publication on profile page, false otherwise.
   **/
  function in_profile()
  {
    return (true == $this->profile);
  }

}

?>