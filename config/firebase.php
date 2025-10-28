<?php

return [
    'projects' => [
        'default' => [
            'credentials' => [
                'file' => storage_path('app/firebase/movyx-firebase-adminsdk.json'),
            ],
            'messaging' => [
                'sender_id' => env('FIREBASE_SENDER_ID'),
            ],
        ],
    ],
];
