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
 * ���ȈՓI�ȃe���v���[�g�G���W��
 * 
 * �L�@:
 * <table>
 * <tr><th>{{ var }}</th><td>�w�肳�ꂽ�ϐ����o�͂���(HTML�G���e�B�e�B�����s��)</td></tr>
 * <tr><th>{{ var|raw }}</th><td>�w�肳�ꂽ�ϐ������̂܂܏o�͂���</td></tr>
 * <tr><th>{% each (val as ext) %}</th><td>foreach���s���B<br>
 * <code>foreach ($val as $ext) {</code></td></tr>
 * <tr><th>{% each (val in key,ext) %}</th><td>foreach���s���B<br>
 * <code>foreach ($val as $key=>$ext) {</code></td></tr>
 * <tr><th>{% if (val) %}�`{% end %}</th><td>if���s���B<br><code>if ($val) {</code></td></tr>
 * <tr><th>{% elseif (val) %}</th><td>elseif���s���B<br><code>} elseif ($val) {</code></td></tr>
 * <tr><th>{% else %}</th><td>else���s���B<br><code>} else {</code></td></tr>
 * <tr><th>{% has(val) %}</th><td>�ϐ�����`����Ă��邩���m�F����B<br>
 * <code>if (isset($val)) {</code></td></tr>
 * <tr><th>{% !has(val) %}</th><td>�ϐ�����`����Ă��Ȃ������m�F����B<br>
 * <code>if (!isset($val)) {</code></td></tr>
 * <tr><th>{% empty(val) %}</th><td>�ϐ����󂩂��m�F����B<br>
 * <code>if (empty($val)) {</code></td></tr>
 * <tr><th>{% !empty(val) %}</th><td>�ϐ�����ł͂Ȃ������m�F����B<br>
 * <code>if (empty($val)) {</code></td></tr>
 * <tr><th>{% end %}</th><td>each if has empty�Ȃǂ����B<br><code>}</code></td></tr>
 * <tr><th>{% PHP�R�[�h %}</th><td>PHP�R�[�h�����̂܂ܔ��s����B</td></tr>
 * <tr><th>{# �R�����g #}</th><td>�R�����g���L�q����B(�o�͂Ɋ܂܂�Ȃ��j</td></tr>
 * <tr><th>{% assign(field,val) %}</th><td>$field��val�̓��e��ݒ肷��B</td></tr>
 * <tr><th>{% extends(template) %}</th><td>�w�肵���e���v���[�g���p������B</td></tr>
 * <tr><th>{% block name %}�`{% endblock name %}</th><td>�p���u���b�N���`����B�p�����ƌp�����
 * ����name�Œ�`���鎖�ɂ��A�p�����̃u���b�N���p����̃u���b�N�Œu��������B</td></tr>
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
     * �e���v���[�g�f�B���N�g����ێ�����
     * @var string
     */
    private $m_templatedir;
    /**
     * �L���b�V���f�B���N�g����ێ�����
     * @var string
     */
    private $m_cachedir;

    /**
     * View������������
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
     * �e���v���[�g�f�B���N�g���̃p�X��ݒ肷��
     * @param string $path �e���v���[�g�f�B���N�g���̃p�X
     */
    public function setTemplateDirectory($path)
    {
        $this->m_templatedir = realpath($path);
    }

    /**
     * �L���b�V���f�B���N�g���̃p�X��ݒ肷��
     * @param string $path �L���b�V���f�B���N�g���̃p�X
     */
    public function setCacheDirectory($path)
    {
        $this->m_cachedir = realpath($path);
    }
    
    /**
     * �e���v���[�g�t�@�C�������݂��邩���m�F����B
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
     * �e���v���[�g����͂��ĕ\������B
     * @param string $template �e���v���[�g�t�@�C�����i�g���q�Ȃ��j
     * @param array $values �e���v���[�g�ɐݒ肷��l�Z�b�g
     */
    public function render($template, array $values)
    {
        // �`�F�b�N
        if (!$this->m_templatedir) die('�e���v���[�g�f�B���N�g����������܂���B');
        if (!$this->m_cachedir) die('�L���b�V���f�B���N�g����������܂���B');
        if (!is_writable($this->m_cachedir)) die('�L���b�V���f�B���N�g���ɏ������߂܂���B');

        // �e���v���[�g�t�@�C��
        $tfile = $this->hasTemplate($template);
        if (!file_exists($tfile)) die('�e���v���[�g�u' . $template . '�v��������܂���B');
        $ttime = filemtime($tfile);

        // �L���b�V���pID�𐶐�
        $cid = sha1($template);
        $cid = substr($cid, 0, 2) . '/' . substr($cid, 2, 2) . '/' . substr($cid, 4);

        // �L���b�V���t�@�C�����𐶐�
        $cfile = $this->m_cachedir . '/' . $cid . '.php';


        $ttime = 0;
        if (!file_exists($cfile) || $ttime <= filemtime($cfile)) {
            // �e���v���[�g�ǂݍ���
            $buf = file_get_contents($tfile);

            // PHP�R�[�h��
            // �ϐ��o��
            $buf = preg_replace_callback('/\{\{(.+)\}\}/', array($this, 'valueReplace'), $buf);

            // �\���o��
            $buf = $this->structureReplace($buf, $values);

            // �L���b�V������
            if (!file_exists(dirname($cfile))) {
                mkdir(dirname($cfile), 0777, true);
            }
            file_put_contents($cfile, $buf);
        }

        $this->display($cfile, $values);
    }

    /**
     * �ϐ��w�蕔����PHP�R�[�h�֒u��������R�[���o�b�N�֐�(preg_replace_callback)
     * @param array $matches
     * @return string �ϊ���̕����� 
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
     * �\������ϊ�����
     * @param string $buffer �e���v���[�g���e
     * @return string �ϊ������e���v���[�g���e
     */
    private function structureReplace($buffer, array $values)
    {
        // �e���v���[�g�̌p��
        $isExtend = false;
        $extends = '<?php $v=new Wtf_View(); $v->render("%s", $values); ?>';
        $blockStart = '';
        $blockEnd = '';
        $regexp = '/\{% extends *\((\w+)\) %\}/';
        if (preg_match($regexp, $buffer, $m)) {
            // �e���v���[�g�̏ꍇ
            $blockStart = '<?php if (!function_exists("block_$1")) {' . "\n";
            $blockStart.= '    function block_$1($values) {' . "\n";
            $blockStart.= '        extract($values);?>' . "\n";
            $blockEnd = "\n" . '<?php }} ?>';
            $extends = sprintf($extends, $m[1]);
            $isExtend = true;
            $buffer = preg_replace($regexp, '', $buffer);
        } else {
            // ���C�A�E�g�e���v���[�g�̏ꍇ
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

        // �p�����Ă���ꍇ�͍Ō�Ɍp�����̃e���v���[�g���Ăяo���B
        if ($isExtend) {
            $buffer .= "\n" . $extends;
        }

        return $buffer;
    }

    /**
     * �e���v���[�g�ɕϐ���W�J���Ď��ۂɕ\�����s��
     * @param string $template �e���v���[�g�L���b�V���t�@�C����
     * @param array $values �e���v���[�g�ɐݒ肷��l�Z�b�g
     */
    private function display($template, array $values)
    {
        extract($values);
        include $template;
    }
}