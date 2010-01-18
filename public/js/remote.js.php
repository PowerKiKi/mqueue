<?php 
header('Content-type: application/javascript'); 
require_once('jquery-1.4.min.js');
require_once('script.js');
?>

$(document).ready(function()
{
	$("head").append('<link href="http://mqueue/css/global.css" media="screen" rel="stylesheet" type="text/css" />');
	scanIMDB();
})