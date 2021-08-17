<?php
namespace hirohiro716\MyBookmarks\Setting;

use hirohiro716\Scent\Hash;
use hirohiro716\Scent\Database\AbstractMultipleRecordMapper;
use hirohiro716\Scent\Database\Columns;
use hirohiro716\Scent\Properties;
use hirohiro716\Scent\Validate\ValidationException;
use hirohiro716\Scent\Validate\ValueValidator;
use hirohiro716\Scent\StringObject;
use hirohiro716\Scent\Database\WhereSet;
use hirohiro716\Scent\Helper;
use hirohiro716\MyBookmarks\Setting\SettingProperty as Property;
use hirohiro716\MyBookmarks\Setting\SettingColumn as Column;
use hirohiro716\MyBookmarks\Database\Database;
use hirohiro716\Scent\Hashes;
use hirohiro716\Scent\Validate\PropertyValidationException;
use hirohiro716\Scent\Validate\CauseProperty;

/**
 * 設定情報をデータベースに入出力するクラス。
 * 
 * @author hiro
 */
class Setting extends AbstractMultipleRecordMapper
{
    
    public function __construct($database)
    {
        parent::__construct($database);
        parent::addRecord($this->createDefaultRecord());
    }
    
    public function isPermittedSearchConditioEmptyUpdate(): bool
    {
        return true;
    }
    
    public function getTableName(): string
    {
        return "setting";
    }
    
    /**
     * 設定の定数を取得する。
     * 
     * @return Properties
     */
    public function getProperties(): Properties
    {
        return Property::properties();
    }
    
    public function getColumns(): Columns
    {
        return Column::columns();
    }
    
    public function createDefaultRecord(): Hash
    {
        $hash = new Hash();
        $rootURL = new StringObject($_SERVER["SCRIPT_NAME"]);
        $hash->put(Property::const(Property::ROOT_URL), $rootURL->replace("try_initialization.php", "")->get());
        $hash->put(Property::const(Property::PASSWORD), "");
        $hash->put(Property::const(Property::AUTHENTICATION_FAILURE_JSON), "");
        return $hash;
    }
    
    public function validate(): void
    {
        $exception = new PropertyValidationException("入力されている情報に不備があります。");
        foreach (Property::properties() as $name) {
            try {
                $validator = new ValueValidator($name->getLogicalName());
                $value = $this->getRecord()->get($name);
                switch ($name) {
                    case Property::const(Property::ROOT_URL):
                    case Property::const(Property::PASSWORD):
                        $validator->addBlankCheck();
                        $validator->execute($value);
                        break;
                    case Property::const(Property::AUTHENTICATION_FAILURE_JSON):
                        break;
                }
            } catch (ValidationException $innerException) {
                $causeProperty = new CauseProperty($name, $innerException->getMessage());
                $exception->addCauseProperty($causeProperty);
            }
        }
        if ($exception->getCauseProperties()->size() > 0) {
            throw $exception;
        }
    }
    
    public function normalize(): void
    {}
    
    public function edit(string ...$orderByColumns): void
    {
        parent::edit(...$orderByColumns);
        $hash = $this->createDefaultRecord();
        foreach (parent::getRecords() as $record) {
            $hash->put($record->get(Column::const(Column::NAME)), $record->get(Column::const(Column::VALUE)));
        }
        parent::clearRecords();
        parent::addRecord($hash);
    }
    
    public function update(): void
    {
        $record = $this->getRecord();
        parent::clearRecords();
        foreach ($record as $constName => $value) {
            $hash = new Hash();
            $hash->put(Column::const(Column::NAME), $constName);
            $hash->put(Column::const(Column::VALUE), $value);
            parent::addRecord($hash);
        }
        parent::update();
    }
    
    /**
     * レコード情報を取得する。
     *
     * @return Hash
     */
    public function getRecord(): Hash
    {
        parent::getRecords()->rewind();
        return parent::getRecords()->current();
    }
    
    /**
     * レコード情報をセットする。
     *
     * @param Hash $record
     */
    public function setRecord(Hash $record): void
    {
        parent::clearRecords();
        parent::addRecord($record);
    }
    
    /**
     * 設定値を取得する。
     * 
     * @param SettingProperty $name
     * @return string
     */
    public function fetchValue(SettingProperty $name): string
    {
        $whereSet = new WhereSet();
        $whereSet->addEqual(Column::const(Column::NAME)->getPhysicalName(), $name->getPhysicalName());
        $records = $this->search(array($whereSet));
        if ($records->size() == 0) {
            return $this->createDefaultRecord()->get($name);
        }
        $record = $records->current();
        return $record->get(Column::const(Column::VALUE));
    }
    
    /**
     * 設定値を取得する。
     * 
     * @param SettingProperty $name
     * @param Database $database 接続済みDatabaseインスタンス
     * @return string
     */
    public static function fetchValueStatic(SettingProperty $name, $database = null): string
    {
        if (Helper::instanceIsThisName($database, "hirohiro716\MyBookmarks\Database\Database") == false) {
            $database = new Database();
            $database->connect();
        }
        $instance = new self($database);
        return $instance->fetchValue($name);
    }
    
    /**
     * @deprecated
     */
    public function getRecords(): Hashes
    {
        return parent::getRecords();
    }
    
    /**
     * @deprecated
     */
    public function addRecord(Hash $record): void
    {}
    
    /**
     * @deprecated
     */
    public function removeRecord(Hash $record): void
    {}
    
    /**
     * @deprecated
     */
    public function clearRecords(): void
    {}
    
}
