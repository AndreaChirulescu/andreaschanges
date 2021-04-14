// SPDX-FileCopyrightText: 2021 Andrea Chirulescu <andrea.chirulescu@gmail.com>
// SPDX-FileCopyrightText: 2021 Harald Eilertsen <haraldei@anduin.net>
//
// SPDX-License-Identifier: AGPL-3.0-or-later

<?php
class wpdb {
    public $insert_id = NULL;
    public function insert(string $table, array $data) { $this->insert_id = 1; }
    public function prepare(string $query, mixed $args) { return "prepared"; }
    public function get_results(string $query) { return NULL; }
}

$wpdb = new wpdb();
