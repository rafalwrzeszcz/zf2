<?php
/**
 * Zend Framework
 *
 * LICENSE
 *
 * This source file is subject to the new BSD license that is bundled
 * with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://framework.zend.com/license/new-bsd
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@zend.com so we can send you a copy immediately.
 *
 * @category   Zend
 * @package    Zend_Db
 * @subpackage Statement
 * @copyright  Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */

/**
 * @namespace
 */
namespace Zend\Db\Statement\Pdo;

use Zend\Db\Statement\Pdo,
    Zend\Db\Statement\Exception as StatementException;

/**
 * Proxy class to wrap a PDOStatement object for Ibm Databases.
 * Matches the interface of PDOStatement.  All methods simply proxy to the
 * matching method in PDOStatement.  PDOExceptions thrown by PDOStatement
 * are re-thrown as Zend_Db_Statement_Exception.
 *
 * @uses       \Zend\Db\Statement\Exception
 * @uses       \Zend\Db\Statement\Pdo\Pdo
 * @category   Zend
 * @package    Zend_Db
 * @subpackage Statement
 * @copyright  Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */
class Ibm extends Pdo
{
    /**
    * Returns an array containing all of the result set rows.
    *
    * Behaves like parent, but if limit()
    * is used, the final result removes the extra column
    * 'zend_db_rownum'
    *
    * @param int $style OPTIONAL Fetch mode.
    * @param int $col   OPTIONAL Column number, if fetch mode is by column.
    * @return array Collection of rows, each in a format by the fetch mode.
    * @throws \Zend\Db\Statement\Exception
    */
    public function fetchAll($style = null, $col = null)
    {
        $data = parent::fetchAll($style, $col);
        $results = array();
        $remove = $this->_adapter->foldCase('ZEND_DB_ROWNUM');

        foreach ($data as $row) {
            if (is_array($row) && array_key_exists($remove, $row)) {
                unset($row[$remove]);
            }
            $results[] = $row;
        }
        return $results;
    }

    /**
     * Binds a parameter to the specified variable name.
     *
     * @param mixed $parameter Name the parameter, either integer or string.
     * @param mixed $variable  Reference to PHP variable containing the value.
     * @param mixed $type      OPTIONAL Datatype of SQL parameter.
     * @param mixed $length    OPTIONAL Length of SQL parameter.
     * @param mixed $options   OPTIONAL Other options.
     * @return bool
     * @throws \Zend\Db\Statement\Exception
     */
    public function _bindParam($parameter, &$variable, $type = null, $length = null, $options = null)
    {
        try {
            if (($type === null) && ($length === null) && ($options === null)) {
                return $this->_stmt->bindParam($parameter, $variable);
            } else {
                return $this->_stmt->bindParam($parameter, $variable, $type, $length, $options);
            }
        } catch (\PDOException $e) {
            throw new StatementException($e->getMessage(), $e->getCode(), $e);
        }
    }
}
