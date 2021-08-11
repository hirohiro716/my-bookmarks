<?php
namespace hirohiro716\MyBookmarks\Database;

use hirohiro716\Scent\Database\SQLite;

/**
 * データベースクラス。
 * 
 * @author hiro
 */
class Database extends SQLite
{
    public function __construct()
    {
        parent::__construct(__DIR__ . "/../../database/database.db");
    }
    
}
