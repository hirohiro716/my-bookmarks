<?php

use hirohiro716\MyBookmarks\AbstractWebPage;
use hirohiro716\MyBookmarks\Database\Database;
use hirohiro716\MyBookmarks\Auth\Authenticator;
use hirohiro716\MyBookmarks\Bookmark\BookmarkColumn as Column;
use hirohiro716\MyBookmarks\Bookmark\Bookmark;
use hirohiro716\Scent\StringObject;

require "../vendor/autoload.php";

/**
 * ポップアップページのクラス。
 *
 * @author hiro
 */
class PopupIndexPage extends AbstractWebPage
{
    
    public function getTemplateFileLocation(): string
    {
        return "popup/index.tpl";
    }
}

$page = new PopupIndexPage();
if (AbstractWebPage::REQUIRE_SECURE_CONNECTION && $page->isHTTPS() == false) {
    echo "Your connection is not secure.";
    exit();
}
if (Authenticator::isAuthenticated() != true) {
    $url = new StringObject($_SERVER["SCRIPT_NAME"]);
    $referer = "popup/index.php";
    $page->redirect($url->replace($referer, "auth.php") . "?referer=" . $referer);
    exit();
}
// Fetch records
$database = new Database();
$database->connect();
$bookmark = new Bookmark($database);
$records = $bookmark->search(array(), "", "ORDER BY " . Column::const(Column::SORT_NUMBER));
$labelAndBookmarks = array();
foreach ($records as $record) {
    $bookmark = $record->getArray();
    $labeling = new StringObject($record->get(Column::const(Column::LABELING)));
    $labelAndBookmarks[$labeling->get()][] = $bookmark;
}
$page->assign("label_and_bookmarks", $labelAndBookmarks);
$page->display();
