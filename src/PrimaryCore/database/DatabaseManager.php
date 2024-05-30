<?php

namespace PrimaryCore\database;

use SQLite3;

class DatabaseManager {
    private SQLite3 $db;

    public function __construct(string $path) {
        $this->db = new SQLite3($path);
        $this->createTables();
    }

    private function createTables(): void {

        $this->db->exec("CREATE TABLE IF NOT EXISTS players (
            id INTEGER PRIMARY KEY AUTOINCREMENT,
            username TEXT NOT NULL,
            server_Rank INT DEFAULT 0,
            score INT DEFAULT 0
        )");


        $this->db->exec("CREATE TABLE IF NOT EXISTS server_ranks (
            id INTEGER PRIMARY KEY AUTOINCREMENT,
            username TEXT NOT NULL,
            rank INTEGER
        )");

        $this->db->exec("CREATE TABLE IF NOT EXISTS economy (
            id INTEGER PRIMARY KEY AUTOINCREMENT,
            username TEXT NOT NULL,
            coins INT DEFAULT 0
        )");

    }

    public function getDatabase(): SQLite3 {
        return $this->db;
    }

    public function close(): void {

        $this->db->close();
    }
}