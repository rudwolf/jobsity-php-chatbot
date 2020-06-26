<?php

namespace App\Library;

class AmdorenApiLibrary {
    public static function convertCurrency($currency1, $currency2, $amount = 1) {
        $config = config('services.amdoren');
        $url = $config['url'];
        $key = $config['key'];

        $searchReplaceArray = array(
            '%CURRENCY1%' => $currency1,
            '%CURRENCY2%' => $currency2,
            '%API_KEY%' => $key,
            '%AMOUNT%' => $amount,
        );

        $curl_url = str_replace(
            array_keys($searchReplaceArray),
            array_values($searchReplaceArray),
            $url
        );

        $ch = curl_init($curl_url);

        curl_setopt($ch, CURLOPT_URL, $curl_url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 15);
        curl_setopt($ch, CURLOPT_TIMEOUT, 30);

        $json_string = curl_exec($ch);
        $parsed_json = json_decode($json_string);

        $data['error'] = $parsed_json->error;
        $data['error_message'] = $parsed_json->error_message;
        $data['amount'] = $parsed_json->amount;
        return $data;
    }
}