<!--{extends file="frame.tpl"}-->

<!--{block name=head}-->

<title>設定 - My Bookmarks -</title>

<script type="text/javascript">
$scent(function() {
    $('#bookmarklet_popup').on('click', function() {
        let textArea = $('#bookmarklet textarea');
        textArea.text('');
        textArea.load('<!--{$root}-->setting/bookmarklet_popup.php', function() {
            textArea.text(encodeURI(textArea.text().split('  ').join('')));
            $('#bookmarklet').slideDown('fast');
            textArea.select();
            textArea.focus();
        });
    });
    $('#bookmarklet_add').on('click', function() {
        let textArea = $('#bookmarklet textarea');
        textArea.text('');
        textArea.load('<!--{$root}-->setting/bookmarklet_add.php', function() {
            textArea.text(encodeURI(textArea.text().split('  ').join('')));
            $('#bookmarklet').slideDown('fast');
            textArea.select();
            textArea.focus();
        });
    });
    $('#bookmarklet').find('button').on('click', function() {
        $('#bookmarklet').slideUp('fast');
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
        <div>
            <!--{assign var="key" value="password"}-->
            <label for="<!--{$key}-->">
                パスワード
            </label>
            <input type="password" id="<!--{$key}-->" name="<!--{$key}-->" placeholder="[変更なし]">
        </div>
        <div>
            <label>
                ブックマークレット
            </label>
            <span class="anchors">
                <a href="javascript:;" id="bookmarklet_popup">ポップアップ表示用</a>
                <a href="javascript:;" id="bookmarklet_add">追加用</a>
            </span>
        </div>
        <div>
            <button type="button">保存</button>
        </div>
    </form>
    <div id="bookmarklet" style="display:none;">
        <h3>ブックマークレット</h3>
        <p>下記をブックマークのURL欄に貼り付けてください。</p>
        <textarea></textarea>
        <button type="button" class="tool">閉じる</button>
    </div>
</section>

<!--{/block}-->
