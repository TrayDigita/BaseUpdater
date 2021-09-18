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

abstract class AbstractAdapter
{
    private $id;

    /**
     * @var int|numeric priority of update
     */
    protected $priority = 10;

    /**
     * @var string adapter name
     */
    protected $name = '';

    /**
     * @var string adapter version
     */
    protected $version = '';

    /**
     * @var string adapter description
     */
    protected $description = '';

    /**
     * @var AbstractUpdater
     */
    protected $updater;

    /**
     * @param AbstractUpdater $updater
     * @param string $id
     */
    public function __construct(
        AbstractUpdater $updater,
        string $id = ''
    ) {
        // don't allow new construct for safe usage
        if ($this->id) {
            return;
        }
        $this->updater = $updater;
        // class name
        $className = get_class($this);
        // if id is empty
        $this->id = trim($id)?: strtolower(str_replace('\\', '_', $className));
        // if priority is not a numeric, fallback default
        ! is_numeric($this->priority) && $this->priority = 10;
        // set min for -999
        $this->priority < -999 && $this->priority = -999;
        // make default value when it was not valid name
        if (!$this->name || ! is_string($this->name)) {
            $this->name = strstr($className, '\\', true);
        }
        // do construct
        $this->onConstruct();
    }

    /**
     * Method called on construct
     */
    public function onConstruct()
    {
        // override here
    }

    /**
     * @return string
     */
    public function getId(): string
    {
        return $this->id;
    }

    /**
     * @return float|int|string
     */
    public function getPriority()
    {
        return $this->priority;
    }

    /**
     * @return string
     */
    public function getName() : string
    {
        return $this->name;
    }

    /**
     * @return string
     */
    public function getVersion(): string
    {
        return $this->version;
    }

    /**
     * @return string
     */
    public function getDescription(): string
    {
        return $this->description;
    }

    /**
     * @return AbstractUpdater
     */
    public function getUpdater(): AbstractUpdater
    {
        return $this->updater;
    }

    /**
     * @param bool $force
     *
     * @return AbstractResult|object
     */
    abstract public function update(bool $force = false);
}
