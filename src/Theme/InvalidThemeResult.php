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

namespace TrayDigita\BaseUpdater\Theme;

use TrayDigita\BaseUpdater\Abstracts\AbstractInvalidResult;
use TrayDigita\BaseUpdater\Abstracts\AbstractThemeAdapter;
use WP_Error;

class InvalidThemeResult extends AbstractInvalidResult
{
    /**
     * @param AbstractThemeAdapter $adapter
     * @param WP_Error $reason
     */
    public function __construct(AbstractThemeAdapter $adapter, WP_Error $reason)
    {
        parent::__construct($adapter, $reason);
    }
}
