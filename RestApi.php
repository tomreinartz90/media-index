<?php

/**
 * Created by PhpStorm.
 * User: t.reinartz
 * Date: 29-11-2017
 * Time: 13:19
 */
class RestApi
{
    private function CallAPI($method, $url, $data = false)
    {
        $curl = curl_init();


        switch ($method) {
            case "POST":
            case "PUT":
                if ($data) {
                    curl_setopt($curl, CURLOPT_HTTPHEADER, array('Content-Type: application/json', 'Content-Length: ' . strlen($data)));
                    curl_setopt($curl, CURLOPT_POSTFIELDS, $data);
                }
                curl_setopt($curl, CURLOPT_CUSTOMREQUEST, $method);
                curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
                break;
            default:
                break;
        }

        curl_setopt($curl, CURLOPT_URL, $url);
        curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);

        $result = curl_exec($curl);


        if ($result === false) {
            echo("==================================\n");
            echo 'Curl error: ' . curl_error($curl) . "\n";
            echo 'Curl url: ' . $url . "\n";
            echo($url . "\n");
            echo("==================================\n");
        }

        curl_close($curl);
        return $result;
    }

    function get($url)
    {
        return $this->CallAPI('GET', $url);
    }

    function put($url, $data)
    {
        return $this->CallAPI('PUT', $url, $data);
    }

    function post($url, $data)
    {
        return $this->CallAPI('POST', $url, $data);
    }
}