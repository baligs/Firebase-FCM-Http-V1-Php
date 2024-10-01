<h1>Firebase FCM HTTP V1 Integration Using PHP</h1>
<p>This tutorial demonstrates how to send notifications using Firebase Cloud Messaging (FCM) HTTP V1 with PHP. By following this guide, you'll be able to integrate FCM notifications into your PHP applications, leveraging Firebase's powerful messaging service to engage users on mobile or web platforms.</p>

<h3>Prerequisites</h3>
<p>Before starting, make sure you have the following:</p>
<ul>
<li>PHP 7.4 or Above</li>
<li>Firebase project with FCM enabled</li>
<li>Service account (JSON file)</li>
</ul>

<h2>Step 1: Firebase Project Setup</h2>
<ol>
<li>Go to the <a href="https://console.firebase.google.com">Firebase Console</a></li>
<li>Create a new project or use an existing one.</li>
<li>Navigate to Project Settings → Service Accounts.</li>
<li>Click Generate new private key to download the service account JSON file.</li>

</ol>

<h2>Step 2: Install Firebase SDK (Unofficial)</h2>

> You can install the Firebase SDK for PHP by using <a href="https://github.com/kreait/firebase-php"> kreait/firebase-php</a>. This library provides easy integration with Firebase services.
> Run the following command to install it:

```bash
composer require kreait/firebase-php
```

<h2>Step 3: Install PHP HTTP Client </h2>
You will need a client to handle HTTP requests. A popular choice is <a href="https://github.com/guzzle/guzzle">Guzzle</a>,which you can install with the following command:

```bash
composer require guzzlehttp/guzzle
```

<h2>Step 4: Install Google API Client </h2>
For generating access tokens, you will need the <a href="https://github.com/googleapis/google-api-php-client">Google API PHP Client</a>. Refer to the documentation for detailed installation instructions.

```bash
composer require google/apiclient
```

<h2>Step 5: Generate Access Token</h2>
<p>To send notifications via Firebase, you'll first need to generate an access token. Here's a PHP script to do that:</p>

```php
require_once "vendor/autoload.php";

use GuzzleHttp\Client;

$credentialsFilePath = 'PATH_TO_SERVICE_ACCOUNT_JSON_FILE';
$creds = file_get_contents($credentialsFilePath);
$this->firebase_project_id = json_decode($creds)->project_id;
$client = new \Google_Client();
$client->setAuthConfig($credentialsFilePath);
$client->addScope('https://www.googleapis.com/auth/cloud-platform');
$client->addScope('https://www.googleapis.com/auth/firebase.messaging');
$client->refreshTokenWithAssertion();
$token = $client->getAccessToken();
$this->accessToken = $token['access_token'];
```

<h1>Usage Examples</h1>

<h2>Sending to Single Device</h2>

<p>To send a notification to a single device, use the following method:</p>

<b>Note:</b>
<ol>
<li>Avoid using certain reserved keywords (e.g., read_at, sender, id, from, to, gcm) in the data attribute.</li>
<li>Ensure the total payload size (including the FCM token) does not exceed 4096 bytes. The FCM token is approximately 163 bytes.</li>
<li>Nested arrays are not allowed in the data array, but you can send JSON-encoded strings.</li>
</ol>

```php
$userFcmToken = "DEVICE_TOKEN_OF_USER";
$notificationTitle = "TITLE_OF_NOTIFICATION";
$notificationBody = "BODY_OF_NOTIFICATION";
// extra data can be array but not an empty array. remember avoid using Firebase reserved keyword or prefixes of these keywords. 
$extraData = null;

$message = 
[
    'token' => $userFcmToken,
    'notification' => 
        [
            'title' => $notificationTitle,
            'body' => $notificationBody,
        ]
        
];

if(!empty($extraData))
{
    $message['notification']['data']  = $extraData;
}

$client = new Client();
$response = $client->post('https://fcm.googleapis.com/v1/projects/' . $this->firebase_project_id . '/messages:send', [
            'headers' => [
                'Authorization' => 'Bearer ' . $this->accessToken,
                'Content-Type' => 'application/json',
            ],
            'json' => [
                'message' => $message,
            ],
        ]);
```

<h2>Sending to Multiple Device</h2>

> With HTTP V1, notifications to multiple devices are sent using topics. You can learn more about Firebase FCM topics <a>here</a>.
<br>

<b>Important Notes:</b>
<ul>
<li>An app instance can subscribe to a maximum of 2000 topics.</li>
<li>If too many subscription requests are made in a short period, the FCM servers may respond with a 429 RESOURCE_EXHAUSTED error. In such cases, retry using exponential backoff.</li>
</ul>


<h3>Step 1: Subscribe to a Topic or Add FCM Tokens to a Topic</h3>

```php

require_once "vendor/autoload.php";


use Kreait\Firebase\Factory;
$topic = "NAME_OF_TOPIC";
$tokens = ['ARR_OF_TOKENS'];
$factory = (new Factory)->withServiceAccount($this->credentialsFilePath);
$cloudMessaging = $factory->createMessaging();
$cloudMessaging->subscribeToTopic($topic, $tokens);
```


<h3>Step 2: Send a Notification to a Topic</h3>


```php

require_once "vendor/autoload.php";


use Kreait\Firebase\Factory;
$topic = "TOPIC_NAME";
$title = "TITLE_OF_NOTIFICATION";
$body = "BODY_OF_NOTIFICATION";
// extra data can be array but not an empty array. remember avoid using Firebase reserved keyword or prefixes of these keywords.
$extraData = [];

$messageBody = [
            'topic' => $topic,
            'notification' => [
                'title' => $title,
                'body' => $body,
            ],

        ];
        if (!empty($extraData)) {
            $messageBody['data'] = ['payload' => json_encode($extraData)];
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

```



<h1>Sending Notifications Using Laravel (Firebase FCM HTTP V1)</h1>

If you're using Laravel, you can easily integrate Firebase FCM HTTP V1 by using the following class: 
<a href="https://github.com/baligs/Firebase-FCM-Http-V1-Php/blob/main/LaravelFcmHelper.php">LaravelFcmHelper</a>

<p>This helper class simplifies the process of sending FCM notifications in Laravel applications, making it easy to engage with your users through Firebase's reliable notification system.</p>