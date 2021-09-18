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

namespace TrayDigita\BaseUpdater\Theme;

use TrayDigita\BaseUpdater\Abstracts\AbstractAdapter;
use TrayDigita\BaseUpdater\Abstracts\AbstractResult;
use TrayDigita\BaseUpdater\Abstracts\AbstractThemeAdapter;
use WP_Theme;
use function version_compare;

/**
 * @property-read AbstractThemeAdapter $adapter
 * @property-read ThemeResultMetaData $metadata
 * @property-read WP_Theme $theme
 */
class ThemeResult extends AbstractResult
{
    /**
     * @var WP_Theme
     */
    protected $theme;

    /**
     * @param AbstractAdapter $adapter
     * @param WP_Theme $theme
     * @param array $metadata
     */
    public function __construct(
        AbstractAdapter $adapter,
        WP_Theme $theme,
        array $metadata
    ) {
        parent::__construct($adapter, $theme->get_stylesheet());
        $this->theme = $theme;
        $this->metadata = new ThemeResultMetaData(
            $this,
            $metadata
        );
    }

    /**
     * @return WP_Theme
     */
    public function getTheme(): WP_Theme
    {
        return $this->theme;
    }

    public function isNeedUpdate(): bool
    {
        return version_compare(
            $this->theme->get('Version'),
            $this->metadata->getNewVersion()
        );
    }

    /**
     * @return array
     */
    public function getTransient(): array
    {
        $result = ThemeResultMetaData::DEFAULT_RESULT;
        foreach ($result as $key => $v) {
            $result[$key] = $this->metadata->get($key);
        }
        return $result;
    }

    /**
     * @param string $name
     *
     * @return mixed|WP_Theme
     * @inheritDoc
     */
    public function __get(string $name)
    {
        if ($name === 'theme') {
            return $this->getTheme();
        }
        return parent::__get($name);
    }
}
