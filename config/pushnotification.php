<?php

/**
 * @see https://github.com/Edujugon/PushNotification
 */

return [
    'gcm' => [
        'priority' => 'normal',
        'dry_run' => false,
        'apiKey' => 'AAAAFcK2xkQ:APA91bFBhnBzrE_kjbvdz8Vs_pMAJXeVw-YNLbsdpojIZ6W0eytLmEtsGAPfiyKgyBb4gBhGgc22E6CRb0kmzNeCx1zIo9xCN-QT5oyrXRpC9E6lO8GbZCE8UyIcfQbpMLywA5SgT2GR',
    ],
    'fcm' => [
        'priority' => 'normal',
        'dry_run' => false,
        'apiKey' => env('PUSH_NOTIFICATION'),
    ],
    'apn' => [
        'certificate' => __DIR__ . '/iosCertificates/apns-dev-cert.pem',
        'passPhrase' => 'secret', //Optional
        'passFile' => __DIR__ . '/iosCertificates/yourKey.pem', //Optional
        'dry_run' => true,
    ],
];
