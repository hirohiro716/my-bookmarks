<?php
namespace hirohiro716\MyBookmarks\Bookmark;

use hirohiro716\Scent\Database\AbstractColumn;

/**
 * ブックマーク情報カラムのクラス。
 *
 * @author hiro
 */
class BookmarkColumn extends AbstractColumn
{
    
    public const ID = 0;
    
    public const URL = 1;
    
    public const NAME = 2;
    
    public const ICON_URL = 3;
    
    public const LABELING = 4;
    
    public const SORT_NUMBER = 5;
    
    public function getTableName(): string
    {
        return Bookmark::getTableNameStatic();
    }
    
    public function getLogicalName(): string
    {
        switch ($this) {
            case self::const(self::ID):
                return "ID";
            case self::const(self::URL):
                return "URL";
            case self::const(self::NAME):
                return "名前";
            case self::const(self::ICON_URL):
                return "アイコンのURL";
            case self::const(self::LABELING):
                return "ラベル付け";
            case self::const(self::SORT_NUMBER):
                return "ソート番号";
        }
    }
    
}