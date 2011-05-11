<?php
/**
 * Short Description of Pdo file.
 * 
 * Long Description of Pdo file.
 * 
 * @category  <category>
 * @package   <package>
 * @copyright Copyright (c) t-kawamoto
 * @license   <license>
 * @version   <version>
 * @link      <link>
 */
namespace Alma\Helper\Database;

/**
 * Short Description of Pdo class.
 * 
 * Long Description of Pdo class.
 * 
 * @author t-kawamoto
 * @category  <category>
 * @package   <package>
 * @copyright Copyright (c) t-kawamoto
 * @license   <license>
 * @version   <version>
 * @link      <link>
 */
class Pdo
{
    /**
     * データベースハンドラを保持する
     * @var \PDO
     */
    protected $m_dbh;
    /**
     * DB実行ステートメントを保持する
     * @var \PDOStatement
     */
    protected $m_statement;

    public function createDsn(array $options)
    {
        // ドライバは必須
        if (!isset($options['driver'])) {
            return false;
        }

        // DSNの構築
        $dsn = $options['driver'] . ':';
        unset($options['driver']);

        $first = true;
        foreach ($options as $k => $v) {
            if (!$first) $dsn.= ';';
            $dsn .= sprintf('%s=%s', $k, $v);
            $first = false;
        }

        return $dsn;
    }

    public function connect($dsn, $username, $passwd, array $options)
    {
        $this->m_dbh = new \PDO($dsn, $username, $passwd, $options);
    }

    public function getRawHandler()
    {
        return $this->m_dbh;
    }

    public function begin()
    {
        $this->m_dbh->beginTransaction();
    }

    public function commit()
    {
        $this->m_dbh->commit();
    }

    public function rollback()
    {
        $this->m_dbh->rollBack();
    }

    public function query($query)
    {
        $this->m_statement = $this->m_dbh->query($query);
        return $this->m_statement;
    }

    public function execute($query, array $data = array(), array $driveroptions = array())
    {
        $this->m_statement = $this->m_dbh->prepare($query, $driveroptions);
        $this->m_statement->execute($data);
        return $this->m_statement;
    }
    
    public function clear()
    {
        $this->m_statement = null;
    }
}