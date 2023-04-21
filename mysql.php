<?php
require_once("shimm.php");
$c = mysql_connect('localhost', 'root', '') or die(mysql_error());
mysql_select_db('mccodes', $c);