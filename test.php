<?php 

ini_set('display_errors', '1');
ini_set('display_startup_errors', '1');
error_reporting(E_ALL);

const mykey = 1;
echo mykey; 

echo '<br>';   
$arr = [     'mykey' => 'key in array.', ];  

echo $arr[mykey]; 

 ?>