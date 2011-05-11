<?php
/**
 * ヘルパーの読み込み名称のリストを提供する定数クラス
 * 
 * @category  Framework
 * @package   Alma/Lists
 * @copyright Copyright (c) 2011 Takeshi Kawamoto <yuki@transrain.net>
 * @license   http://www.apache.org/licenses/LICENSE-2.0.txt Apache License Version 2.0
 * @version   1.0.0
 * @link      https://github.com/ariela/alma
 */
namespace Alma\Lists;

/**
 * ヘルパーの読み込み名称のリストを提供する定数クラス
 * 
 * @author    Takeshi Kawamoto
 * @category  Framework
 * @package   Alma/Lists
 * @copyright Copyright (c) 2011 Takeshi Kawamoto <yuki@transrain.net>
 * @license   http://www.apache.org/licenses/LICENSE-2.0.txt Apache License Version 2.0
 * @version   1.0.0
 * @link      https://github.com/ariela/alma
 */
class Helper
{
    /**
     * PDO データベースヘルパー名
     */
    const DB_PDO = 'database/pdo';
    
    /**
     * Twig ビューヘルパー名
     */
    const VIEW_TWIG = 'view/twig';
}