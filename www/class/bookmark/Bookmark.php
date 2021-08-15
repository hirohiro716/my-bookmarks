<?php
namespace hirohiro716\MyBookmarks\Bookmark;

use hirohiro716\Scent\Hash;
use hirohiro716\Scent\Database\AbstractRecordMapper;
use hirohiro716\Scent\Database\Columns;
use hirohiro716\MyBookmarks\Bookmark\BookmarkColumn as Column;
use Exception;
use hirohiro716\Scent\Validate\PropertyValidationException;
use hirohiro716\Scent\Validate\ValidationException;
use hirohiro716\Scent\Validate\ValueValidator;
use hirohiro716\Scent\Validate\CauseProperty;
use hirohiro716\Scent\StringObject;
use hirohiro716\MyBookmarks\Database\Database;
use hirohiro716\MyBookmarks\Setting\Setting;
use hirohiro716\MyBookmarks\Setting\SettingProperty as Property;
use hirohiro716\Scent\Helper;

/**
 * ブックマーク情報をデータベースに入出力するクラス。
 *
 * @author hiro
 */
class Bookmark extends AbstractRecordMapper
{

    public function getTableName(): string
    {
        return "bookmark";
    }

    public function getColumns(): Columns
    {
        return Column::columns();
    }

    public function createDefaultRecord(): Hash
    {
        $hash = new Hash();
        try {
            $database = new Database();
            $database->connect();
            // icon URL
            $hash->put(Column::const(Column::ICON_URL), Setting::fetchValueStatic(Property::const(Property::ROOT_URL), $database) . "media/favicon.svg");
            // Sort number
            $sql = new StringObject("SELECT MAX(");
            $sql->append(Column::const(Column::SORT_NUMBER));
            $sql->append(") FROM ");
            $sql->append($this->getTableName());
            $sql->append(";");
            $maximumSortNumber = new StringObject($database->fetchOne($sql));
            if (Helper::isNull($maximumSortNumber->toInteger())) {
                $maximumSortNumber->set(0);
            }
            $newSortNumber = new StringObject($maximumSortNumber->toInteger() + 100);
            $newSortNumber = $newSortNumber->subString(0, -2);
            $newSortNumber->append("00");
            $hash->put(Column::const(Column::SORT_NUMBER), $newSortNumber->toInteger());
        } catch (Exception $exception) {
        }
        return $hash;
    }
    
    public function insert(): void
    {
        parent::insert();
        $this->getRecord()->put(Column::const(Column::ID), $this->getDatabase()->fetchLastAutoIncrementID());
    }
    
    public function isDeleted(): bool
    {
        return false;
    }

    public function delete(): void
    {
        parent::physicalDelete();
    }

    public function validate(): void
    {
        $exception = new PropertyValidationException("入力されている情報に不備があります。");
        foreach (Column::columns() as $column) {
            try {
                $validator = new ValueValidator($column->getLogicalName());
                $value = $this->getRecord()->get($column);
                switch ($column) {
                    case Column::const(Column::URL):
                    case Column::const(Column::NAME):
                    case Column::const(Column::ICON_URL):
                        $validator->addBlankCheck();
                        $validator->execute($value);
                        break;
                    case Column::const(Column::SORT_NUMBER):
                        $validator->addBlankCheck();
                        $validator->addIntegerCheck();
                        $validator->execute($value);
                        break;
                    case Column::const(Column::ID):
                    case Column::const(Column::LABELING):
                        break;
                }
            } catch (ValidationException $innerException) {
                $causeProperty = new CauseProperty($column, $innerException->getMessage());
                $exception->addCauseProperty($causeProperty);
            }
        }
        if ($exception->getCauseProperties()->size() > 0) {
            throw $exception;
        }
    }

    public function normalize(): void
    {
        foreach (Column::columns() as $column) {
            switch ($column) {
                case Column::const(Column::ICON_URL):
                    $url = new StringObject($this->getRecord()->get(Column::const(Column::URL)));
                    if ($url->indexOf("javascript:") == 0) {
                        $value = new StringObject($this->getRecord()->get($column));
                        if ($value->subString(0, 1)->equals("/") && $value->indexOf("media/favicon.svg") > 0) {
                            $value = $value->replace("favicon.svg", "script.svg");
                        }
                        $this->getRecord()->put($column, $value->get());
                    }
                    break;
                case Column::const(Column::SORT_NUMBER):
                    $value = new StringObject($this->getRecord()->get($column));
                    $this->getRecord()->put($column, $value->toInteger());
                    break;
                case Column::const(Column::ID):
                case Column::const(Column::URL):
                case Column::const(Column::NAME):
                case Column::const(Column::LABELING):
                    break;
            }
        }
    }
    
}