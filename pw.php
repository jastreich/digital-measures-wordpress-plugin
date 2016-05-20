<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);
require_once('publications_week.inc.php');
require_once('config.inc.php');

$pw = new publications_week($dm_configs->default);
echo $pw->mla();



?>