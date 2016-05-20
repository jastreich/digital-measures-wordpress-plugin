=== Plugin Name ===
Contributors: jastreich
Tags: digital measures, publications, citations
Requires at least: 4.1
Tested up to: 4.2.2
License: GPLv2 or later
License URI: http://www.gnu.org/licenses/gpl-2.0.html

Allows higher ed institutions who use Digital Measures' Activity Insight to pull data using the web API and display it on their WordPress sites.

== Description ==


This plugin allows higher educational institutions who use Digital Measures' Activity Insight to pull data using the web API (Web Services).
Then use that to populate faculty publication citations, educational acheivements (degrees), and presentation citations, in MLA and APA citation standards using shortcodes.

## License
General Public License version 2. 

## Shortcode for Publications

`[digitalmeasures username="epanther" published_only="true" profile_only="false" limit="5" instance="default" format="apa"]`

 - *username* should be the username in Digital Measures for the faculty member.
 - *type* is optional. The default is "publication".
 - *published_only* is optional. The default is "no", and will limit publications listed to those with a status of published.
 - *profile_only* is optional. The default is "yes", and will limit publications listed to those where "show on faculty profile page" in Digital Measures is set to "yes".
 - *limit* is optional. The default is "0" which lists all matching publications, otherwise this is the maximum number of publications to list.
 - *instance* is optional. This will choose which instance of Digital Meausres to pull from. These are setup in the config.inc.php file.
 - *format* is optional. This decides which citation standard to use. Options are "mla" for MLA style or "apa" for APA style. The default style is MLA.
 - *authors* is optional. If present this will limit the authors in the following way:
   - If the number of authors on the publication is less than this value, all authors are shown.
   - If the number of authors on the publication is one more than all authors, all authors are shown.
   - If the number of authors on the publication is two or more larger than authors, this number of authors will be shown followed by "and others" in MLA or "et. al" in APA.
 - *cache* is optional. If supplied, it will override the "Cache Length" value entered in on the Digital Measures Settings page for the data retrieved by this instance of the shortcode.
 - *unpublished_text* is optional. If supplied, it will override the "Alternate Text for Unpublished Works" value entered in on the Digital Measures Settings page for the data retrieved by this instance of the shortcode.

## Shortcode for Education


`[digitalmeasures type="education" username="epanther" show_location="true" show_dates="range"]`

`[digitalmeasures type="education" username="epanther" show_dates="true"]`

 - *username* should be the username in Digital Measures for the faculty member.
 - *type* should be "education" to show the education of a faculty member.
 - *show_location* is optional. This controls showing or not showing the locations (city and state) of the university the degree was earned at. The default is "false".
 - *show_dates* is optional. This controls if and how the dates of the degree are shown. The default is "false".
   - *true* shows the completion date only.
   - *range* shows the start and end dates for the degrees.
   - *false* doesn't show the dates at all.

## Shortcode for Presentations

`[digitalmeasures type="presentations" username="epanther" profile_only="false" limit="5" instance="default" format="apa"]`

 - *username* should be the username in Digital Measures for the faculty member.
 - *type* should be "presentations" to show presentations.
 - *profile_only* is optional. The default is "yes", and will limit publications listed to those where "show on faculty profile page" in Digital Measures is set to "yes".
 - *limit* is optional. The default is "0" which lists all matching publications, otherwise this is the maximum number of publications to list.
 - *instance* is optional. This will choose which instance of Digital Measures to pull from. These are setup in the config.inc.php file.
 - *format* is optional. This decides which citation standard to use. Options are "mla" for MLA style or "apa" for APA style, default is MLA.
 - *authors* is optional. If present this will limit the authors in the following way:
   - If the number of authors on the publication is less than this value, all authors are shown.
   - If the number of authors on the publication is one more than all authors, all authors are shown.
   - If the number of authors on the publication is two or more larger than authors, this number of authors will be shown followed by "and others" in MLA or "et. al" in APA.

## Shortcode for Awards and Honors

`[digitalmeasures type="awards" username="epanther"]`

 - *username* should be the username in Digital Measures for the faculty member.
 - *type* should be "awards" to show awards.
 - *limit* is optional. The default is "0" which lists all awards, otherwise this is the maximum number of awards to list.

## Shortcode for Current Research

`[digitalmeasures type="current_research" username="epanther"]`

 - *username* should be the username in Digital Measures for the faculty member.
 - *type* should be "current_research" to show current research.
 - *limit* is optional. The default is "0" which lists all research, otherwise this is the maximum number of items to list.
 
 ## Shortcode for Contracts and Grants

`[digitalmeasures type="grants" username="epanther"]`

 - *username* should be the username in Digital Measures for the faculty member.
 - *type* should be "grants" to show grants.
 - *limit* is optional. The default is "0" which lists all grants, otherwise this is the maximum number of grants to list.

== Installation ==

Install files to the `/wp-content/plugins/digitalmeasures/` directory.

Add the following to your `wp-config.php`, or copy or move config.inc.php to `wp-content/digitalmeasures/config.inc.php`.
Then edit that file to reflect your Digital Measures' Web Services Account credentials.

`
   $dm_configs = array
  (
    'default' => array
    (
      'username' => '<b>digital measures webservices account username goes here</b>',
      'password' => '<b>password goes here</b>',
      'key' => '<b>University key</b>'
    )
  );
`

Enable the plugin in the plugin page of the admin section.