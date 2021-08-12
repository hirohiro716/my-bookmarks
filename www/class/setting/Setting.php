<?php
namespace hirohiro716\MyBookmarks\Setting;

use hirohiro716\Scent\Encrypter;
use hirohiro716\Scent\Hash;
use hirohiro716\Scent\Database\AbstractMultipleRecordMapper;
use hirohiro716\Scent\Database\Columns;
use hirohiro716\Scent\Properties;
use hirohiro716\Scent\Validate\ValidationException;
use hirohiro716\Scent\Validate\ValueValidator;
use hirohiro716\Scent\StringObject;
use hirohiro716\Scent\Database\WhereSet;
use hirohiro716\Scent\Helper;
use hirohiro716\MyBookmarks\Setting\SettingName as Name;
use hirohiro716\MyBookmarks\Setting\SettingColumn as Column;
use hirohiro716\MyBookmarks\Database\Database;
use hirohiro716\Scent\Hashes;
use hirohiro716\Scent\Validate\PropertyValidationException;
use hirohiro716\Scent\Validate\CauseProperty;
use hirohiro716\Scent\Filesystem\File;

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
        return Name::properties();
    }
    
    public function getColumns(): Columns
    {
        return Column::columns();
    }
    
    public function createDefaultRecord(): Hash
    {
        $hash = new Hash();
        $rootURL = new StringObject($_SERVER["SCRIPT_NAME"]);
        $hash->put(Name::const(Name::ROOT_URL), $rootURL->replace("try_initialization.php", "")->get());
        $hash->put(Name::const(Name::PASSWORD), "");
        $hash->put(Name::const(Name::AUTHENTICATION_FAILURE_JSON), "");
        return $hash;
    }
    
    public function validate(): void
    {
        $exception = new PropertyValidationException("入力されている情報に不備があります。");
        foreach (Name::properties() as $name) {
            try {
                $validator = new ValueValidator($name->getLogicalName());
                $value = $this->getRecord()->get($name);
                switch ($name) {
                    case Name::const(Name::ROOT_URL):
                    case Name::const(Name::PASSWORD):
                        $validator->addBlankCheck();
                        $validator->execute($value);
                        break;
                    case Name::const(Name::AUTHENTICATION_FAILURE_JSON):
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
     * @param SettingName $name
     * @return string
     */
    public function fetchValue(SettingName $name): string
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
     * @param SettingName $name
     * @param Database $database 接続済みDatabaseインスタンス
     * @return string
     */
    public static function fetchValueStatic(SettingName $name, $database = null): string
    {
        if (Helper::instanceIsThisName($database, "hirohiro716\MyBookmarks\Database\Database") == false) {
            $database = new Database();
            $database->connect();
        }
        $instance = new self($database);
        return $instance->fetchValue($name);
    }
    
    private const ENCRYPT_KEY_FILE_LOCATION = __DIR__ . "/../../database/key";
    
    /**
     * パスワード暗号化用のキーファイルを作成する。
     */
    public static function createFileOfEncryptKey(): void
    {
        $file = new File(self::ENCRYPT_KEY_FILE_LOCATION);
        $file->writeAll(StringObject::createRandomString(32));
        $file->changeMode("0660");
    }
    
    /**
     * パスワード暗号化用のキーを読み込む。
     *
     * @return string パスワード暗号化用のキー
     */
    public static function readEncryptKey(): string
    {
        $file = new File(self::ENCRYPT_KEY_FILE_LOCATION);
        return $file->readAll();
    }
    
    private const ENCRYPT_IV_FILE_LOCATION = __DIR__ . "/../../database/iv";
    
    /**
     * パスワード暗号化用のivファイルを作成する。
     *
     * @param string $iv
     */
    public static function createFileOfEncryptIV(): void
    {
        $file = new File(self::ENCRYPT_IV_FILE_LOCATION);
        $file->writeAll(Encrypter::createIV());
        $file->changeMode("0660");
    }
    
    /**
     * パスワード暗号化用のivを読み込む。
     *
     * @return string パスワード暗号化用のiv
     */
    public static function readEncryptIV(): string
    {
        $file = new File(self::ENCRYPT_IV_FILE_LOCATION);
        return $file->readAll();
    }
    
    /**
     * 設定値を暗号化する。
     *
     * @param string $decrypted
     */
    public static function encrypt(string $decrypted): string
    {
        $encrypter = new Encrypter(Setting::readEncryptKey(), Setting::readEncryptIV());
        return $encrypter->encrypt($decrypted);
    }
    
    /**
     * 設定値を復号化する。
     *
     * @param string $encrypted
     */
    public static function decrypt(string $encrypted): string
    {
        $encrypter = new Encrypter(Setting::readEncryptKey(), Setting::readEncryptIV());
        return $encrypter->decrypt($encrypted);
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
