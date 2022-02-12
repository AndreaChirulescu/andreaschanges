<?php declare(strict_types=1);
// SPDX-FileCopyrightText: 2022 Andrea Chirulescu <andrea.chirulescu@gmail.com>
// SPDX-FileCopyrightText: 2022 Harald Eilertsen <haraldei@anduin.net>
//
// SPDX-License-Identifier: AGPL-3.0-or-later

function display_giglog() : string
{
    $c = new GiglogAdmin_ConcertsTable();
    return $c->render();
}

add_shortcode('getconcerts', 'display_giglog');
?>
