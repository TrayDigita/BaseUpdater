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

namespace TrayDigita\BaseUpdater\Plugin\Adapter;

use TrayDigita\BaseUpdater\Abstracts\AbstractPluginAdapter;
use TrayDigita\BaseUpdater\Plugin\InvalidPluginResult;
use WP_Error;

class Noop extends AbstractPluginAdapter
{
    /**
     * @inheritDoc
     */
    public function update(bool $force = false) : InvalidPluginResult
    {
        return new InvalidPluginResult($this, new WP_Error(
            'tray-digita:updater'
        ));
    }
}
