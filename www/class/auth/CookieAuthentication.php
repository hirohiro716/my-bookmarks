<?php
namespace hirohiro716\MyBookmarks\Auth;

use hirohiro716\Scent\Hash;
use hirohiro716\Scent\Database\AbstractRecordMapper;
use hirohiro716\Scent\Database\Columns;
use Exception;
use hirohiro716\Scent\Validate\PropertyValidationException;
use hirohiro716\Scent\Validate\ValidationException;
use hirohiro716\Scent\Validate\ValueValidator;
use hirohiro716\Scent\Validate\CauseProperty;
use hirohiro716\Scent\StringObject;
use hirohiro716\MyBookmarks\Database\Database;
use hirohiro716\MyBookmarks\Setting\Setting;
use hirohiro716\Scent\Helper;
use hirohiro716\MyBookmarks\Auth\CookieAuthenticationColumn as Column;
use hirohiro716\MyBookmarks\Setting\SettingProperty as Property;
use hirohiro716\Scent\PasswordHasher;
use hirohiro716\Scent\Database\WhereSet;
use hirohiro716\Scent\Datetime;
use hirohiro716\MyBookmarks\AbstractWebPage;
use hirohiro716\Scent\Cookie;

/**
 * Cookie認証情報をデータベースに入出力するクラス。
 *
 * @author hiro
 */
class CookieAuthentication extends AbstractRecordMapper
{
    
    private const LIFETIME_SECOND = 60 * 60 * 24 * 90;
    
    public function getTableName(): string
    {
        return "cookie_authentication";
    }

    public function getColumns(): Columns
    {
        return Column::columns();
    }

    public function createDefaultRecord(): Hash
    {
        $hash = new Hash();
        $datetime = new Datetime();
        $hash->put(Column::const(Column::ACCESS_TIME), $datetime->toDatetimeString());
        return $hash;
    }
    
    public function insert(): void
    {
        self::encryptRecord($this->getRecord());
        parent::insert();
    }
    
    /**
     * レコード情報を暗号化する。
     *
     * @param Hash $hash
     */
    public static function encryptRecord(Hash $hash): void
    {
        $physicalName = Column::const(Column::ID);
        $hash->put($physicalName, self::encrypt($hash->get($physicalName)));
    }
    
    /**
     * IDを暗号化する。
     *
     * @param string $string
     */
    public static function encrypt(string $string): string
    {
        $hasher = new PasswordHasher($string);
        return $hasher->getHash();
    }
    
    public function isDeleted(): bool
    {
        return false;
    }

    public function update(): void
    {
        self::encryptRecord($this->getRecord());
        parent::update();
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
                    case Column::const(Column::ID):
                    case Column::const(Column::ACCESS_TIME):
                        $validator->addBlankCheck();
                        $validator->execute($value);
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
            $value = $this->getRecord()->get($column);
            switch ($column) {
                case Column::const(Column::ACCESS_TIME):
                    $datetime = new Datetime($value);
                    $this->getRecord()->put($column, $datetime->toDatetimeString());
                    break;
                case Column::const(Column::ID):
                    break;
            }
        }
    }
    
    private const COOKIE_KEY = "ID";
    
    /**
     * 認証を実行する。
     * 
     * @param Database $database
     */
    public static function authenticate($database = null): void
    {
        if (Helper::instanceIsThisName($database, "hirohiro716\MyBookmarks\Database\Database") == false) {
            $database = new Database();
            $database->connect();
        }
        // 期限切れの情報を削除
        $whereSet = new WhereSet();
        $lowerLimit = new Datetime();
        $lowerLimit->addSecond(self::LIFETIME_SECOND * -1);
        $whereSet->addLess(Column::const(Column::ACCESS_TIME), $lowerLimit->toDatetimeString());
        $sql = new StringObject("DELETE FROM ");
        $sql->append(CookieAuthentication::getTableNameStatic());
        $sql->append(" WHERE ");
        $sql->append($whereSet->buildParameterClause());
        $database->execute($sql, $whereSet->buildParameters());
        // 認証情報を取得する
        try {
            $cookie = new Cookie();
            $id = new StringObject($cookie->get(self::COOKIE_KEY));
            $instance = new CookieAuthentication($database);
            $foundRecord = null;
            $hasher = new PasswordHasher($id);
            $records = $instance->search(array());
            foreach ($records as $record) {
                if ($hasher->verify($record->get(Column::const(Column::ID)))) {
                    $foundRecord = $record;
                    break;
                }
            }
            if (Helper::isNull($foundRecord)) {
                return;
            }
            $requireChangeDatetime = new Datetime($foundRecord->get(Column::const(Column::ACCESS_TIME)));
            $requireChangeDatetime->addSecond(self::LIFETIME_SECOND * 0.8);
            if ($requireChangeDatetime->toTimestamp() < Datetime::currentTime()) {
                $whereSet = new WhereSet();
                $whereSet->addEqual(Column::const(Column::ID), $foundRecord->get(Column::const(Column::ID)));
                $instance->setWhereSet($whereSet);
                $instance->edit();
                $instance->delete();
                self::keepAuthentication($database);
            }
            Authenticator::setAuthenticated(true);
        } catch (Exception $exception) {
        }
    }
    
    /**
     * 認証状態を保持する。
     * 
     * @param Database $database
     */
    public static function keepAuthentication($database = null): void
    {
        if (Helper::instanceIsThisName($database, "hirohiro716\MyBookmarks\Database\Database") == false) {
            $database = new Database();
            $database->connect();
        }
        $newId = StringObject::createRandomString(64);
        $rootURL = Setting::fetchValueStatic(Property::const(Property::ROOT_URL));
        $cookie = new Cookie($rootURL, self::LIFETIME_SECOND, AbstractWebPage::REQUIRE_SECURE_CONNECTION);
        $cookie->put(self::COOKIE_KEY, $newId->get());
        $cookieAuthentication = new CookieAuthentication($database);
        $cookieAuthentication->getRecord()->put(Column::const(Column::ID), $newId->get());
        $cookieAuthentication->insert();
    }
    
    /**
     * 認証状態を解除する。
     * 
     * @param Database $database
     */
    public static function deauthenticate($database = null): void
    {
        if (Helper::instanceIsThisName($database, "hirohiro716\MyBookmarks\Database\Database") == false) {
            $database = new Database();
            $database->connect();
        }
        try {
            $whereSet = new WhereSet();
            $cookie = new Cookie();
            $id = new StringObject($cookie->get(self::COOKIE_KEY));
            $instance = new CookieAuthentication($database);
            $foundRecord = null;
            $hasher = new PasswordHasher($id);
            $records = $instance->search(array());
            foreach ($records as $record) {
                if ($hasher->verify($record->get(Column::const(Column::ID)))) {
                    $foundRecord = $record;
                    break;
                }
            }
            if (Helper::isNull($foundRecord)) {
                return;
            }
            $whereSet = new WhereSet();
            $whereSet->addEqual(Column::const(Column::ID), $foundRecord->get(Column::const(Column::ID)));
            $instance->setWhereSet($whereSet);
            $instance->edit();
            $instance->delete();
            $rootURL = Setting::fetchValueStatic(Property::const(Property::ROOT_URL));
            $cookie = new Cookie($rootURL, null, AbstractWebPage::REQUIRE_SECURE_CONNECTION);
            $cookie->remove(self::COOKIE_KEY);
        } catch (Exception $exception) {
        }
    }
}