<?php


$filepath = "storage/app/simulations1/Optics.zip";
$directory = "storage/app/simulations1/";
$ext = substr($filepath, strrpos($filepath, '.') + 1);
$directory = substr($filepath, 0, strrpos($filepath, '.'));
print_r($directory);
print_r($ext);
