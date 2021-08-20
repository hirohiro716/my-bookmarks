<!--{extends file="frame.tpl"}-->

<!--{block name=head}-->

<title>認証 - My Bookmarks -</title>

<script type="text/javascript">
$scent(function() {
    $('button:contains("認証")').bind('click', function() {
        let button = $(this);
        button.prop('disabled', true);
        $scent.postForm($('form'), function(result) {
            if (result.successed) {
                window.location.href = '<!--{$root}-->' + result.referer;
            } else {
                alert('認証できませんでした。');
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
        認証
    </h2>
    <form method="post">
        <!--{assign var="key" value="mode"}-->
        <input type="hidden" id="<!--{$key}-->" name="<!--{$key}-->" value="auth">
        <p>
            <!--{assign var="key" value="password"}-->
            <label for="<!--{$key}-->">パスワード</label>
            <input type="password" id="<!--{$key}-->" name="<!--{$key}-->">
        </p>
        <p>
            <button type="button">認証</button>
        </p>
    </form>
</section>

<!--{/block}-->
