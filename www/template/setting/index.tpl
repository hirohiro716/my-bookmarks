<!--{extends file="frame.tpl"}-->

<!--{block name=head}-->

<title>設定 - My Bookmarks -</title>

<script type="text/javascript">
$scent(function() {
    $('#bookmarklet_add').on('click', function() {
        $('textarea#bookmarklet').load('<!--{$root}-->setting/bookmarklet_add.php', function() {
            let textArea = $('textarea#bookmarklet');
            textArea.text(encodeURI(textArea.text().split('  ').join('')));
            prompt('作成したスクリプト', textArea.text());
        });
    });
    $('button:contains("保存")').bind('click', function() {
        let button = $(this);
        button.prop('disabled', true);
        $scent.postForm($('form'), function(result) {
            if (result.successed) {
                alert('保存しました。');
                window.location.reload();
            } else {
                alert(result.message);
                if (result.cause) {
                    $scent.setErrors(result.cause);
                }
            }
            button.prop('disabled', false);
        },
        function() {
            alert('通信に失敗しました。');
            button.prop('disabled', false);
        });
    });
});
</script>

<!--{/block}-->

<!--{block name=body}-->

<section id="main_contents" class="whitespace">
    <h2>
        設定
    </h2>
    <form method="post">
        <!--{assign var="key" value="token"}-->
        <input type="hidden" id="<!--{$key}-->" name="<!--{$key}-->" value="<!--{$token}-->">
        <!--{assign var="key" value="mode"}-->
        <input type="hidden" id="<!--{$key}-->" name="<!--{$key}-->" value="save">
        <p>
            <!--{assign var="key" value="password"}-->
            <label for="<!--{$key}-->">
                パスワード
            </label>
            <input type="password" id="<!--{$key}-->" name="<!--{$key}-->" placeholder="[変更なし]">
        </p>
        <p>
            <label>
                ブックマークレット
            </label>
            <a href="javascript:;" id="bookmarklet_add">ブックマーク追加用</a>
            <textarea id="bookmarklet" style="display:none;">
            </textarea>
        </p>
        <p>
            <button type="button">保存</button>
        </p>
    </form>
</section>

<!--{/block}-->
