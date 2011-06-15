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
namespace alma\helper\database;

/**
 * Doctrineデータベースヘルパークラス
 * 
 * @author    Takeshi Kawamoto <yuki@transrain.net>
 * @category  Helper
 * @package   Alma/Helper/Database
 * @copyright Copyright (c) 2011 Takeshi Kawamoto <yuki@transrain.net>
 * @license   http://www.apache.org/licenses/LICENSE-2.0.txt Apache License Version 2.0
 * @version   1.0.0
 * @link      https://github.com/ariela/alma
 */
class Doctrine extends AbstractDatabase
{
    /**
     * EntityManager
     * @var \Doctrine\ORM\EntityManager
     */
    protected $m_manager;

    public function connect()
    {
        $cache = null;
        if (function_exists('apc_add')) {
            $cache = new \Doctrine\Common\Cache\ApcCache();
        } elseif (extension_loaded('xcache')) {
            $cache = new \Doctrine\Common\Cache\XcacheCache();
        } else {
            $cache = new \Doctrine\Common\Cache\ArrayCache();
        }

        $config = new \Doctrine\ORM\Configuration();
        $config->setMetadataCacheImpl($cache);
        $config->setQueryCacheImpl($cache);

        $driverImpl = $config->newDefaultAnnotationDriver(ALMA_DIR_APPLICATION . '/Entities');
        $config->setMetadataDriverImpl($driverImpl);

        $config->setProxyDir(ALMA_DIR_APPLICATION . '/Proxies');
        $config->setProxyNamespace('Proxies');

        if (ALMA_DEBUG) {
            $config->setAutoGenerateProxyClasses(true);
        } else {
            $config->setAutoGenerateProxyClasses(false);
        }

        $this->m_manager = \Doctrine\ORM\EntityManager::create($this->m_configSelected, $config);
    }

    /**
     * @return \Doctrine\ORM\EntityManager
     */
    public function getEntityManager()
    {
        return $this->m_manager;
    }
}