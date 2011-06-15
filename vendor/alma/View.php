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
namespace alma;

/**
 * 超簡易的なテンプレートエンジン
 * 
 * 記法:
 * <table>
 * <tr><th>{{ var }}</th><td>指定された変数を出力する(HTMLエンティティ化を行う)</td></tr>
 * <tr><th>{{ var|raw }}</th><td>指定された変数をそのまま出力する</td></tr>
 * <tr><th>{% each (val as ext) %}</th><td>foreachを行う。<br>
 * <code>foreach ($val as $ext) {</code></td></tr>
 * <tr><th>{% each (val in key,ext) %}</th><td>foreachを行う。<br>
 * <code>foreach ($val as $key=>$ext) {</code></td></tr>
 * <tr><th>{% if (val) %}～{% end %}</th><td>ifを行う。<br><code>if ($val) {</code></td></tr>
 * <tr><th>{% elseif (val) %}</th><td>elseifを行う。<br><code>} elseif ($val) {</code></td></tr>
 * <tr><th>{% else %}</th><td>elseを行う。<br><code>} else {</code></td></tr>
 * <tr><th>{% has(val) %}</th><td>変数が定義されているかを確認する。<br>
 * <code>if (isset($val)) {</code></td></tr>
 * <tr><th>{% !has(val) %}</th><td>変数が定義されていないかを確認する。<br>
 * <code>if (!isset($val)) {</code></td></tr>
 * <tr><th>{% empty(val) %}</th><td>変数が空かを確認する。<br>
 * <code>if (empty($val)) {</code></td></tr>
 * <tr><th>{% !empty(val) %}</th><td>変数が空ではないかを確認する。<br>
 * <code>if (empty($val)) {</code></td></tr>
 * <tr><th>{% end %}</th><td>each if has emptyなどを閉じる。<br><code>}</code></td></tr>
 * <tr><th>{% PHPコード %}</th><td>PHPコードをそのまま発行する。</td></tr>
 * <tr><th>{# コメント #}</th><td>コメントを記述する。(出力に含まれない）</td></tr>
 * <tr><th>{% assign(field,val) %}</th><td>$fieldにvalの内容を設定する。</td></tr>
 * <tr><th>{% extends(template) %}</th><td>指定したテンプレートを継承する。</td></tr>
 * <tr><th>{% block name %}～{% endblock name %}</th><td>継承ブロックを定義する。継承元と継承先で
 * 同一nameで定義する事により、継承元のブロックを継承先のブロックで置き換える。</td></tr>
 * </table>
 *
 * @author    Takeshi Kawamoto <yuki@transrain.net>
 * @category  Framework
 * @package   Alma
 * @copyright Copyright (c) 2011 Takeshi Kawamoto <yuki@transrain.net>
 * @license   http://www.apache.org/licenses/LICENSE-2.0.txt Apache License Version 2.0
 * @version   1.0.0
 * @link      https://github.com/ariela/alma
 */
class View
{
    /**
     * テンプレートディレクトリを保持する
     * @var string
     */
    private $m_templatedir;
    /**
     * キャッシュディレクトリを保持する
     * @var string
     */
    private $m_cachedir;

    /**
     * Viewを初期化する
     */
    public function __construct()
    {
        $this->m_templatedir = array(
                                    realpath(ALMA_DIR_APPLICATION . '/templates'),
                                    realpath(ALMA_DIR_SYSTEM . '/templates'),
                               );
        $this->m_cachedir = realpath(ALMA_DIR_APPLICATION . '/caches');
    }

    /**
     * テンプレートディレクトリのパスを設定する
     * @param string $path テンプレートディレクトリのパス
     */
    public function setTemplateDirectory($path)
    {
        $this->m_templatedir = realpath($path);
    }

    /**
     * キャッシュディレクトリのパスを設定する
     * @param string $path キャッシュディレクトリのパス
     */
    public function setCacheDirectory($path)
    {
        $this->m_cachedir = realpath($path);
    }
    
    /**
     * テンプレートファイルが存在するかを確認する。
     */
    public function hasTemplate($template)
    {
        if (is_array($this->m_templatedir)) {
            $find = false;
            foreach ($this->m_templatedir as $d) {
                $path = realpath($d . '/' . $template . '.html');
                if ($path !== false) {
                    $find = $path;
                    break;
                }
            }
            return $find;
        } else {
            return realpath($this->m_templatedir . '/' . $template . '.html');
        }
    }

    /**
     * テンプレートを解析して表示する。
     * @param string $template テンプレートファイル名（拡張子なし）
     * @param array $values テンプレートに設定する値セット
     */
    public function render($template, array $values)
    {
        // チェック
        if (!$this->m_templatedir) die('テンプレートディレクトリが見つかりません。');
        if (!$this->m_cachedir) die('キャッシュディレクトリが見つかりません。');
        if (!is_writable($this->m_cachedir)) die('キャッシュディレクトリに書き込めません。');

        // テンプレートファイル
        $tfile = $this->hasTemplate($template);
        if (!file_exists($tfile)) die('テンプレート「' . $template . '」が見つかりません。');
        $ttime = filemtime($tfile);

        // キャッシュ用IDを生成
        $cid = sha1($template);
        $cid = substr($cid, 0, 2) . '/' . substr($cid, 2, 2) . '/' . substr($cid, 4);

        // キャッシュファイル名を生成
        $cfile = $this->m_cachedir . '/' . $cid . '.php';


        $ttime = 0;
        if (!file_exists($cfile) || $ttime <= filemtime($cfile)) {
            // テンプレート読み込み
            $buf = file_get_contents($tfile);

            // PHPコード化
            // 変数出力
            $buf = preg_replace_callback('/\{\{(.+)\}\}/', array($this, 'valueReplace'), $buf);

            // 構造出力
            $buf = $this->structureReplace($buf, $values);

            // キャッシュ生成
            if (!file_exists(dirname($cfile))) {
                mkdir(dirname($cfile), 0777, true);
            }
            file_put_contents($cfile, $buf);
        }

        $this->display($cfile, $values);
    }

    /**
     * 変数指定部分をPHPコードへ置き換えるコールバック関数(preg_replace_callback)
     * @param array $matches
     * @return string 変換後の文字列 
     */
    private function valueReplace($matches)
    {
        $tokens = array_map('trim', explode('|', $matches[1]));

        $arg = '$' . array_shift($tokens);

        $raw = false;
        foreach ($tokens as $token) {
            if (strtolower($token) === 'raw') {
                $raw = true;
            } else {
                $arg = "$token($arg)";
            }
        }

        if ($raw) {
            return '<?php echo ' . $arg . ' ?>';
        } else {
            return '<?php echo htmlspecialchars(' . $arg . ', ENT_QUOTES) ?>';
        }
    }

    /**
     * 構造式を変換する
     * @param string $buffer テンプレート内容
     * @return string 変換したテンプレート内容
     */
    private function structureReplace($buffer, array $values)
    {
        // テンプレートの継承
        $isExtend = false;
        $extends = '<?php $v=new Wtf_View(); $v->render("%s", $values); ?>';
        $blockStart = '';
        $blockEnd = '';
        $regexp = '/\{% extends *\((\w+)\) %\}/';
        if (preg_match($regexp, $buffer, $m)) {
            // テンプレートの場合
            $blockStart = '<?php if (!function_exists("block_$1")) {' . "\n";
            $blockStart.= '    function block_$1($values) {' . "\n";
            $blockStart.= '        extract($values);?>' . "\n";
            $blockEnd = "\n" . '<?php }} ?>';
            $extends = sprintf($extends, $m[1]);
            $isExtend = true;
            $buffer = preg_replace($regexp, '', $buffer);
        } else {
            // レイアウトテンプレートの場合
            $blockStart = '<?php if (!function_exists("block_$1")) {' . "\n";
            $blockStart.= '    function block_$1($values) {' . "\n";
            $blockStart.= '        extract($values);?>' . "\n";
            $blockEnd = "\n" . '<?php }}; block_$1($values); ?>';
        }

        $reppattern = array(
            '/\{% block (\w+) %\}/' => $blockStart,
            '/\{% endblock (\w+) %\}/' => $blockEnd,
            '/\{% each *\((\w+) as (\w+)\) %\}/U' => '<?php foreach ($$1 as $$2) { ?>',
            '/\{% each *\((\w+) as (\w+) *, *(\w+)\) %\}/U' => '<?php foreach ($$1 as $$2 => $$3) { ?>',
            '/\{% if *\((\w+)\) %\}/U' => '<?php if ($$1) { ?>',
            '/\{% else *if *\((\w+)\) %\}/U' => '<?php } elseif ($$1) { ?>',
            '/\{% else %\}/' => '<?php } else { ?>',
            '/\{% end %\}/' => '<?php } ?>',
            '/\{% has *\((\w+)\) %\}/U' => '<?php if(isset($$1)) { ?>',
            '/\{% !has *\((\w+)\) %\}/U' => '<?php if(!isset($$1)) { ?>',
            '/\{% empty *\((\w+)\) %\}/U' => '<?php if(empty($$1)) { ?>',
            '/\{% !empty *\((\w+)\) %\}/U' => '<?php if(!empty($$1)) { ?>',
            '/\{% assign *\((\w+) *, *([^)]+)\) %\}/U' => '<?php $$1 = $2; $values["$1"] = $2; ?>',
            '/\{% (.+) %\}/sU' => '<?php $1 ?>',
            '/\{#(.+)#\}/sU' => '<?php /*$1*/ ?>',
        );
        $buffer = preg_replace(array_keys($reppattern), array_values($reppattern), $buffer);

        // 継承している場合は最後に継承元のテンプレートを呼び出す。
        if ($isExtend) {
            $buffer .= "\n" . $extends;
        }

        return $buffer;
    }

    /**
     * テンプレートに変数を展開して実際に表示を行う
     * @param string $template テンプレートキャッシュファイル名
     * @param array $values テンプレートに設定する値セット
     */
    private function display($template, array $values)
    {
        extract($values);
        include $template;
    }
}