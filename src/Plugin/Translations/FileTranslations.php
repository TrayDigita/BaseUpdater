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

namespace TrayDigita\BaseUpdater\Plugin\Translations;

use TrayDigita\TranslationMeta\AbstractTranslation;
use TrayDigita\TranslationMeta\AbstractTranslations;
use TrayDigita\BaseUpdater\Plugin\PluginInfo;
use function determine_locale;

/**
 * @method FileTranslation|FileTranslation[]|array[] get(string $td, string $locale = null, string $hash = null)
 */
class FileTranslations extends AbstractTranslations
{
    /**
     * @var PluginInfo
     */
    protected $plugin;

    /**
     * @var string
     */
    protected $locale;

    /**
     * @param PluginInfo $plugin
     * @param string $locale
     */
    public function __construct(PluginInfo $plugin, string $locale = null)
    {
        $this->locale = $locale?: determine_locale();
        $this->plugin = $plugin;
    }

    /**
     * @return PluginInfo
     */
    public function getPlugin(): PluginInfo
    {
        return $this->plugin;
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
