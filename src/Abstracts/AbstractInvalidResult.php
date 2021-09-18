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

use WP_Error;

/**
 * Base result as invalid
 * @property-read WP_Error $reason
 * @property-read array $context
 */
abstract class AbstractInvalidResult extends AbstractResult
{
    /**
     * @var WP_Error
     */
    protected $reason;

    /**
     * @var array
     */
    protected $context;

    /**
     * @param AbstractAdapter $adapter
     * @param WP_Error $reason
     * @param array $context
     */
    public function __construct(AbstractAdapter $adapter, WP_Error $reason, array $context = [])
    {
        parent::__construct($adapter, '');
        $this->reason = $reason;
        $this->context = $context;
    }

    /**
     * @return WP_Error
     */
    public function getReason() : WP_Error
    {
        return $this->reason;
    }

    /**
     * @return array
     */
    public function getContext(): array
    {
        return $this->context;
    }

    public function getTransient(): array
    {
        return [];
    }

    /**
     * @param string $name
     *
     * @return mixed|WP_Error
     */
    public function __get(string $name)
    {
        switch ($name) {
            case 'reason':
                return $this->getReason();
            case 'context':
                return $this->getContext();
        }

        return parent::__get($name);
    }
}
