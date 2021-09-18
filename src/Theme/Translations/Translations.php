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

namespace TrayDigita\BaseUpdater\Theme\Translations;

use TrayDigita\TranslationMeta\AbstractTranslation;
use TrayDigita\TranslationMeta\AbstractTranslations;
use TrayDigita\BaseUpdater\Interfaces\ThemeTranslationInterface;
use WP_Theme;

/**
 * @method Translation|Translation[]|array[] get(string $td, string $locale = null, string $hash = null)
 */
class Translations extends AbstractTranslations
{
    /**
     * @var WP_Theme
     */
    protected $theme;

    /**
     * @param WP_Theme $theme
     */
    public function __construct(WP_Theme $theme)
    {
        $this->theme = $theme;
    }

    /**
     * @return WP_Theme
     */
    public function getTheme(): WP_Theme
    {
        return $this->theme;
    }

    /**
     * @param AbstractTranslation $translation
     * @param string|null $locale
     *
     * @return bool|string
     * @inheritDoc
     */
    public function add(AbstractTranslation $translation, string $locale = null)
    {
        // not allowed to use another translation
        if (!$translation instanceof ThemeTranslationInterface) {
            return false;
        }
        /**
         * @var AbstractTranslation|ThemeTranslationInterface $translation
         */
        return parent::add($translation, $locale);
    }

    /**
     * @param AbstractTranslation $translation
     * @param string|null $locale
     * @inheritDoc
     */
    public function set(AbstractTranslation $translation, string $locale = null) : string
    {
        // not allowed to use another translation
        if (!$translation instanceof ThemeTranslationInterface) {
            return '';
        }
        /**
         * @var AbstractTranslation|ThemeTranslationInterface $translation
         */
        return parent::set($translation, $locale);
    }
}
