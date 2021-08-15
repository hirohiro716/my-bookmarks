<?php

use hirohiro716\MyBookmarks\AbstractWebPage;
use hirohiro716\Scent\StringObject;
use hirohiro716\Scent\JSON;
use hirohiro716\MyBookmarks\Database\Database;
use hirohiro716\MyBookmarks\Auth\Authenticator;

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
if (AbstractWebPage::REQUIRE_SECURE_CONNECTION && $page->isHTTPS() == false) {
    echo "Your connection is not secure.";
    exit();
}
// Processing of each mode
$request = $page->getRequestValues();
$mode = new StringObject($request->get("mode"));
switch ($mode) {
    case "auth":
        $result = array("successed" => false);
        $password = $page->getPostValue("password");
        try {
            $database = new Database();
            $database->connect();
            if (Authenticator::execute($password, $database)) {
                $result["successed"] = true;
            }
        } catch (Exception $exception) {
            $result["message"] = $exception->getMessage();
        }
        echo JSON::fromArray($result);
        exit();
    case "logout":
        Authenticator::logout();
        $url = new StringObject($_SERVER["SCRIPT_NAME"]);
        $page->redirect($url->replace("auth.php", "index.php"));
        break;
}
$page->display();

