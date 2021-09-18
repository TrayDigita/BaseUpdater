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
use TrayDigita\BaseUpdater\Theme\Adapter\Noop;
use TrayDigita\BaseUpdater\Theme\InvalidThemeResult;
use TrayDigita\BaseUpdater\Theme\ThemeResult;
use TrayDigita\BaseUpdater\Theme\Translations\FileTranslations;
use WP_Theme;
use function determine_locale;
use function is_object;
use function is_string;

/**
 * @method AbstractThemeAdapter|null get(string $adapter)
 */
abstract class AbstractThemeUpdater extends AbstractUpdater
{
    /**
     * @var string
     */
    protected $mode = 'theme';

    /**
     * @var string
     */
    protected $transient_name = 'update_themes';

    /**
     * @var WP_Theme
     */
    protected $theme;

    /**
     * @var FileTranslations
     */
    protected $translations;

    /**
     * @param WP_Theme $theme
     * @param string $id
     * @param LoggerInterface|null $logger
     * @uses wp_get_theme() if $theme is empty
     */
    final public function __construct(WP_Theme $theme, string $id, LoggerInterface $logger = null)
    {
        parent::__construct($id, $logger);
        $this->theme = $theme;
        $this->translations = new FileTranslations($theme, determine_locale());
    }

    /**
     * @return WP_Theme
     */
    public function getTheme(): WP_Theme
    {
        return $this->theme;
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
        if ($adapter instanceof AbstractThemeAdapter) {
            $this->adapters[$adapter->getId()] = $adapter;
            return true;
        }
        // invalid adapter
        return false;
    }

    /**
     * @param string|AbstractThemeAdapter $adapter
     */
    public function remove($adapter)
    {
        if (is_object($adapter)) {
            if (!$adapter instanceof AbstractThemeAdapter) {
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
     * @return ThemeResult|InvalidThemeResult
     */
    public function update(bool $force = false)
    {
        /**
         * @var ThemeResult|null $result
         */
        $result = parent::update($force);
        return $result??(new Noop($this))->update();
    }
}
