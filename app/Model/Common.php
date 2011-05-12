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
     * @var \Alma\Helper\Database\Doctrine
     */
    protected $db;

    /**
     * モデルの初期化
     */
    public function __construct()
    {
        // ヘルパーのロード
        $this->loadHelper(HelperList::DB_DOCTRINE, 'db');

        // PDOヘルパーを接続
        $this->db->connect();

        // システム情報追加
        $this->sys_name = 'Alma';
        $this->sys_url = 'http://alma.transrain.net/';
        $this->sys_version = '0.0.1';
        $this->assets_style = 'assets/styles';

        // システム設定取得
        $em = $this->db->getEntityManager();
        $q = $em->createQuery('SELECT u FROM Entities\Config u');
        $conf = $q->getResult();
        foreach ($conf as $row) {
            $field = $row->getName();
            $this->$field = $row->getValue();
        }
        $em->flush();
    }
}