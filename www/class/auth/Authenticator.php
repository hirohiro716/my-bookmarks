<?php
namespace hirohiro716\MyBookmarks\Auth;

use hirohiro716\Scent\AbstractObject;
use hirohiro716\Scent\Helper;
use hirohiro716\MyBookmarks\Database\Database;
use hirohiro716\MyBookmarks\Setting\Setting;
use hirohiro716\MyBookmarks\Setting\SettingProperty as Property;
use hirohiro716\Scent\JSON;
use hirohiro716\Scent\ArrayHelper;
use hirohiro716\Scent\PasswordHasher;
use hirohiro716\MyBookmarks\Session;

/**
 * 認証情報をセッションに保持するクラス。
 *
 * @author hiro
 */
class Authenticator extends AbstractObject
{

    private const SESSION_KEY_AUTHENTICATED = "session_key_authenticated";
    
    /**
     * 認証を行う。
     *
     * @param string $password パスワード
     * @param Database $database 接続済みDatabaseインスタンス
     * @return bool
     */
    public static function execute(string $password, $database = null): bool
    {
        if (Helper::instanceIsThisName($database, "hirohiro716\MyBookmarks\Database\Database") == false) {
            $database = new Database();
            $database->connect();
        }
        $setting = new Setting($database);
        // Check the number of authentication failures
        $authenticationFailureArray = array();
        $authenticationFailureJSON = $setting->fetchValue(Property::const(Property::AUTHENTICATION_FAILURE_JSON));
        if ($authenticationFailureJSON) {
            $json = new JSON($authenticationFailureJSON);
            $authenticationFailureArray = $json->toArray();
        }
        $ipAddress = $_SERVER["REMOTE_ADDR"];
        $numberOfAuthenticationFailures = 0;
        if (ArrayHelper::existsKey($authenticationFailureArray, $ipAddress)) {
            $numberOfAuthenticationFailures = $authenticationFailureArray[$ipAddress];
        }
        if ($numberOfAuthenticationFailures >= 3) {
            return false;
        }
        // Verify
        $passwordHash = $setting->fetchValue(Property::const(Property::PASSWORD));
        $passwordHasher = new PasswordHasher($password);
        $result = $passwordHasher->verify($passwordHash);
        if ($result) {
            $authenticationFailureJSON = "";
        } else {
            $authenticationFailureArray[$ipAddress] = $numberOfAuthenticationFailures + 1;
            $authenticationFailureJSON = JSON::fromArray($authenticationFailureArray);
        }
        $setting->edit();
        $setting->getRecord()->put(Property::const(Property::AUTHENTICATION_FAILURE_JSON), $authenticationFailureJSON);
        $setting->update();
        if ($result) {
            CookieAuthentication::keepAuthentication($database);
            $session = new Session();
            $session->put(self::SESSION_KEY_AUTHENTICATED, true);
        }
        return $result;
    }
    
    /**
     * 認証済みかどうか確認する。
     *
     * @return bool
     */
    public static function isAuthenticated(): bool
    {
        CookieAuthentication::authenticate();
        $session = new Session();
        if ($session->existsKey(self::SESSION_KEY_AUTHENTICATED)) {
            return $session->get(self::SESSION_KEY_AUTHENTICATED);
        }
        return false;
    }
    
    /**
     * 認証済みかどうかをセットする。
     * 
     * @param bool $isAuthenticated
     */
    public static function setAuthenticated(bool $isAuthenticated): void
    {
        $session = new Session();
        $session->put(self::SESSION_KEY_AUTHENTICATED, $isAuthenticated);
    }

    /**
     * ログアウトする。
     */
    public static function logout()
    {
        CookieAuthentication::deauthenticate();
        $session = new Session();
        $session->remove(self::SESSION_KEY_AUTHENTICATED);
    }
}