<?php
namespace hirohiro716\MyBookmarks\Setting;

use hirohiro716\Scent\AbstractProperty;

/**
 * 設定名定数クラス。
 * 
 * @author hiro
 */
class SettingName extends AbstractProperty
{
    
    public const ROOT_URL = 0;
    
    public const SETTING_PASSWORD = 1;
    
    public const AUTHENTICATION_FAILURE_JSON = 2;
    
    public function getLogicalName(): string
    {
        switch ($this) {
            case self::const(self::ROOT_URL):
                return "TOPページのURL";
            case self::const(self::SETTING_PASSWORD):
                return "設定パスワード";
            case self::const(self::AUTHENTICATION_FAILURE_JSON):
                return "認証失敗JSON";
        }
    }
    
}
