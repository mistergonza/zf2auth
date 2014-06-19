<?php
/**
 * @author mistergonza <gonza.work@gmail.com>
 */
namespace Auth\Wrapper;

use 
Zend\Authentication\AuthenticationService,
Zend\Authentication\Storage\Session as SessionStorage,
//Zend\Authentication\Adapter\DbTable as AuthAdapter,
Auth\Adapter\AuthAdapter;

class AuthWrapper
{
    private $authAdapter;
    private $authService;
    private $storage;
    private $request;
    
    /**
     * 
     * @param type $db_adapter
     * @param \Zend\Authentication\Storage\StorageInterface $storage
     */
    public function __construct($db_adapter, $storage = null)
    {
        $this->authAdapter = new AuthAdapter($db_adapter,
                           'users',
                           'login',
                           'password'/*,
                           '(?)' */
                           );
        if(!$storage) 
        {
            $storage = new SessionStorage();   
        }
        $this->authService =  new AuthenticationService();
        $this->authService->setStorage($storage);
        
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
            $adapter = $this->authAdapter;
            
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
        return  $this->authService->hasIdentity();
    }
    
    public function logout()
    {
        
    }
}
