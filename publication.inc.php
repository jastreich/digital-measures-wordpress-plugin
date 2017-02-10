<?php
/**
 * @file publication.inc.php
 * Contains the publication class
 * @author Jeremy Streich
 **/

/**
 * @class publication 
 * Defines a publication object.
 **/
class publication
{
  public $id;
  public $type;
  public $status;
  public $title;
  public $journal_title;
  public $profile;
  public $authors;
  public $editors;
  public $publisher;
  public $city;
  public $volume;
  public $issue;
  public $pagenum;
  public $doi;
  public $isbn;
  public $pub_day;
  public $pub_month;
  public $pub_year;
  public $link;
  public $meta;

  /**
   * Constructor for the publication object.
   * @param int $id the id of this publication
   * @param string $type Type of publication (e.g. book, article, etc.)
   * @param string $status The publication status of the work.
   * @param string $title The title of the work.
   * @param string $journal_title The title of the journal.
   * @param bool $profile Weather or not to show this on the profile page.
   * @param array $authors The list of authors who wrote the work.
   * @param array $editors The editors who edited the work.
   * @param string $publisher The publisher of the book or article.
   * @param string $volume The volume the work appears in.
   * @param string $issue The issue the work appears in.
   * @param string $pagenum The pages the work is on.
   * @param string $doi The doi number for openly published works.
   * @param string $isbn The isbn of the publication.
   * @param string $pub_month The month the work was first published.
   * @param string $pub_year The year the work was first published.
   * @param string $pub_link The url where the article is availible online.
   **/
  function __construct($id = '',$type = '',$status = '',$title = '',$journal_title = '',$profile = false,
    $authors = array(),$editors = array(),$publisher = '',$city = '',$volume = '',$issue = '',$pagenum = '',
    $doi = '',$isbn = '',$pub_day = '',$pub_month = '',$pub_year ='',$pub_link = '')
  {
    $this->id = trim($id);
    $this->type = trim($type);
    $this->status = trim($status);
    $this->title = trim($title);
    $this->journal_title = trim($journal_title);
    $this->profile = (trim($profile) === 'Yes' || trim($profile) === 'Y' ? true : false);
    $this->authors = $authors;
    $this->editors = $editors;
    $this->publisher = trim($publisher);
    $this->city = trim($city);
    $this->volume = trim($volume);
    $this->issue = trim($issue);
    $this->pagenum = trim($pagenum);
    $this->doi = trim($doi);
    $this->isbn = trim($isbn);
    $this->pub_day = trim($pub_day);
    $this->pub_month = trim($pub_month);
    $this->pub_year = trim($pub_year);
    $this->link = trim($pub_link);
    
    if(strpos($this->doi,':') !== false)
    {
      $this->doi = strstr($this->doi,':');
    }
    if($this->link == '' && $this->doi != '')
    {
      $this->link = 'http://dx.doi.org/' . $this->doi;
    }
//hmm..    
    if(strpos($this->link,'http:') !== 0 && strpos($this->link,'https:') !== 0 && $this->link != '')
    {
      $this->link = 'http://' . $this->link;
    }
  }

  /** 
   * Inspector for year of publication.
   * @return string The year of publication
   **/
  function get_year()
  {
    return $this->pub_year;
  }

  /**
   * Inspector for The title of the work.
   * @return string The title of the work.
   **/
  function get_title()
  {
    return $this->title;
  }

  /**
   * Inspector for The title of the journal
   * @return string The title of the journal
   **/
  function get_journal_title()
  {
    return $this->journal_title;
  }

  /**
   * Returns weather or not the work is published.
   * @return bool True if the work is published, otherwise false.
   **/
  function is_published()
  {
    return ('published' == strtolower($this->status));
  }

  /** returns weather or not the publication should be listed in the profile.
   * @return true if the Faculty has agreed to list publication on profile page, false otherwise.
   **/
  function in_profile()
  {
    return (true == $this->profile);
  }

  /** 
   * Get the MLA format for this publication's citation citation
   * @param string $type The type of output, either 'html' or 'text'.
   * @param int $authors The limit of authors to show per publication.
   * @param string $unpublished The text to show in place of date for unpublished works.
   * @param bool $strict If we're doing strict citations.
   * @return The publications citation in mla format.
   * @see contributor::mla()
   **/
  function mla($type = 'text', $authors = null,$unpublished = '',$strict = false)
  {
    $cite = mb_convert_encoding(htmlentities(mla_contribs($this->authors,$type,$authors,$strict),ENT_COMPAT,"UTF-8"), 'HTML-ENTITIES', 'UTF-8');
 
    $edsfirst = false;
    if('' == trim($cite))
    {
      $edsfirst = true;
      if(count($this->editors) > 0 && ('' != $this->editors[0]->mla()))
      {
        for($i = 0;count($this->editors) > $i; ++$i)
        {
          if('html' == $type)
          {
            $cite .= mb_convert_encoding(htmlentities($this->editors[$i]->mla($type,$i > 0 && $strict),ENT_COMPAT,"UTF-8"), 'HTML-ENTITIES', 'UTF-8');
          }
          else
          {
            $cite .= $this->editors[$i]->mla($type,$i > 0 && $strict);
          }
          if($i + 2 == count($this->editors))
          {
            $cite .= ', and ';
          }
          else if($i + 1 == count($this->editors))
          {
            $cite .= ', ed' . (count($this->editors) > 1 ? 's' : '') . '. ';
          }
          else
          {
            $cite .= ', ';
          }
        }
      }
    }


    $cite .= ' ';

    // Title
    if('' != trim($this->title))
    {
      if('html' == $type || 'HTML' == $type)
      {
        if($this->link != '')
        {
          $cite .= '<a href="' . $this->link . '">';
        }

        if('' != $this->journal_title)
        {
          $cite .= '&ldquo;' . mb_convert_encoding(htmlentities(str_replace('"',"'",$this->title),ENT_COMPAT,"UTF-8"), 'HTML-ENTITIES', 'UTF-8') . '.&rdquo;';
        }
        else
        {
           $cite .= '<i>' . mb_convert_encoding(htmlentities(str_replace('"',"'",$this->title),ENT_COMPAT,"UTF-8"), 'HTML-ENTITIES', 'UTF-8') . '.</i>'; 
        }

        if($this->link != '')
        {
          $cite .= '</a>';
        }
        $cite .= ' ';
      }
      else
      {
        if('' != $this->journal_title)
        {
          $cite .= '"' . str_replace('"',"'",$this->title) .  (dm_endsWith($this->title,'?') ? '"' : '."'); //'." ';
        }
        else
        {
          $cite = $this->title;
        }
      }
    }

    if('' != $this->journal_title)
    {
      if('html' == $type)
      {
        $cite .= '<i>';

        $cite .= mb_convert_encoding(htmlentities($this->journal_title,ENT_COMPAT,"UTF-8"), 'HTML-ENTITIES', 'UTF-8');
        $cite .= '</i>';
      }
      else
      {
        $cite .= $this->journal_title;
      }
    }

    if('' != $this->volume || '' != $this->issue)
    {
      $cite .= ' ';
    }

    if('' != $this->volume && '' != $this->issue)
    {
      $cite .= $this->volume . '.' . $this->issue . ' ';
    }
    else if('' != $this->volume)
    {
      $cite .= $this->volume . '. ';
    }
    else if('' != $this->issue)
    {
      $cite .= $this->issue . '. ';
    }
    else
    {
      if(substr($cite,-2) != '. ' && substr($cite,-6) != '.</i> ')
      {
        $cite .= '. ';
      }
    }

    //Editors
    if(count($this->editors) > 0 && ('' != $this->editors[0]->mla()) && !$edsfirst)
    {
      $cite .= 'Ed. ';
      for($i = 0;count($this->editors) > $i; ++$i)
      {
        if('html' == $type)
        {
          $cite .= mb_convert_encoding(htmlentities($this->editors[$i]->mla(),ENT_COMPAT,"UTF-8"), 'HTML-ENTITIES', 'UTF-8');
        }
        else
        {
          $cite .= $this->editors[$i]->mla();
        }
        if($i + 2 == count($this->editors))
        {
          $cite .= ', and ';
        }
        else if($i + 1 == count($this->editors))
        {
          $cite .= '. ';
        }
        else
        {
          $cite .= ', ';
        }
      }
    }

    // Publisher 
    if('' != trim($this->publisher))
    {
      if('html' == $type)
      {
        $cite .= mb_convert_encoding(htmlentities($this->publisher,ENT_COMPAT,"UTF-8"), 'HTML-ENTITIES', 'UTF-8') . ', ';
      }
      else
      {
        $cite .= $this->publisher . '. ';
      }

    }

    // Year
    if('' != $this->pub_year)
    {
      if('' != $this->journal_title)
      {
        $cite .= '(' . $this->pub_year . ')';
      }
      else
      {
        $cite .= $this->pub_year;
      }
      if('' != $this->pagenum)
      {
        $cite .= ': ';
      }
      else
      {
      }
    }
    else if($unpublished != '')
    {
      $cite .= '(' . $unpublished . ') ';
    }

    // Pages
    if('' != $this->pagenum)
    {
      $cite .= $this->pagenum;
    }
    else
    {
      $cite .= '. ';
    }

    // Clean up spaces and periods
    $cite = trim($cite);
    $cite = rtrim($cite,'.');
    $cite .= '.';
    $search[] = '..';
    $replace[] = '.';

    $search[] = '. .';
    $replace[] = '. ';

    $search[] = '.</i> .';
    $replace[] = '.</i> ';

    $search[] = '.</i></a> .';
    $replace[] = '.</i></a> ';

    $cite = str_replace($search,$replace,$cite);
    if('html' == $type)
    {
      return utf8_encode($cite);
    }
    return $cite;
  }

  /** 
   * Get the APA format for this publication's citation citation 
   * @param string $type The type of output, either 'html' or 'text'.
   * @param int $authors The limit of authors to show per publication.
   * @param string $unpublished The string to display in place of date for unpublished works.
   * @return The publications citation in APA format.
   * @see contributor::apa()
   **/
  function apa($type = 'text',$authors = null,$unpublished = '')
  {
    $cite = apa_contribs($this->authors,$type,$authors);
    if($type == 'html' || $type == 'HTML')
    {
      $cite =  mb_convert_encoding(htmlentities($cite,ENT_COMPAT,"UTF-8"), 'HTML-ENTITIES', 'UTF-8');
    }

    $edsfirst = false;
    if('' == trim($cite))
    {
      $edsfirst = true;
      if(count($this->editors) > 0 && ('' != $this->editors[0]->apa()))
      {
        for($i = 0;count($this->editors) > $i; ++$i)
        {
          if('html' == $type)
          {
            $cite .= mb_convert_encoding(htmlentities($this->editors[$i]->apa(),ENT_COMPAT,"UTF-8"), 'HTML-ENTITIES', 'UTF-8');
          }
          else
          {
            $cite .= $this->editors[$i]->apa();
          }
          if($i + 2 == count($this->editors))
          {
            if('html' == $type || 'HTML' == $type)
            {
              $cite .= ', &amp; ';
            }
            else
            {
              $cite .= ', & ';
            }
          }
          else if($i + 1 == count($this->editors))
          {
            $cite .= ', ed' . (count($this->editors) > 1 ? 's' : '') . '. ';
          }
          else
          {
            $cite .= ', ';
          }
        }
      }
    }

    // Year
    if('' != $this->pub_year)
    {
      $cite .= '(' . mb_convert_encoding(htmlentities($this->pub_year,ENT_COMPAT,"UTF-8"), 'HTML-ENTITIES', 'UTF-8');
      if('' != $this->pub_month)
      {
        $dm_months = unserialize(dm_months);
        $cite .= ', ' . (is_numeric($this->pub_month) ? $dm_months[$this->pub_month] : $this->pub_month);
        if('' != $this->pub_day)
        {
          $cite .= (is_numeric($this->pub_month) ? ' ' . $this->pub_day : '');
        }
      }
      $cite .= '). ';
    }
    else if($unpublished != '')
    {
      $cite .= '(' . $unpublished . '). ';
    }

    // Title
    if('' != trim($this->title))
    {
      if('html' == $type || 'HTML' == $type)
      {
        if('' == $this->journal_title)
        {
          $cite .= '<i>';
        }
        if($this->link != '')
        {
          $cite .= '<a href="' . $this->link . '">';
        }
        $cite .= mb_convert_encoding(htmlentities($this->title,ENT_COMPAT,"UTF-8"), 'HTML-ENTITIES', 'UTF-8') . (dm_endsWith($this->title,'?') ? '' : '.');
        if($this->link != '')
        {
          $cite .= '</a>';
        }
        if('' == $this->journal_title)
        {
          $cite .= '</i>';
        }
        $cite .= ' ';
      }
      else
      {
        $cite .= $this->title . '. ';
      }

    }

    // Editors
    if(count($this->editors) > 0 && ('' != $this->editors[0]->apa()) && !$edsfirst)
    {
      for($i = 0;count($this->editors) > $i; ++$i)
      {
        if('html' == $type)
        {
          $cite .= mb_convert_encoding(htmlentities($this->editors[$i]->apa(),ENT_COMPAT,"UTF-8"), 'HTML-ENTITIES', 'UTF-8');
        }
        else
        {
          $cite .= $this->editors[$i]->apa();
        }
        if($i + 2 == count($this->editors))
        {
          if('html' == $type || 'HTML' == $type)
          {
            $cite .= ', &amp; ';
          }
          else
          {
            $cite .= ', & ';
          }
        }
        else if($i + 1 == count($this->editors))
        {
          $cite .= ' (Ed' . (count($this->editors) > 1 ? 's' : '') . '.). ';
        }
        else
        {
          $cite .= ', ';
        }
      }
    }

    // Journal Title
    if('' != $this->journal_title)
    {
      if('html' == $type)
      {
        $cite .= '<i>';
        $cite .= mb_convert_encoding(htmlentities($this->journal_title,ENT_COMPAT,"UTF-8"), 'HTML-ENTITIES', 'UTF-8');
        $cite .= '</i>';
      }
      else
      {
        $cite .= $this->journal_title;
      }
    }

    // Volume and Issue
    if('' != $this->volume && '' != $this->issue)
    {
      $cite .= ', <i>' . $this->volume . '</i>(' . $this->issue . ')';
    }
    else if('' != $this->volume)
    {
      $cite .= ($this->journal_title == '' ? ' ' : ', ') . '<i>' . $this->volume . '</i>';
    }
    else if('' != $this->issue)
    {
      $cite .= ($this->journal_title == '' ? ' ' : ', ') . '<i>' . $this->issue . '</i>';
    }

    // Pages
    if('' != $this->pagenum)
    {
      $cite .= ', ' . $this->pagenum . '. ';
    }
    else if('' != $this->journal_title || '' != $this->volume || '' != $this->issue)
    {
      $cite .= '. ';
    }

    // Publisher
    if('' != $this->publisher && dm_startsWith($this->type,'Book'))
    {
      if('html' == $type)
      {
        if('' != $this->city)
        {
          $cite .=  mb_convert_encoding(htmlentities($this->city,ENT_COMPAT,"UTF-8"), 'HTML-ENTITIES', 'UTF-8') . ': ';
        }
        $cite .= mb_convert_encoding(htmlentities($this->publisher,ENT_COMPAT,"UTF-8"), 'HTML-ENTITIES', 'UTF-8') . '. ';
      }
      else
      {
        $cite .= $this->publisher . '. ';
      }
    }

    // Clean up spaces and periods
    $cite = trim($cite);
    $cite = rtrim($cite,'. ');
    if(!dm_endsWith($cite,'.</i>'))
    {
      $cite .= '.';
    }
    $search[] = '..';
    $replace[] = '.';
    $search[] = '. .';
    $replace[] = '. ';
    $cite = str_replace($search,$replace,$cite);
    return $cite;
    
  }

};

/**
 * Helper function to compare two publications by date.
 * @param publication $a
 * @param publication $b
 * @return positive number
 **/
function pub_cmp($a, $b)
{
  if($a->get_year() == $b->get_year())
  {
    $a_month = array_search($a->pub_month,unserialize(dm_months));
    $b_month = array_search($b->pub_month,unserialize(dm_months));
    if($a_month == $b_month)
    {
      if(!$a->pub_day)
      {
        return -1;
      }
      else if(!$b->pub_day)
      {
        return 1;
      }

      return ($a->pub_day - $b->pub_day) * -1;
    }
    else {
      return ($a_month - $b_month) * -1;
    }
  }
  else if('' == $a->get_year())
  {
    return -1;
  }
  else if('' == $b->get_year())
  {
    return 1;
  }
  return ($a->get_year() - $b->get_year())*-1;
}

/**
 * Compare two ed
 * @param string $a The first ed.
 * @param string $b The second ed.
 **/
function ed_cmp($a,$b)
{
  return strcmp($a->get_completed(),$b->get_completed());

}