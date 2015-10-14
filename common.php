<?php

unset($_SESSION);
session_start();

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

require_once "dropbox-sdk/Dropbox/autoload.php";

use \Dropbox as dbx;

function getWebAuth() {
    $appInfo = dbx\AppInfo::loadFromJsonFile("app-info.json");
    $clientIdentifier = "my-app/1.0";
    $redirectUri = "http://localhost/csv/authfinish.php";
    $csrfTokenStore = new dbx\ArrayEntryStore($_SESSION, 'dropbox-auth-csrf-token');
    return new dbx\WebAuth($appInfo, $clientIdentifier, $redirectUri, $csrfTokenStore);
}

function getcsvfile($localfile, $remotefile) {
    $f = fopen("$localfile", "a+");
    $fileMetadata = $dbxClient->getFile($remotefile, $f);
    fclose($f);
}
