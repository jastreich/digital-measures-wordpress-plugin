<?php
 /**
  * @file publications_list.inc.php
  * The meat of the application happens within this file.
  * @author Jeremy Streich
  **/

if(!function_exists('dm_startsWith')) {
  function dm_startsWith($haystack, $needle)
  {
    return $needle === "" || strpos($haystack, $needle) === 0;
  }
}

if(!function_exists('dm_endsWith')) {
  function dm_endsWith($haystack, $needle)
  {
      return $needle === "" || substr($haystack, -strlen($needle)) === $needle;
  }
}
if(!defined('dm_months')) {
  define
  (
    'dm_months',
    serialize(array
    (
      1  => 'January',
      2  => 'February',
      3  => 'March',
      4  => 'May',
      5  => 'June',
      6  => 'July',
      7  => 'August',
      9  => 'September',
      10 => 'October',
      11 => 'November',
      12 => 'December'
    ))
  );
}
require_once('contributor.inc.php');
require_once('publication.inc.php');
require_once('presentation.inc.php');
require_once('degree.inc.php');
require_once('award.inc.php');
require_once('research.inc.php');
require_once('grant.inc.php');

/**
 * @class publications_list
 * This object represents a faculty's profile, as far as their intelectual contributions. Fetches information from Digital Measures.
 **/
class publications_list
{
  private $username;
  private $password;
  private $epanther;
  private $school;
  private $college;
  private $first_name;
  private $middle_name;
  private $last_name;
  private $email;
  private $phone;
  private $phone_ex;
  private $room;
  private $website;
  private $network;
  private $research;
  private $pubs;
  private $education;
  private $awards;
  private $current_research;
  private $grants;
  private $pub_meta;


  /** 
   * Constructor for the publications class which popluates the profile by fetching information from Digital Measures
   * @param string $u the ePatherID of the user.
   * @param array $config Activity Insight Configuration
   **/
  function __construct($config = null,$days=30)
  {
    $this->username = $config['username'];
    $this->password = $config['password'];

    $ret = '';

    date_default_timezone_set("America/Chicago");
    $end= new DateTime();
    $start = date("Y-m-d", strtotime("-{$days} day"));
    //echo ('<h1>' . $start . '</h1>');
    //echo('<h1>' . $end->format('Y-m-d') . '</h1>');
    $ch = curl_init('https://webservices.digitalmeasures.com/login/service/v4/SchemaData/INDIVIDUAL-ACTIVITIES-' . $config['key'] . '/INTELLCONT?start=' . $start . '&endDate=' . $end->format('Y-m-d') . '&end=' . $end->format('Y-m-d'));
    curl_setopt($ch, CURLOPT_HEADER, 0);
    curl_setopt ($ch, CURLOPT_RETURNTRANSFER, 1);
    curl_setopt ($ch, CURLOPT_CONNECTTIMEOUT, 0);
    curl_setopt($ch, CURLOPT_USERPWD, "$this->username:$this->password");
    curl_setopt($ch, CURLOPT_HTTPAUTH, CURLAUTH_BASIC);
    $ret = curl_exec($ch);
    curl_close($ch);

    $this->username = '';
    $this->password = '';

    //Make parsing easier by removing dmd namespace
    $ret = str_replace('dmd:','',$ret);

//    echo '<pre>';var_dump(htmlentities($ret));echo '</pre>';

    $dom = new DOMDocument;
    $good = $dom->loadXML($ret);
    if (!$good)
    {
      echo 'Bad... ' . $ret;
      exit;
    }

    $xml = simplexml_import_dom($dom);


    $this->pubs = array();
    $this->pub_meta = array();

    // Publications

    try
    {
      if(isset($xml->Data))
      {
        $xml = $xml->Data;
      }

      foreach($xml->Record as $rec)
      {
        $attrs = $rec->attributes();
        $meta = array();
        foreach($rec->IndexEntry as $ind)
        {
          $attrs = $ind->attributes();
          $meta[] = '' . $attrs->text;
        }


        foreach($rec->INTELLCONT as $pub)
        {


          $attrs = $pub->attributes();
          $a = array();
          foreach($pub->INTELLCONT_AUTH as $author)
          {
            $a[] = new contributor($author->FNAME,$author->MNAME,$author->LNAME);
          }
          $e = array();
          foreach($pub->INTELLCONT_EDITOR as $editor)
          {
            $e[] = new contributor($editor->FNAME,$editor->MNAME,$editor->LNAME);
          }
          if(count($e) == 0 && isset($pub->EDITORS) && '' != $pub->EDITORS && null != $pub->EDITORS)
          {
            $e[0] = new contributor($pub->EDITORS);
          }

          $is_article = (strpos($pub->CONTYPE,'Journal Article') === 0) || (strpos($pub->CONTYPE,'Online Article') === 0);
          // // // //
          if( ('' . $pub->DTY_PUB) == '')
          {
            continue;
          }
          
          $this->pubs[] = new publication
          (
            $attrs['id'],
            $pub->CONTYPE,
            $pub->STATUS,
            $pub->TITLE,
            ($is_article ? $pub->PUBLISHER : $pub->TITLE_SECONDARY),
            (isset($pub->INCLUDE_PROFILE) ? $pub->INCLUDE_PROFILE : false), //$pub->INCLUDE_PROFILE,
            $a,
            $e,
            ($is_article ? '' : $pub->PUBLISHER),
            $pub->PUBCTYST,
            $pub->VOLUME,
            $pub->ISSUE,
            $pub->PAGENUM,
            $pub->DOI,
            $pub->ISBNISSN,
            $pub->DTD_PUB,
            $pub->DTM_PUB,
            $pub->DTY_PUB,
            $pub->WEB_ADDRESS
          );
          $this->pub_meta[] = $meta;
        }
      }
      for($pub_index = 0; count($this->pubs) > $pub_index; ++$pub_index)
      {
        $this->pubs[$pub_index]->meta = $this->pub_meta[$pub_index];
      }
      if(is_array($this->pubs))
      {
        usort($this->pubs,"pub_cmp");
      }
    }
    catch(Exception $e)
    {
      echo $e->message;
    }
  }

  /**
   * Output this profile as list of mla citations of publications.
   * @parma string $type 'html' or 'text' supported. This is what the output method will eventually be.
   * @param bool $only_published if true then only published works will be output
   * @param bool $only_profile if true then olny the publications marked to be shown on a profile page will be output.
   * @param int $limit the maximum number of publications to display. limit of 0 means show all matching the previous arguments.
   * @param string $unpublished The text to be shown in place of date for unpublished works.
   * @param bool $strict If the mla is stict format or not.
   * @param string $college Limit to the passed College.
   * @param string $dept Limit to the passed Department.
   * @param bool $show_meta Show the meta (College and department) or not
   * @return string of mla formated citations for output
   **/
  public function mla($type = 'html', $only_published = false,$only_profile = false,$limit = 0,$authors = null,$unpublished = '',$strict = false,$college=null,$dept=null,$show_meta=true)
  {
    $ret = '';
    if('array' == $type)
    {
      $ret = array();
    } 
    $i = 0;
    foreach($this->pubs as $p)
    { 
      if( ( !$only_published || ($only_published && $p->is_published())) &&
          (
            !$college || 
            strtolower(preg_replace('/\s*/', '',trim($college))) === strtolower(preg_replace('/\s*/', '',trim($p->meta[0])))
          ) &&
          (
            !$dept ||
            (isset($p->meta[1]) && strtolower(preg_replace('/\s*/', '',trim($dept))) === strtolower(preg_replace('/\s*/', '',trim($p->meta[1]))))
          ) &&
          (isset($p->pub_month) && $p->pub_month != '') &&
          //(isset($p->pub_day) && $p->pub_day != '') &&
          (isset($p->pub_year) && $p->pub_year != '')
        )
      {
        if($show_meta) {
          $ret .= '<div class="pub-meta"><span class="pub-meta-school">' . $p->meta[0] . '</span> <span class="pub-meta-department">' . (isset($p->meta[1]) ? $p->meta[1] : '') . '</span></div>';
        }
        if('html' == $type)
        {
          $ret .= '<div class="publication">';
        }
        if('array' == $type)
        {
          $ret[] = $p->mla($type,$authors,$unpublished,$strict);
        }
        else
        {
          $ret .= $p->mla($type,$authors,$unpublished,$strict);
        }
        //echo $p->pub_year . ' ' . $p->pub_month . ' ' . $p->pub_day . '<br/>';
        if('html' == $type)
        {
          $ret .= '</div>';
        }
      }
      $i++;
    }
    return $ret;
  }


  /**
   * Output this profile as list of mla citations of presentations.
   * @parma string $type 'html' or 'text' supported. This is what the output method will eventually be.
   * @param bool $only_profile if true then olny the presentations marked to be shown on a profile page will be output.
   * @param int $limit the maximum number of presentations to display. limit of 0 means show all matching the previous arguments.
   * @return string of mla formated citations for output
   **/
  public function mla_presentations($type = 'html',$only_profile = false,$limit = 0)
  {
    $ret = '';
    if('array' == $type)
    {
      $ret = array();
    } 
    $i = 1;
    foreach($this->presentations as $p)
    {

      if( !$only_profile   || ($only_profile   && $p->in_profile()) )
      {
        $i++;
        if('html' == $type)
        {
          $ret .= '<div class="presentation">';
        }
        if('array' == $type)
        {
          $ret[] = $p->mla($type);
        }
        else
        {
          $ret .= $p->mla($type);
        }
        if('html' == $type)
        {
          $ret .= '</div>';
        }
      }
      if($limit != 0 && $i > $limit)
      {
        break;
      }
    }

    return $ret;
  }



  /**
   * Output this profile's publications as list of APA citations of publications.
   * @param string $type 'html' or 'text' supported. This is what the output method will eventually be.
   * @param bool $only_published if true then only published works will be output
   * @param bool $only_profile if true then olny the publications marked to be shown on a profile page will be output.
   * @param int $limit the maximum number of publications to display. limit of 0 means show all matching the previous arguments.
   * @param int $authors the number of authors to display.
   * @param string $unpublished The text to shown in place of date for unpublished works.
   * @param string $college Limit to the passed College.
   * @param string $dept Limit to the passed Department.
   * @param bool $show_meta Show the meta (College and department) or not
   * @return string of mla formated citations for output
   **/
  function apa($type = 'html', $only_published = false,$only_profile = false,$limit = 0,$authors = null,$unpublished = '',$college=null,$dept=null,$show_meta=true)
  {
    $ret = '';
    if('array' == $type)
    {
      $ret = array();
    }
    $i = 0;
    foreach($this->pubs as $p)
    {
      if( ( !$only_published || ($only_published && $p->is_published())) &&
          (
            !$college || 
            strtolower(preg_replace('/\s*/', '',trim($college))) === strtolower(preg_replace('/\s*/', '',trim($p->meta[0])))
          ) &&
          (
            !$dept ||
            (isset($p->meta[1]) && strtolower(preg_replace('/\s*/', '',trim($dept))) === strtolower(preg_replace('/\s*/', '',trim($p->meta[1]))))
          ) &&
          (isset($p->pub_month) && $p->pub_month != '') &&
          //(isset($p->pub_day) && $p->pub_day != '') &&
          (isset($p->pub_year) && $p->pub_year != '')
        )
      {
        if($show_meta) {
          $ret .= '<div class="pub-meta"><span class="pub-meta-school">' . $p->meta[0] . '</span> <span class="pub-meta-department">' . (isset($p->meta[1]) ? $p->meta[1] : '') . '</span></div>';
        }
        if('html' == $type)
        {
          $ret .= '<div class="publication">';
        }
        if('array' == $type)
        {
          $ret[] = $p->apa($type,$authors,$unpublished);
        }
        else
        {
          $ret .= $p->apa($type,$authors,$unpublished);
        }
        if('html' == $type)
        {
          $ret .= '</div>';
        }
        
      }
      $i++;
    }
    return $ret;
  }

/**
   * Output this profile as list of mla citations of presentations.
   * @parma string $type 'html' or 'text' supported. This is what the output method will eventually be.
   * @param bool $only_profile if true then olny the presentations marked to be shown on a profile page will be output.
   * @param int $limit the maximum number of presentations to display. limit of 0 means show all matching the previous arguments.
   * @return string of mla formated citations for output
   **/
  public function apa_presentations($type = 'html',$only_profile = false,$limit = 0)
  {
    $ret = '';
    if('array' == $type)
    {
      $ret = array();
    } 
    $i = 1;
    foreach($this->presentations as $p)
    {



      if( !$only_profile   || ($only_profile   && $p->in_profile()) )
      {
        $i++;
        if('html' == $type)
        {
          $ret .= '<div class="presentation">';
        }
        if('array' == $type)
        {
          $ret[] = $p->apa($type);
        }
        else
        {
          $ret .= $p->apa($type);
        }
        if('html' == $type)
        {
          $ret .= '</div>';
        }
      }
      if($limit != 0 && $i > $limit)
      {
        break;
      }
    }
    return $ret;
  }

};
