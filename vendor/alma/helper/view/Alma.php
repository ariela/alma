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
namespace alma\helper\view;

/**
 * Alma�t����View�ŏo�͏������s���r���[�N���X
 * 
 * @author    Takeshi Kawamoto <yuki@transrain.net>
 * @category  Helper
 * @package   Alma/Helper/View
 * @copyright Copyright (c) 2011 Takeshi Kawamoto <yuki@transrain.net>
 * @license   http://www.apache.org/licenses/LICENSE-2.0.txt Apache License Version 2.0
 * @version   1.0.0
 * @link      https://github.com/ariela/alma
 */
class Alma implements IView
{
    /**
     * Twig�𗘗p���ĉ�ʏo�͂��s��
     * @param string $template �e���v���[�g��
     * @param mixed  $data     �o�̓f�[�^ 
     */
    public function display($template, $data = null)
    {
        $engine = new \alma\View();

        if ($data === null) {
            $data = array();
        } elseif (is_object($data) && $data instanceof \alma\Model) {
            $data = $data->toArray();
        }

        if ($engine->hasTemplate($template) === false) {
            throw new \alma\Exception("�e���v���[�g�t�@�C���u{$template}�v��������܂���B");
        }

        // �e���v���[�g�G���W�������s
        try {
            $engine->render($template, $data);
        } catch (\Exception $e) {
            throw new \alma\Exception('Alma View�̏����ŃG���[���������܂����B', 'Alma View�G���[', $e);
        }
    }
}
