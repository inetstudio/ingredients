<?php

return [

    /*
     * Настройки изображений
     */

    'images' => [
        'quality' => 75,
        'conversions' => [
            'ingredient' => [
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
                        [
                            'name' => 'content_front',
                            'quality' => 70,
                            'fit' => [
                                'width' => 768,
                                'height' => 512,
                            ],
                        ],
                    ],
                ],
            ],
        ],
        'crops' => [
            'ingredient' => [
                'og_image' => [
                    [
                        'title' => 'Выбрать область',
                        'name' => 'default',
                        'ratio' => '968/475',
                        'size' => [
                            'width' => 968,
                            'height' => 475,
                            'type' => 'min',
                            'description' => 'Минимальный размер области — 968x475 пикселей',
                        ],
                    ],
                ],
                'preview' => [
                    [
                        'title' => 'Область по умолчанию',
                        'name' => 'default',
                        'ratio' => '380/360',
                        'size' => [
                            'width' => 380,
                            'height' => 360,
                            'type' => 'min',
                            'description' => 'Минимальный размер области — 380x360 пикселей'
                        ],
                    ],
                    [
                        'title' => 'Размер 3х4',
                        'name' => '3_4',
                        'ratio' => '3/4',
                        'size' => [
                            'width' => 384,
                            'height' => 512,
                            'type' => 'min',
                            'description' => 'Минимальный размер области 3x4 — 384x512 пикселей'
                        ],
                    ],
                    [
                        'title' => 'Размер 3х2',
                        'name' => '3_2',
                        'ratio' => '3/2',
                        'size' => [
                            'width' => 768,
                            'height' => 512,
                            'type' => 'min',
                            'description' => 'Минимальный размер области 3x2 — 768x512 пикселей'
                        ],
                    ],
                ],
            ],
        ],
    ],
];
