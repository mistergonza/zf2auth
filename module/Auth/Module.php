<?php
/**
 * @author mistergonza <gonza.work@gmail.com>
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

        $eventManager->attach('route', array($this, 'allowAccess'), -100);
    }
    
    public function getControllerConfig() 
    {
        return array(
            'factories' => array(
                'Auth\Controller\AuthController' => function ($sm)
                {
                    $sl = $sm->getServiceLocator();

                    $auth_wrapper = $sl->get('AuthWrapper');

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
                    $config = $this->getConfig();
                    
                    $wrapper = new Wrapper\AuthWrapper($db_adapter, $config);
                    
                    return $wrapper;
                },
                'Auth\Wrapper\ACLWrapper' => function ($sm) {
                    $config = $sm->get('config');

                    $wrapper = new Wrapper\ACLWrapper($config);

                    return $wrapper;
                },
            ),
        );
    }
    
    public function allowAccess(MvcEvent $e)
    {
        $application = $e->getApplication();
        $routeMatch = $e->getRouteMatch();

        $sm = $application->getServiceManager();

        $auth_wrapper = $sm->get('AuthWrapper');
        $acl_wrapper = $sm->get('ACLWrapper');

        $role = "guest";

        if($auth_wrapper->hasIdentity())
        {
            $user_info = $auth_wrapper->getAuthenticatedUserInfo();
            $role = $user_info['role'];
        }

        $controller = $routeMatch->getParam('controller');
        $action = $routeMatch->getParam('action');

        if (!$acl_wrapper->hasResource($controller))
        {
            throw new \Exception('Resource ' . $controller . ' not defined');
        }

        if (!$acl_wrapper->isAllowed($role, $controller, $action))
        {
            $routeMatch->setParam('controller', 'Auth\Controller\Forbidden');
            $routeMatch->setParam('action', 'index');
        }
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