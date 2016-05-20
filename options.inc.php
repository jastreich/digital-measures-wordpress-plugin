<?php



add_action('admin_menu', 'dm_create_menu');
add_action('network_admin_menu', 'dm_create_network_menu');


/**
 * Add the normal settings page to the menu.
 *
 **/
function dm_create_menu()
{
  add_submenu_page('options-general.php','Digital Measures Shortcodes','Digital Measures','manage_options','dm_settings','dm_settings_page');
}

/**
 * Add the network setting page to the menu
 *
 **/
function dm_create_network_menu()
{
  add_submenu_page(
       'settings.php',
       'Digital Measures Shortcode Network Setting',
       'Digital Measures',
       'manage_network_options',
       'dm-network-settings',
       'dm_network_settings_page'
  );
}

add_action( 'admin_init', 'register_dm_settings' );

/**
 * Add settings to the normal settings page.
 *
 **/
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

/**
 * Setting page.
 *
 **/
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
    <th scope="row">Default Publication Limit</th>
    <td>
      <input type="number" name="dm_limit" value="<?php echo esc_attr( get_option('dm_limit',DM_LIMIT) ); ?>" />
      <p class="description">The maximum number of publications to list. Setting this to "0" will list all matching publications. This setting can be overridden using the <code>limit</code> option on a shortcode.</p>
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
      <p class="description">If set to "yes", this will limit public will limit publications listed to those where "show on faculty profile page" in Digital Measures is set to "yes" This setting can be overridden using the <code>profile_only</code> option on a shortcode..</p>
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
  <li><em>profile_only</em> is optional. The default is "yes", and will limit publications listed to those where "show on faculty profile page" in Digital Measures is set to "yes".</li>
  <li><em>limit</em> is optional. The default is "0" which lists all matching publications, otherwise this is the maximum number of publications to list.</li>
  <li><em>instance</em> is optional. This will choose which instance of Digital Measures to pull from. These are setup in the config.inc.php file.</li>
  <li><em>format</em> is optional. This decides which citation standard to use. Options are "mla" for MLA style or "apa" for APA style, default is MLA.</li>
  <li><em>authors</em> is optional. If present this will limit the authors in the following way:
    <ul style="list-style-type: square;padding-left:20px;"><li>If the number of authors on the publication is less than this value, all authors are shown.</li>
      <li>If the number of authors on the publication is one more than all authors, all authors are shown.</li>
      <li>If the number of authors on the publication is two or more larger than authors, this number of authors will be shown followed by "and others" in MLA or "et. al" in APA.</li>
    </ul>
  </li>
</ul>
</div>
<?php
}

/**
 * Nework settings page.
 *
 **/
function dm_network_settings()
{
  ?>
  <div class="wrap">
    <?php screen_icon(); ?>
    <h2>My Plugin Options</h2>
    <form action="options.php" method="POST">
      <?php settings_fields( 'dm-network-settings-group' ); ?>
      <?php do_settings_sections( 'dm-network-settings' ); ?>
      <?php submit_button(); ?>
    </form>
  </div>
  <?php
}


add_action( 'admin_init', 'dm_network_admin_init' );

/**
 * Network setting fields.
 *
 **/
function dm_network_admin_init()
{
  register_setting( 'dm-netowrk-settings-group', 'dm-network-setting' );

  // Sections
  add_settings_section( 'dm-network-section', 'Digital Measure Network Options', 'dm_network_callback', 'dm-network' );

  // Fields
  add_settings_field( 'dm-username', 'Username', 'dm_username_callback', 'dm-network', 'dm-network-section' );
  add_settings_field( 'dm-password', 'Password', 'dm_password_callback', 'dm-network', 'dm-network-section' );
  add_settings_field( 'dm-key', 'Key', 'dm_key_callback', 'dm-network', 'dm-network-section'); 
}

/**
 * Network Callback
 *
 **/
function dm_network_callback()
{

}

/**
 * Username.
 *
 **/
function dm_username_callback()
{
  $setting = esc_attr( get_option( 'dm-username' ) );
  echo "<input type='text' name='dm-username' value='$setting' />";
}

/**
 * Password.
 *
 **/
function dm_password_callback()
{
  $setting = esc_attr( get_option( 'dm-password' ) );
  echo "<input type='password' name='dm-password' value='$setting' />";
}

/**
 * Key.
 *
 **/
function dm_key_callback()
{
  $setting = esc_attr( get_option( 'dm-key' ) );
  echo "<input type='password' name='dm-key' value='$setting' />";
}

/**
 * Clear Digiral Measures Cache.
 *
 **/
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