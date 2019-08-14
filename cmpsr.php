<?php

/**
 * Cmpsr
 *
 * This package was writted mostly because developing on an iPad is very limited
 * and DraftCode, although has offline running capability, did not have support for
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

    if (!file_exists($composerFile)) {
        throw new Exception('Could not locate a "cmpsr.json" or "composer.json" file.');
    }

    $contents = file_get_contents($composerFile);
    $data = ['data' => _cmpsr_json_recode($contents)];

    $response = json_decode(_cmpsr_send_request($data), true);

    if (!$response['status'] ?? false) {
        throw new Exception('An error occurred. Err: ' . $response['error'] ?? 'unknown');
    }

    $filename = basename($response['url']);

    echo "Downloading ZIP file from {$response['url']}..." . PHP_EOL;

    _cmpsr_download_file($response['url'], $filename);

    if (!file_exists($filename)) {
        echo "Error downloading ZIP file..." . PHP_EOL;
    }

    echo "Download complete. Unzipping..." . PHP_EOL;

    $zip = new ZipArchive;

    if ($zip->open($filename)) {
        $res = $zip->extractTo(__DIR__);
        $zip->close();
    }

    echo ((isset($res) && $res) ? 'Unzipped successfully!' : 'Failed to unzip') . PHP_EOL;

    unlink($filename);

    echo "Complete.";
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

/**
 * @param  array $data
 * @return string|null
 */
function _cmpsr_send_request(array $data): string
{
    $curl = curl_init();

    curl_setopt_array($curl, array(
        CURLOPT_URL => CMPSR_BASE_URL . "/install",
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_TIMEOUT => 300,
        CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
        CURLOPT_CUSTOMREQUEST => "POST",
        CURLOPT_POSTFIELDS => json_encode($data),
        CURLOPT_HTTPHEADER => array(
            "Accept: application/json",
            "Content-Type: application/json",
        ),
    ));

    $response = curl_exec($curl);
    $err = curl_error($curl);

    curl_close($curl);

    return empty($err) ? $response : json_encode(['status' => false, 'error' => $err]);
}

/**
 * @param  string $url
 * @param  string $path
 * @return void
 */
function _cmpsr_download_file($url, $path): void
{
    $newfilename = $path;

    if ($file = fopen($url, "rb")) {
        $newfile = fopen($newfilename, "wb");

        if ($newfile) {
            while (!feof($file)) {
                fwrite($newfile, fread($file, 1024 * 8), 1024 * 8);
            }
        }
    }

    if ($file) {
        fclose($file);
    }

    if ($newfile ?? false) {
        fclose($newfile);
    }
}

cmpsr_install();
