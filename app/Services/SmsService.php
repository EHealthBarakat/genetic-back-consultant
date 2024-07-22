<?php

namespace App\Services;

class SmsService
{
    protected $api_Key;
    public function __construct()
    {
        $this->api_Key = config('sms.' . self::name() . '.api_key');
    }

    public static function name()
    {
        return 'KAVENEGAR';
    }

    public function sendWithPattern(string $pattern_code, string $recipient, array $pattern_values): array
    {
        $path = 'https://api.kavenegar.com/v1/' . $this->api_Key . '/verify/lookup.json';

        $params = [
            "receptor" => $recipient,
            "template" => $pattern_code,
        ];
        $tokens = ["token", "token2", "token3", "token10", "token20"];
        $counter = 0;

        array_map(function ($v1, $v2) use (&$params, &$counter) {
            $params[!is_null($v1) ? $v1 : "" . $counter++] = !is_null($v2) ? $v2 : "";
        }, $tokens, $pattern_values);

        return [$response=$this->runCurl($path, $params)];

//  $response = Http::get('https://api.kavenegar.com/v1/' . $api_Key . '/verify/lookup.json?receptor=' . $username . '&token=' . $user_verify->otp_code . '&template=' . $pattern_code);
//        if ($response[0]->status == 5) {
//
//            activity()->withProperties([
//                'to' => $recipient,
//                'pattern' => $pattern_code,
//                'pattern_values' => $pattern_values,
//                'ref_id' => $response
//            ])->byAnonymous()->log("sms-kavenegar");
//
//            return [
//                'reference_number' => $response[0]->messageid,
//                'gateway' => self::name()
//            ];
//        }

    }


    protected function runCurl($url, $data = null)
    {
        $headers = array(
            'Accept: application/json',
            'Content-Type: application/x-www-form-urlencoded',
            'charset: utf-8'
        );
        $fields_string = "";
        if (!is_null($data)) {
            $fields_string = http_build_query($data);
        }
        $handle = curl_init();
        curl_setopt($handle, CURLOPT_URL, $url);
        curl_setopt($handle, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($handle, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($handle, CURLOPT_SSL_VERIFYHOST, false);
        curl_setopt($handle, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($handle, CURLOPT_POSTFIELDS, $fields_string);

        $response     = curl_exec($handle);

        return  $json_response = json_decode($response);

    }

}
