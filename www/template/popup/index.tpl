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
    <h1><img title="My Bookmarks" alt="My Bookmarks" src="<!--{$root}-->media/logo.svg"></h1>
    <section id="bookmarks">
        <!--{foreach from=$label_and_bookmarks key=labeling item=bookmarks}-->
            <!--{assign var="isLabeling" value=$labeling|strlen > 0}-->
            <!--{if $isLabeling}-->
                <div class="group">
                    <a class="label">
                        <img class="icon" src="<!--{$root}-->media/directory.svg">
                        <!--{$labeling}-->
                    </a>
            <!--{/if}-->
            <!--{foreach from=$bookmarks item=bookmark}-->
                <!--{assign var="key_id" value="id"}-->
                <!--{assign var="key_url" value="url"}-->
                <a class="bookmark" id="<!--{$bookmark.$key_id}-->" url="<!--{$bookmark.$key_url}-->">
                    <!--{assign var="key" value="icon_url"}-->
                    <img class="icon" src="<!--{$root}-->media/internet.svg" data-src="<!--{$bookmark.$key}-->">
                    <span class="name">
                        <!--{assign var="key" value="name"}-->
                        <!--{$bookmark.$key}-->
                    </span>
                    <img class="edit" src="<!--{$root}-->media/edit.svg">
                </a>
            <!--{/foreach}-->
            <!--{if $isLabeling}-->
                </div>
            <!--{/if}-->
        <!--{/foreach}-->
    </section>
</body>

</html>
