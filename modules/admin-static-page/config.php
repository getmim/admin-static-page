<?php

return [
    '__name' => 'admin-static-page',
    '__version' => '0.0.2',
    '__git' => 'git@github.com:getmim/admin-static-page.git',
    '__license' => 'MIT',
    '__author' => [
        'name' => 'Iqbal Fauzi',
        'email' => 'iqbalfawz@gmail.com',
        'website' => 'http://iqbalfn.com/'
    ],
    '__files' => [
        'modules/admin-static-page' => ['install','update','remove'],
        'theme/admin/static-page' => ['install','update','remove']
    ],
    '__dependencies' => [
        'required' => [
            [
                'admin' => NULL
            ],
            [
                'lib-formatter' => NULL
            ],
            [
                'lib-form' => NULL
            ],
            [
                'lib-pagination' => NULL
            ],
            [
                'static-page' => NULL
            ],
            [
                'admin-site-meta' => NULL 
            ]
        ],
        'optional' => []
    ],
    'autoload' => [
        'classes' => [
            'AdminStaticPage\\Controller' => [
                'type' => 'file',
                'base' => 'modules/admin-static-page/controller'
            ]
        ],
        'files' => []
    ],
    'routes' => [
        'admin' => [
            'adminStaticPage' => [
                'path' => [
                    'value' => '/page'
                ],
                'method' => 'GET',
                'handler' => 'AdminStaticPage\\Controller\\Page::index'
            ],
            'adminStaticPageEdit' => [
                'path' => [
                    'value' => '/page/(:id)',
                    'params' => [
                        'id'  => 'number'
                    ]
                ],
                'method' => 'GET|POST',
                'handler' => 'AdminStaticPage\\Controller\\Page::edit'
            ],
            'adminStaticPageRemove' => [
                'path' => [
                    'value' => '/page/(:id)/remove',
                    'params' => [
                        'id'  => 'number'
                    ]
                ],
                'method' => 'GET',
                'handler' => 'AdminStaticPage\\Controller\\Page::remove'
            ]
        ]
    ],
    'adminUi' => [
        'sidebarMenu' => [
            'items' => [
                'static-page' => [
                    'label' => 'Static Page',
                    'icon' => '<i class="fas fa-columns"></i>',
                    'priority' => 0,
                    'route' => ['adminStaticPage'],
                    'perms' => 'manage_static_page'
                ]
            ]
        ]
    ],
    'libForm' => [
        'forms' => [
            'admin.static-page.index' => [
                'q' => [
                    'label' => 'Search',
                    'type' => 'search',
                    'nolabel' => true,
                    'rules' => []
                ]
            ],
            'admin.static-page.edit' => [
                '@extends' => ['std-site-meta'],
                'title' => [
                    'label' => 'Title',
                    'type' => 'text',
                    'rules' => [
                        'required' => TRUE
                    ]
                ],
                'slug' => [
                    'label' => 'Slug',
                    'type' => 'text',
                    'slugof' => 'title',
                    'rules' => [
                        'required' => TRUE,
                        'empty' => FALSE,
                        'unique' => [
                            'model' => 'StaticPage\\Model\\StaticPage',
                            'field' => 'slug',
                            'self' => [
                                'service' => 'req.param.id',
                                'field' => 'id'
                            ]
                        ]
                    ]
                ],
                'content' => [
                    'label' => 'About',
                    'type' => 'summernote',
                    'rules' => []
                ],
                'meta-schema' => [
                    'options' => [
                        'WebPage'     => 'WebPage',
                        'AboutPage'   => 'AboutPage',
                        'ContactPage' => 'ContactPage',
                        'ProfilePage' => 'ProfilePage',
                        'QAPage'      => 'QAPage'
                    ]
                ]
            ]
        ]
    ]
];