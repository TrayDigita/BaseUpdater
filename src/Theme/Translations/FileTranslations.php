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
use WP_Theme;
use function determine_locale;

/**
 * @method FileTranslation|FileTranslation[]|array[] get(string $td, string $locale = null, string $hash = null)
 */
class FileTranslations extends AbstractTranslations
{
    /**
     * @var array<string, array<string, array<string, AbstractTranslation>>>
     */
    protected $translations = [];

    /**
     * @var WP_Theme
     */
    protected $theme;

    /**
     * @var string
     */
    protected $locale;

    /**
     * @var bool
     */
    private $translated = false;

    /**
     * @param WP_Theme $theme
     * @param string|null $locale
     */
    public function __construct(WP_Theme $theme, string $locale = null)
    {
        $this->locale = $locale?: determine_locale();
        $this->theme = $theme;
    }

    /**
     * @return static
     */
    public function initTranslations() : FileTranslations
    {
        if ($this->translated) {
            return $this;
        }

        $this->translated = true;
        $stylesheet = $this->theme->get_stylesheet();
        $textdomain = $this->theme->get('TextDomain');
        $domainpath = $this->theme->get('DomainPath');
        $theme_language_path = $this->theme->get_stylesheet_directory();
        if ($domainpath) {
            $theme_language_path .= $domainpath;
        } else {
            /**
             * @see \WP_Theme::load_textdomain()
             */
            $theme_language_path .= '/languages';
        }

        $locale = $this->getLocale();
        if (file_exists("$theme_language_path/$locale.mo")) {
            $this->add(
                new FileTranslation($this, "$theme_language_path/$locale.mo"),
                $locale
            );
        }
        if (file_exists("$theme_language_path/$locale.po")) {
            $this->add(
                new FileTranslation($this, "$theme_language_path/$locale.po"),
                $locale
            );
        }

        if (is_dir(WP_LANG_DIR)) {
            $dir = WP_LANG_DIR ."/themes/$stylesheet";
            if (is_file("$dir/$textdomain-$locale.po")) {
                $this->add(
                    new FileTranslation(
                        $this,
                        "$dir/$textdomain-$locale.po"
                    ),
                    $locale
                );
            }
            if (is_file("$dir/$textdomain-$locale.mo")) {
                $this->add(
                    new FileTranslation(
                        $this,
                        "$dir/$textdomain-$locale.mo"
                    ),
                    $locale
                );
            }
        }

        return $this;
    }

    public function getTranslations(): array
    {
        // do init
        $this->initTranslations();
        return parent::getTranslations();
    }

    /**
     * @return WP_Theme
     */
    public function getTheme(): WP_Theme
    {
        return $this->theme;
    }

    /**
     * @return string
     */
    public function getLocale(): string
    {
        return $this->locale;
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
        if (!$translation instanceof FileTranslation) {
            return false;
        }

        return parent::add($translation, $locale);
    }

    /**
     * @param AbstractTranslation $translation
     * @param string $locale
     * @inheritDoc
     */
    public function set(AbstractTranslation $translation, string $locale = null) : string
    {
        // not allowed to use another translation
        if (!$translation instanceof FileTranslation) {
            return '';
        }

        return parent::set($translation, $locale);
    }
}
