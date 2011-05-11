<?php
namespace Alma\Helper\View;

/**
 * Viewとして必要なメソッドを宣言するインターフェース
 */
interface IView
{

    /**
     * 指定したテンプレートを出力する
     * @param string $template テンプレートファイル名
     * @param mixed  $data     出力データ
     */
    public function display($template, $data = null);
}
