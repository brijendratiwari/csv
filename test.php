<?php

require_once "./dropbox-sdk/Dropbox/autoload.php";
use \Dropbox as dbx;

$dbxClient = new dbx\Client("uYl-Cut4EBkAAAAAAAAIXwbnQP8Kwr6JrQf8u9iCuh1IRaT8Vk9Yy2V5tK9orBNk", "my-app/1.0");

$f = fopen("./data-b.csv", "a+");
$fileMetadata = $dbxClient->getFile("/data-b.csv", $f);
fclose($f);
print_r($fileMetadata);