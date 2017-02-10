<?php
  /**
   * Plugin Name: Digital Measures Shortcodes
   * Plugin UPI:
   * Description: A plugin to provide access to Digital Measures' Activity Insight data like citation information for faculty's publications, presentations and education.
   * Version: 1.2
   * Author: Jeremy Streich
   * Author URI: http://uwm.edu/lsito/
   **/

  /**
   * @file digitalmeasures.php
   * This file provides the Wordpress framing for the Digital Measures Activity Insight plugin.
   * @Author Jeremy Streich
   **/



  // Defaults Values for Digitial Measures ShortCode


  /**
   * Default maximum cache (int in minutes)
   **/
  DEFINE("DM_CACHELENGTH",1440);

  /**
   * Default username is blank.  If username is left blank the shortcode will be empty
   **/
  DEFINE("DM_USERNAME", '');

  /**
   * Default tpye is 'publications', the only current supported option.
   **/
  DEFINE("DM_TYPE", 'publications');

  /**
   * Default published_only is false, show works in with other statuses.
   **/
  DEFINE("DM_PUBLISHED_ONLY", "no");

  /**
   * Default profile_only is true, show only the works the faculty mark in Digital Measures.
   **/
  DEFINE("DM_PROFILE_ONLY", "yes");

  /**
   * Default format is 'mla'
   **/
  DEFINE("DM_FORMAT", 'mla');

  /**
   * Default limit is 0, meaning publication list is unlimited.
   **/
  DEFINE("DM_LIMIT", 0);

  DEFINE("DM_AUTHORS",null);

  DEFINE("DM_SHOW_LOCATION",false);
  DEFINE("DM_SHOW_DATES",true);

  /**
   * Deafault instance is 'default'
   **/
   DEFINE('DM_INSTANCE','default');


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


  // register the shortcode
  add_shortcode("digitalmeasures","digitalmeasures_handler");
  add_shortcode("digitalmeasures_list","digitalmeasures_list_handler");

  // Add the CSS to the head of the document
  add_action('wp_enqueue_scripts','digitalmeasures_load_style');
if(!function_exists('mb_convert_encoding'))
{
  function mb_convert_encoding($str,$a = '',$b='',$c='',$d='')
  {
    return $str;
  }
}

  function digitalmeasures_load_style()
  {
    wp_register_style('digitalmeasures-style',plugins_url('/style/digitalmeasures.css', __FILE__));
    wp_enqueue_style('digitalmeasures-style');
  }

  /**
   * Function used to handle shortcode.
   * @param array $incomingfrompost the array WordPress passes to shortcodes
   *   'username' => username (UWM epanther) for digital measures. If omitted or left blank, the shortcode will show no output.
   *   'type' => What type of digital meausres data are using. Currently only publications are supported. (optional, default 'publications')
   *   'published_only' => If true, only show items that are listed as published. (Optional, default false)
   *   'profile_only' => If true, only show items that have profile set. (Optional, default true)
   *   'limit' => If 0 no limit, otherwise this is the max number of items to show. (Optional, default 0)
   *   'format' => Citation standard to use, curredntly supported is 'mla' and 'apa'. (Optional, default 'mla').
   * @return string The digital measures data.
   **/
  function digitalmeasures_handler($incomingfrompost)
  {

    $incomingfrompost = shortcode_atts
    (
       array
       (
         'username' => DM_USERNAME,
         'type' => DM_TYPE,
         'published_only' => get_option('dm_published_only',DM_PUBLISHED_ONLY),
         'profile_only' => get_option('dm_profile_only',DM_PROFILE_ONLY),
         'limit' => get_option('dm_limit',DM_LIMIT),
         'format' => get_option('dm_format',DM_FORMAT),
         'instance' => DM_INSTANCE,
         'show_dates' => DM_SHOW_DATES,
         'show_location' => DM_SHOW_LOCATION,
         'authors' => get_option('dm_authors',DM_AUTHORS),
         'cache' => get_option('dm_cache_length',DM_CACHELENGTH),
         'unpublished_text' => get_option('dm_unpublished_text','')
       ),
       $incomingfrompost
    );
    return digitalmeasures_function($incomingfrompost);
  }

  /**
   * Function that does the work for displaying the shortcode.
   * @param array $args the arguments passed from the handler
   * @see digitalmeasures_handler()
   * @return string The citations for publications from digital measures.
   **/
  function digitalmeasures_function($args)
  {
    global $dm_configs;
    if(!isset($dm_configs))
    {
      if(defined('WP_CONTENT_DIR'))
      {
        $dm_config_file = WP_CONTENT_DIR . '/digitalmeasures/config.inc.php';
      }

      if(file_exists($dm_config_file))
      {
        require_once($dm_config_file);
      }
      else
      {
        require_once('config.inc.php');
      }
    }

    if($args['username'] == '')
    {
      return '';
    }
    require_once('profile.inc.php');
    $profile = get_transient('dm_' . $args['username']);
    if($profile === false)
    {
      $profile = new profile($args['username'],$dm_configs[$args['instance']]);
      set_transient('dm_' . $args['username'],$profile, $args['cache'] * 60);
      $trans = get_transient('dm_transients');
      if(!$trans)
      {
        $trans = array();
      }
      $trans[] = 'dm_' . $args['username'];
      set_transient('dm_transients',$trans, $args['cache'] * 60);
      //echo 'Not cached.';
    }
    else
    {
      //echo 'cached.';
    }
    if($args['type'] == 'publications') // PUBLICATIONS
    {
      if($args['format'] == 'apa')
      {
        $ret = $profile->apa
        (
          'html',
          ($args['published_only'] === 'true' || $args['published_only'] === true || $args['published_only'] === 'yes'),
          ($args['profile_only'] === 'true' || $args['profile_only'] === true || $args['profile_only'] === 'yes'),
          ($args['limit'] == 0 ? null : $args['limit']), $args['authors'], $args['unpublished_text']
        );
        return $ret;
      }
      else
      {

        $ret = $profile->mla
        (
          'html',
          ($args['published_only'] === 'true' || $args['published_only'] === true || $args['published_only'] === 'yes'),
          ($args['profile_only'] === 'true' || $args['profile_only'] === true || $args['profile_only'] === 'yes'),
          $args['limit'],
          $args['authors'],
          $args['unpublished_text'],
          (stripos($args['format'],'strict') !==  false)
        );

        return $ret;
      }
    }
    else if($args['type'] == 'presentations') // PRESENTATIONS
    {
      if($args['format'] == 'apa')
      {
        $ret = $profile->apa_presentations
        (
          'html',
          ($args['profile_only'] === 'true' || $args['profile_only'] === true),
          $args['limit']
        );
        return $ret;
      }
      else
      {
        $ret = $profile->mla_presentations
        (
          'html',
          ($args['profile_only'] === 'true' || $args['profile_only'] === true),
          $args['limit']
        );
        return $ret;
      }
    }
    else if($args['type'] == 'education') // EDUCATION
    {
      $ret = $profile->education($args['show_location'],$args['show_dates']);
      return $ret;
    }
    else if($args['type'] == 'awards') // EDUCATION
    {
      $ret = $profile->awards($args['limit']);
      return $ret;
    }
    else if($args['type'] == 'current_research') // EDUCATION
    {
      $ret = $profile->current_research($args['limit']);
      return $ret;
    }
    else if($args['type'] == 'grants') // EDUCATION
    {
      $ret = $profile->grants($args['limit']);
      return $ret;
    }
  }



 /**
   * Function used to handle shortcode.
   * @param array $incomingfrompost the array WordPress passes to shortcodes
   * 
   * @return string The digital measures data.
   **/
  function digitalmeasures_list_handler($incomingfrompost)
  {

    $args = shortcode_atts
    (
       array
       (
          'format' => 'apa',
          'college' => '',
          'dept' => '',
          'days' => '30',
          'show_meta' => true
       ),
       $incomingfrompost
    );
    require_once('publications_list.inc.php');
    require_once('config.inc.php');

    $pw = new publications_list($dm_configs['default'],$args['days']);
    $ret = '';
    if('mla' == $args['format']) 
    {
      //public function mla($type = 'html', $only_published = false,$only_profile = false,$limit = 0,$authors = null,$unpublished = '',$strict = false,$college=null,$dept=null,$show_meta=true)
      $ret = $pw->mla('html',true,false,0,null,'',false,$args['college'],$args['dept'],($args['show_meta'] === 'true' || $args['show_meta'] === true || $args['show_meta'] === 'yes'));
    }
    else if('mla' == $args['format']) 
    {
      //public function mla($type = 'html', $only_published = false,$only_profile = false,$limit = 0,$authors = null,$unpublished = '',$strict = false,$college=null,$dept=null,$show_meta=true)
      $ret = $pw->mla('html',true,false,0,null,'',true,$args['college'],$args['dept'],($args['show_meta'] === 'true' || $args['show_meta'] === true || $args['show_meta'] === 'yes'));
    }
    else if('apa' == $args['format'])
    {
      //function apa($type = 'html', $only_published = false,$only_profile = false,$limit = 0,$authors = null,$unpublished = '',$college=null,$dept=null,$show_meta=true)
      $ret = $pw->apa('html',true,false,0,null,'',$args['college'],$args['dept'],($args['show_meta'] === 'true' || $args['show_meta'] === true || $args['show_meta'] === 'yes'));
    }

    return $ret;
  }


  function dm_create_menu()
  {
    add_submenu_page('options-general.php','Digital Measures Shortcodes','Digital Measures','manage_options','dm_settings','dm_settings_page');
    add_action( 'admin_init', 'register_dm_settings' );
  }


  function register_dm_settings()
  {
    register_setting('dm-settings-group', 'dm_unpublished_text');
    register_setting('dm-settings-group', 'dm_cache_length');
    register_setting('dm-settings-group', 'dm_format');
    register_setting('dm-settings-group', 'dm_limit');
    register_setting('dm-settings-group', 'dm_published_only');
    register_setting('dm-settings-group', 'dm_profile_only');
    register_setting('dm-settings-group', 'dm_authors');
  }


function dm_settings_page()
{
?>
<div class="wrap">
<h2>Digital Measures Shortcode</h2>   
<h3>Settings</h3>
<form action="options.php" method="post">
  <?php settings_fields( 'dm-settings-group' ); ?>
  <?php do_settings_sections( 'dm-settings-group' ); ?>
<table class="form-table">
  <tr valign="top">
    <th scope="row">Cache Length</th>
    <td>
      <input type="number" name="dm_cache_length" value="<?php echo esc_attr( get_option('dm_cache_length',DM_CACHELENGTH) ); ?>" />
      <p class="description">The maximum amount of time, in <b>minutes</b>, to keep Digital Measures data cached.</p>
      <button onclick="event.preventDefault();jQuery.post(ajaxurl,{action:'dm_clear_cache'},function(response){alert('Cache cleared.');});">Clear Digital Measures Cache</button>
    </td>
  </tr>
  <tr valign="top">
    <th scope="row">Alternate Text for Unpublished Works</th>
    <td>
      (<input type="text" name="dm_unpublished_text" value="<?php echo esc_attr( get_option('dm_unpublished_text') ); ?>" />)
      <p class="description">The text to replace the year when a work isn't published yet. The text will be rendered inside of parenthsies. Suggested/common values are "Forthcoming" and "In Press".</p>
    </td>
  </tr>
  <tr>
    <th scope="row">Default Citation Format</th>
    <td>
      <input type="radio" name="dm_format" value="mla" <?php echo (get_option('dm_format',DM_FORMAT) == 'mla' ? 'checked="checked"' : ''); ?> />MLA<br/>
      <input type="radio" name="dm_format" value="apa" <?php echo (get_option('dm_format',DM_FORMAT) == 'apa' ? 'checked="checked"' : ''); ?>/>APA<br/>
      <input type="radio" name="dm_format" value="strict-mla" <?php echo (get_option('dm_format',DM_FORMAT) == 'strict-mla' ? 'checked="checked"' : ''); ?>/>Strict MLA
      <p class="description">The default citation standard to use for the site. This setting can be overridden using the <code>format</code> option on a shortcode.</p>
    </td>
  </tr>
  <tr>
    <th scope="row">Default Limit</th>
    <td>
      <input type="number" name="dm_limit" value="<?php echo esc_attr( get_option('dm_limit',DM_LIMIT) ); ?>" />
      <p class="description">The maximum number of items to list. Setting this to "0" will list all matching items. This setting can be overridden using the <code>limit</code> option on a shortcode.</p>
    </td>
  </tr>
  <tr>
    <th scope="row">Default published_only</th>
    <td>
      <input type="radio" name="dm_published_only" value="yes" <?php echo (get_option('dm_published_only',DM_PUBLISHED_ONLY) === true || get_option('dm_published_only',DM_PUBLISHED_ONLY) === 'true' || get_option('dm_published_only',DM_PUBLISHED_ONLY) === 'yes' ? 'checked="checked"' : ''); ?> />Yes<br/>
      <input type="radio" name="dm_published_only" value="no" <?php echo (get_option('dm_published_only',DM_PUBLISHED_ONLY) === false || get_option('dm_published_only',DM_PUBLISHED_ONLY) === 'false' || get_option('dm_published_only',DM_PUBLISHED_ONLY) === 'no'  ? 'checked="checked"' : ''); ?> />No<br/>
      <p class="description">If set to "yes", this will limit publications listed to those with a status of published. This setting can be overridden using the <code>published_only</code> option on a shortcode.</p>
    </td>
  </tr>
  <tr>
    <th scope="row">Default profile_only</th>
    <td>
      <input type="radio" name="dm_profile_only" value="yes" <?php echo (get_option('dm_profile_only',DM_PROFILE_ONLY) === true || get_option('dm_profile_only',DM_PROFILE_ONLY) === 'true' || get_option('dm_profile_only',DM_PROFILE_ONLY) === 'yes'  ? 'checked="checked"' : ''); ?> />Yes<br/>
      <input type="radio" name="dm_profile_only" value="no" <?php echo (get_option('dm_profile_only',DM_PROFILE_ONLY) === false || get_option('dm_profile_only',DM_PROFILE_ONLY) === 'false' || get_option('dm_profile_only',DM_PROFILE_ONLY) === 'no' ? 'checked="checked"' : ''); ?> />No<br/>
      <p class="description">If set to "yes", this will limit publications or presentations listed to those where "show on faculty profile page" in Digital Measures is set to "yes" This setting can be overridden using the <code>profile_only</code> option on a shortcode.</p>
    </td>
  </tr>

  <tr>
    <th scope="row">Default Author Limit</th>
    <td>
      <input type="number" name="dm_authors" value="<?php echo esc_attr( get_option('dm_authors',DM_AUTHORS) ); ?>" />
      <p class="description">If non-zero this will limit the authors in the following way:
         <ul style="list-style-type: square;padding-left:20px;">
           <li>If the number of authors on the publication is less than this value, all authors are shown.</li>
           <li>If the number of authors on the publication is one more than all authors, all authors are shown.</li>
           <li>If the number of authors on the publication is two or more larger than authors, this number of authors will be shown followed by "and others" in MLA or "et. al" in APA.</li>
          </ul>
          This setting can be overridden using the <code>authors</code> option on a shortcode.</p>
    </td>
  </tr>


</table>

<?php submit_button(); ?>
</form>
<hr/>
<h3>How to use the ShortCode</h3>
<h4 id="shortcode-for-publications">Shortcode for Publications<a href="#shortcode-for-publications"></a></h4>
<p><code>[digitalmeasures username="epanther" published_only="true" profile_only="false" limit="5" instance="default" format="apa"]</code></p>
<p><code>[digitalmeasures username="epanther"]</code></p>
<ul style="list-style-type: square;padding-left:20px;">
  <li><em>username</em> should be the username in Digital Measures for the faculty member.</li>
  <li><em>type</em> is optional. The default is "publication".</li>
  <li><em>published_only</em> is optional. The default is "no", and will limit publications listed to those with a status of published.</li>
  <li><em>profile_only</em> is optional. The default is "yes", and will limit publications listed to those where "show on faculty profile page" in Digital Measures is set to "yes".</li>
  <li><em>limit</em> is optional. The default is "0" which lists all matching publications, otherwise this is the maximum number of publications to list.</li>
  <li><em>instance</em> is optional. This will choose which instance of Digital Meausres to pull from. These are setup in the config.inc.php file.</li>
  <li><em>format</em> is optional. This decides which citation standard to use. Options are "mla" for MLA style or "apa" for APA style. The default style is MLA.</li>
  <li><em>authors</em> is optional. If present this will limit the authors in the following way:
    <ul style="list-style-type: square;padding-left:20px;">
      <li>If the number of authors on the publication is less than this value, all authors are shown.</li>
      <li>If the number of authors on the publication is one more than all authors, all authors are shown.</li>
      <li>If the number of authors on the publication is two or more larger than authors, this number of authors will be shown followed by "and others" in MLA or "et. al" in APA.</li>
    </ul>
  </li>
  <li><em>cache</em> is optional. If supplied, it will override the "Cache Length" value entered in on the Digital Measures Settings page for the data retrieved by this instance of the shortcode.</li>
  <li><em>unpublished_text</em> is optional. If supplied, it will override the "Alternate Text for Unpublished Works" value entered in on the Digital Measures Settings page for the data retrieved by this instance of the shortcode.</li>
</ul>

<h4 id="shortcode-for-education">Shortcode for Education<a href="#shortcode-for-education"></a></h4>
<p><code>[digitalmeasures type="education" username="epanther" show_location="true" show_dates="range"]</code></p>
<p><code>[digitalmeasures type="education" username="epanther" show_dates="true"]</code></p>
<ul style="list-style-type: square;padding-left:20px;">
  <li><em>username</em> should be the username in Digital Measures for the faculty member.</li>
  <li><em>type</em> should be "education" to show the education of a faculty member.</li>
  <li><em>show_location</em> is optional. This controls showing or not showing the locations (city and state) of the university the degree was earned at. The default is "false".</li>
  <li><em>show_dates</em> is optional. This controls if and how the dates of the degree are shown. The default is "false".
    <ul style="list-style-type: square;padding-left:20px;">
      <li><em>true</em> shows the completion date only.</li>
      <li><em>range</em> shows the start and end dates for the degrees.</li>
      <li><em>false</em> doesn't show the dates at all.</li>
    </ul>
  </li>
</ul>

<h4 id="shortcode-for-presentations">Shortcode for Presentations<a href="#shortcode-for-presentations"></a></h4>
<p><code>[digitalmeasures type="presentations" username="epanther" profile_only="false" limit="5" instance="default" format="apa"]</code></p>
<p><code>[digitalmeasures type="presentations" username="epanther"]</code></p>
<ul style="list-style-type: square;padding-left:20px;">
  <li><em>username</em> should be the username in Digital Measures for the faculty member.</li>
  <li><em>type</em> should be "presentations" to show presentations.</li>
  <li><em>profile_only</em> is optional. The default is "yes", and will limit presentations listed to those where "show on faculty profile page" in Digital Measures is set to "yes".</li>
  <li><em>limit</em> is optional. The default is "0" which lists all matching presentations, otherwise this is the maximum number of presentations to list.</li>
  <li><em>instance</em> is optional. This will choose which instance of Digital Measures to pull from. These are setup in the config.inc.php file.</li>
  <li><em>format</em> is optional. This decides which citation standard to use. Options are "mla" for MLA style or "apa" for APA style, default is MLA.</li>
  <li><em>authors</em> is optional. If present this will limit the authors in the following way:
    <ul style="list-style-type: square;padding-left:20px;"><li>If the number of authors on the presentation is less than this value, all authors are shown.</li>
      <li>If the number of authors on the presentation is one more than all authors, all authors are shown.</li>
      <li>If the number of authors on the presentation is two or more larger than authors, this number of authors will be shown followed by "and others" in MLA or "et. al" in APA.</li>
    </ul>
  </li>
</ul>

<h4 id="shortcode-for-awards-and-honors">Shortcode for Awards and Honors</h4>
<p><code>[digitalmeasures type="awards" username="epanther"]</code></p>
<ul style="list-style-type: square;padding-left:20px;">
  <li><em>username</em> should be the username in Digital Measures for the faculty member.</li>
  <li><em>type</em> should be "awards" to show awards.</li>
    <li><em>limit</em> is optional. The default is "0" which lists all awards, otherwise this is the maximum number of awards to list.</li>
</ul>

<h4 id="shortcode-for-current-research">Shortcode for Current Research</h4>
<p><code>[digitalmeasures type="current_research" username="epanther"]</code></p>
<ul style="list-style-type: square;padding-left:20px;">
  <li><em>username</em> should be the username in Digital Measures for the faculty member.</li>
  <li><em>type</em> should be "current_research" to show current research.</li>
  <li><em>limit</em> is optional. The default is "0" which lists all current research, otherwise this is the maximum number of items to list.</li>
</ul>

<h4 id="shortcode-for-contracts-and-grants">Shortcode for Contracts and Grants</h4>
<p><code>[digitalmeasures type="grants" username="epanther"]</code></p>
<ul style="list-style-type: square;padding-left:20px;">
  <li><em>username</em> should be the username in Digital Measures for the faculty member.</li>
  <li><em>type</em> should be "grants" to show grants.</li>
    <li><em>limit</em> is optional. The default is "0" which lists all grants, otherwise this is the maximum number of grants to list.</li>
</ul>

<h4 id="shortcode-for-lists">List Recent Publications</h4>
<p><code>[digitalmeasures_list format="apa" college="College" department="Department" days="30" show_meta="true" ]</code></p>
<ul style="list-style-type: square;padding-left:20px;">
  <li><em>format</em> is optional. This decides which citation standard to use. Options are "mla" for MLA style or "apa" for APA style. Default is MLA.</li>
  <li><em>college</em> is optional. Filter only this school or college.</li>
  <li><em>department</em> is optional. Filter only this department.</li>
  <li><em>days</em> is optional. The number of days to return. Default is 30.</li>
  <li><em>show_meta</em> is optional. To output the school/college and department above each publication. Default is <code>true</code>.</li>
<ul>

</div>
<?php
}


add_action('admin_menu', 'dm_create_menu');

function dm_clear_cache()
{
  $trans = get_transient('dm_transients');
  if(!$trans)
  {
    $trans = array();
  }
  $i = 0;
  foreach($trans as $t)
  {
    ++$i;
    delete_transient($t);
  }
  echo $i;
  $trans = array();
  set_transient('dm_transients',$trans,DM_CACHELENGTH * 60);
}

add_action('wp_ajax_dm_clear_cache','dm_clear_cache');




?>
