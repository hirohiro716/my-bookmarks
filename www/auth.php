<?php

use hirohiro716\MyBookmarks\AbstractWebPage;
use hirohiro716\Scent\StringObject;
use hirohiro716\Scent\JSON;
use hirohiro716\MyBookmarks\Setting\Setting;
use hirohiro716\MyBookmarks\Setting\SettingName as Name;
use hirohiro716\Scent\PasswordHasher;
use hirohiro716\MyBookmarks\Database\Database;
use hirohiro716\Scent\ArrayHelper;
use hirohiro716\Scent\Session;

require "vendor/autoload.php";

/**
 * ログインページのクラス。
 * 
 * @author hiro
 */
class AuthPage extends AbstractWebPage
{
    
    public function getTemplateFileLocation(): string
    {
        return "auth.tpl";
    }
    
}

$page = new AuthPage();
if ($page->isHTTPS() == false) {
    // TODO
    /*
    echo "Your connection is not secure.";
    exit();
    */
}
// Processing of each mode
$post = $page->getPostValues();
$mode = new StringObject($post->get("mode"));
switch ($mode) {
    case "auth":
        $result = array();
        try {
            $database = new Database();
            $database->connect();
            $setting = new Setting($database);
            // Check the number of authentication failures
            $authenticationFailureArray = array();
            $authenticationFailureJSON = $setting->fetchValue(Name::const(Name::AUTHENTICATION_FAILURE_JSON));
            if ($authenticationFailureJSON) {
                $json = new JSON($authenticationFailureJSON);
                $authenticationFailureArray = $json->toArray();
            }
            $ipAddress = $_SERVER["REMOTE_ADDR"];
            $numberOfAuthenticationFailures = 0;
            if (ArrayHelper::isExistKey($authenticationFailureArray, $ipAddress)) {
                $numberOfAuthenticationFailures = $authenticationFailureArray[$ipAddress];
            }
            if ($numberOfAuthenticationFailures >= 3) {
                header('HTTP', true, 500);
                exit();
            }
            // Verify
            $passwordHash = $setting->fetchValue(Name::const(Name::PASSWORD));
            $passwordHasher = new PasswordHasher($post->get("password"));
            $result["successed"] = $passwordHasher->verify($passwordHash);
            if ($result["successed"]) {
                $authenticationFailureJSON = "";
                $session = new Session();
                $session->put("authenticated", true);
            } else {
                $authenticationFailureArray[$ipAddress] = $numberOfAuthenticationFailures + 1;
                $authenticationFailureJSON = JSON::fromArray($authenticationFailureArray);
            }
            $setting->edit();
            $setting->getRecord()->put(Name::const(Name::AUTHENTICATION_FAILURE_JSON), $authenticationFailureJSON);
            $setting->update();
        } catch (Exception $exception) {
            $result["message"] = $exception->getMessage();
        }
        echo JSON::fromArray($result);
        exit();
}
$page->display();

