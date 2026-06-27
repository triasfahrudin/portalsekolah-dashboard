<?php defined('BASEPATH') or exit('No direct script access allowed');




function create_cronjob($data)
{

    $CI = &get_instance();
    $curl = curl_init();

    curl_setopt_array($curl, array(
        CURLOPT_URL => 'https://api.cron-job.org/jobs',
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_ENCODING => '',
        CURLOPT_MAXREDIRS => 10,
        CURLOPT_TIMEOUT => 0,
        CURLOPT_FOLLOWLOCATION => true,
        CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
        CURLOPT_CUSTOMREQUEST => 'PUT',
        CURLOPT_POSTFIELDS => json_encode($data),
        CURLOPT_HTTPHEADER => array(
            'Authorization: Bearer SSWXcy1MTCb+K080+lP7NVaWmguESGa4BDxkOLRet88=',
            'Content-Type: application/json'
        ),
    ));

    $response = curl_exec($curl);

    curl_close($curl);
}

function update_cronjob($jobId, $data)
{
    $CI = &get_instance();

    $curl = curl_init();

    curl_setopt_array($curl, array(
        CURLOPT_URL            => 'https://api.cron-job.org/jobs/' . $jobId,
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_ENCODING       => '',
        CURLOPT_MAXREDIRS      => 10,
        CURLOPT_TIMEOUT        => 0,
        CURLOPT_FOLLOWLOCATION => true,
        CURLOPT_HTTP_VERSION   => CURL_HTTP_VERSION_1_1,
        CURLOPT_CUSTOMREQUEST  => 'PATCH',
        CURLOPT_POSTFIELDS     => json_encode($data),
        CURLOPT_HTTPHEADER     => array(
            'Authorization: Bearer SSWXcy1MTCb+K080+lP7NVaWmguESGa4BDxkOLRet88=',
            'Content-Type: application/json',
        ),
    ));

    curl_exec($curl);
    curl_close($curl);
}

function delete_cronjob($jobId)
{
    $CI = &get_instance();

    $curl = curl_init();

    curl_setopt_array($curl, array(
        CURLOPT_URL            => 'https://api.cron-job.org/jobs/' . $jobId,
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_ENCODING       => '',
        CURLOPT_MAXREDIRS      => 10,
        CURLOPT_TIMEOUT        => 0,
        CURLOPT_FOLLOWLOCATION => true,
        CURLOPT_HTTP_VERSION   => CURL_HTTP_VERSION_1_1,
        CURLOPT_CUSTOMREQUEST  => 'DELETE',
        CURLOPT_HTTPHEADER     => array(
            'Authorization: Bearer SSWXcy1MTCb+K080+lP7NVaWmguESGa4BDxkOLRet88=',
            'Content-Type: application/json',
        ),
    ));

    curl_exec($curl);
    curl_close($curl);
}

function reset_cronjob($url)
{
    $cronjobInfo = cek_cronjob($url);

    if ($cronjobInfo['jobId'] != null) {

        $requestData = [
            'job' => [
                'enabled' => 'true',
            ],
        ];

        update_cronjob($cronjobInfo['jobId'], $requestData);
    } else {
        $requestData['job']['url']           = $url;
        $requestData['job']['saveResponses'] = true;
        $requestData['job']['schedule']      = [
            'timezone'  => 'Asia/Jakarta',
            'expiresAt' => 0,
            'hours'     => [-1],
            'mdays'     => [-1],
            'minutes'   => [-1],
            'months'    => [-1],
            'wdays'     => [-1],
        ];

        create_cronjob($requestData);
    }
}

function cek_cronjob($urlToFind)
{
    $CI = &get_instance();

    $curl = curl_init();

    curl_setopt_array($curl, array(
        CURLOPT_URL            => 'https://api.cron-job.org/jobs',
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_ENCODING       => '',
        CURLOPT_MAXREDIRS      => 10,
        CURLOPT_TIMEOUT        => 0,
        CURLOPT_FOLLOWLOCATION => true,
        CURLOPT_HTTP_VERSION   => CURL_HTTP_VERSION_1_1,
        CURLOPT_CUSTOMREQUEST  => 'GET',
        CURLOPT_HTTPHEADER     => array(
            'Authorization: Bearer ' . $_ENV['CRONJOB_API_KEY'],
            'Content-Type: application/json',
        ),
    ));

    $response = curl_exec($curl);
    $info = curl_getinfo($curl);

    curl_close($curl);

    $jobInfo = array('jobId' => null, 'enabled' => null);

    if ($info['http_code'] == 200) {
        $data = json_decode($response, true);

        foreach ($data['jobs'] as $job) {
            if ($job['url'] === $urlToFind) {
                $jobInfo['jobId']   = $job['jobId'];
                $jobInfo['enabled'] = $job['enabled'];
                break;
            }
        }
    } 
    
    return $jobInfo;
}
