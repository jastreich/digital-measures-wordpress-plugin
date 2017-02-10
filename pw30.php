<!Doctype html>
<html>
  <head>
    <title>Digital Measures Publications</title>
<style>
  .publication, .presentation{margin-bottom:1.8em;}
  .publication i, .presentation i{font-style:italic;}
</style>
   </head>
   <body>

<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);
require_once('publications_list.inc.php');
require_once('config.inc.php');
if(!function_exists('mb_convert_encoding'))
{
  function mb_convert_encoding($str,$a = '',$b='',$c='',$d='')
  {
    return $str;
  }
}
$pw = new publications_list($dm_configs['default'],30);
echo $pw->mla('html',true);



?>
</body></html>
