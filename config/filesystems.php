<?php

return [

    /*
     * Расширение файла конфигурации app/config/filesystems.php
     * добавляет локальные диски для хранения изображений постов и пользователей
     */

    'ingredients' => [
        'driver' => 'local',
        'root' => storage_path('app/public/ingredients'),
        'url' => env('APP_URL').'/storage/ingredients',
        'visibility' => 'public',
    ],

];
