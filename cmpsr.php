<?php

/**
 * Cmpsr
 *
 * This package was writted mostly because developing on an iPad is very limited
 * and DrafeCode, although has offline running capability, did not have support for
 * Composer. This package will thus use the cmpsr.co API to generate the composer
 * packages outside and then return the ZIP as a package. It is by no means perfect
 * and I will be updating it as time goes.
 *
 * @author   Neo Ighodaro <neo@creativitykills.co>
 * @package  Cmpsr
 * @version  1.0
 * @license  MIT
 */

defined('CMPSR_BASE_URL') or define('CMPSR_BASE_URL', 'https://cmpsr.co');


/**
 * @return void
 */
function cmpsr_install(): void
{
    $composerFile = file_exists(__DIR__ . '/cmpsr.json')
        ? __DIR__ . '/cmpsr.json'
        : __DIR__ . '/composer.json';

    if (!$contents = file_get_contents($composerFile)) {
        throw new Exception('Could not locate a "cmpsr.json" or "composer.json" file.');
    }

    $data = ['data' => _cmpsr_json_recode($contents)];

    // Get and escape the contents of the composer.json file
    // Send the POST request to the server
    // fetch the returning ZIP if successful
    // Unzip it and move to the correct directory
}


/**
 * @return void
 */
function cmpsr_fetch(string $hash): void
{
    // Try to fetch
    // If fails then try to install
}


/**
 * @return string
 */
function _cmpsr_json_recode(string $contents): string
{
    return json_encode(json_decode($contents));
}

function _cmpsr_send_request()
{
    $curl = curl_init();

    curl_setopt_array($curl, array(
        CURLOPT_URL => CMPSR_BASE_URL . "/install",
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_TIMEOUT => 300,
        CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
        CURLOPT_CUSTOMREQUEST => "POST",
        CURLOPT_POSTFIELDS => "{\n\t\"data\": \"{\\\"name\\\": \\\"neo\\/dumpr\\\",\\\"description\\\": \\\"a project\\\",\\\"require\\\":{\\\"symfony\\/var-dumper\\\": \\\"^4.3\\\"}}\"\n}",
        CURLOPT_HTTPHEADER => array(
            "Accept: application/json",
            "Content-Type: application/json",
        ),
    ));

    $response = curl_exec($curl);
    $err = curl_error($curl);

    curl_close($curl);

    if (!$err) {
        echo $response;
    }
}

cmpsr_install();
