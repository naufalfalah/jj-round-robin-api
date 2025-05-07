<?php

use Carbon\Carbon;
use Hashids\Hashids;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use Twilio\Rest\Client;

if (!function_exists('get_fulltime')) {

    function get_fulltime($date, $format = 'd, M Y @ h:i a')
    {
        $new_date = new \DateTime($date);
        return $new_date->format($format);
    }
}

if (!function_exists('get_date')) {

    function get_date($date)
    {
        return get_fulltime($date, 'D  d/m/Y');
    }
}
if (!function_exists('containsOnlyNull')) {
    function containsOnlyNull($input)
    {
        return empty(array_filter($input, function ($a) {
            return $a !== null;
        }));
    }
}

if (!function_exists('get_date_month')) {

    function get_date_month($date)
    {
        return get_fulltime($date, 'M Y');
    }
}

if (!function_exists('get_time')) {

    function get_time($date, $format = 'h:i A')
    {
        $new_date = new \DateTime($date);
        return $new_date->format($format);
    }
}

if (!function_exists('get_date_differences')) {
    function get_date_differences($start, $end, $interval = '1 month')
    {
        $start = new \DateTime($start); // Today date
        $end = new \DateTime($end); // Create a datetime object from your Carbon object
        $interval = \DateInterval::createFromDateString($interval); // 1 month interval
        $period = new DatePeriod($start, $interval, $end); // Get a set of date beetween the 2 period

        return $period;
    }
}

if (!function_exists('get_price')) {

    function get_price($price)
    {
        return 'SGD ' . number_format($price, 2);
    }
}

if (!function_exists('safeCount')) {

    function safeCount($array)
    {
        if (is_array($array) || is_object($array)) {
            return count($array);
        } else {
            return 0;
        }
    }
}

if (!function_exists('dummy_image')) {

    function dummy_image($type = null)
    {
        switch ($type) {
            case 'user':
                return asset('front/assets/images/avatars/avatar-13.png');
            default:
                return asset('front/assets/images/products/product_dummy.png');
        }
    }
}

if (!function_exists('check_file')) {

    function check_file($file = null, $type = null)
    {
        if ($file && $file != '' && file_exists($file)) {
            return asset($file);
        } else {
            return dummy_image($type);
        }
    }
}

if (!function_exists('hashids_encode')) {

    function hashids_encode($str)
    {
        $hashids = new Hashids('', 20);
        return $hashids->encode($str);
    }
}

if (!function_exists('hashids_decode')) {

    function hashids_decode($str)
    {
        try {
            $hashids = new Hashids('', 20);
            return $hashids->decode($str)[0];
        } catch (Exception $e) {
            return abort(404);
        }
    }
}

if (!function_exists('send_email')) {
    function send_email($view, $to, $subject = 'Welcome !', $newdata = null, $from_email = null, $from_name = null)
    {
        $from_name = $from_name ?? env('MAIL_FROM_NAME');
        $from_email = $from_email ?? env('MAIL_FROM_ADDRESS');

        $data = [];
        $data['subject'] = $subject;
        $data['to'] = $to;
        $data['from_name'] = $from_name;
        $data['from_email'] = $from_email;
        $data['email_data'] = $newdata;

        try {
            Mail::send('emails.' . $view, $data, function ($message) use ($data) {
                $message->from($data['from_email'], $data['from_name']);
                $message->subject($data['subject']);
                $message->to($data['to']);
            });
            return true;
        } catch (\Exception $ex) {
            return response()->json($ex);
        }
    }
}


if (!function_exists('user_types')) {
    function user_types($index = null)
    {
        $arr = [
            'normal' => ['title' => 'Normal', 'class' => 'blue'],
            'admin' => ['title' => 'Admin', 'class' => 'danger'],
        ];
        if ($index) {
            return $arr[$index] ?? $arr['admin'];
        }
        return $arr;
    }
}

if (!function_exists('download_file')) {
    function download_file($file)
    {
        if (file_exists($file)) {
            header('Content-Description: File Transfer');
            header('Content-Type: application/octet-stream');
            header('Content-Disposition: attachment; filename="' . basename($file) . '"');
            header('Expires: 0');
            header('Cache-Control: must-revalidate');
            header('Pragma: public');
            header('Content-Length: ' . filesize($file));
            flush(); // Flush system output buffer
            readfile($file);
            die();
        }
        abort(404);
    }
}

if (!function_exists('ordinal')) {
    function ordinal($number)
    {
        $ends = ['th', 'st', 'nd', 'rd', 'th', 'th', 'th', 'th', 'th', 'th'];
        if ((($number % 100) >= 11) && (($number % 100) <= 13)) {
            return $number . 'th';
        } else {
            return $number . $ends[$number % 10];
        }

    }

    if (!function_exists('getQuotientAndRemainder')) {
        function getQuotientAndRemainder($divisor, $dividend)
        {
            $quotient = (int) ($divisor / $dividend);
            $remainder = $divisor % $dividend;
            return ['quotient' => $quotient, 'remainder' => $remainder];
        }
    }
}

if (!function_exists('get_states')) {
    function get_states()
    {
        $states = [[
            'name' => 'New South Wales',
            'abbreviation' => '(NSW)',
        ], [
            'name' => 'Victoria',
            'abbreviation' => '(VIC)',
        ], [
            'name' => 'Queensland',
            'abbreviation' => '(QLD)',
        ], [
            'name' => 'Tasmania',
            'abbreviation' => '(TAS)',
        ], [
            'name' => 'South Australia',
            'abbreviation' => '(SA)',
        ], [
            'name' => 'Western Australia',
            'abbreviation' => '(WA)',
        ], [
            'name' => 'Northern Territory',
            'abbreviation' => '(NT)',
        ], [
            'name' => 'Australian Capital Territory',
            'abbreviation' => '(ACT)',
        ]];
        return $states;
    }
}

if (!function_exists('convertToHoursMins')) {
    function convertToHoursMins($time)
    {
        $hours = floor($time / 60);
        $minutes = ($time % 60);
        if ($minutes == 0) {
            $output_format = $hours == 1 ? '%02d hour' : '%02d hours';
            $hoursToMinutes = sprintf($output_format, $hours);
        } elseif ($hours == 0) {
            if ($minutes < 10) {
                $minutes = '0' . $minutes;
            }
            $output_format = $minutes == 1 ? '%02d min' : '%02d mins';
            $hoursToMinutes = sprintf($output_format, $minutes);
        } else {
            $output_format = $hours == 1 ? '%02d hour %02d mins' : '%02d hours %02d mins';
            $hoursToMinutes = sprintf($output_format, $hours, $minutes);
        }

        return $hoursToMinutes;
    }
}

if (!function_exists('ads_check_counter')) {
    function ads_check_counter($item, $match)
    {
        $counter = 0;
        foreach ($item as $key => $value) {
            if ($match == $value) {
                $counter = $counter + 1;
            }
        }
        return $counter;
    }
}

if (!function_exists('uploadSingleFile')) {
    function uploadSingleFile($file, $path = 'uploads/images/', $types = 'png,jpeg,jpg,csv,docs,docx,xls,xlsx,doc', $filesize = '20000', $rule_msgs = [])
    {
        $path = $path . date('Y') . '/';
        if (!file_exists($path)) {
            mkdir($path, 0755, true);
        }
        $rules = ['file' => 'required|mimes:' . $types . '|max:' . $filesize];
        $validator = Validator::make(['file' => $file], $rules, $rule_msgs);
        if ($validator->passes()) {
            $rand = time() . '_' . Str::random(15) . '_';
            $f_name = $rand . $file->getClientOriginalName();
            $filename = $path . $f_name;
            //full size image
            $file->move($path, $f_name);
            return $filename;
        } else {
            return ['error' => $validator->errors()->first('file')];
        }
    }
}

if (!function_exists('fileManagerUploadFile')) {
    function fileManagerUploadFile($file, $path = 'uploads/images/', $types = 'png,jpeg,jpg,svg,csv,doc,docx,xls,xlsx,pdf,webp,zip,mp3,mp4,text/plain', $maxFileSize = 25000)
    {
        $path = $path . date('Y') . '/';
        if (!file_exists($path)) {
            mkdir($path, 0755, true);
        }

        $rules = [
            'file' => 'required|max:' . $maxFileSize,
        ];

        $customMessages = [
            'file.required' => 'The file is required.',
            'file.mimes' => 'Invalid file format. Allowed formats are ' . $types . '.',
            'file.max' => 'File size should be less than or equal to ' . $maxFileSize / 1000 . ' MB.',
        ];

        $validator = Validator::make(['file' => $file], $rules, $customMessages);

        if ($validator->fails()) {
            return ['error' => $validator->errors()->first('file')];
        } else {
            $rand = time() . '_' . Str::random(15) . '_';
            $f_name = $rand . $file->getClientOriginalName();
            $filename = $path . $f_name;

            $file->move($path, $f_name);

            return $filename;
        }
    }
}

if (!function_exists('limit_text')) {
    function limit_text($text, $limit)
    {
        if (str_word_count($text, 0) > $limit) {
            $words = str_word_count($text, 2);
            $pos = array_keys($words);
            $text = substr($text, 0, $pos[$limit]) . '...';
        }
        return $text;
    }
}

if (!function_exists('bank_list')) {
    function bank_list()
    {
        return [
            0 => [
                'name' => 'Al Baraka Bank (Pakistan) Limited',
            ],
            1 => [
                'name' => 'Allied Bank Limited',
            ],
            2 => [
                'name' => 'Askari Bank',
            ],
            3 => [
                'name' => 'Bank Alfalah Limited',
            ],
            4 => [
                'name' => 'Bank Al-Habib Limited',
            ],
            5 => [
                'name' => 'Bank Islami Pakistan Limited',
            ],
            6 => [
                'name' => 'Citi Bank',
            ],
            7 => [
                'name' => 'Deutsche Bank A.G',
            ],
            8 => [
                'name' => 'The Bank of Tokyo-Mitsubishi UFJ',
            ],
            10 => [
                'name' => 'Dubai Islamic Bank Pakistan Limited',
            ],
            11 => [
                'name' => 'Faysal Bank Limited',
            ],
            12 => [
                'name' => 'First Women Bank Limited',
            ],
            13 => [
                'name' => 'Habib Bank Limited',
            ],
            14 => [
                'name' => 'Standard Chartered Bank (Pakistan) Limited',
            ],
            15 => [
                'name' => 'Habib Metropolitan Bank Limited',
            ],
            16 => [
                'name' => 'Industrial and Commercial Bank of China',
            ],
            17 => [
                'name' => 'JS Bank Limited',
            ],
            18 => [
                'name' => 'MCB Bank Limited',
            ],
            19 => [
                'name' => 'MCB Islamic Bank Limited',
            ],
            20 => [
                'name' => 'Meezan Bank Limited',
            ],
            21 => [
                'name' => 'National Bank of Pakistan',
            ],
            22 => [
                'name' => 'Bank of Punjab',
            ],
            23 => [
                'name' => 'Sindh Bank',
            ],
            24 => [
                'name' => 'Bank of Khyber',
            ],
            25 => [
                'name' => 'Soneri Bank',
            ],
            26 => [
                'name' => 'Summit Bank',
            ],
        ];
    }
}

if (!function_exists('csvToArray')) {
    function csvToArray($filename = '', $delimiter = ',')
    {
        if (!file_exists($filename) || !is_readable($filename)) {
            return false;
        }

        $header = null;
        $data = [];
        if (($handle = fopen($filename, 'r')) !== false) {
            while (($row = fgetcsv($handle, 1000, $delimiter)) !== false) {
                if (!$header) {
                    $header = $row;
                } else {
                    $data[] = array_combine($header, $row);
                }

            }
            fclose($handle);
        }

        return $data;
    }
}

if (!function_exists('calculateTimeDifference')) {
    function calculateTimeDifference($startDatetime, $endDatetime)
    {
        // Convert the start and end datetimes to Unix timestamps
        $startTimestamp = strtotime($startDatetime);
        $endTimestamp = strtotime($endDatetime);

        // Calculate the time difference in seconds
        $timeDifference = $endTimestamp - $startTimestamp;

        // Convert the time difference from seconds to hours and minutes
        $hours = floor($timeDifference / 3600);
        $minutes = round(($timeDifference % 3600) / 60);

        // Return the time difference as a formatted string
        return sprintf('%02d:%02d', $hours, $minutes);
    }

    // function calculateTimeDifference($startDatetime, $endDatetime)
    // {
    //     // Convert the start and end datetimes to Unix timestamps
    //     $startTimestamp = strtotime($startDatetime);
    //     $endTimestamp = strtotime($endDatetime);

    //     // Calculate the time difference in seconds
    //     $timeDifference = $endTimestamp - $startTimestamp;

    //     // Convert the time difference from seconds to minutes
    //     $timeDifferenceInMinutes = round($timeDifference / 60);

    //     // Return the time difference in minutes
    //     return $timeDifferenceInMinutes;
    // }

}

if (!function_exists('getTimeDifference')) {
    function getTimeDifference($startDatetime, $endDatetime)
    {
        $startDatetime = Carbon::parse($startDatetime);
        $endDatetime = Carbon::parse($endDatetime);

        $timeDiff = $endDatetime->diffInMinutes($startDatetime);

        return $timeDiff;
    }
}

if (!function_exists('get_client_ip')) {
    function get_client_ip()
    {
        // $ipaddress = '';
        // if (isset($_SERVER['HTTP_CLIENT_IP'])) {
        //     $ipaddress = $_SERVER['HTTP_CLIENT_IP'];
        // } elseif(isset($_SERVER['HTTP_X_FORWARDED_FOR'])) {
        //     $ipaddress = $_SERVER['HTTP_X_FORWARDED_FOR'];
        // } elseif(isset($_SERVER['HTTP_X_FORWARDED'])) {
        //     $ipaddress = $_SERVER['HTTP_X_FORWARDED'];
        // } elseif(isset($_SERVER['HTTP_FORWARDED_FOR'])) {
        //     $ipaddress = $_SERVER['HTTP_FORWARDED_FOR'];
        // } elseif(isset($_SERVER['HTTP_FORWARDED'])) {
        //     $ipaddress = $_SERVER['HTTP_FORWARDED'];
        // } elseif(isset($_SERVER['REMOTE_ADDR'])) {
        //     $ipaddress = $_SERVER['REMOTE_ADDR'];
        // } else {
        //     $ipaddress = 'UNKNOWN';
        // }
        // return $ipaddress;

        // Get real visitor IP behind CloudFlare network
        if (isset($_SERVER['HTTP_CF_CONNECTING_IP'])) {
            $_SERVER['REMOTE_ADDR'] = $_SERVER['HTTP_CF_CONNECTING_IP'];
            $_SERVER['HTTP_CLIENT_IP'] = $_SERVER['HTTP_CF_CONNECTING_IP'];
        }
        $client = @$_SERVER['HTTP_CLIENT_IP'];
        $forward = @$_SERVER['HTTP_X_FORWARDED_FOR'];
        $remote = $_SERVER['REMOTE_ADDR'];

        if (filter_var($client, FILTER_VALIDATE_IP)) {
            $ip = $client;
        } elseif (filter_var($forward, FILTER_VALIDATE_IP)) {
            $ip = $forward;
        } else {
            $ip = $remote;
        }

        return $ip;
    }
}

if (!function_exists('json_validator')) {
    function json_validator($data)
    {
        if (!empty($data)) {
            return is_string($data) &&
            is_array(json_decode($data, true)) ? true : false;
        }
        return false;
    }
}

if (!function_exists('priority')) {
    function priority($p = null)
    {
        $data = [
            [
                'class' => 'danger',
                'text' => 'Urgent'
            ],
            [
                'class' => 'warning',
                'text' => 'High',
            ],
            [
                'class' => 'secondary',
                'text' => 'Normal',
            ],
        ];

        return $data[$p] ?? $data;
    }
}

if (!function_exists('all_priority')) {
    function all_priority()
    {
        $data = ['Urgent','High', 'Normal'];

        return $data;
    }
}

if (!function_exists('updateEnv')) {
    function updateEnv($key, $value)
    {
        $envFile = base_path('.env');

        // Check if the .env file exists
        if (!file_exists($envFile)) {
            return false;
        }

        // Read the .env file into an array
        $envData = file($envFile);

        // Loop through each line in the array
        foreach ($envData as $index => &$line) {
            // Check if the line contains the key we want to update
            if (strpos($line, $key) !== false) {
                // Update the value
                $line = "$key=$value\n";
                break;
            }
        }

        // Implode the array into a string and write it back to the .env file
        $newEnvData = implode('', $envData);
        file_put_contents($envFile, $newEnvData);

        return true;
    }
}

if (!function_exists('send_whatsapp_msg')) {
    function send_whatsapp_msg($msg, $phone_number, $tw_id = null, $tw_token = null, $tw_number = null)
    {

        Log::info("Whatsapp Message Send {$phone_number}");

        // $tw_id = empty($tw_id) ? config('services.twilio.account_sid') : $tw_id;
        // $tw_token = empty($tw_token) ? config('services.twilio.auth_token') : $tw_token;
        // $tw_number = empty($tw_number) ? config('services.twilio.phone_number') : $tw_number;

        // $client = new Client($tw_id,$tw_token);

        // $message = $client->messages->create(
        //     $phone_number, // Text this number
        //     [
        //       'from' => $tw_number, // From a valid Twilio number
        //       'body' =>  $msg
        //     ]
        // );

        // return $message->sid;
    }
}

if (!function_exists('send_push_notification')) {
    function send_push_notification($title, $msg, $receiver_id, $lead_id, $type = 'user')
    {
        $url = 'https://fcm.googleapis.com/fcm/send';
        $FcmToken = App\Models\UserDeviceToken::whereNotNull('device_token')->where('user_id', $receiver_id)->where('user_type', 'user')->pluck('device_token')->all();

        $serverKey = config('services.firebase.service_key');
        $data = [
            'registration_ids' => $FcmToken,
            'data' => [
                'notify_route' => route('user.notifications'),
                'title' => $title,
                'body' => $msg,
                'icon' => asset('front/assets/images/favicon.png'),
                'image' => asset('front/assets/images/logo.png'),
                // 'click_action' => route('admin.tasks_managment.task_detail',hashids_encode($task_id))
            ]
        ];
        $encodedData = json_encode($data);
        $headers = [
            'Authorization:key=' . $serverKey,
            'Content-Type: application/json',
        ];
        $ch = curl_init();
        curl_setopt_array($ch, [
            CURLOPT_URL => $url,
            CURLOPT_POST => true,
            CURLOPT_HTTPHEADER => $headers,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_SSL_VERIFYHOST => 0,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            // Disabling SSL Certificate support temporarly
            CURLOPT_SSL_VERIFYPEER => false,
            CURLOPT_POSTFIELDS => $encodedData
        ]);
        // Execute post
        $result = curl_exec($ch);
        if ($result === false) {
            die('Curl failed: ' . curl_error($ch));
        }
        // Close connection
        curl_close($ch);
        // FCM response
        // dd($result);
        // return $result;

        App\Models\Notification::create([
            'user_id' => $receiver_id,
            'title' => $title,
            'body' => $msg,
            'user_type' => $type,
            'lead_url' => route('user.leads-management.client_details', $lead_id)
        ]);
    }
}

if (!function_exists('send_admin_push_notification_to_user')) {
    function send_admin_push_notification_to_user($title, $msg, $users, $lead_id = null, $type = 'user')
    {
        $url = 'https://fcm.googleapis.com/fcm/send';

        if (is_array($users)) {
            $FcmToken = App\Models\UserDeviceToken::whereIn('user_id', $users)
                ->whereNotNull('device_token')
                ->where('user_type', 'user')
                ->pluck('device_token')
                ->all();
        } else {
            $FcmToken = App\Models\UserDeviceToken::where('user_id', $users)
                ->whereNotNull('device_token')
                ->where('user_type', 'user')
                ->pluck('device_token')
                ->all();
        }

        $serverKey = config('services.firebase.service_key');
        $data = [
            'registration_ids' => $FcmToken,
            'data' => [
                'notify_route' => route('user.notifications'),
                'title' => $title,
                'body' => $msg,
                'icon' => asset('front/assets/images/favicon.png'),
                'image' => asset('front/assets/images/logo.png'),
            ]
        ];

        if ($lead_id !== null) {
            $data['data']['lead_url'] = route('user.leads-management.client_details', $lead_id);
        }

        $encodedData = json_encode($data);
        $headers = [
            'Authorization:key=' . $serverKey,
            'Content-Type: application/json',
        ];

        $ch = curl_init();
        curl_setopt_array($ch, [
            CURLOPT_URL => $url,
            CURLOPT_POST => true,
            CURLOPT_HTTPHEADER => $headers,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_SSL_VERIFYHOST => 0,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_SSL_VERIFYPEER => false,
            CURLOPT_POSTFIELDS => $encodedData
        ]);

        $result = curl_exec($ch);

        if ($result === false) {
            die('Curl failed: ' . curl_error($ch));
        }

        curl_close($ch);

        foreach ($users as $user) {
            App\Models\Notification::create([
                'user_id' => $user,
                'title' => $title,
                'body' => $msg,
                'user_type' => $type,
                'lead_url' => ($lead_id !== null) ? route('user.leads-management.client_details', $lead_id) : null
            ]);
        }
    }
}

if (!function_exists('send_push_notification_to_admin')) {
    function send_push_notification_to_admin($title, $msg, $admins, $client_id, $type = 'admin')
    {
        $url = 'https://fcm.googleapis.com/fcm/send';
        $FcmToken = App\Models\UserDeviceToken::whereNotNull('device_token')
        ->whereIn('user_id', $admins)
        ->where('user_type', 'admin')
        ->pluck('device_token')
        ->all();

        $serverKey = config('services.firebase.service_key');
        $data = [
            'registration_ids' => $FcmToken,
            'data' => [
                'notify_route' => route('admin.notifications'),
                'title' => $title,
                'body' => $msg,
                'icon' => asset('front/assets/images/favicon.png'),
                'image' => asset('front/assets/images/logo.png'),
            ]
        ];

        if ($client_id !== null) {
            // $data['data']['client_url'] = route('admin.client-management.view',$client_id);
            $data['data']['client_url'] = '';
        }

        $encodedData = json_encode($data);
        $headers = [
            'Authorization:key=' . $serverKey,
            'Content-Type: application/json',
        ];

        $ch = curl_init();
        curl_setopt_array($ch, [
            CURLOPT_URL => $url,
            CURLOPT_POST => true,
            CURLOPT_HTTPHEADER => $headers,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_SSL_VERIFYHOST => 0,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_SSL_VERIFYPEER => false,
            CURLOPT_POSTFIELDS => $encodedData
        ]);

        $result = curl_exec($ch);

        if ($result === false) {
            die('Curl failed: ' . curl_error($ch));
        }

        curl_close($ch);

        foreach ($admins as $admin) {
            App\Models\Notification::create([
                'user_id' => $admin,
                'title' => $title,
                'body' => $msg,
                'user_type' => $type,
                // 'lead_url' => route('admin.client-management.view',$client_id)
            ]);
        }
    }

}

if (!function_exists('arrayHasEmptyValue')) {
    function arrayHasEmptyValue(array $array)
    {
        foreach ($array as $value) {
            if (trim($value) === '') {
                return true;
            }
        }
        return false;
    }
}

if (!function_exists('pay_topup_badge')) {
    function pay_topup_badge($status)
    {
        $color = '';
        if ($status == 'processing') {
            $color = 'secondary';
        } elseif ($status == 'completed') {
            $color = 'success';
        } elseif ($status == 'canceled') {
            $color = 'dark';
        } elseif ($status == 'declined') {
            $color = 'danger';
        }
        return $color;
    }
}

if (!function_exists('pay_topup_badge_admin')) {
    function pay_topup_badge_admin($status)
    {
        $color = '';
        if ($status == 'pending') {
            $color = 'secondary';
        } elseif ($status == 'approve') {
            $color = 'success';
        } elseif ($status == 'canceled') {
            $color = 'dark';
        } elseif ($status == 'rejected') {
            $color = 'danger';
        }
        return $color;
    }
}
if (!function_exists('pay_topup_text_admin')) {
    function pay_topup_text_admin($status)
    {
        $text = '';
        if ($status == 'pending') {
            $text = 'Processing';
        } elseif ($status == 'approve') {
            $text = 'Completed';
        } elseif ($status == 'canceled') {
            $text = 'Canceled';
        } elseif ($status == 'rejected') {
            $text = 'Declined';
        }
        return $text;
    }
}



if (!function_exists('ads_status_color')) {
    function ads_status_color($status)
    {
        $color = '';
        if ($status == 'pending') {
            $color = 'warning';
        } elseif ($status == 'created_and_approved') {
            $color = 'info';
        } elseif ($status == 'running' || $status == 'complete') {
            $color = 'success';
        } elseif ($status == 'inactive' || $status == 'pause' || $status == 'close') {
            $color = 'danger';
        } elseif ($status == 'reject') {
            $color = 'dark';
        } else {
            $color = 'info';
        }
        return $color;
    }
}

if (!function_exists('ads_status_text')) {
    function ads_status_text($status)
    {
        $text = '';
        if ($status == 'pending') {
            $text = 'Pending Creation';
        } elseif ($status == 'created_and_approved') {
            $text = 'Created and Approved';
        } elseif ($status == 'running') {
            $text = 'Live';
        } elseif ($status == 'complete') {
            $text = 'Complete';
        } elseif ($status == 'Invactive') {
            $text = 'Inactive';
        } elseif ($status == 'pause') {
            $text = 'Out of Funds';
        } elseif ($status == 'close') {
            $text = 'Closed';
        } elseif ($status == 'reject') {
            $text = 'Stopped';
        } else {
            $text = $status;
        }
        return $text;
    }
}

if (!function_exists('admin_lead_status')) {
    function admin_lead_status($status)
    {
        $text = '';
        if ($status == 'contacted') {
            $text = 'Contacted';
        } elseif ($status == 'appointment_set') {
            $text = 'Appointment Set';
        } elseif ($status == 'burst') {
            $text = 'Burst';
        } elseif ($status == 'follow_up') {
            $text = 'follow_up';
        } elseif ($status == 'call_back') {
            $text = 'call_back';
        } else {
            $text = '';
        }
        return $text;
    }
}

if (!function_exists('ads_type_text')) {
    function ads_type_text($types)
    {
        $texts = [];

        foreach ($types as $type) {
            switch ($type) {
                case '3in1_valuation':
                    $texts[] = '3 in 1 Valuation';
                    break;
                case 'hbd_valuation':
                    $texts[] = 'HBD Valuation';
                    break;
                case 'condo_valuation':
                    $texts[] = 'Condo Valuation';
                    break;
                case 'landed_valuation':
                    $texts[] = 'Landed Valuation';
                    break;
                case 'rental_valuation':
                    $texts[] = 'Rental Valuation';
                    break;
                case 'post_launch_generic':
                    $texts[] = 'Post Launch Generic';
                    break;
                case 'executive_launch_generic':
                    $texts[] = 'Executive Launch Generic';
                    break;
            }
        }

        return implode(',', $texts);
    }
}


if (!function_exists('status_active_inactive_color')) {
    function status_active_inactive_color($status)
    {
        $color = '';
        if ($status == 'active') {
            $color = 'success';
        } else {
            $color = 'danger';
        }
        return $color;
    }
}

if (!function_exists('status_active_inactive_text')) {
    function status_active_inactive_text($status)
    {
        $text = '';
        if ($status == 'active') {
            $text = 'Active';
        } else {
            $text = 'In Active';
        }
        return $text;
    }
}

if (!function_exists('send_discord_msg')) {
    function send_discord_msg($url, $data)
    {
        $post_array = [
            'content' => $data,
            'embeds' => null,
            'attachments' => []
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
                'Cookie: __dcfduid=8ec71370974011ed9aeb96cee56fe4d4; __sdcfduid=8ec71370974011ed9aeb96cee56fe4d49deabe12bc0fc3d686d23eaa0b49af957ffe68eadec722cff5170d5c750b00ea'
            ],
        ]);

        $response = curl_exec($curl);

        curl_close($curl);
    }
}

if (!function_exists('send_wp_message')) {
    function send_wp_message($client_number, $message)
    {
        $curl = curl_init();
        $api_key = config('app.wp_api_key');
        curl_setopt_array($curl, [
            CURLOPT_URL => 'https://api.p.2chat.io/open/whatsapp/send-message',
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => '',
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 0,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => 'POST',
            CURLOPT_POSTFIELDS => json_encode([
                'to_number' => $client_number,
                'from_number' => '+6589469107',
                'text' => $message
            ]),
            CURLOPT_HTTPHEADER => [
                'Content-Type: application/json',
                'X-User-API-Key: ' . $api_key
            ],
            CURLOPT_SSL_VERIFYPEER => false
        ]);

        $response = curl_exec($curl);

        if (curl_errno($curl)) {
            echo 'Curl error: ' . curl_error($curl);
        }

        curl_close($curl);
        return $response;
    }
}

if (!function_exists('generateDateRange')) {
    function generateDateRange($start_date, $end_date, $format)
    {
        // Convert start and end dates to timestamps
        $start_timestamp = strtotime($start_date);
        $end_timestamp = strtotime($end_date);

        // Initialize empty array to store dates
        $date_range = [];

        // Loop through each date and add to array
        $current_date = $start_timestamp;
        while ($current_date <= $end_timestamp) {
            $date_range[] = date($format, $current_date);
            $current_date = strtotime('+1 day', $current_date); // Increment current date by 1 day
        }

        return $date_range;
    }
}

if (!function_exists('format_number')) {
    function format_number($number)
    {
        $suffixes = ['', 'k', 'M', 'B', 'T'];
        $suffix_index = floor(log10(abs($number)) / 3);
        $formatted_number = number_format($number / pow(1000, $suffix_index), 2) . $suffixes[$suffix_index];
        return $formatted_number;
    }

}

if (!function_exists('number_with_suffixes')) {
    function number_with_suffixes($number)
    {
        $suffixes = ['', 'k', 'M', 'B', 'T'];
        $suffix_index = floor(log10(abs($number)) / 3);
        $formatted_number = number_format($number / pow(1000, $suffix_index), 2) . $suffixes[$suffix_index];
        return $formatted_number;
    }
}


if (!function_exists('snake_to_sentence_case')) {
    function snake_to_sentence_case($string)
    {
        $title = str_replace('_', ' ', $string);
        
        return ucwords(strtolower($title));
    }
}
