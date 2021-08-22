/**
 * Set event handler.
 */
function setEventHandler(url, token, idToScroll) {
    /*
     * Search button
     */
    $('a#search').on('click', function() {
        let input = $('input#keyword');
        if (input.css('display') == 'none') {
            input.css('opacity', 0.5);
            input.css('display', 'inline-block');
            input.css('width', '0em');
            input.animate({'width':'8em', 'opacity':'1'}, 300, function() {
                input.focus();
                input.select();
            });
        } else {
            input.fadeOut(200);
        }
    });
    $('input#keyword').on('keyup', function(event) {
        if (event.keyCode == 13) {
            let input = $(this);
            location.href = url + '?keyword=' + input.val();
        }
    });
    /*
     * Sub menu
     */
    let subMenu = $('<div></div>');
    subMenu.css('position', 'fixed');
    subMenu.css('width', '15em');
    subMenu.css('background-color', 'rgba(0,0,0,0.6)');
    subMenu.css('border-radius', '0.7em');
    subMenu.hide();
    $('body').append(subMenu);
    $('a#submenu').on('click', function() {
        subMenu.css('top', event.pageY + 'px');
        subMenu.css('left', (event.pageX - subMenu.width()) + 'px');
        subMenu.slideToggle(100);
    });
    function addSubMenuItem(name, functionOfSubMenuItem) {
        let item = $('<a href="javascript:;"></a>');
        item.css('padding', '1em');
        item.css('display', 'block');
        if (subMenu.children().length > 0) {
            item.css('border-top', '1px solid #fff');
        }
        item.css('color', '#fff');
        item.css('text-decoration', 'none');
        item.text(name);
        item.bind('click', function(event) {
            subMenu.slideToggle(100);
        });
        item.bind('click', functionOfSubMenuItem);
        subMenu.append(item);
    }
    /*
     * Enable / disable the save button
     */
    let previousRow;
    function changedInputValue(inputOrSelect) {
        let isChanged = (inputOrSelect.val() !== inputOrSelect.attr('original_value'));
        let row = inputOrSelect.parents('.row');
        if (isChanged) {
            row.find('.save').removeAttr('disabled');
            // Undo previous row
            if (typeof previousRow !== 'undefined' && previousRow.is(row) == false) {
                if (previousRow.attr('id') == 'new_row') {
                    previousRow.fadeOut();
                    $('button:contains("追加")').fadeIn();
                } else {
                    previousRow.find('a.save').attr('disabled', 'disabled');
                    previousRow.find('input, select').each(function(index, current) {
                        let inputOrSelect = $(current);
                        let originalValue = inputOrSelect.attr('original_value');
                        inputOrSelect.val(originalValue);
                        let img = inputOrSelect.prev('img');
                        if (img.length > 0) {
                            img.attr('src', originalValue);
                        }
                    });
                }
            }
            previousRow = row;
        } else {
            row.find('.save').attr('disabled', 'disabled');
        }
    }
    $('#rows input, #rows select').on('change keyup', function(event) {
        changedInputValue($(this));
    });
    /*
     * Change icon
     */
    $('.icon').on('click', function(event) {
        let img = $(this);
        let originalURL = img.attr('src');
        let url = prompt('アイコンのURLを入力してください。', originalURL);
        if (url == null || url.length == 0 || url == originalURL) {
            return;
        }
        img.attr('src', url);
        let input = img.next();
        input.val(url);
        changedInputValue(input);
    });
    /*
     * Jump to URL
     */
    $('a label:contains("URL:")').on('click', function() {
        let url = $(this).parents('p').find('input').val()
        window.open(url);
    });
    /*
     * New labeling
     */
    $('#rows select').on('change', function(event) {
        let select = $(this);
        if (select.val() != '{new_labeling}') {
            return;
        }
        let newLabeling = prompt('新しいラベルを入力してください。');
        if (newLabeling == null || newLabeling.length == 0) {
            select.val(select.attr('original_value'));
        } else {
            let newOption = $('<option value="' + newLabeling + '">' + newLabeling + '</option>');
            select.append(newOption);
            select.val(newLabeling);
        }
        changedInputValue(select);
    });
    /*
     * Delete button
     */
    $('.delete').on('click', function(event) {
        if (confirm("本当に削除しますか？") == false) {
            return;
        }
        let row = $(this).parents('.row');
        let values = {};
        row.find('input').each(function(index, current) {
            let input = $(current);
            values[input.attr('name')] = input.val();
        });
        values['token'] = token;
        values['mode'] = 'delete';
        $scent.post(values, url, function(result) {
            if (result['successed']) {
                location.reload();
            } else {
                alert(result['message']);
            }
        }, function(result) {
            alert('通信に失敗しました。');
        });
    });
    /*
     * Save button
     */
    $('.save').on('click', function(event) {
        let button = $(this);
        if (typeof button.attr('disabled') !== 'undefined') {
            return;
        }
        let row = button.parents('.row');
        let values = {};
        row.find('input, select').each(function(index, current) {
            let input = $(current);
            
            values[input.attr('name')] = input.val();
        });
        values['token'] = token;
        values['mode'] = 'save';
        $scent.post(values, url, function(result) {
            if (result['successed']) {
                alert('保存しました。');
                if (idToScroll == 'new_row') {
                    location.href = url;
                } else {
                    location.reload();
                }
            } else {
                $.each(result['cause'], function(name, value) {
                    row.find('[name="' + name + '"]').focus();
                    return;
                });
                alert(result['message']);
            }
        }, function(result) {
            alert('通信に失敗しました。');
        });
    });
    /*
     * Add button
     */
    $('button:contains("追加")').on('click', function(event) {
        let button = $(this);
        let row = $('#new_row');
        let values = {};
        values['mode'] = 'fetch_default_record';
        values['token'] = token;
        $scent.post(values, url, function(result) {
            if (result['successed']) {
                row.find('input, select').each(function(index, current) {
                    let inputOrSelect = $(current);
                    inputOrSelect.val(result['record'][inputOrSelect.attr('name')]);
                });
                let img = row.find('.left').find('img');
                img.attr('src', img.next().val());
                button.hide();
                $('p#nothing').hide();
                row.fadeIn();
            } else {
                alert(result['message']);
            }
        }, function(result) {
            alert('通信に失敗しました。');
        });
    });
    /*
     * Sort number reset
     */
    addSubMenuItem('並び順を再設定', function() {
        if (confirm("現在の並び順を維持したまま並び順の番号を再設定しますか？") == false) {
            return;
        }
        let values = {};
        values['token'] = token;
        values['mode'] = 'renumber_of_sort';
        $scent.post(values, url, function(result) {
            if (result['successed']) {
                location.reload();
            } else {
                alert(result['message']);
            }
        }, function(result) {
            alert('通信に失敗しました。');
        });
    });
    /*
     * Import from HTML
     */
    addSubMenuItem('HTMLからインポート', function() {
        $('#import_section').show();
    });
    $('#import_section button:contains("キャンセル")').on('click', function(event) {
        $('#import_section textarea').val('');
        $('#import_section').hide();
    });
    $('#import_section button:contains("インポート")').on('click', function(event) {
        // Control view
        let setDisableImportSection = function(isDisable) {
            let textarea = $('#import_section textarea');
            let importButton = $('#import_section button:contains("インポート")');
            let cancelButton = $('#import_section button:contains("キャンセル")');
            textarea.prop('disabled', isDisable);
            importButton.prop('disabled', isDisable);
            cancelButton.prop('disabled', isDisable);
            let waitCircle = $('#import_section p img');
            if (isDisable) {
                waitCircle.show();
            } else {
                waitCircle.hide();
            }
        };
        // Create JSON
        let html = $('#import_section textarea').val();
        if (html.length == 0) {
            return;
        }
        let elements = $.parseHTML(html);
        setDisableImportSection(true);
        let array = [];
        let firstLabeling;
        let labeling = '';
        let isDlEnd = false;
        $(elements).find('h3, a, dl, dt, p').each(function(index, current) {
            let element = $(current);
            switch (element.prop("tagName")) {
            case "A":
                let object = {};
                object['a_href'] = element.attr('href');
                object['a_text'] = element.text();
                object['h3_text'] = labeling;
                array.push(object);
                break;
            case "H3":
                labeling = element.text();
                if (typeof firstLabeling === 'undefined') {
                    firstLabeling = labeling;
                }
                break;
            case "DL":
                isDlEnd = false;
                break;
            case "DT":
                isDlEnd = true;
                break;
            case "P":
                if (isDlEnd) {
                    labeling = firstLabeling;
                }
                break;
            }
        });
        let json = JSON.stringify(array);
        // Send
        let values = {'json': json, 'mode': 'import_from_json', 'token': token};
        $scent.post(values, url, function(result) {
            if (result['successed']) {
                location.reload();
            } else {
                alert(result['message']);
                setDisableImportSection(false);
            }
        }, function(result) {
            alert('通信に失敗しました。');
            setDisableImportSection(false);
        });
    });
    /*
     * Scroll to a specific ID
     */
    if (idToScroll == 'new_row') {
        $('button:contains("追加")').hide();
    } else {
        $('#new_row').hide();
    }
    if (idToScroll.length > 0) {
        let scrollTo = $('#' + idToScroll);
        if (scrollTo.length > 0) {
            $scent.smoothScroll(scrollTo, 500, -50);
        }
    }
}


