<!DOCTYPE NETSCAPE-Bookmark-file-1>
<META HTTP-EQUIV="Content-Type" CONTENT="text/html; charset=UTF-8">
<TITLE>My Bookmarks</TITLE>
<H1>My Bookmarks</H1>
<!--{foreach from=$label_and_bookmarks key=labeling item=bookmarks}-->
    <DL><p>
        <DT><H3><!--{$labeling}--></H3>
        <DL><p>
        <!--{foreach from=$bookmarks item=bookmark}-->
            <DT><A HREF="<!--{$bookmark.href}-->"><!--{$bookmark.text}--></A>
        <!--{/foreach}-->
        </DL><p>
<!--{/foreach}-->
