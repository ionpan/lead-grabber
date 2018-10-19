<?php

$dbh = require('database.php');
$secrets = require ('secrets.php');

require 'vendor' . DIRECTORY_SEPARATOR . 'autoload.php';
header('Content-Type: text/json; charset=utf-8');

use \DrewM\MailChimp\MailChimp;

$MailChimp = new MailChimp($secrets['mailchimp']['accessToken']);
$MailChimp->verify_ssl = false;

if (!isset($_POST['Register'])) {
    echo json_encode([
        'status' => 'error',
        'message' => ['The form is empty.'],
    ]);
}

$postData = $_POST['Register'];
$errors = [];

$postFullname = isset($postData['fullname']) ? $postData['fullname'] : null;
$postEmail = isset($postData['email']) ? $postData['email'] : null;
$postOptin = (int) isset($postData['optin']) ? $postData['optin'] : 0;

if (!$postFullname) {
    $errors[] = 'Full name is required.';
}
if (!$postEmail) {
    $errors[] = 'E-mail is required.';
}
if (strlen($postFullname) > 50) {
    $errors[] = 'Full name is too long.';
}
if (strlen($postEmail) > 50) {
    $errors[] = 'E-mail is too long.';
}
if ($postEmail && !filter_var($postEmail, FILTER_VALIDATE_EMAIL)) {
    $errors[] = 'E-mail is invalid.';
}

if ($errors) {
    echo json_encode([
        'status' => 'error',
        'errors' => $errors,
    ]);
    die();
}

try {
    $sth = $dbh->prepare('SELECT COUNT(*) FROM `contact` WHERE `email` = :email');
    $sth->execute([':email' => $postEmail]);
    if ($sth->fetchColumn()) {
        echo json_encode([
            'status' => 'ok',
        ]);
        die();
    }

    $sth = $dbh->prepare('INSERT INTO `contact` (registration_datetime, full_name, email, optin) VALUES (:registrationDatetime, :fullName, :email, :optin)');
    $sth->execute([
        ':registrationDatetime' => (new Datetime())->format('Y-m-d H:i:s'),
        ':fullName' => $postFullname,
        ':email' => $postEmail,
        ':optin' => $postOptin,
    ]);
} catch (Exception $e) {
    echo json_encode([
        'status' => 'error',
        'errors' => ['Internal server error.'],
    ]);
    die();
}

$result = $MailChimp->post('lists/' . $secrets['mailchimp']['listId'] . '/members', [
    'merge_fields' => ['FNAME' => $postFullname, 'LNAME' => ''],
    'email_address' => $postEmail,
    'status' => 'subscribed',
    'marketing_permissions' =>
    [
        [
            'marketing_permission_id' => 'GDPR',
            'enabled' => (bool) $postOptin,
        ]
    ],
        ]);

if (!$MailChimp->success()) {
    echo json_encode([
        'status' => 'error',
        'errors' => ['Internal server error.'],
    ]);
    die();
}

echo json_encode([
    'status' => 'ok',
]);
