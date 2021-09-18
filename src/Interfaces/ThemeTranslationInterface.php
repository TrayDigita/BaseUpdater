<?php
declare(strict_types=1);

namespace TrayDigita\BaseUpdater\Interfaces;

use TrayDigita\BaseUpdater\Theme\Translations\FileTranslations;
use TrayDigita\BaseUpdater\Theme\Translations\Translations;

interface ThemeTranslationInterface
{
    /**
     * @return Translations|FileTranslations
     */
    public function getThemeTranslations();
}
