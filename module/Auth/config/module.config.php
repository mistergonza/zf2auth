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
    'service_manager' => array(
        'aliases' => array(
            'AuthWrapper' => 'Auth\Wrapper\AuthWrapper',
            'ACLWrapper' => 'Auth\Wrapper\ACLWrapper'
        ),
    ),
    'controllers' => array(
        'invokables' => array(
            'Auth\Controller\Auth' => 'Auth\Controller\AuthController',
            'Auth\Controller\Forbidden' => 'Auth\Controller\ForbiddenController',
        ),
        'aliases' => array(
            'Auth\Controller\Auth' => 'Auth\Controller\AuthController'
        ),
    ),
    'auth' => array(
        'crypt_method' => 'md5', //md5 or bcrypt
    ),
    'acl' => array(
        'roles' => array(
            'guest' => null,
            'user' => array('guest'),
        ),
        'permissions' => array(
            'Auth\Controller\Auth' => array(
                'allow' => array(
                    'guest' => array('login'),
                    'user' => null,
                ),
                'deny' => array(
                    'guest' => null,
                ),
            ),
        ),
    ),
    'view_manager' => array(
        'template_path_stack' => array(
            __DIR__ . '/../view',
        ),
    ),
);