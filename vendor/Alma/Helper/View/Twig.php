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
namespace Alma\Helper\View;

/**
 * Twigで出力処理を行うビュークラス
 * 
 * @author    Takeshi Kawamoto <yuki@transrain.net>
 * @category  Helper
 * @package   Alma/Helper/View
 * @copyright Copyright (c) 2011 Takeshi Kawamoto <yuki@transrain.net>
 * @license   http://www.apache.org/licenses/LICENSE-2.0.txt Apache License Version 2.0
 * @version   1.0.0
 * @link      https://github.com/ariela/alma
 */
class Twig implements IView
{
    /**
     * テンプレートディレクトリパス
     */
    protected $tmplatedir = array();
    /**
     * Twigエンジンを保持
     * @var Twig_Environment
     */
    protected $engine;

    /**
     * Twigを初期化する
     */
    public function __construct()
    {
        // テンプレートディレクトリの指定
        $loader = new \Twig_Loader_Filesystem(array(
                    realpath(ALMA_DIR_TEMPLATES),
                    realpath(ALMA_DIR_SYSTEM . '/templates'),
                ));

        // テンプレートエンジンの作成
        $this->engine = new \Twig_Environment($loader, array(
                    'cache' => realpath(ALMA_DIR_CACHE),
                    'auto_reload' => true,
                ));
    }

    /**
     * Twigを利用して画面出力を行う
     * @param string $template テンプレート名
     * @param mixed  $data     出力データ 
     */
    public function display($template, $data = null)
    {
        if ($data === null) {
            $data = array();
        } elseif (is_object($data) && $data instanceof \Alma\Model) {
            $data = $data->toArray();
        }

        // 拡張子がない場合はhtmlをつける
        $info = pathinfo($template);
        if (!isset($info['extension'])) {
            $template .= '.html';
        }

        if (!file_exists(ALMA_DIR_TEMPLATES . '/' . $template)
                && !file_exists(ALMA_DIR_SYSTEM . '/templates/' . $template)) {
            throw new \Alma\Exception("テンプレートファイル「{$template}」が見つかりません。");
        }

        // テンプレートエンジンを実行
        try {
            $template = $this->engine->loadTemplate($template);
            echo $template->render($data);
        } catch (\Exception $e) {
            throw new \Alma\Exception('Twigの処理でエラーが発生しました。', 'Twigエラー', $e);
        }
    }
}
