<!--{extends file="frame.tpl"}-->

<!--{block name=head}-->

<title>設定 -出荷依頼-</title>

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
        <h3>全般</h3>
        <p>
            <!--{assign var="key" value="setting_password"}-->
            <label for="<!--{$key}-->">
                設定パスワード
            </label>
            <input type="password" id="<!--{$key}-->" name="<!--{$key}-->" placeholder="[変更なし]">
        </p>
        <p>
            <!--{assign var="key" value="receiver_email_address"}-->
            <label for="<!--{$key}-->">
                出荷依頼を受信する<br>E-mailアドレス
            </label>
            <input type="text" id="<!--{$key}-->" name="<!--{$key}-->" value="<!--{$settings.$key}-->">
        </p>
        <h3>E-mail(SMTP)</h3>
        <p>
            <!--{assign var="key" value="email_smtp_sender"}-->
            <label for="<!--{$key}-->">
                E-mailアドレス
            </label>
            <input type="text" id="<!--{$key}-->" name="<!--{$key}-->" value="<!--{$settings.$key}-->">
        </p>
        <p>
            <!--{assign var="key" value="email_smtp_server"}-->
            <label for="<!--{$key}-->">
                サーバー
            </label>
            <input type="text" id="<!--{$key}-->" name="<!--{$key}-->" value="<!--{$settings.$key}-->">
        </p>
        <p>
            <!--{assign var="key" value="email_smtp_is_use_tls"}-->
            <label for="<!--{$key}-->">
                <input type="checkbox" id="<!--{$key}-->" name="<!--{$key}-->" value="<!--{BOOLEAN_VALUE_ENABLED}-->" <!--{if $settings.$key == BOOLEAN_VALUE_ENABLED}-->checked="checked"<!--{/if}-->>
                TLS
            </label>
        </p>
        <p>
            <!--{assign var="key" value="email_smtp_user"}-->
            <label for="<!--{$key}-->">
                ユーザー
            </label>
            <input type="text" id="<!--{$key}-->" name="<!--{$key}-->" value="<!--{$settings.$key}-->">
        </p>
        <p>
            <!--{assign var="key" value="email_smtp_password"}-->
            <label for="<!--{$key}-->">
                パスワード
            </label>
            <input type="password" id="<!--{$key}-->" name="<!--{$key}-->" value="<!--{$settings.$key}-->">
        </p>
        <p>
            <!--{assign var="key" value="email_smtp_port_number"}-->
            <label for="<!--{$key}-->">
                ポート番号
            </label>
            <input type="number" id="<!--{$key}-->" name="<!--{$key}-->" value="<!--{$settings.$key}-->">
        </p>
        <p>
            <button type="button">保存</button>
        </p>
    </form>
</section>

<!--{/block}-->
