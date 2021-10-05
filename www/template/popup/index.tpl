<!DOCTYPE html>
<html lang="ja">

<head>

<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0, minimum-scale=1.0, maximum-scale=10.0, user-scalable=yes">

<link rel="icon" href="<!--{$root}-->media/favicon.svg">

<link rel="stylesheet" href="<!--{$root}-->css/popup.css">

<script src="//ajax.googleapis.com/ajax/libs/jquery/3.3.1/jquery.min.js"></script>
<script src="<!--{$root}-->javascript/popup.js"></script>

<script type="text/javascript">
$(window).bind('load', function() {
    setEventHandler();
});
</script>

<title>My Bookmarks</title>

</head>

<body>
    <section id="wait_circle">
        <img src="<!--{$root}-->media/wait_circle.svg">
    </section>
    <h1><img title="My Bookmarks" alt="My Bookmarks" src="<!--{$root}-->media/logo.svg"></h1>
    <section id="bookmarks">
        <!--{foreach from=$label_and_bookmarks key=labeling item=bookmarks}-->
            <!--{assign var="isLabeling" value=$labeling|strlen > 0}-->
            <!--{if $isLabeling}-->
                <div class="group">
                    <a class="label">
                        <img class="icon" src="<!--{$root}-->media/directory.svg">
                        <span class="name">
                            <!--{$labeling}-->
                        </span>
                    </a>
            <!--{/if}-->
            <!--{foreach from=$bookmarks item=bookmark}-->
                <!--{assign var="key" value="id"}-->
                <div id="<!--{$bookmark.$key}-->" class="bookmark">
                    <!--{assign var="key" value="url"}-->
                    <a url="<!--{$bookmark.$key}-->">
                        <!--{assign var="key" value="icon_url"}-->
                        <!--{if $bookmark.$key|substr:0:1 == "/"}-->
                            <img class="icon" src="<!--{$bookmark.$key}-->">
                        <!--{else}-->
                            <img class="icon" src="<!--{$root}-->media/internet.svg" data-src="<!--{$bookmark.$key}-->">
                        <!--{/if}-->
                        <span class="name">
                            <!--{assign var="key" value="name"}-->
                            <!--{$bookmark.$key}-->
                        </span>
                    </a>
                    <img class="edit" src="<!--{$root}-->media/edit.svg">
                    <img class="open_in_window" src="<!--{$root}-->media/new_window.svg">
                </div>
            <!--{/foreach}-->
            <!--{if $isLabeling}-->
                </div>
            <!--{/if}-->
        <!--{/foreach}-->
    </section>
</body>

</html>
