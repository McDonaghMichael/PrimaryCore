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
            score INT DEFAULT 0,
            gang_name TEXT DEFAULT NULL
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

        $this->db->exec("CREATE TABLE IF NOT EXISTS settings (
            id INTEGER PRIMARY KEY AUTOINCREMENT,
            username TEXT NOT NULL,
            scoreboard BOOLEAN DEFAULT 1,
            announcements BOOLEAN DEFAULT 1
        )");

        $this->db->exec("CREATE TABLE IF NOT EXISTS prison_ranks (
            id INTEGER PRIMARY KEY AUTOINCREMENT,
            username TEXT NOT NULL,
            mine_rank INT DEFAULT 0,
            prestige_level INT DEFAULT 0
            
        )");

        $this->db->exec("CREATE TABLE IF NOT EXISTS gangs (
            id INTEGER PRIMARY KEY AUTOINCREMENT,
            gang_name TEXT NOT NULL,
            description TEXT,
            reputation INT DEFAULT 0,
            public INT DEFAULT 0,
            leader TEXT
        )");

    }

    
    public function getDatabase(): SQLite3 {
        return $this->db;
    }

    public function close(): void {

        $this->db->close();
    }
}
