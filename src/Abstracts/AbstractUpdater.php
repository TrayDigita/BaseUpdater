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
use Psr\Log\NullLogger;
use TrayDigita\BaseUpdater\Plugin\PluginInfo;
use WP_Theme;
use function get_plugins;
use function is_string;
use function strpos;

abstract class AbstractUpdater
{
    /**
     * @var string
     */
    protected $mode = '';

    /**
     * @var array<string, WP_Theme>
     */
    private static $themes = null;

    /**
     * @var array<string, PluginInfo>
     */
    private static $plugins = null;

    /**
     * @var array<string, AbstractAdapter>
     */
    protected $adapters = [];

    /**
     * @var string
     */
    protected $wp_version;

    /**
     * @var LoggerInterface
     */
    protected $logger;

    /**
     * @var array
     */
    protected $processed = [];

    /**
     * @var string
     */
    private $id;

    /**
     * @param string $id
     * @param LoggerInterface|null $logger
     *
     * @uses \Psr\Log\NullLogger for totally disable logs, default @uses ArrayLogger
     */
    public function __construct(string $id, LoggerInterface $logger = null)
    {
        $this->wp_version = get_bloginfo('version');
        $this->logger = $logger??new NullLogger();
        $this->id = $id;
    }

    /**
     * @return string
     */
    final public function getId(): string
    {
        return $this->id;
    }

    /**
     * @return string
     */
    public function getMode(): string
    {
        return $this->mode;
    }

    /**
     * Get all themes
     *
     * @param bool $recheck true if rechecking from @function wp_get_themes();
     *
     * @return WP_Theme[]
     */
    public static function themes(bool $recheck = false) : array
    {
        if ($recheck || self::$themes === null) {
            self::$themes = wp_get_themes();
        }
        return self::$themes;
    }

    /**
     * @param string $slug
     *
     * @return WP_Theme|null
     */
    public static function theme(string $slug)
    {
        $themes = self::themes();
        return $themes[$slug]??null;
    }

    /**
     * @return array<string, WP_Theme>
     */
    public function getThemes(bool $recheck = false): array
    {
        return self::themes($recheck);
    }

    /**
     * @param string $slug
     *
     * @return WP_Theme|null
     */
    public function getThemeBySlug(string $slug)
    {
        return self::theme($slug);
    }


    /**
     * Get all plugins
     *
     * @param bool $recheck true if rechecking from @function get_plugins();
     *
     * @return array<string, PluginInfo> key as plugin slug
     */
    public static function plugins(bool $recheck = false) : array
    {
        if ($recheck || self::$plugins === null) {
            self::$plugins = [];
            foreach (get_plugins() as $slug => $item) {
                $slug = trim($slug, '/');
                self::$plugins[$slug] = new PluginInfo($slug, $item);
            }
        }
        return self::$plugins;
    }

    /**
     * @param string $slug
     *
     * @return PluginInfo|null
     */
    public static function plugin(string $slug)
    {
        $plugins = self::plugins();
        $slug = trim($slug, '/');
        if (isset($plugins[$slug])) {
            return $plugins[$slug];
        }
        // check base slug
        if (strpos($slug, '/') === false) {
            foreach ($plugins as $item) {
                if ($item->getBaseSlug() === $slug) {
                    return $item;
                }
            }
        }

        return null;
    }

    /**
     * Get all plugins
     *
     * @param bool $recheck
     *
     * @return array<string, PluginInfo>
     */
    public function getPlugins(bool $recheck = false): array
    {
        return self::plugins($recheck);
    }

    /**
     * @param string $slug
     *
     * @return PluginInfo|null
     */
    public function getPluginBySlug(string $slug)
    {
        return self::plugin($slug);
    }

    /**
     * @return array<string, AbstractAdapter>
     */
    public function getAdapters(): array
    {
        return $this->adapters;
    }

    /**
     * @return string
     */
    public function getWpVersion(): string
    {
        return $this->wp_version;
    }

    /**
     * @return LoggerInterface
     */
    public function getLogger() : LoggerInterface
    {
        return $this->logger;
    }

    /**
     * @param LoggerInterface $logger
     */
    public function setLogger(LoggerInterface $logger)
    {
        $this->logger = $logger;
    }

    /**
     * @return array<string, AbstractResult>
     */
    public function getProcessed(): array
    {
        return $this->processed;
    }

    /**
     * @param AbstractAdapter $adapter
     */
    public function add(AbstractAdapter $adapter)
    {
        $this->adapters[$adapter->getId()] = $adapter;
    }

    /**
     * @param string|AbstractAdapter $adapter
     */
    public function remove($adapter)
    {
        if ($adapter instanceof AbstractAdapter) {
            $adapter = $adapter->getId();
        }

        if (is_string($adapter)) {
            unset($this->adapters[$adapter]);
        }
    }

    /**
     * @param string $adapter
     *
     * @return AbstractAdapter|null
     */
    public function get(string $adapter)
    {
        return $this->adapters[$adapter]??null;
    }

    /**
     * @param bool $force
     *
     * @return AbstractResult|null
     */
    public function update(bool $force = false)
    {
        foreach ($this->getAdapters() as $key => $adapter) {
            if (!isset($this->processed[$key]) || $force) {
                $update = $adapter->update($force);
                $this->processed[$key] = $update;
                if (!$update instanceof AbstractInvalidResult
                    && $update instanceof AbstractResult
                ) {
                    return $update;
                }
            }
        }

        return null;
    }
}
