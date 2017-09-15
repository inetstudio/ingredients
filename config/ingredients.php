<?php

return [

    /*
     * Настройки изображений
     */

    'images' => [
        'quality' => 75,
        'conversions' => [
            'og_image' => [
                'default' => [
                    [
                        'name' => 'og_image_default',
                        'size' => [
                            'width' => 968,
                            'height' => 475,
                        ],
                    ],
                ],
            ],
            'preview' => [
                'default' => [
                    [
                        'name' => 'preview_default',
                        'size' => [
                            'width' => 380,
                            'height' => 360,
                        ],
                    ],
                ],
                '3_2' => [
                    [
                        'name' => 'preview_3_2',
                        'size' => [
                            'width' => 768,
                            'height' => 512,
                        ],
                    ],
                ],
                '3_4' => [
                    [
                        'name' => 'preview_3_4',
                        'size' => [
                            'width' => 384,
                            'height' => 512,
                        ],
                    ],
                ],
            ],
            'content' => [
                'default' => [
                    [
                        'name' => 'content_admin',
                        'size' => [
                            'width' => 140,
                        ],
                    ],
                ],
            ],
        ]
    ],
];
