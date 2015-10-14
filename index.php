<?php

/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */


// ----------------------------------------------------------
// In the URL handler for "/dropbox-auth-start"
require_once './common.php';


$authorizeUrl = getWebAuth()->start();
header("Location: $authorizeUrl");