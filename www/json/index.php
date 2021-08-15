<?php

use hirohiro716\MyBookmarks\AbstractWebPage;
use hirohiro716\MyBookmarks\Database\Database;
use hirohiro716\MyBookmarks\Auth\Authenticator;
use hirohiro716\MyBookmarks\Bookmark\BookmarkColumn as Column;
use hirohiro716\MyBookmarks\Bookmark\Bookmark;
use hirohiro716\Scent\JSON;
use hirohiro716\Scent\Helper;

require "../vendor/autoload.php";

$result = array("successed" => false);
try {
    if (AbstractWebPage::REQUIRE_SECURE_CONNECTION && Helper::isHTTPS() == false || Authenticator::isAuthenticated() != true) {
        $result["message"] = "Your connection has been refused.";
    } else {
        // Fetch records
        $database = new Database();
        $database->connect();
        $bookmark = new Bookmark($database);
        $records = $bookmark->search(array(), "", "ORDER BY " . Column::const(Column::SORT_NUMBER));
        $result['bookmark'] = array();
        foreach ($records as $record) {
            $result['bookmark'][] = $record->getArray();
        }
        $result['successed'] = true;
    }
} catch (Exception $exception) {
    $result["message"] = $exception->getMessage();
}
echo JSON::fromArray($result);
