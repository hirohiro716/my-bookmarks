<?php
namespace hirohiro716\MyBookmarks\Setting;

use hirohiro716\Scent\Database\AbstractColumn;

/**
 * 設定情報カラムのクラス。
 *
 * @author hiro
 */
class SettingColumn extends AbstractColumn
{
    
    public const NAME = 0;
    
    public const VALUE = 1;
    
    public function getTableName(): string
    {
        return Setting::getTableNameStatic();
    }
    
    public function getLogicalName(): string
    {
        switch ($this) {
            case self::const(self::NAME):
                return "設定名";
            case self::const(self::VALUE):
                return "設定値";
        }
    }
    
}