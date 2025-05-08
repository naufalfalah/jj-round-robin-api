<?php

if (!function_exists('send_discord_msg')) {
    /**
     * Send a message to Discord using cURL.
     *
     * @param string $url
     * @param string $data
     * @return string|false
     */
    function send_discord_msg($url, $data)
    {
        $post_array = [
            'content' => $data,
            'embeds' => null,
            'attachments' => [],
        ];
        $curl = curl_init();

        curl_setopt_array($curl, [
            CURLOPT_URL => $url,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => '',
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 0,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => 'POST',
            CURLOPT_POSTFIELDS => json_encode($post_array),
            CURLOPT_HTTPHEADER => [
                'Content-Type: application/json',
                'Cookie: __dcfduid=8ec71370974011ed9aeb96cee56fe4d4; __sdcfduid=8ec71370974011ed9aeb96cee56fe4d49deabe12bc0fc3d686d23eaa0b49af957ffe68eadec722cff5170d5c750b00ea',
            ],
            CURLOPT_SSL_VERIFYPEER => false,
        ]);

        $response = curl_exec($curl);
        curl_close($curl);

        return $response;
    }
}
