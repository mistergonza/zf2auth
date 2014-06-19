<?php
return array(
    'router' => array(
        'routes' => array(
            'auth' => array(
                'type' => 'Segment',
                'options' => array(
                    'route' => '/auth/[:action/]',
                    'defaults' => array(
                        'controller' => 'Auth\Controller\Auth',
                        'action'     => 'login',
                    ),
                ),
            ),
        ),
    ),
    'controllers' => array(
        'invokables' => array(
            'Auth\Controller\Auth' => 'Auth\Controller\AuthController'
        ),
        'aliases' => array(
            'Auth\Controller\Auth' => 'Auth\Controller\AuthController'
        ),
    ),
    'auth' => array(
        'crypt_method' => 'bcrypt', //md5 or bcrypt
    ),
    'view_manager' => array(
        'template_path_stack' => array(
            __DIR__ . '/../view',
        ),
    ),
);