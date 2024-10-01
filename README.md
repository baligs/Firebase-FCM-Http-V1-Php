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
<h2>Step 3: Generate Access Token</h2>