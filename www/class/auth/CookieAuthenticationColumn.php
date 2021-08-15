<?php
namespace hirohiro716\MyBookmarks\Auth;

use hirohiro716\Scent\Database\AbstractColumn;

/**
 * Cookie認証のカラム定数クラス。
 *
 * @author hiro
 */
class CookieAuthenticationColumn extends AbstractColumn
{

    public const ID = 1;

    public const ACCESS_TIME = 2;
    
    public function getLogicalName(): string
    {
        switch ($this) {
            case self::const(self::ID):
                return "ID";
            case self::const(self::ACCESS_TIME):
                return "アクセス時刻";
        }
    }
    
    public function getTableName(): string
    {
        return CookieAuthentication::getTableNameStatic();
    }
    
}
