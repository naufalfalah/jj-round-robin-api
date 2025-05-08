<?php

namespace App\Services;

use App\Models\Notification as ModelsNotification;
use App\Models\UserDeviceToken;
use Kreait\Firebase\Messaging\CloudMessage;
use Kreait\Firebase\Messaging\Notification;

class FirebaseService
{
    protected $messaging;

    public function __construct()
    {
        $this->messaging = config('services.firebase.messaging');
    }

    public function sendNotification($deviceToken, $title, $body, $data = [])
    {
        $notification = Notification::create($title, $body);

        $message = CloudMessage::withTarget('token', $deviceToken)
            ->withNotification($notification)
            ->withData($data);

        return $this->messaging->send($message);
    }

    public function send_push_notification($title, $msg, $receiver_id, $lead_id, $type = 'user')
    {
        $url = 'https://fcm.googleapis.com/fcm/send';
        $FcmToken = UserDeviceToken::whereNotNull('device_token')
            ->where('user_id', $receiver_id)
            ->where('user_type', 'user')
            ->pluck('device_token')
            ->all();

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
            ],
        ];
        $encodedData = json_encode($data);
        $headers = [
            'Authorization:key='.$serverKey,
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
            CURLOPT_POSTFIELDS => $encodedData,
        ]);
        // Execute post
        $result = curl_exec($ch);
        if ($result === false) {
            exit('Curl failed: '.curl_error($ch));
        }
        // Close connection
        curl_close($ch);

        ModelsNotification::create([
            'user_id' => $receiver_id,
            'title' => $title,
            'body' => $msg,
            'user_type' => $type,
            'lead_url' => route('user.leads-management.client_details', hashids_encode($lead_id)),
            'lead_id' => $lead_id ?? null,
        ]);

        return true;
    }

    public function send_admin_push_notification_to_user($title, $msg, $users, $lead_id = null, $type = 'user')
    {
        $url = 'https://fcm.googleapis.com/fcm/send';

        if (is_array($users)) {
            $FcmToken = UserDeviceToken::whereIn('user_id', $users)
                ->whereNotNull('device_token')
                ->where('user_type', 'user')
                ->pluck('device_token')
                ->all();
        } else {
            $FcmToken = UserDeviceToken::where('user_id', $users)
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
            ],
        ];

        if ($lead_id !== null) {
            $data['data']['lead_url'] = route('user.leads-management.client_details', $lead_id);
        }

        $encodedData = json_encode($data);
        $headers = [
            'Authorization:key='.$serverKey,
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
            CURLOPT_POSTFIELDS => $encodedData,
        ]);

        $result = curl_exec($ch);

        if ($result === false) {
            exit('Curl failed: '.curl_error($ch));
        }

        curl_close($ch);

        foreach ($users as $user) {
            ModelsNotification::create([
                'user_id' => $user,
                'title' => $title,
                'body' => $msg,
                'user_type' => $type,
                'lead_url' => ($lead_id !== null) ? route('user.leads-management.client_details', hashids_encode($lead_id)) : null,
                'lead_id' => $lead_id ?? null,
            ]);
        }
    }

    public function send_push_notification_to_admin($title, $msg, $admins, $client_id, $type = 'admin')
    {
        $url = 'https://fcm.googleapis.com/fcm/send';
        $FcmToken = UserDeviceToken::whereNotNull('device_token')
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
            ],
        ];

        if ($client_id !== null) {
            // $data['data']['client_url'] = route('admin.client-management.view',$client_id);
            $data['data']['client_url'] = '';
        }

        $encodedData = json_encode($data);
        $headers = [
            'Authorization:key='.$serverKey,
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
            CURLOPT_POSTFIELDS => $encodedData,
        ]);

        $result = curl_exec($ch);

        if ($result === false) {
            exit('Curl failed: '.curl_error($ch));
        }

        curl_close($ch);

        foreach ($admins as $admin) {
            ModelsNotification::create([
                'user_id' => $admin,
                'title' => $title,
                'body' => $msg,
                'user_type' => $type,
                // 'lead_url' => route('admin.client-management.view',$client_id)
            ]);
        }
    }
}
