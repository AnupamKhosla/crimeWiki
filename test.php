<?php 
require_once('include/config.php');
require_once('include/functions.php');


$x = [1];
function cmp($a, $b) {
	return 1;
}
usort($x, "cmp");
var_dump($x);
?>