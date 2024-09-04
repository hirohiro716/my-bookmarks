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
use hirohiro716\MyBookmarks\Setting\Setting;
use hirohiro716\MyBookmarks\Setting\SettingProperty as Property;
use hirohiro716\Scent\Helper;
use hirohiro716\Scent\ArrayHelper;
use hirohiro716\MyBookmarks\Database\Database;

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
    
    private const SORT_NUMBER_STEP = 100;

    public function createDefaultRecord(): Hash
    {
        $hash = new Hash();
        try {
            $database = $this->getDatabase();
            if (Helper::isNull($database) == false) {
                // icon URL
                $hash->put(Column::const(Column::ICON_URL), Setting::fetchValueStatic(Property::const(Property::ROOT_URL), $database) . "media/internet.svg");
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
                $newSortNumber = new StringObject($maximumSortNumber->toInteger() + self::SORT_NUMBER_STEP);
                $newSortNumber = $newSortNumber->subString(0, -2);
                $newSortNumber->append("00");
                $hash->put(Column::const(Column::SORT_NUMBER), $newSortNumber->toInteger());
            }
        } catch (Exception $exception) {
        }
        return $hash;
    }
    
    /**
     * 現在のレコードにセットされているURLを元に直下のアイコンURLを設定する。
     */
    public function setIconOfDirectlyBelow(): void
    {
        $url = new StringObject($this->getRecord()->get(Column::const(Column::URL)));
        $parts = $url->split("://");
        if ($url->length() == 0 || ArrayHelper::count($parts) < 2) {
            return;
        }
        $protocol = $parts[0];
        $domain = new StringObject($parts[1]);
        $domain = new StringObject($domain->split("/")[0]);
        $iconURL = $protocol . "://" . $domain . "/favicon.ico";
        if (Helper::existsURL($iconURL, 1)) {
            $this->getRecord()->put(Column::const(Column::ICON_URL), $iconURL);
        }
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
                        if ($value->subString(0, 1)->equals("/") && $value->indexOf("media/internet.svg") > 0) {
                            $value = $value->replace("internet.svg", "script.svg");
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
    
    /**
     * 現在の並び順を維持したまま並び順の番号を再設定する。
     *
     * @param Database $database 接続済みDatabaseインスタンス
     */
    public static function renumberOfSort(Database $database): void
    {
        $sql = new StringObject("SELECT * FROM ");
        $sql->append(self::getTableNameStatic());
        $sql->append(" ORDER BY ");
        $sql->append(Column::const(Column::SORT_NUMBER));
        $sql->append(", ");
        $sql->append(Column::const(Column::ID));
        $sql->append(";");
        $records = $database->fetchRecords($sql);
        $labelAndRecords = array();
        foreach ($records as $record) {
            $labeling = new StringObject($record->get(Column::const(Column::LABELING)));
            $labelAndRecords[$labeling->get()][] = $record;
        }
        $sortNumber = self::SORT_NUMBER_STEP;
        foreach ($labelAndRecords as $records) {
            foreach ($records as $record) {
                $sqlForUpdate = new StringObject("UPDATE ");
                $sqlForUpdate->append(self::getTableNameStatic());
                $sqlForUpdate->append(" SET ");
                $sqlForUpdate->append(Column::const(Column::SORT_NUMBER));
                $sqlForUpdate->append(" = ");
                $sqlForUpdate->append($sortNumber);
                $sqlForUpdate->append(" WHERE ");
                $sqlForUpdate->append(Column::const(Column::ID));
                $sqlForUpdate->append(" = ");
                $sqlForUpdate->append($record->get(Column::const(Column::ID)));
                $sqlForUpdate->append(";");
                $database->execute($sqlForUpdate);
                $sortNumber += self::SORT_NUMBER_STEP;
            }
        }
    }
}