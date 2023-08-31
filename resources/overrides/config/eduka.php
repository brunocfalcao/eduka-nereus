<?php

/**
 * The Eduka configuration. Brain of all the major workflow, payments,
 * frontend, and backend decisions. Each configuration strongly have an
 * effect on the framework, so ensure you understand it well. Each of the
 * configuration keys will have a detailed explanation of what it works for.
 *
 * Most of the configuration keys have a "negation" configuration. Meaning
 * it's to "block" something from being triggered, since the framework
 * privileges the action to be made, and not to be blocked. So, you can use
 * these keys to then block/filter whatever you need.
 */
return [

    'mail' => [

        /**
         * The eduka mail notifications sender information. Any eduka
         * system notification will be sent using the "from" key information.
         */
        'from' => [
            /**
             * The eduka system admin name. For notifications that are system
             * related (like creating a new domain, new course, etc.).
             */
            'name' => env('EDUKA_FROM_NAME', null),

            /**
             * The eduka system admin email. For system notification as written
             * above.
             */
            'email' => env('EDUKA_FROM_EMAIL', null),
        ],

        /**
         * In case an eduka system notification is sent, this will be the
         * recipient that it will be sent to. Only used for system
         * notifications.
         */
        'to' => [
            'email' => env('EDUKA_TO_EMAIL', null),
        ],
    ],

    'system' => [

        /**
         * Global configuration that is used to allow emails to be sent. This
         * configuration is global, means that ALL emails will not be sent,
         * neither the respective notification will be recorded (in case).
         */
        'stop_notifications' => env('EDUKA_STOP_NOTIFICATIONS', false),

        /**
         * Providers that will be additionally loaded, no matter what
         * site context you are. You can use to load providers you want
         * to test routes, or other things.
         */
        'load_providers' => [
            //\MasteringNova\MasteringNovaServiceProvider::class
        ],

        'backend' => [
            /**
             * The backend url base domain. If a course is not matched, then it
             * will try to match the backend.
             */
            'url' => env('EDUKA_BACKEND_URL', 'brunofalcao.local')
        ]
    ],
];
