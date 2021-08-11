<!--{extends file="frame.tpl"}-->

<!--{block name=head}-->

<title>出荷依頼</title>

<script type="text/javascript" src="https://ajaxzip3.github.io/ajaxzip3.js"></script>
<script type="text/javascript">
$scent(function() {
    $('input[name*=_address]').bind('focus click', function(event) {
        if ($(this).val().length > 0) {
            return;
        }
        let zipcode = $(this).parent().parent().find('input[name*="_zipcode"]');
        AjaxZip3.zip2addr(zipcode[0],'', this, this);
    });
    $('#receiver a:contains("注文者を入力")').bind('click', function() {
        $('#orderer input').each(function(index, current) {
            let id = $(current).attr('id');
            id = id.replace('orderer', 'receiver');
            $('#' + id).val($(current).val());
        });
    });
    $('#product a:contains("追加")').bind('click', function() {
        let maximumProductNumber = 1;
        let rows = $('tr');
        rows.each(function(index, current) {
            let currentProductNumber = parseInt($(current).attr('product_number'));
            if (maximumProductNumber < currentProductNumber) {
                maximumProductNumber = currentProductNumber;
            }
        });
        let productNumber = maximumProductNumber + 1;
        let trs = $('tr[product_number="1"]').clone();
        trs.attr('product_number', productNumber);
        trs.find('input').each(function(index, current) {
            let input = $(current);
            input.attr('id', input.attr('id').replace('1', productNumber));
            input.attr('name', input.attr('name').replace('1', productNumber));
            input.val('');
        });
        $('table').append(trs);
    });
    $('#product').on('click', 'a:contains("削除")', function() {
        if ($('tr').length == 2) {
            return;
        }
        let productNumber = parseInt($(this).parent().parent().attr('product_number'));
        $('tr[product_number="' + productNumber + '"]').remove();
        $('tr').each(function(index, currentTr) {
            let tr = $(currentTr);
            let currentProductNumber = parseInt(tr.attr('product_number'));
            if (productNumber < currentProductNumber) {
                newProductNumber = currentProductNumber - 1;
                tr.attr('product_number', newProductNumber);
                
                tr.find('input').each(function(index, currentInput) {
                    let input = $(currentInput);
                    input.attr('id', input.attr('id').replace(currentProductNumber, newProductNumber));
                    input.attr('name', input.attr('name').replace(currentProductNumber, newProductNumber));
                });
            }
        });
    });
    $('input#delivery_date').datepicker({dateFormat: 'yy-mm-dd'});
    $('button:contains("送信")').bind('click', function() {
        $('input').css('box-shadow', '');
        let button = $(this);
        button.prop('disabled', true);
        $scent.postForm($('form'), function(result) {
            if (result.successed) {
                alert('送信しました。');
                window.location.href = '<!--{$smarty.server.SCRIPT_NAME}-->';
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
    <form method="post">
        <!--{assign var="key" value="mode"}-->
        <input type="hidden" id="<!--{$key}-->" name="<!--{$key}-->" value="send">
        
        <section id="basic">
            <p>
                <!--{assign var="key" value="reply_email_address"}-->
                <label for="<!--{$key}-->">
                    返信E-mailアドレス
                </label>
                <input type="text" id="<!--{$key}-->" name="<!--{$key}-->" value="<!--{$defaultValues.$key}-->">
            </p>
        </section>
        <section id="orderer" class="block">
            <h2>注文者</h2>
            <p>
                <!--{assign var="key" value="orderer_name"}-->
                <label for="<!--{$key}-->">
                    お名前
                </label>
                <input type="text" id="<!--{$key}-->" name="<!--{$key}-->" value="<!--{$defaultValues.$key}-->">
                <!--{assign var="key" value="orderer_honorific"}-->
                <input type="text" id="<!--{$key}-->" name="<!--{$key}-->" value="<!--{$defaultValues.$key}-->">
            </p>
            <p>
                <!--{assign var="key" value="orderer_zipcode"}-->
                <label for="<!--{$key}-->">
                    郵便番号
                </label>
                <input type="tel" id="<!--{$key}-->" name="<!--{$key}-->" value="<!--{$defaultValues.$key}-->" maxlength="7">
            </p>
            <p>
                <!--{assign var="key" value="orderer_address"}-->
                <label for="<!--{$key}-->">
                    住所
                </label>
                <input type="text" id="<!--{$key}-->" name="<!--{$key}-->" value="<!--{$defaultValues.$key}-->">
            </p>
            <p>
                <!--{assign var="key" value="orderer_building"}-->
                <label for="<!--{$key}-->">
                    建物名
                </label>
                <input type="text" id="<!--{$key}-->" name="<!--{$key}-->" value="<!--{$defaultValues.$key}-->">
            </p>
            <p>
                <!--{assign var="key" value="orderer_tel"}-->
                <label for="<!--{$key}-->">
                    電話番号
                </label>
                <input type="tel" id="<!--{$key}-->" name="<!--{$key}-->" value="<!--{$defaultValues.$key}-->">
            </p>
        </section>
        <section id="receiver" class="block">
            <div class="title">
                <h2>受取人</h2>
                <a href="javascript:;">注文者を入力</a>
            </div>
            <p>
                <!--{assign var="key" value="receiver_name"}-->
                <label for="<!--{$key}-->">
                    お名前
                </label>
                <input type="text" id="<!--{$key}-->" name="<!--{$key}-->" value="<!--{$defaultValues.$key}-->">
                <!--{assign var="key" value="receiver_honorific"}-->
                <input type="text" id="<!--{$key}-->" name="<!--{$key}-->" value="<!--{$defaultValues.$key}-->">
            </p>
            <p>
                <!--{assign var="key" value="receiver_zipcode"}-->
                <label for="<!--{$key}-->">
                    郵便番号
                </label>
                <input type="tel" id="<!--{$key}-->" name="<!--{$key}-->" value="<!--{$defaultValues.$key}-->" maxlength="7">
            </p>
            <p>
                <!--{assign var="key" value="receiver_address"}-->
                <label for="<!--{$key}-->">
                    住所
                </label>
                <input type="text" id="<!--{$key}-->" name="<!--{$key}-->" value="<!--{$defaultValues.$key}-->">
            </p>
            <p>
                <!--{assign var="key" value="receiver_building"}-->
                <label for="<!--{$key}-->">
                    建物名
                </label>
                <input type="text" id="<!--{$key}-->" name="<!--{$key}-->" value="<!--{$defaultValues.$key}-->">
            </p>
            <p>
                <!--{assign var="key" value="receiver_tel"}-->
                <label for="<!--{$key}-->">
                    電話番号
                </label>
                <input type="tel" id="<!--{$key}-->" name="<!--{$key}-->" value="<!--{$defaultValues.$key}-->">
            </p>
        </section>
        <section id="delivery" class="block">
            <h2>配送について</h2>
            <p>
                <!--{assign var="key" value="is_collect"}-->
                <label for="<!--{$key}-->">
                    <input type="checkbox" id="<!--{$key}-->" name="<!--{$key}-->" value="<!--{BOOLEAN_VALUE_ENABLED}-->" <!--{if $defaultValues.$key == BOOLEAN_VALUE_ENABLED}-->checked="checked"<!--{/if}-->>
                    代金引換の利用
                </label>
            </p>
            <p>
                <!--{assign var="key" value="transport_method"}-->
                <label for="<!--{$key}-->">
                    運搬方法
                </label>
                <select id="<!--{$key}-->" name="<!--{$key}-->">
                    <!--{assign var="optionValue" value="冷凍"}-->
                    <option value="<!--{$optionValue}-->" <!--{if $defaultValues.$key == $optionValue}-->selected="selected"<!--{/if}-->><!--{$optionValue}--></option>
                    <!--{assign var="optionValue" value="冷蔵"}-->
                    <option value="<!--{$optionValue}-->" <!--{if $defaultValues.$key == $optionValue}-->selected="selected"<!--{/if}-->><!--{$optionValue}--></option>
                    <!--{assign var="optionValue" value="常温"}-->
                    <option value="<!--{$optionValue}-->" <!--{if $defaultValues.$key == $optionValue}-->selected="selected"<!--{/if}-->><!--{$optionValue}--></option>
                </select>
            </p>
            <p>
                <!--{assign var="key" value="delivery_date"}-->
                <label for="<!--{$key}-->">到着日：</label>
                <input type="text" id="<!--{$key}-->" name="<!--{$key}-->" value="<!--{$defaultValues.$key}-->" readonly="readonly">
            </p>
            <p>
                <!--{assign var="key" value="delivery_timezone"}-->
                <label for="<!--{$key}-->">
                    到着時間帯
                </label>
                <select id="<!--{$key}-->" name="<!--{$key}-->">
                    <option value="">指定なし</option>
                    <!--{assign var="optionValue" value="午前中"}-->
                    <option value="<!--{$optionValue}-->" <!--{if $defaultValues.$key == $optionValue}-->selected="selected"<!--{/if}-->><!--{$optionValue}--></option>
                    <!--{assign var="optionValue" value="14時から16時"}-->
                    <option value="<!--{$optionValue}-->" <!--{if $defaultValues.$key == $optionValue}-->selected="selected"<!--{/if}-->><!--{$optionValue}--></option>
                    <!--{assign var="optionValue" value="16時から18時"}-->
                    <option value="<!--{$optionValue}-->" <!--{if $defaultValues.$key == $optionValue}-->selected="selected"<!--{/if}-->><!--{$optionValue}--></option>
                    <!--{assign var="optionValue" value="18時から20時"}-->
                    <option value="<!--{$optionValue}-->" <!--{if $defaultValues.$key == $optionValue}-->selected="selected"<!--{/if}-->><!--{$optionValue}--></option>
                    <!--{assign var="optionValue" value="19時から21時"}-->
                    <option value="<!--{$optionValue}-->" <!--{if $defaultValues.$key == $optionValue}-->selected="selected"<!--{/if}-->><!--{$optionValue}--></option>
                </select>
            </p>
            <p>
                <!--{assign var="key" value="memo"}-->
                <label for="<!--{$key}-->">メモ：</label>
                <textarea id="<!--{$key}-->" name="<!--{$key}-->"><!--{$defaultValues.$key}--></textarea>
            </p>
        </section>
        <section id="product" class="block">
            <div class="title">
                <h2>商品について</h2>
                <a href="javascript:;">追加</a>
            </div>
            <table>
                <!--{assign var="number" value=1}-->
                <!--{while $defaultValues["product_code"|cat:$number]}-->
                    <tr product_number="<!--{$number}-->">
                        <td>
                            <!--{assign var="key" value="product_code"|cat:$number}-->
                            <input type="number" id="<!--{$key}-->" name="<!--{$key}-->" value="<!--{$defaultValues.$key}-->" placeholder="No.">
                            <!--{assign var="key" value="product_name"|cat:$number}-->
                            <input type="text" id="<!--{$key}-->" name="<!--{$key}-->" value="<!--{$defaultValues.$key}-->" placeholder="商品名">
                        </td>
                    </tr>
                    <tr product_number="<!--{$number}-->">
                        <td>
                            <!--{assign var="key" value="product_price"|cat:$number}-->
                            <input type="number" id="<!--{$key}-->" name="<!--{$key}-->" value="<!--{$defaultValues.$key}-->" placeholder="価格">
                            ×
                            <!--{assign var="key" value="product_quantity"|cat:$number}-->
                            <input type="number" id="<!--{$key}-->" name="<!--{$key}-->" value="<!--{$defaultValues.$key}-->" placeholder="数量">
                            <a class="product_delete" href="javascript:;">削除</a>
                        </td>
                    </tr>
                    <!--{assign var="number" value=$number+1}-->
                <!--{/while}-->
                <tr product_number="<!--{$number}-->">
                    <td>
                        <!--{assign var="key" value="product_code"|cat:$number}-->
                        <input type="number" id="<!--{$key}-->" name="<!--{$key}-->" value="<!--{$defaultValues.$key}-->" placeholder="No.">
                        <!--{assign var="key" value="product_name"|cat:$number}-->
                        <input type="text" id="<!--{$key}-->" name="<!--{$key}-->" value="<!--{$defaultValues.$key}-->" placeholder="商品名">
                    </td>
                </tr>
                <tr product_number="<!--{$number}-->">
                    <td>
                        <!--{assign var="key" value="product_price"|cat:$number}-->
                        <input type="number" id="<!--{$key}-->" name="<!--{$key}-->" value="<!--{$defaultValues.$key}-->" placeholder="価格">
                        ×
                        <!--{assign var="key" value="product_quantity"|cat:$number}-->
                        <input type="number" id="<!--{$key}-->" name="<!--{$key}-->" value="<!--{$defaultValues.$key}-->" placeholder="数量">
                        <a class="product_delete" href="javascript:;">削除</a>
                    </td>
                </tr>
            </table>
        </section>
        <section class="buttons">
            <button type="button">送信</button>
        </section>
    </form>
</section>

<!--{/block}-->
