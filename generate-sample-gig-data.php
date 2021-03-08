<?php
/*
 * Copyright (C) 2021 Harald Eilertsen, Andrea Chirulescu
 *
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU Affero General Public License as
 * published by the Free Software Foundation, either version 3 of the
 * License, or (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU Affero General Public License for more details.
 *
 * You should have received a copy of the GNU Affero General Public License
 * along with this program.  If not, see <http://www.gnu.org/licenses/>.
 */

abstract class GeneratorBase
{
    abstract public function get();

    function pick($arr)
    {
        return $arr[array_rand($arr)];
    }
}

class BandNameGenerator extends GeneratorBase
{
    private $prefixes = array(
        "",
        "a",
        "the",
    );

    private $adverbs = array(
        "bestial",
        "dead",
        "incongruent",
        "ladylike",
        "slimy",
        "dandy",
        "leftist",
        "flamboyant",
    );

    private $verbs = array(
        "kill",
        "regurgitat",
        "destroy",
        "blasphem",
        "strangl",
        "terroriz",
        "mutilat",
    );

    private $endings = array(
        "er",
        "ers",
        "ing",
        "ed",
    );

    public function get()
    {
        $parts = array(
            $this->pick($this->prefixes),
            $this->pick($this->adverbs),
            $this->pick($this->verbs));

        return trim(implode(" ", $parts) . $this->pick($this->endings));
    }
}

class VenueGenerator extends GeneratorBase
{
    private $prefix1 = array(
        "",
        "royal",
        "shabby",
        "happy",
        "drunken",
    );

    private $prefix2 = array(
        "",
        "music",
        "fiddler",
        "rock",
        "metal",
        "mental",
    );

    private $type = array(
        "hall",
        "museum",
        "asylum",
        "stage",
        "cottage",
        "opera",
        "lighthouse"
    );

    public function get()
    {
        $parts = array(
            $this->pick($this->prefix1),
            $this->pick($this->prefix2),
            $this->pick($this->type));

        return trim(implode(" ", $parts));
    }
}

class LinkGenerator extends GeneratorBase
{
    public function get()
    {
        return 'https://example.com/' . bin2hex(random_bytes(8));
    }
}

$band = new BandNameGenerator();
$venue = new VenueGenerator();
$link = new LinkGenerator();
$date = new DateTime();

for ($i = 0; $i < 10; $i++) {
    $date->add(new DateInterval('P' . random_int(0, 60) . 'D'));

    echo implode("\t", array($band->get(), $venue->get(), $date->format('Y-m-d'), $link->get(), $link->get())) . "\n";
}
