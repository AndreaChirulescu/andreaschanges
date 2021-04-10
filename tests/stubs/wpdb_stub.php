<?php
class wpdb {
    public $insert_id = NULL;
    public function insert(string $table, array $data) { $this->insert_id = 1; }
    public function prepare(string $query, mixed $args) { return "prepared"; }
    public function get_results(string $query) { return NULL; }
}

$wpdb = new wpdb();
