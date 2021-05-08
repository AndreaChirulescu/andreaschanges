<?php

// SPDX-FileCopyrightText: 2021 Andrea Chirulescu <andrea.chirulescu@gmail.com>
// SPDX-FileCopyrightText: 2021 Harald Eilertsen <haraldei@anduin.net>
//
// SPDX-License-Identifier: AGPL-3.0-or-later

abstract class GeneratorBase
{
    abstract public function get() : string;

    function pick(array $arr) : string
    {
        return $arr[array_rand($arr)];
    }
}

class BandNameGenerator extends GeneratorBase
{
    /**
     * @var string[]
     *
     * @psalm-var array{0: string, 1: string, 2: string}
     */
    private array $prefixes = array(
        "",
        "a",
        "the",
    );

    /**
     * @var string[]
     *
     * @psalm-var array{0: string, 1: string, 2: string, 3: string, 4: string, 5: string, 6: string, 7: string}
     */
    private array $adverbs = array(
        "bestial",
        "dead",
        "incongruent",
        "ladylike",
        "slimy",
        "dandy",
        "leftist",
        "flamboyant",
    );

    /**
     * @var string[]
     *
     * @psalm-var array{0: string, 1: string, 2: string, 3: string, 4: string, 5: string, 6: string}
     */
    private array $verbs = array(
        "kill",
        "regurgitat",
        "destroy",
        "blasphem",
        "strangl",
        "terroriz",
        "mutilat",
    );

    /**
     * @var string[]
     *
     * @psalm-var array{0: string, 1: string, 2: string, 3: string}
     */
    private array $endings = array(
        "er",
        "ers",
        "ing",
        "ed",
    );

    public function get() : string
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
    /**
     * @var string[]
     *
     * @psalm-var array{0: string, 1: string, 2: string, 3: string, 4: string}
     */
    private array $prefix1 = array(
        "",
        "royal",
        "shabby",
        "happy",
        "drunken",
    );

    /**
     * @var string[]
     *
     * @psalm-var array{0: string, 1: string, 2: string, 3: string, 4: string, 5: string}
     */
    private array $prefix2 = array(
        "",
        "music",
        "fiddler",
        "rock",
        "metal",
        "mental",
    );

    /**
     * @var string[]
     *
     * @psalm-var array{0: string, 1: string, 2: string, 3: string, 4: string, 5: string, 6: string}
     */
    private array $type = array(
        "hall",
        "museum",
        "asylum",
        "stage",
        "cottage",
        "opera",
        "lighthouse"
    );

    public function get() : string
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
    /**
     * @return string
     */
    public function get() : string
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
