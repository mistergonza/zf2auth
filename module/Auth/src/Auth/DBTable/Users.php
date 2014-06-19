<?php
/**
 * @author mistergonza <gonza.work@gmail.com>
 */
namespace Auth\DBTable;

use Zend\Db\Adapter\Adapter;
use Zend\Db\ResultSet\ResultSet;
use Zend\Db\TableGateway\AbstractTableGateway;

class Users extends AbstractTableGateway
{
    protected $table = 'users';
    public function __construct(Adapter $adapter)
    {
        $this->adapter = $adapter;

        $this->resultSetPrototype = new ResultSet();

        $this->initialize();
    }
    
    public function fetchAll()
    {
        $resultSet = $this->select();
        return $resultSet;
    }
    
    public function getUser($user)
    {

        $rowset = $this->select(array(
            'login' => $user,
        ));

        $row = $rowset->current();

        return $row;
    }
}
