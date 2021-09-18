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

use function call_user_func;

/**
 * @property-read array $transient
 * @property-read AbstractAdapter $adapter
 * @property-read AbstractResultMetaData|null $metadata
 * @mixin AbstractResultMetaData
 */
abstract class AbstractResult
{
    /**
     * @var AbstractAdapter
     */
    protected $adapter;

    /**
     * @var AbstractResultMetaData|null
     */
    protected $metadata = null;

    /**
     * @var string
     */
    protected $slug;

    /**
     * @param AbstractAdapter $adapter
     * @param string $slug
     */
    public function __construct(AbstractAdapter $adapter, string $slug)
    {
        $this->adapter = $adapter;
        $this->slug = $slug;
    }

    /**
     * @return AbstractAdapter
     */
    public function getAdapter(): AbstractAdapter
    {
        return $this->adapter;
    }

    /**
     * @return AbstractResultMetaData|null
     */
    public function getMetadata()
    {
        return $this->metadata;
    }

    /**
     * @return string
     */
    public function getSlug(): string
    {
        return $this->slug;
    }

    /**
     * @return bool
     */
    public function isNeedUpdate() : bool
    {
        return false;
    }

    /**
     * @return array<string, string|bool>
     */
    abstract public function getTransient() : array;

    /**
     * Magic method getting property
     *
     * @param string $name
     *
     * @return mixed
     */
    public function __get(string $name)
    {
        switch ($name) {
            case 'adapter':
                return $this->getAdapter();
            case 'slug':
                return $this->getSlug();
            case 'metadata':
                return $this->getMetadata();
            case 'transient':
                return $this->getTransient();
        }
        $metadata = $this->getMetadata();
        return $metadata ? $metadata->$name : null;
    }

    /**
     * @param string $name
     * @param array $arguments
     *
     * @return mixed|null|false
     * @uses AbstractResultMetaData
     */
    public function __call(string $name, array $arguments)
    {
        $metadata = $this->getMetadata();
        if (!$metadata) {
            return null;
        }
        return call_user_func($name, ...$arguments);
    }
}
