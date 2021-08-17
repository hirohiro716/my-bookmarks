<?php

use hirohiro716\MyBookmarks\AbstractWebPage;
use hirohiro716\Scent\StringObject;
use hirohiro716\MyBookmarks\Database\Database;
use hirohiro716\MyBookmarks\Auth\Authenticator;
use hirohiro716\MyBookmarks\Bookmark\BookmarkColumn as Column;
use hirohiro716\MyBookmarks\Bookmark\Bookmark;
use hirohiro716\Scent\JSON;
use hirohiro716\Scent\Validate\PropertyValidationException;
use hirohiro716\Scent\Database\WhereSet;
use hirohiro716\MyBookmarks\Session;

require "vendor/autoload.php";

class IndexPage extends AbstractWebPage
{
    public function getTemplateFileLocation(): string
    {
        return "index.tpl";
    }
    
}

$page = new IndexPage();
if (AbstractWebPage::REQUIRE_SECURE_CONNECTION && $page->isHTTPS() == false) {
    echo "Your connection is not secure.";
    exit();
}
if (Authenticator::isAuthenticated() != true) {
    $url = new StringObject($_SERVER["SCRIPT_NAME"]);
    $page->redirect($url->replace("index.php", "auth.php"));
    exit();
}
// Processing of each mode
$session = new Session();
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
            $database->beginTransaction();
            $bookmark = new Bookmark($database);
            $id = new StringObject($post->get(Column::const(Column::ID)));
            $isEdit = $id->length() > 0;
            if ($isEdit) {
                $whereSet = new WhereSet();
                $whereSet->addEqual(Column::const(Column::ID), $id->toInteger());
                $bookmark->setWhereSet($whereSet);
                $bookmark->edit();
            }
            $bookmark->getRecord()->addArray($post->getArray());
            $bookmark->validate();
            $bookmark->normalize();
            if ($isEdit) {
                $bookmark->update();
            } else {
                $bookmark->insert();
            }
            $database->commit();
            $result["successed"] = true;
        } catch (PropertyValidationException $exception) {
            $result["message"] = $exception->getDetailMessage();
            $result["cause"] = $exception->toArrayOfCauseMessages()->getArray();
        } catch (Exception $exception) {
            $result["message"] = $exception->getMessage();
        }
        echo JSON::fromArray($result);
        exit();
    case "delete":
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
            $database->beginTransaction();
            $bookmark = new Bookmark($database);
            $id = new StringObject($post->get(Column::const(Column::ID)));
            $whereSet = new WhereSet();
            $whereSet->addEqual(Column::const(Column::ID), $id->toInteger());
            $bookmark->setWhereSet($whereSet);
            $bookmark->delete();
            $database->commit();
            $result["successed"] = true;
        } catch (Exception $exception) {
            $result["message"] = $exception->getMessage();
        }
        echo JSON::fromArray($result);
        exit();
    case "fetch_default_record":
        // Valid token
        if ($session->isValidToken($post->get("token")) == false) {
            header('HTTP', true, 500);
            exit();
        }
        // Fetch from database
        $result = array("successed" => false);
        try {
            $database = new Database();
            $database->connect();
            $bookmark = new Bookmark($database);
            $result["record"] = $bookmark->getRecord()->getArray();
            $result["successed"] = true;
        } catch (Exception $exception) {
            $result["message"] = $exception->getMessage();
        }
        echo JSON::fromArray($result);
        exit();
    case "import_from_json":
        // Valid token
        if ($session->isValidToken($post->get("token")) == false) {
            header('HTTP', true, 500);
            exit();
        }
        // Import from JSON
        $result = array("successed" => false);
        try {
            $database = new Database();
            $database->connect();
            $database->beginTransaction();
            $json = new JSON($post->get("json"));
            foreach ($json->toArray() as $element) {
                try {
                    $bookmark = new Bookmark($database);
                    $record = $bookmark->getRecord();
                    $record->put(Column::const(Column::URL), $element["a_href"]);
                    $record->put(Column::const(Column::NAME), $element["a_text"]);
                    $record->put(Column::const(Column::LABELING), $element["h3_text"]);
                    $bookmark->setIconOfDirectlyBelow();
                    $bookmark->validate();
                    $bookmark->normalize();
                    $bookmark->insert();
                } catch (PropertyValidationException $exception) {
                }
            }
            $database->commit();
            $result["successed"] = true;
        } catch (Exception $exception) {
            $result["message"] = $exception->getMessage();
        }
        echo JSON::fromArray($result);
        exit();
}
// Create token
$page->assign("token", $session->createToken());
// Fetch records
$get = $page->getGetValues();
$keyword = new StringObject($get->get("keyword"));
$whereSetArray = array();
if ($keyword->length() > 0) {
    $keyword = $keyword->sanitize();
    $page->assign("keyword", $keyword->get());
    foreach (Column::values() as $column) {
        switch ($column) {
            case Column::const(Column::URL):
            case Column::const(Column::NAME):
            case Column::const(Column::ICON_URL):
            case Column::const(Column::LABELING):
                $whereSet = new WhereSet();
                $whereSet->addLike("LOWER(" . $column . ")", "%" . $keyword->toLower() . "%");
                $whereSetArray[] = $whereSet;
                break;
        }
    }
}
$database = new Database();
$database->connect();
$bookmark = new Bookmark($database);
$records = $bookmark->search($whereSetArray, "", "ORDER BY " . Column::const(Column::SORT_NUMBER));
$page->assign("records", $records);
// Initial value sent by GET
$isSentInitialValueByGET = false;
foreach (Column::columns() as $column) {
    if ($get->isExistKey($column)) {
        $isSentInitialValueByGET = true;
    }
}
if ($isSentInitialValueByGET) {
    $sentValues = $bookmark->getRecord();
    $sentValues->addArray($get->getArray());
    $page->assign("sent_values_by_get", $sentValues->getArray());
}
$page->display();
