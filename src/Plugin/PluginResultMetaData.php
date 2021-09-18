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

use TrayDigita\BaseUpdater\Abstracts\AbstractResultMetaData;
use function array_filter;
use function is_array;
use function is_string;
use function preg_match;
use function strtolower;

/**
 * @property-read string $id
 * @property-read string $slug
 * @property-read string $plugin
 * @property-read string $version
 * @property-read string $new_version
 * @property-read string $url
 * @property-read string $package
 * @property-read array<string, string> $icons
 * @property-read array<string, string> $banners
 * @property-read array<string, string> $banners_rtl
 * @property-read string $requires
 * @property-read string $requires_php
 * @property-read array $compatibility
 */
class PluginResultMetaData extends AbstractResultMetaData
{
    const DEFAULT_RESULT = [
        'id'     => '',   // url without protocol,
        'slug'   => '', // plugin slug of wp url
        'plugin' => '', // plugin slug to file dir/file.php
        'new_version' => '', // the new version
        'url' => '', // plugin url
        'package' => '', // plugin zip url
        'icons' => [], // contains 'default','2x','1x','svg'
        'banners' => [], // contains 'default','2x','1x','svg'
        'banners_rtl' => [],
        'requires' => '', // min wp version
        'tested' => '', // tested with
        'requires_php' => '', // required minimum php
        "compatibility" => [] // list of compatibility
    ];

    /**
     * @var array<string, string|array|mixed>
     */
    protected $metadata = self::DEFAULT_RESULT;

    /**
     * @param PluginResult $result
     * @param array $metadata
     */
    public function __construct(PluginResult $result, array $metadata)
    {
        parent::__construct($result, $metadata);
    }

    /**
     * @param string $key
     * @param mixed $value
     *
     * @return mixed
     */
    protected function normalizeValue(string $key, $value)
    {
        $key = $this->normalizeKey($key);
        switch ($key) {
            case 'banner':
            case 'banners_rtl':
            case 'icons':
                if (is_string($value)) {
                    preg_match(
                        '~^https?://[^/]+/.+\.(png|jpe?g|webp|svg)(?:[?].*)?$~i',
                        $value,
                        $match
                    );
                    if (!empty($match[1])) {
                        $keyName = strtolower($match[1]);
                        $value = [$keyName => $value];
                    }
                } elseif (is_array($value)) {
                    $values = [];
                    if (!empty($v['1x'])) {
                        $values['1x'] = $value['1x'];
                    }
                    if (!empty($v['2x'])) {
                        $values['2x'] = $value['2x'];
                    }
                    if (!empty($v['svg'])) {
                        $values['svg'] = $value['svg'];
                    }
                    $value = $values;
                }
                break;
            case 'compatibility':
                $value = ! is_array($value) ? [$value] : $value;
                $value = array_filter($value, 'is_string');
                break;
        }
        // compat
        if (empty($meta['version']) && isset($meta['new_version'])) {
            $meta['version'] = $meta['new_version'];
        }
        if (empty($meta['new_version']) && isset($meta['version'])) {
            $meta['new_version'] = $meta['version'];
        }
        return $value;
    }
}
