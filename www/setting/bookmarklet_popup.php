<?php
use hirohiro716\MyBookmarks\AbstractWebPage;

require "../vendor/autoload.php";

class BookmarkletPopup extends AbstractWebPage
{
    public function getTemplateFileLocation(): string
    {
        return "setting/bookmarklet_popup.tpl";
    }
    
}

$page = new BookmarkletPopup();
if ($page->isHTTPS()) {
    $page->assign("protocol", "https://");
} else {
    $page->assign("protocol", "http://");
}
$page->assign("server", $_SERVER["SERVER_NAME"]);
$page->display();
