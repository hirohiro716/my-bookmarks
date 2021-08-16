<!--{extends file="frame.tpl"}-->

<!--{block name=head}-->
<title>My Bookmarks</title>

<script src="<!--{$root}-->javascript/bookmark_editor.js"></script>
<script type="text/javascript">
$scent(function() {
    let idToScroll = '<!--{$smarty.get.scroll}-->';
    <!--{if $sent_values_by_get}-->
        idToScroll = 'new_row';
    <!--{/if}-->
    setEventHandler('<!--{$root}-->', '<!--{$token}-->', idToScroll);
});
</script>
<!--{/block}-->

<!--{block name=body}-->

<section id="main_contents" class="whitespace">
    <h2>
        ブックマーク
    </h2>
    <section id="menu">
        <!--{assign var="key" value="keyword"}-->
        <input type="text" id="<!--{$key}-->" name="<!--{$key}-->" value="<!--{$keyword|escape}-->" style="display:none;">
        <a id="search">
            <img title="検索" alt="検索" src="<!--{$root}-->media/search.svg">
        </a>
        <a href="<!--{$root}-->setting/">
            <img title="設定" alt="設定" src="<!--{$root}-->media/setting.svg">
        </a>
        <a href="<!--{$root}-->auth.php?mode=logout">
            <img title="ログアウト" alt="ログアウト" src="<!--{$root}-->media/logout.svg">
        </a>
    </section>
    <section id="rows">
        <!--{if $records->size() > 0}-->
            <!--{foreach from=$records->toArray() item=record}-->
                <div class="row">
                    <div class="left">
                        <p>
                            <!--{assign var="key" value="id"}-->
                            <a id="<!--{$record.$key}-->"></a>
                            <input type="hidden" name="<!--{$key}-->" value="<!--{$record.$key}-->">
                            <!--{assign var="key" value="icon_url"}-->
                            <img class="icon" src="<!--{$record.$key}-->">
                            <input type="hidden" name="<!--{$key}-->" value="<!--{$record.$key}-->" original_value="<!--{$record.$key|escape}-->">
                            <!--{assign var="key" value="name"}-->
                            <input type="text" name="<!--{$key}-->" value="<!--{$record.$key|escape}-->" original_value="<!--{$record.$key|escape}-->" placeholder="WEBサイト名" style="width:calc(100% - 3em);">
                        </p>
                        <p>
                            <!--{assign var="key" value="url"}-->
                            <label for="<!--{$key}-->">URL:</label>
                            <input type="text" name="<!--{$key}-->" value="<!--{$record.$key|escape}-->" original_value="<!--{$record.$key|escape}-->" placeholder="https://www…" style="width:calc(100% - 5em);">
                        </p>
                        <p>
                            <!--{assign var="key" value="labeling"}-->
                            <label for="<!--{$key}-->">ラベル:</label>
                            <input type="text" name="<!--{$key}-->" value="<!--{$record.$key|escape}-->" original_value="<!--{$record.$key|escape}-->" style="width:calc(100% - 8em);">
                        </p>
                        <p>
                            <!--{assign var="key" value="sort_number"}-->
                            <label for="<!--{$key}-->">並び順:</label>
                            <input type="number" name="<!--{$key}-->" value="<!--{$record.$key|escape}-->" original_value="<!--{$record.$key|escape}-->" style="width:5em;">
                        </p>
                    </div>
                    <div class="right">
                        <a class="delete" href="javascript:;">
                            <img title="削除" alt="削除" src="<!--{$root}-->media/delete.svg">
                        </a>
                        <a class="save" href="javascript:;" disabled="disabled">
                            <img title="保存" alt="保存" src="<!--{$root}-->media/save.svg">
                        </a>
                    </div>
                </div>
            <!--{/foreach}-->
        <!--{else}-->
            <p style="margin:3em 0 2em;">
                情報がありません。
            </p>
        <!--{/if}-->
        <div id="new_row" class="row">
            <div class="left">
                <p>
                    <!--{assign var="key" value="icon_url"}-->
                    <img class="icon" src="<!--{$sent_values_by_get.$key}-->">
                    <input type="hidden" name="<!--{$key}-->" value="<!--{$sent_values_by_get.$key|escape}-->">
                    <!--{assign var="key" value="name"}-->
                    <input type="text" name="<!--{$key}-->" value="<!--{$sent_values_by_get.$key|escape}-->" placeholder="WEBサイト名" style="width:calc(100% - 3em);">
                </p>
                <p>
                    <!--{assign var="key" value="url"}-->
                    <label for="<!--{$key}-->">URL:</label>
                    <input type="text" name="<!--{$key}-->" value="<!--{$sent_values_by_get.$key|escape}-->" placeholder="https://www…" style="width:calc(100% - 5em);">
                </p>
                <p>
                    <!--{assign var="key" value="labeling"}-->
                    <label for="<!--{$key}-->">ラベル:</label>
                    <input type="text" name="<!--{$key}-->" value="<!--{$sent_values_by_get.$key|escape}-->" style="width:calc(100% - 8em);">
                </p>
                <p>
                    <!--{assign var="key" value="sort_number"}-->
                    <label for="<!--{$key}-->">並び順:</label>
                    <input type="number" name="<!--{$key}-->" value="<!--{$sent_values_by_get.$key|escape}-->" style="width:5em;">
                </p>
            </div>
            <div class="right">
                <a class="save" href="javascript:;">
                    <img title="保存" alt="保存" src="<!--{$root}-->media/save.svg">
                </a>
            </div>
        </div>
    </section>
    <section>
        <button type="button">追加</button>
    </section>
</section>

<!--{/block}-->
