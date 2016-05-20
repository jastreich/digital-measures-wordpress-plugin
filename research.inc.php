<?php
/**
 * @file research.inc.php
 * Research Areas
 * @author Jeremy Streich
 **/

/**
 * @class research
 * Represents research currently in progress
 **/
class research
{
  private $title;
  private $description;
  private $collaborators;

  /**
   * Constructor for a research object.
   * @param string $title The title of research you're doing.
   * @param string $description A general description of the research.
   **/
  public function __construct($title,$description,$collaborators)
  {
    $this->title = trim($title);
    $this->description = trim($description);
    $this->collaborators = $collaborators;
  }

  /**
   * Displays a research object.
   * @return a string containg the HTML of the research object.
   **/
  public function display($limit = 0)
  {
    $ret =  '<div class="current-research-activity"><span class="research-title">' . $this->title . '</span> <span class="research-description">' . $this->description . '</span>';
    if(is_array($this->collaborators))
    {
      $i = count($this->collaborators);
      foreach ($this->collaborators as $key => $value)
      {
        if(trim($value))
        {
          $ret .= ' <span class="collaborator">' . $value . ($i > 1 ? ',' : '') . ' </span>';
        }
        $i--;
      }
    }
    $ret .= '</div>';
    return $ret;
  }

}


?>