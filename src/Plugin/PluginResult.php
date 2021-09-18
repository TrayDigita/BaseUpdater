<?php
/*
 * Copyright (C) 2021 Tray Digita
 *
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program.  If not, see <http://www.gnu.org/licenses/>.
 *
 */

declare(strict_types=1);

namespace TrayDigita\BaseUpdater\Plugin;

use TrayDigita\BaseUpdater\Abstracts\AbstractPluginAdapter;
use TrayDigita\BaseUpdater\Abstracts\AbstractResult;
use function version_compare;

/**
 * @property-read AbstractPluginAdapter $adapter
 * @property-read PluginResultMetaData $metadata
 * @property-read PluginInfo $plugin
 * @mixin PluginResultMetaData
 */
class PluginResult extends AbstractResult
{
    /**
     * @var PluginInfo
     */
    protected $plugin;

    /**
     * @param AbstractPluginAdapter $adapter
     * @param PluginInfo $pluginInfo
     * @param array $metadata
     */
    public function __construct(
        AbstractPluginAdapter $adapter,
        PluginInfo $pluginInfo,
        array $metadata
    ) {
        $this->plugin = $pluginInfo;
        parent::__construct($adapter, $pluginInfo->getSlug());
        $this->metadata = new PluginResultMetaData(
            $this,
            $metadata
        );
    }

    /**
     * @return PluginInfo
     */
    public function getPlugin(): PluginInfo
    {
        return $this->plugin;
    }

    /**
     * @return bool
     */
    public function isNeedUpdate(): bool
    {
        return version_compare(
            $this->plugin->getVersion(),
            $this->metadata->getNewVersion(),
            '<'
        );
    }

    /**
     * @return array<string, string|bool|mixed>
     */
    public function getTransient(): array
    {
        // EXAMPLE :
        // {
        //    "plugins": {
        //        "hello-dolly.php": {
        //            "id": "w.org/plugins/hello-dolly",
        //            "slug": "hello-dolly",
        //            "plugin": "hello-dolly.php",
        //            "new_version": "1.7.2",
        //            "url": "https://wordpress.org/plugins/hello-dolly/",
        //            "package": "http://downloads.wordpress.org/plugin/hello-dolly.1.7.2.zip",
        //            "icons": {
        //                "2x": "https://ps.w.org/hello-dolly/assets/icon-256x256.jpg?rev=2052855",
        //                "1x": "https://ps.w.org/hello-dolly/assets/icon-128x128.jpg?rev=2052855"
        //            },
        //            "banners": {
        //                "1x": "https://ps.w.org/hello-dolly/assets/banner-772x250.jpg?rev=2052855"
        //            },
        //            "banners_rtl": [],
        //            "requires": "4.6",
        //            "tested": "5.5.6",
        //            "requires_php": false,
        //            "compatibility": []
        //        }
        //    },
        //    "translations": []
        //}
        $result = PluginResultMetaData::DEFAULT_RESULT;
        foreach ($result as $key => $v) {
            $result[$key] = $this->metadata->get($key);
        }
        return $result;
    }

    /**
     * @param string $name
     *
     * @return mixed|PluginInfo
     * @inheritDoc
     */
    public function __get(string $name)
    {
        if ($name === 'plugin') {
            return $this->getPlugin();
        }
        return parent::__get($name);
    }
}
