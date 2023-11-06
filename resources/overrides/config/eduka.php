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
         * If a notification needs to be send, and there is no course admin
         * email contextualized, then it will fallback to this email.
         */
        'from' => [
            'name' => env('EDUKA_FALLBACK_NAME', null),
            'email' => env('EDUKA_FALLBACK_EMAIL', null),
        ],

        /**
         * In case an eduka system notification is sent, this will be the
         * recipient that it will be sent to. Only used for system
         * notifications.
         */
        'to' => [
            'email' => env('EDUKA_ADMIN_TO', null),
        ],
    ],

    /**
     * Global configuration that is used to allow emails to be sent. This
     * configuration is global, means that ALL emails will not be sent,
     * neither the respective notification will be recorded (in case).
     */
    'stop_notifications' => env('EDUKA_STOP_NOTIFICATIONS', true),

    /**
     * Providers that will be additionally loaded, no matter what
     * site context you are. You can use to load providers you want
     * to test routes, or other things.
     */
    'load_providers' => [
        //\MasteringNova\MasteringNovaServiceProvider::class
    ],

    /**
     * All the courses that are loaded into eduka, even if it's not rendered
     * at the moment by the visitor, need to be listed here. This way eduka
     * can perform activities like migrate, migrate:fresh, vendor publish,
     * all for each of the courses that are mentioned here. This doesn't
     * invalidate that the courses table needs still to be populated.
     */
    'courses' => [
        [
            // E.g.: brunocfalcao/course-mastering-nova
            'package-name' => '',

            // E.g.: MasteringNova\Database\Seeders\MasteringNovaCourseSeeder
            'seeder-class' => '',

            // E.g.: MasteringNova\MasteringNovaServiceProvider
            'provider-class' => '',
        ],
        // ...
    ],

    'backend' => [
        /**
         * The backend url base domain. If a course is not matched, then it
         * will try to match the respective default backend URL here.
         */
        'url' => env('EDUKA_BACKEND_URL', 'brunofalcao.local'),
    ],

    /**
     * The assets transfer will run on each composer update to copy all
     * the assets from resources/assets from each course landing page, and
     * from the backend, to the main laravel resources path, grouped by
     * vendor name. This is because the Vite::asset() on each course package
     * doesn't locate assets inside the custom course package, but only
     * on the main resource() folder.
     *
     * Each entry has the key as the vendor name so we can search it on the
     * vendors/brunocfalcao directory. Inside it will always be
     * resource/assets that will be copied to the main
     * resources/<vendor-name>/assets.
     */
    'assets-transfer-vendors' => [
        'course-mastering-nova',
        'dev',
    ],
];
