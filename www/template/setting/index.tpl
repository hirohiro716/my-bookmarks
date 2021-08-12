<!--{extends file="frame.tpl"}-->

<!--{block name=head}-->

<title>設定 - My Bookmarks -</title>

<script type="text/javascript">
$scent(function() {
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
            <button type="button">保存</button>
        </p>
    </form>
</section>

<!--{/block}-->
