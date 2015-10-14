<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

require_once './common.php';
require_once "./dropbox-sdk/Dropbox/autoload.php";

use \Dropbox as dbx;

try {
    $myarr = array("state" => $_GET["state"],
        "code" => $_GET["code"]);
    if (!isset($_SESSION['accessToken'])) {
        list($accessToken, $userId, $urlState) = getWebAuth()->finish($myarr);

        $_SESSION['accessToken'] = $accessToken;
        $_SESSION['userID'] = $userId;
        $_SESSION['urlState'] = $urlState;
    } else {
        $accessToken = $_SESSION['accessToken'];
        $userId = $_SESSION['userID'];
        $urlState = $_SESSION['urlState'];
    }

    assert($urlState === null);  // Since we didn't pass anything in start()
} catch (dbx\WebAuthException_BadRequest $ex) {
    error_log("/dropbox-auth-finish: bad request: " . $ex->getMessage());
    // Respond with an HTTP 400 and display error page...
} catch (dbx\WebAuthException_BadState $ex) {
    // Auth session expired.  Restart the auth process.
    header('Location: /dropbox-auth-start');
} catch (dbx\WebAuthException_Csrf $ex) {
    error_log("/dropbox-auth-finish: CSRF mismatch: " . $ex->getMessage());
    // Respond with HTTP 403 and display error page...
} catch (dbx\WebAuthException_NotApproved $ex) {
    error_log("/dropbox-auth-finish: not approved: " . $ex->getMessage());
} catch (dbx\WebAuthException_Provider $ex) {
    error_log("/dropbox-auth-finish: error redirect from Dropbox: " . $ex->getMessage());
} catch (dbx\Exception $ex) {
    error_log("/dropbox-auth-finish: error communicating with Dropbox API: " . $ex->getMessage());
}

print "Access Token: " . $accessToken . "\n";


$dbxClient = new dbx\Client(trim($accessToken), "my-app/1.0");

$f = fopen("./data-b.csv", "a+");
$fileMetadata = $dbxClient->getFile("/data-b.csv", $f);
fclose($f);

$fa = fopen("./data-a.csv", "a+");
$fileMetadata = $dbxClient->getFile("/data-a.csv", $fa);
fclose($fa);
echo '<pre>';

$data_csv = array();

$file = fopen("data-a.csv", "r");
while (!feof($file)) {
    $temp = fgetcsv($file);
    if (!empty($temp)) {
        $key = $temp[1];
        unset($temp[1]);
        unset($temp[3]);
        unset($temp[6]);
        unset($temp[7]);
        unset($temp[8]);
        unset($temp[9]);
        $data_csv[$key] = $temp;
    }
}
fclose($file);

$datab_csv = array();
$file_b = fopen("data-b.csv", "r");
while (!feof($file_b)) {
    $temp = fgetcsv($file_b);
    if (!empty($temp)) {
        $key = $temp[4];

        unset($temp[1]);
        unset($temp[4]);
        unset($temp[5]);
        unset($temp[6]);
        unset($temp[7]);
        unset($temp[8]);
        unset($temp[9]);

        $datab_csv[$key] = $temp;
    }
}
fclose($file_b);

//var_dump($datab_csv);
//var_dump($data_csv);
$final = array_merge_recursive($data_csv, $datab_csv);

$filefinal = fopen("data-final.csv", "w+");

foreach ($final as $line) {
    fputcsv($filefinal, $line);
}

fclose($filefinal);
echo 'New file create with name data-final.csv';