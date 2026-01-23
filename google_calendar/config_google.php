<?php
require_once __DIR__ . '/../vendor/autoload.php';

function getClient(): Google_Client {
    $credentialsPath = __DIR__ . '/credentials.json';
    if (!file_exists($credentialsPath)) {
        throw new RuntimeException('Missing credentials.json in google_calendar.');
    }

    $client = new Google_Client();
    $client->setApplicationName('Psy System Calendar');
    $client->setScopes(Google_Service_Calendar::CALENDAR);
    $client->setAuthConfig($credentialsPath);
    $client->setAccessType('offline');
    $client->setPrompt('select_account consent');

    return $client;
}