<?php

use GuzzleHttp\Client;
use Kreait\Firebase\Factory;

class LaravelFcmHelper
{

    private string $accessToken, $credentialsFilePath;

    public function __construct()
    {
        $this->credentialsFilePath = 'JSON_FILE_PATH';
        $creds = file_get_contents($this->credentialsFilePath);
        $this->firebase_project_id = json_decode($creds)->project_id;
        $client = new \Google_Client();
        $client->setAuthConfig($this->credentialsFilePath);
        $client->addScope('https://www.googleapis.com/auth/cloud-platform');
        $client->addScope('https://www.googleapis.com/auth/firebase.messaging');
        $client->refreshTokenWithAssertion();
        $token = $client->getAccessToken();
        $this->accessToken = $token['access_token'];
    }

    public function sendNotificationToSingle($registrationToken, $title, $body, $data = [])
    {

        $messageBody = [
            'token' => $registrationToken,
            'notification' => [
                'title' => $title,
                'body' => $body,
            ],

        ];
        if (!empty($data)) {
            $messageBody['data'] = ['payload' => json_encode($data)];
        }
        $client = new Client();
        $response = $client->post('https://fcm.googleapis.com/v1/projects/' . $this->firebase_project_id . '/messages:send', [
            'headers' => [
                'Authorization' => 'Bearer ' . $this->accessToken,
                'Content-Type' => 'application/json',
            ],
            'json' => [
                'message' => $messageBody,
            ],
        ]);

        return json_decode($response->getBody()->getContents());
//  old
//        $url = 'https://fcm.googleapis.com/fcm/send';
//
//        $headers = [
//            'Authorization: key=' . $this->apiKey,
//            'Content-Type: application/json',
//        ];
//
//        $message = [
//            'to' => $registrationToken,
//            'notification' => [
//                'title' => $title,
//                'body' => $body,
//            ],
//            'data' => $data,
//        ];
//
//        return $this->sendRequest($url, $headers, json_encode($message));
    }

    public function sendNotificationToTopic($topic, $title, $body, $data = [])
    {

        $messageBody = [
            'topic' => $topic,
            'notification' => [
                'title' => $title,
                'body' => $body,
            ],

        ];
        if (!empty($data)) {
            $messageBody['data'] = ['payload' => json_encode($data)];
        }
        $client = new Client();
        $response = $client->post('https://fcm.googleapis.com/v1/projects/' . $this->firebase_project_id . '/messages:send', [
            'headers' => [
                'Authorization' => 'Bearer ' . $this->accessToken,
                'Content-Type' => 'application/json',
            ],
            'json' => [
                'message' => $messageBody,
            ],
        ]);

        return json_decode($response->getBody()->getContents());
    }

    private function subscribeToTopic($tokens, $topic)
    {
        $factory = (new Factory)
            ->withServiceAccount($this->credentialsFilePath);

        $cloudMessaging = $factory->createMessaging();
        return $cloudMessaging->subscribeToTopic($topic, $tokens);
    }

    public function unsubscribeFromTopic($tokens, $topic, $cloudMessaging = null)
    {
        if (empty($cloudMessaging)) {
            $factory = (new Factory)
                ->withServiceAccount($this->credentialsFilePath);

            $cloudMessaging = $factory->createMessaging();
        }

        return $cloudMessaging->unsubscribeFromTopic($topic, $tokens);
    }

    public function sendNotificationsToMultiple(array $registrationTokens, $title, $body, $data = [], $chunkSize = 900, $tmpTopic = null)
    {
        if (empty($tmpTopic)) {
            $tmpTopic = "TMP" . time();
        }
        if (count($registrationTokens) > $chunkSize) {
            foreach (array_chunk($registrationTokens, $chunkSize) as $chunk) {
                $this->subscribeToTopic($chunk, $tmpTopic);
            }
        } else {
            $this->subscribeToTopic($registrationTokens, $tmpTopic);
        }

        $res = $this->sendNotificationToTopic($tmpTopic, $title, $body, $data);

        // make a job to un-subscribe tokens from topic

        return $res;

    }

}

