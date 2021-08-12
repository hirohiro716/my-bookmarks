<?php

use hirohiro716\MyBookmarks\AbstractWebPage;
use hirohiro716\Scent\StringObject;
use hirohiro716\Scent\JSON;
use hirohiro716\MyBookmarks\Setting\Setting;
use hirohiro716\MyBookmarks\Setting\SettingName as Name;
use hirohiro716\Scent\PasswordHasher;
use hirohiro716\MyBookmarks\Database\Database;
use hirohiro716\Scent\Session;
use hirohiro716\Scent\Validate\PropertyValidationException;

require "../vendor/autoload.php";

/**
 * 設定ページのクラス。
 * 
 * @author hiro
 */
class SettingIndexPage extends AbstractWebPage
{
    
    public function getTemplateFileLocation(): string
    {
        return "setting/index.tpl";
    }
    
}

$page = new SettingIndexPage();
if ($page->isHTTPS() == false) {
    // TODO
    /*
    echo "Your connection is not secure.";
    exit();
    */
}
$session = new Session();
if ($session->get("authenticated") !== true) {
    $url = new StringObject($_SERVER["SCRIPT_NAME"]);
    $page->redirect($url->replace("index.php", "auth.php"));
    exit();
}
// Processing of each mode
$post = $page->getPostValues();
$mode = new StringObject($post->get("mode"));
switch ($mode) {
    case "save":
        // Valid token
        if ($session->isValidToken($post->get("token")) == false) {
            header('HTTP', true, 500);
            exit();
        }
        // Save to database
        $result = array("successed" => false);
        try {
            $database = new Database();
            $database->connect();
            $setting = new Setting($database);
            $setting->edit();
            // Password to hash
            $password = new StringObject($post->get(Name::const(Name::PASSWORD)));
            if ($password->length() == 0) {
                $password->set($setting->getRecord()->get(Name::const(Name::PASSWORD)));
            } else {
                $passwordHasher = new PasswordHasher($password);
                $password->set($passwordHasher->getHash());
            }
            $post->put(Name::const(Name::PASSWORD), $password->get());
            // Remove non-existent setting name
            $postArray = array();
            foreach (Name::properties() as $name) {
                if ($post->isExistKey($name)) {
                    $postArray[$name->getPhysicalName()] = $post->get($name);
                }
            }
            $setting->getRecord()->addArray($postArray);
            $setting->validate();
            $setting->normalize();
            $setting->update();
            $result["successed"] = true;
        } catch (PropertyValidationException $exception) {
            $result["message"] = $exception->getDetailMessage();
            $result["cause"] = $exception->toArrayOfCauseMessages()->getArray();
        } catch (Exception $exception) {
            $result["message"] = $exception->getMessage();
        }
        echo JSON::fromArray($result);
        exit();
}
// Create token
$page->assign("token", $session->createToken());
// Fetch values
$database = new Database();
$database->connect();
$setting = new Setting($database);
$setting->edit();
$page->assign("settings", $setting->getRecord()->getArray());
$page->display();

