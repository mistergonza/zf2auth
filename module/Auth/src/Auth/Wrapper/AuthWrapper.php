<?php
/**
 * @author mistergonza <gonza.work@gmail.com>
 */
namespace Auth\Wrapper;

use 
Zend\Authentication\AuthenticationService,
Zend\Authentication\Storage\Session as SessionStorage,
Zend\Authentication\Adapter\DbTable as AuthAdapter,
Zend\Db\Sql;

use
Auth\Adapter\AuthAdapter as BcryptAuthAdapter;

class AuthWrapper
{
    protected $zendDb;
    private $authAdapter;
    private $authService;
    protected $authConfig;
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
        $this->zendDb = $db_adapter;

        $auth_config = array(
            'table_name' => (isset($config['auth']['table_name'])) ? $config['auth']['table_name'] : 'users',
            'identity_column' => (isset($config['auth']['identity_column'])) ? $config['auth']['identity_column'] : 'login',
            'credential_column' => (isset($config['auth']['credential_column'])) ? $config['auth']['credential_column'] : 'password',
            'role_column' => (isset($config['auth']['role_column'])) ? $config['auth']['role_column'] : 'role',
            'crypt_method' => (isset($config['auth']['crypt_method'])) ? $config['auth']['crypt_method'] : 'md5',
        );

        $this->authConfig = $auth_config;

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
     * @return \Zend\Authentication\Adapter\AbstractAdapter
     */
    public function getAuthAdapter()
    {
        return $this->authAdapter;
    }

    /**
     * @return \Zend\Authentication\AuthenticationService
     */
    public function getAuthService()
    {
        return $this->authService;
    }

    public function setAuthAdapter(\Zend\Authentication\Adapter\AbstractAdapter $auth_adapter)
    {
        $this->authAdapter = $auth_adapter;
        return $this;
    }

    /**
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
     * @param \Zend\Http\Request $request
     * @return \Auth\Wrapper\AuthWrapper
     */
    public function setRequest(\Zend\Http\Request $request) 
    {
        $this->request = $request;
        return $this;
    }

    /**
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
            
            return $this->getAuthService()->authenticate($adapter)->isValid();
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

    public function getIdentity()
    {
        return $this->getAuthService()->getIdentity();
    }

    /**
     * @return array
     * @throws \Exception\RuntimeException
     * @throws \Exception
     */
    public function getAuthenticatedUserInfo()
    {
        $result =  array();

        if($this->hasIdentity())
        {
            $sql = new Sql\Sql($this->zendDb);

            $select = $sql->select();

            $select
                ->from($this->authConfig['table_name'])
                ->where(array($this->authConfig['identity_column'] => $this->getIdentity()));

            $statement = $sql->prepareStatementForSqlObject($select);

            try {
                $sql_result = $statement->execute();
                $result_users = array();
                // iterate result, most cross platform way
                foreach ($sql_result as $row) {
                    $result_users[] = $row;
                }
            }
            catch (\Exception $e)
            {
                throw new \Exception\RuntimeException(
                    'The supplied parameters to DbTable failed to '
                    . 'produce a valid sql statement, please check table and column names '
                    . 'for validity.', 0, $e
                );
            }

            if(count($result_users) != 1)
            {
                throw new \Exception('Not possible to determine the current user.');
            }

            foreach ($result_users as $user)
            {
                $result = array(
                    'id' => $user['id'],
                    'identity' => $user[$this->authConfig['identity_column']],
                    'role' => $user[$this->authConfig['role_column']],
                );
            }
        }

        return $result;
    }

    public function clearIdentity()
    {
        if($this->hasIdentity())
        {
            $this->getAuthService()->clearIdentity();
        }
        return $this;
    }
}
