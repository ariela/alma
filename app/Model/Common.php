<?php
namespace Model;

use \Alma\Lists\Helper as HelperList;

/**
 * 共通のモデル情報
 */
class Common extends \Alma\Model
{
    /**
     * DBヘルパーを設定する
     * @var \Alma\Helper\Database\Pdo
     */
    protected $db;

    /**
     * モデルの初期化
     */
    public function __construct()
    {
        // ヘルパーのロード
        $this->loadHelper(HelperList::DB_PDO, 'db');
        // PDOヘルパーを接続
        $dsn = $this->db->createDsn(array(
                    'driver' => 'mysql',
                    'host' => 'localhost',
                    'dbname' => 'cview',
                    'charset' => 'utf8',
                ));
        $this->db->connect($dsn, 'cview', 'cview', array(\PDO::ATTR_EMULATE_PREPARES => false));

        // システム情報追加
        $this->sys_name = 'Alma';
        $this->sys_url = 'http://alma.transrain.net/';
        $this->sys_version = '0.0.1';
        $this->assets_style = 'assets/styles';

        // システム設定取得
        $query = 'SELECT * FROM cv_configs';
        $stmt = $this->db->execute($query);
        $rows = $stmt->fetchAll(\PDO::FETCH_CLASS);
        foreach ($rows as $row) {
            $field = $row->ckey;
            $this->$field = $row->cvalue;
        }
    }
}