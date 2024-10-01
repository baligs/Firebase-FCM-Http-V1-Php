<h1>Firebase FCM HTTP V1 Integration Using PHP</h1>
<p>This tutorial demonstrates how to send notifications using Firebase Cloud Messaging (FCM) HTTP V1 with PHP.</p>

<h3>Prerequisites</h3>

<ul>
<li>PHP 7.4 or Above</li>
<li>Firebase project with FCM enabled</li>
<li>Service account (json file)</li>
</ul>

<h2>Step 1: Firebase Project Setup</h2>
<ol>
<li>Go to the <a href="https://console.firebase.google.com">Firebase Console</a></li>
<li>Create a new project or use an existing one.</li>
<li>Navigate to Project Settings → Service Accounts.</li>
<li>Click Generate new private key to download the service account JSON file.</li>

</ol>

<h2>Step 2: Install Firebase SDK <small>(Un-official)</small> </h2>

> Go to <a href="https://github.com/kreait/firebase-php"> kreait firebase-php </a> And Install it.
> Or You can run following command

```bash
composer require kreait/firebase-php
```

<h2>Step 3: Install PHP HTTP Client </h2>
You will need a client to make HTTP requests. You can use <a href="https://github.com/guzzle/guzzle">Guzzle</a> :

```bash
composer require guzzlehttp/guzzle
```

<h2>Step 4: Generate Access Token</h2>

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

You can send notification to single device using following method:

<b>Remember</b>
<p>First we can't use some words in data attribute, like read_at, sender, id, from, to,  gcm, or any value prefixed by google.</p>
<p>Second request payload size including fcm token  size must not greater be 4096 bytes. FCM token size is 163 Bytes aprox.</p>
<p>Third we can't use nested array in data array</p>

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

>In Http V1 to send notifications to multiple devices. You must use topics.
<br>
> You can read <a>here</a> about Firebase FCM Topics.
<br>
> FCM topic messaging allows you to send a message to multiple devices that have opted in to a particular topic

