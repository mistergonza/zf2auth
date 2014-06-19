<?php
/**
 * 
 */
namespace Auth;

use Zend\Mvc\ModuleRouteListener;
use Zend\Mvc\MvcEvent;

class Module
{
    public function onBootstrap(MvcEvent $e)
    {
        $eventManager        = $e->getApplication()->getEventManager();
        $moduleRouteListener = new ModuleRouteListener();
        $moduleRouteListener->attach($eventManager);
    }
    
    public function getControllerConfig() 
    {
        return array(
            'factories' => array(
                'Auth\Controller\AuthController' => function ($sm)
                {
                    $sl = $sm->getServiceLocator();

                    $auth_wrapper = $sl->get('Auth\Wrapper\AuthWrapper');

                    $controller = new Controller\AuthController();
                    $controller->setAuthWrapper($auth_wrapper);
                    
                    return $controller;
                }
            ),
        );
    }
    
    public function getServiceConfig() 
    {
        return array(
            'factories' => array(
                'Auth\Wrapper\AuthWrapper' => function ($sm) {
                    
                    $db_adapter =  $sm->get('Zend\Db\Adapter\Adapter');
                    
                    $wrapper = new Wrapper\AuthWrapper($db_adapter);
                    
                    return $wrapper;
                },
            ),
        );
    }
    
    public function getConfig()
    {
        return include __DIR__ . '/config/module.config.php';
    }

    public function getAutoloaderConfig()
    {
        return array(
            'Zend\Loader\StandardAutoloader' => array(
                'namespaces' => array(
                    __NAMESPACE__ => __DIR__ . '/src/' . __NAMESPACE__,
                ),
            ),
        );
    }
}
