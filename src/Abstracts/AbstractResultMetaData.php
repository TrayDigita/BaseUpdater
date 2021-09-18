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

use function array_merge;
use function is_string;
use function str_replace;
use function strtolower;
use function substr;

/**
 * @property-read AbstractResult $result
 * @property-read array<string, mixed> $metadata
 * @property-read string $version version
 * @property-read string $new_version new version
 * @property-read string $package the zip package
 * @property-read string $slug slug / unique identifier
 * @property-read string $requires require minimum wp version
 * @property-read string $requires_php require minimum PHP version
 */
abstract class AbstractResultMetaData
{
    /**
     * @var AbstractResult
     */
    protected $result;

    /**
     * @var array<string, mixed>
     */
    protected $metadata = [];

    /**
     * @param AbstractResult $result
     * @param array $metadata
     */
    public function __construct(
        AbstractResult $result,
        array $metadata
    ) {
        $this->result = $result;
        $this->metadata = $this->initialize($metadata);
    }

    /**
     * Initialize metadata
     *
     * @param array $metadata
     *
     * @return array
     */
    protected function initialize(array $metadata) : array
    {
        $default = [
            'slug' => '',
            'version' => '',
            'new_version' => '',
            'url' => '',
        ];
        $meta = array_merge($default, $this->metadata);
        if (empty($meta['slug'])) {
            $meta['slug'] = $this->result->getSlug();
        }
        foreach ($metadata as $key => $value) {
            if (! is_string($key)) {
                continue;
            }
            $key = $this->normalizeKey($key);
            $value = $this->normalizeValue($key, $value);
            if (is_string($value)) {
                $value = trim($value);
            }
            if (empty($meta[$key])) {
                $meta[$key] = $value;
            }
        }

        if (!$meta['slug']) {
            $meta['slug'] = $this->result->getSlug();
        }
        if (!$meta['version']) {
            $meta['version'] = $meta['new_version'];
        }
        if (!$meta['new_version']) {
            $meta['new_version'] = $meta['version'];
        }

        return $meta;
    }

    /**
     * @return AbstractResult
     */
    public function getResult(): AbstractResult
    {
        return $this->result;
    }

    /**
     * @return array
     */
    public function getMetadata(): array
    {
        return $this->metadata;
    }

    /***
     * @param string $name
     *
     * @return string
     */
    protected function normalizeKey(string $name) : string
    {
        return strtolower(str_replace(' ', '_', trim($name)));
    }

    /**
     * @param string $key
     * @param $value
     *
     * @return mixed
     */
    protected function normalizeValue(string $key, $value)
    {
        $key = $this->normalizeKey($key);
        switch ($key) {
            case 'slug':
                return is_string($value) && trim($value) !== ''
                    ? trim($value)
                    : $this->result->getSlug();
            default:
                return is_string($value) ? trim($value) : $value;
        }
    }

    /**
     * @param string $name
     *
     * @return mixed|string|array
     */
    public function get(string $name)
    {
        if (isset($this->metadata[$name])) {
            return $this->metadata[$name];
        }

        return $this->metadata[$this->normalizeKey($name)]??'';
    }

    /**
     * @return string
     */
    public function getVersion(): string
    {
        return $this->get('version')?:$this->get('new_version');
    }

    public function getNewVersion(): string
    {
        return $this->get('new_version')?:$this->get('version');
    }

    /**
     * @return string
     */
    public function getRequires(): string
    {
        return $this->get('requires');
    }

    /**
     * @return string
     */
    public function getRequiresPhp(): string
    {
        return $this->get('requires_php');
    }

    /**
     * @return string
     */
    public function getPackage(): string
    {
        $package = $this->get('package');
        return ! is_string($package)
            || substr($package, -4) !== '.zip'
            ? ''
            : $package;
    }

    /**
     * @param string $name
     *
     * @return array|mixed|string|AbstractAdapter
     */
    public function __get(string $name)
    {
        switch ($this->normalizeKey($name)) {
            case 'result':
                return $this->getResult();
            case 'metadata':
                return $this->getMetadata();
            case 'version':
                return $this->getVersion();
            case 'new_version':
                return $this->getNewVersion();
            case 'package':
                return $this->getPackage();
        }

        return $this->get($name);
    }
}
