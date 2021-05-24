<?php
// SPDX-FileCopyrightText: 2021 Andrea Chirulescu <andrea.chirulescu@gmail.com>
// SPDX-FileCopyrightText: 2021 Harald Eilertsen <haraldei@anduin.net>
//
// SPDX-License-Identifier: AGPL-3.0-or-later

namespace EternalTerror\ViewHelpers;

/**
 * Return HTML code for a selction box with the given options and preselected value.
 *
 * @param string    $name     The name attribute for the selection box
 * @param array     $opts     The options as arrays of [value, label] pairs
 * @param mixed|int $selected The value of the preselected option, or null if no
 *                            option is preselected.
 * @return string
 */
function select_field(string $name, ?array $opts = [], $selected = null) : string
{
    $body = '';
    foreach ($opts as $opt) {
        $sel = selected($selected, $opt[0], false);
        $body .= "<option value=\"{$opt[0]}\"{$sel}>{$opt[1]}</option>";
    }
    return "<select name=\"{$name}\">{$body}</select>";
}
