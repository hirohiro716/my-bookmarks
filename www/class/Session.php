<?php
namespace hirohiro716\MyBookmarks;

use hirohiro716\Scent;

/**
 * UniquePassword独自のセッションクラス。
 * 
 * @author hiro
 */
class Session extends Scent\Session
{
    
    /**
     * コンストラクタ。
     */
    public function __construct()
    {
        parent::__construct(null, AbstractWebPage::REQUIRE_SECURE_CONNECTION);
    }
    
}
