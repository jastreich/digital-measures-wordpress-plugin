<?php
/**
 * @file contributor.inc.php
 * This file contains the contributor class
 * @author Jeremy Streich
 **/

/**
 * @class contributor
 * Represents an author or editor in a publication.
 **/
class contributor
{
  public $first;
  public $middle;
  public $last;

  /**
   * Constructor for the ccontributor class
   * @param string $first The furst name of the contributor
   * @param string $middle The middle name of the contributor
   * @param string $last The last name of the contributor
   **/
  function __construct($first = '',$middle = '',$last = '')
  {
    $this->first = trim($first);
    $this->middle = trim($middle);
    $this->last = trim($last);
  }

  /** 
   * Returns the contributor in MLA name format
   * @param string $type The type of output i.e. text or html. Currently ignored.
   * @param bool $strict 
   **/
  function mla($type = '',$strict = false)
  {
    if(!$strict)
    {
      $cite = ('' != $this->last ? $this->last . ', ' . $this->first : $this->first) . ('' != $this->middle ? ' ' . substr($this->middle,0,1) . '.' : '');
    }
    else
    {
      $cite = $this->first . ' ' . ($this->middle != '' ? substr($this->middle,0,1) . '. ' : '') . $this->last; 
    }
    return $cite;
  }

  /**
   * Returns the contributir in APA format
   * @param string $type The type of output, i.e. text of html. Currently ignored.
   **/
  function apa($type='')
  {
    if($this->last === '' && $this->first !== '' && $this->middle === '')
    {
      return $this->first;
    }
    $cite  = ('' != $this->last ? $this->last : '');
    $cite .= ('' != $this->first ? ', ' . substr($this->first,0,1) . '.': '');
    $cite .= ('' != $this->middle && '' != $this->first ? ' ' . substr($this->middle,0,1) . '.' : '');
    return $cite;
  }

};

function mla_contribs($authors,$type = 'html',$num_authors = null,$strict=false)
{
    $cite = '';
    for($i = 0;count($authors) > $i; ++$i)
    {
      $cite .= $authors[$i]->mla($type,$strict && $i != 0);
      if($num_authors !== null && $i + 1 == $num_authors && $i + 2 <= count($authors))
      {
        $cite .= ', and others. ';
        break;
      }
      else if($i + 2 == count($authors))
      {
        $cite .= ', and ';
      }
      else if($i + 1 == count($authors))
      {
        if('' != trim($cite) && '.' != $cite[strlen($cite)-1] )
        {
          $cite .= '. ';
        }
      }
      else
      {
        $cite .= ', ';
      }
    }
    return $cite;
}

function apa_contribs($authors,$type = 'html',$num_authors = null)
{
   $cite = '';
    for($i = 0;count($authors) > $i; ++$i)
    {
      $cite .= $authors[$i]->apa($type);
      if($num_authors !== null && $i + 1 == $num_authors && $i + 2 <= count($authors))
      {
        $cite .= ' et al.';
        break;
      }
      else if($i + 2 == count($authors))
      {
          $cite .= ', & ';
      }
      else if($i + 1 == count($authors))
      {
        if('' != trim($cite) && '.' != $cite[strlen($cite)-1] )
        {
          $cite .= ' ';
        }
      }
      else
      {
        $cite .= ', ';
      }
    }
    $cite .= ' ';
    return $cite;
}


?>