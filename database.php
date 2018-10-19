<?php

$secrets = require ('secrets.php');

$dbh = new PDO('mysql:host=' . $secrets['db']['hostname'] . ';dbname=' . $secrets['db']['dbName'] . ';charset=utf8', $secrets['db']['user'], $secrets['db']['password']);
$dbh->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
$dbh->setAttribute(PDO::ATTR_EMULATE_PREPARES, false);
$dbh->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_OBJ);

return $dbh;
