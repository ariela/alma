<?php
/**
 * Copyright 2011 Takeshi Kawamoto
 * 
 * Licensed under the Apache License, Version 2.0 (the "License");
 * you may not use this file except in compliance with the License.
 * You may obtain a copy of the License at
 * 
 *    http://www.apache.org/licenses/LICENSE-2.0
 * 
 * Unless required by applicable law or agreed to in writing, software
 * distributed under the License is distributed on an "AS IS" BASIS,
 * WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied.
 * See the License for the specific language governing permissions and
 * limitations under the License.
 */
namespace Alma\Helper\Database;

/**
 * PDOデータベースヘルパークラス
 * 
 * @author    Takeshi Kawamoto <yuki@transrain.net>
 * @category  Helper
 * @package   Alma/Helper/Database
 * @copyright Copyright (c) 2011 Takeshi Kawamoto <yuki@transrain.net>
 * @license   http://www.apache.org/licenses/LICENSE-2.0.txt Apache License Version 2.0
 * @version   1.0.0
 * @link      https://github.com/ariela/alma
 */
class Pdo extends AbstractDatabase
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

    public function connect()
    {
        $options = $this->m_configSelected;
        $dsn = '';

        // ドライバは必須
        if (!isset($options['driver'])) {
            throw new Alma\Exception('データベースドライバが設定されていません。');
        }

        // ユーザ取得
        $user = null;
        if (isset($options['user'])) {
            $user = $options['user'];
            unset($options['user']);
        }

        // パスワード取得
        $pass = null;
        if (isset($options['password'])) {
            $pass = $options['password'];
            unset($options['password']);
        }

        // ドライバーオプション取得
        $driveropt = array();
        if (isset($options['driverOptions'])) {
            $driveropt = $options['driverOptions'];
            unset($options['driverOptions']);
        }

        // DSNの構築
        if (0 === strncmp('pdo_', $options['driver'], 4)) {
            $dsn = substr($options['driver'], 4) . ':';
        } else {
            $dsn = $options['driver'] . ':';
        }
        unset($options['driver']);

        $first = true;
        foreach ($options as $k => $v) {
            if (!$first) $dsn.= ';';
            $dsn .= sprintf('%s=%s', $k, $v);
            $first = false;
        }

        $this->m_dbh = new \PDO($dsn, $user, $pass);
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