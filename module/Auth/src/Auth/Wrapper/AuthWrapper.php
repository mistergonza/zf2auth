<?php
/**
 * @author mistergonza <gonza.work@gmail.com>
 */
namespace Auth\Wrapper;

use 
Zend\Authentication\AuthenticationService,
Zend\Authentication\Storage\Session as SessionStorage,
Zend\Authentication\Adapter\DbTable as AuthAdapter;

use
Auth\Adapter\AuthAdapter as BcryptAuthAdapter;

class AuthWrapper
{
    private $authAdapter;
    private $authService;
    private $storage;
    /**
     * @var \Zend\Http\Request
     */
    private $request;
    
    /**
     * 
     * @param \Zend\Db\Adapter\Adapter $db_adapter
     * @param array $config
     * @param \Zend\Authentication\Storage\StorageInterface $storage
     * @throws \Exception
     * @return \Auth\Wrapper\AuthWrapper
     */
    public function __construct($db_adapter, array $config = array(), $storage = null)
    {
        $auth_config = array(
            'table_name' => (isset($config['auth']['table_name'])) ? $config['auth']['table_name'] : 'users',
            'identity_column' => (isset($config['auth']['identity_column'])) ? $config['auth']['identity_column'] : 'login',
            'credential_column' => (isset($config['auth']['credential_column'])) ? $config['auth']['credential_column'] : 'password',
            'crypt_method' => (isset($config['auth']['crypt_method'])) ? $config['auth']['crypt_method'] : 'md5',
        );
        
        switch ($auth_config['crypt_method'])
        {
            case 'md5':
                $auth_adapter = new AuthAdapter($db_adapter,
                       $auth_config['table_name'],
                       $auth_config['identity_column'],
                       $auth_config['credential_column'],
                       'MD5(?)'
                       );
            break;

            case 'bcrypt':
                $auth_adapter = new BcryptAuthAdapter($db_adapter,
                       $auth_config['table_name'],
                       $auth_config['identity_column'],
                       $auth_config['credential_column']
                       );
            break;

            default:
                throw new \Exception('Invalid crypt method');
            break;
        }
        
        if(!$storage) 
        {
            $storage = new SessionStorage();   
        }
        
        $this->setAuthAdapter($auth_adapter);
        
        $auth_service =  new AuthenticationService();
        $auth_service->setStorage($storage);
        
        $this->setAuthService($auth_service);
        
        return $this;
    }
    
    /**
     * 
     * @return \Zend\Authentication\Adapter\AdapterInterface
     */
    public function getAuthAdapter()
    {
        return $this->authAdapter;
    }

    /**
     * 
     * @return \Zend\Authentication\AuthenticationService
     */
    public function getAuthService()
    {
        return $this->authService;
    }

    public function setAuthAdapter(\Zend\Authentication\Adapter\AdapterInterface $auth_adapter)
    {
        $this->authAdapter = $auth_adapter;
        return $this;
    }

    /**
     * 
     * @param \Zend\Authentication\AuthenticationService $auth_service
     * @return \Auth\Wrapper\AuthWrapper
     */
    public function setAuthService(\Zend\Authentication\AuthenticationService $auth_service)
    {
        $this->authService = $auth_service;
        return $this;
    }

    public function getRequest()
    {
        return $this->request;
    }
        
    /**
     * 
     * @param \Zend\Http\Request $request
     * @return \Auth\Wrapper\AuthWrapper
     */
    public function setRequest(\Zend\Http\Request $request) 
    {
        $this->request = $request;
        return $this;
    }

    /**
     * 
     * @return boolean
     */
    public function authenticate()
    {
        if($this->request->isPost())
        {
            $adapter = $this->getAuthAdapter();
            
            $user = $this->request->getPost('login');
            $password = $this->request->getPost('password'); 
            
            $adapter->setIdentity($user);
            $adapter->setCredential($password);
            
            return $this->authService->authenticate($adapter)->isValid();
        }
        else
        {
            return false;
        }
    }
    
    public function hasIdentity()
    {
        return  $this->getAuthService()->hasIdentity();
    }
    
    public function logout()
    {
        if($this->hasIdentity())
        {
            $this->getAuthService()->clearIdentity();
        }
        return $this;
    }
}
