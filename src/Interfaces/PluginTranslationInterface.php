<?php
declare(strict_types=1);

namespace TrayDigita\BaseUpdater\Interfaces;

use TrayDigita\BaseUpdater\Plugin\Translations\Translations;
use TrayDigita\BaseUpdater\Theme\Translations\FileTranslations;

interface PluginTranslationInterface
{
    /**
     * @return Translations|FileTranslations
     */
    public function getPluginTranslations();
}
