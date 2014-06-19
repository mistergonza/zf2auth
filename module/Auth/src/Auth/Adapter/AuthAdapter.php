<?php
/**
 * @author mistergonza <gonza.work@gmail.com>
 */
namespace Auth\Adapter;

use 
Zend\Authentication\Adapter\AdapterInterface,
Zend\Authentication\Adapter\AbstractAdapter,
Zend\Authentication\Result as AuthenticationResult,
Zend\Db\Adapter\Adapter as DbAdapter,
Zend\Db\Sql,
Zend\Crypt\Password\Bcrypt;

class AuthAdapter extends AbstractAdapter implements AdapterInterface
{
    protected $zendDb;
    protected $tableName;
    protected $identityColumn;
    protected $credentialColumn;
    
    public function __construct(
        DbAdapter $zendDb,
        $tableName = null,
        $identityColumn = null,
        $credentialColumn = null
    )
    {
        $this->zendDb = $zendDb;
        if (null !== $tableName) {
            $this->setTableName($tableName);
        }

        if (null !== $identityColumn) {
            $this->setIdentityColumn($identityColumn);
        }

        if (null !== $credentialColumn) {
            $this->setCredentialColumn($credentialColumn);
        }
    }
    
    public function getTableName()
    {
        return $this->tableName;
    }

    public function getIdentityColumn()
    {
        return $this->identityColumn;
    }

    public function getCredentialColumn()
    {
        return $this->credentialColumn;
    }

    public function setTableName($tableName)
    {
        $this->tableName = $tableName;
        return $this;
    }

    public function setIdentityColumn($identityColumn)
    {
        $this->identityColumn = $identityColumn;
        return $this;
    }

    public function setCredentialColumn($credentialColumn)
    {
        $this->credentialColumn = $credentialColumn;
        return $this;
    }
    
    public function authenticate()
    {
        $sql = new Sql\Sql($this->zendDb);
        $select = $sql->select();
        $select
                ->from($this->getTableName())
                ->where(array(
                    $this->getIdentityColumn() => $this->getIdentity()
                ));
        
        $statement = $sql->prepareStatementForSqlObject($select);
        
        try {
            $result = $statement->execute();
            $result_identities = array();
            // iterate result, most cross platform way
            foreach ($result as $row) {
                $result_identities[] = $row;
            }
        } 
        catch (\Exception $e) 
        {
            throw new Exception\RuntimeException(
                'The supplied parameters to DbTable failed to '
                    . 'produce a valid sql statement, please check table and column names '
                    . 'for validity.', 0, $e
            );
        }
        
        if (($auth_result = $this->authenticateValidateResultSet($result_identities)) instanceof AuthenticationResult) 
        {
            return $auth_result;
        }

        // At this point, ambiguity is already done. Loop, check and break on success.
        foreach ($result_identities as $identity) 
        {
            $auth_result = $this->authenticateValidateResult($identity);
            if ($auth_result->isValid()) {
                break;
            }
        }
        return $auth_result;
    }
    
    protected function authenticateValidateResultSet(array $result_identities)
    {
        $result_info =  array();
        if (count($result_identities) < 1) {
            $result_info['code'] = AuthenticationResult::FAILURE_IDENTITY_NOT_FOUND;
            $result_info['messages'][] = 'A record with the supplied identity could not be found.';
            
            return $this->authenticateCreateAuthResult($result_info);
        }
        elseif (count($result_identities) > 1)
        {
            $result_info['code'] = AuthenticationResult::FAILURE_IDENTITY_AMBIGUOUS;
            $result_info['messages'][] = 'More than one record matches the supplied identity.';
            
            return $this->authenticateCreateAuthResult($result_info);
        }
        
        return true;
    }
    
    protected function authenticateValidateResult($identity)
    {

        $result_info =  array();
        
        $hash = $identity[$this->getCredentialColumn()];
        $password = $this->getCredential();
        
        $bcrypt = new Bcrypt();
        if($bcrypt->verify($password, $hash))
        {

            $result_info['code'] = AuthenticationResult::SUCCESS;
            $result_info['identity'] = $identity[$this->identityColumn];
            $result_info['messages'][] = 'Success.';

            return $this->authenticateCreateAuthResult($result_info);
        }
        else
        {
            $result_info['code'] = AuthenticationResult::FAILURE_CREDENTIAL_INVALID;
            $result_info['messages'][] = 'Credential is invalid.';
            
            return $this->authenticateCreateAuthResult($result_info);
        }
    }
    
    protected function authenticateCreateAuthResult(array $result_info)
    {
        return new AuthenticationResult(
            $result_info['code'],
            $result_info['identity'],
            $result_info['messages']
        );
    }

}
