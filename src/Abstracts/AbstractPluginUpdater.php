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

namespace TrayDigita\BaseUpdater\Abstracts;

use Psr\Log\LoggerInterface;
use TrayDigita\BaseUpdater\Plugin\Adapter\Noop;
use TrayDigita\BaseUpdater\Plugin\InvalidPluginResult;
use TrayDigita\BaseUpdater\Plugin\PluginInfo;
use TrayDigita\BaseUpdater\Plugin\PluginResult;
use TrayDigita\BaseUpdater\Plugin\Translations\FileTranslations;
use function determine_locale;
use function is_object;
use function is_string;

/**
 * @method AbstractPluginAdapter|null get(string $adapter)
 */
abstract class AbstractPluginUpdater extends AbstractUpdater
{
    /**
     * @var string
     */
    protected $mode = 'plugin';

    /**
     * @var string
     */
    protected $transient_name = 'update_plugins';

    /**
     * @var PluginInfo
     */
    protected $plugin;

    /**
     * @var FileTranslations
     */
    protected $translations;

    /**
     * @param PluginInfo $pluginInfo
     * @param string $id
     * @param LoggerInterface|null $logger
     */
    public function __construct(PluginInfo $pluginInfo, string $id, LoggerInterface $logger = null)
    {
        parent::__construct($id, $logger);
        $this->plugin = $pluginInfo;
        $this->translations = new FileTranslations($pluginInfo, determine_locale());
    }

    /**
     * @return PluginInfo
     */
    public function getPlugin() : PluginInfo
    {
        return $this->plugin;
    }

    /**
     * @return FileTranslations
     */
    public function getTranslations(): FileTranslations
    {
        return $this->translations;
    }

    /**
     * @return string
     */
    public function getTransientName(): string
    {
        return $this->transient_name;
    }

    /**
     * @param AbstractPluginAdapter $adapter
     */
    public function add(AbstractAdapter $adapter): bool
    {
        if ($adapter instanceof AbstractPluginAdapter) {
            $this->adapters[$adapter->getId()] = $adapter;
            return true;
        }
        // invalid adapter
        return false;
    }

    /**
     * @param string|AbstractPluginAdapter $adapter
     */
    public function remove($adapter)
    {
        if (is_object($adapter)) {
            if (!$adapter instanceof AbstractPluginAdapter) {
                return;
            }
            $adapter = $adapter->getId();
        }

        if (is_string($adapter)) {
            unset($this->adapters[$adapter]);
        }
    }

    /**
     * @param bool $force
     *
     * @return PluginResult|InvalidPluginResult
     */
    public function update(bool $force = false)
    {
        /**
         * @var PluginResult|null $result
         */
        $result = parent::update($force);
        return $result??(new Noop($this))->update();
    }
}
