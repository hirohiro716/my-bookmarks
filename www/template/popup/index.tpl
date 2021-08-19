<!DOCTYPE html>
<html lang="ja">

<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0, minimum-scale=1.0, maximum-scale=10.0, user-scalable=yes">
<title>My Bookmarks</title>
<link rel="icon" href="<!--{$root}-->media/favicon.svg">
<link rel="stylesheet" href="<!--{$root}-->css/popup.css">
<script src="//ajax.googleapis.com/ajax/libs/jquery/3.3.1/jquery.min.js"></script>
<script src="<!--{$root}-->javascript/popup.js"></script>
<script type="text/javascript">
$(window).bind('load', function() {
    setEventHandler();
});
</script>


</head>

<body>
    <h1><img title="My Bookmarks" alt="My Bookmarks" src="<!--{$root}-->media/logo.svg"></h1>
    <section id="bookmarks">
        <!--{foreach from=$label_and_bookmarks key=labeling item=bookmarks}-->
            <!--{assign var="isLabeling" value=$labeling|strlen > 0}-->
            <!--{if $isLabeling}-->
                <div class="group">
                    <a class="label">
                        <img src="<!--{$root}-->media/directory.svg">
                        <!--{$labeling}-->
                    </a>
                    <!--{foreach from=$bookmarks item=bookmark}-->
                        <!--{assign var="key" value="url"}-->
                        <a class="bookmark" url="<!--{$bookmark.$key}-->">
                            <!--{assign var="key" value="icon_url"}-->
                            <img src="<!--{$bookmark.$key}-->">
                            <!--{assign var="key" value="name"}-->
                            <!--{$bookmark.$key}-->
                        </a>
                    <!--{/foreach}-->
                </div>
            <!--{else}-->
                    <!--{foreach from=$bookmarks item=bookmark}-->
                        <!--{assign var="key" value="url"}-->
                        <a class="bookmark" url="<!--{$bookmark.$key}-->">
                            <!--{assign var="key" value="icon_url"}-->
                            <img src="<!--{$bookmark.$key}-->">
                            <!--{assign var="key" value="name"}-->
                            <!--{$bookmark.$key}-->
                        </a>
                    <!--{/foreach}-->
            <!--{/if}-->
        <!--{/foreach}-->
    </section>
</body>

</html>
