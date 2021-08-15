<?php
namespace hirohiro716\MyBookmarks;

use hirohiro716\Scent;
use hirohiro716\Scent\Filesystem\Directory;
use hirohiro716\MyBookmarks\Database\Database;
use hirohiro716\MyBookmarks\Setting\Setting;
use hirohiro716\MyBookmarks\Setting\SettingProperty;
use hirohiro716\Scent\Helper;

abstract class AbstractWebPage extends Scent\Smarty\AbstractWebPage
{
    
    /* TODO
    public const REQUIRE_SECURE_CONNECTION = true;
     */
    
    public const REQUIRE_SECURE_CONNECTION = false;
    
    public function __construct()
    {
        parent::__construct();
        date_default_timezone_set("Asia/Tokyo");
        $this->assign("root", Setting::fetchValueStatic(SettingProperty::const(SettingProperty::ROOT_URL)));
        $this->assign("class", Helper::findInstanceName($this));
        define("BOOLEAN_VALUE_ENABLED", Database::BOOLEAN_VALUE_ENABLED);
        define("BOOLEAN_VALUE_DISABLED", Database::BOOLEAN_VALUE_DISABLED);
    }
    
    public function getTemplateDirectory(): string
    {
        $templateDirectory = new Directory(__DIR__ . "/../template/");
        return $templateDirectory->getAbsoluteLocation();
    }
    
    public function getCompileDirectory(): string
    {
        $compileDirectory = new Directory(__DIR__ . "/../template/compile/");
        return $compileDirectory->getAbsoluteLocation();
    }
    
    public function getLeftDelimiter(): string
    {
        return "<!--{";
    }
    
    public function getRightDelimiter(): string
    {
        return "}-->";
    }
    
}
