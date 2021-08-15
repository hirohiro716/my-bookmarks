/**
 * Set event handler.
 */
function setEventHandler(url, token) {
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
    /*
     * Search
     */
    $('input#keyword').on('keyup', function(event) {
        if (event.keyCode == 13) {
            let input = $(this);
            location.href = url + '?keyword=' + input.val();
        }
    });
    /*
     * Enable / disable the save button
     */
    let previousRow;
    function changedInputValue(input) {
        let isChanged = (input.val() !== input.attr('original_value'));
        let row = input.parents('.row');
        if (isChanged) {
            row.find('.save').removeAttr('disabled');
            // Undo previous row
            if (typeof previousRow !== 'undefined' && previousRow.is(row) == false) {
            	if (previousRow.attr('id') == 'new_row') {
            		previousRow.fadeOut();
            	} else {
            		previousRow.find('a.save').attr('disabled', 'disabled');
                	previousRow.find('input').each(function(index, current) {
                		let input = $(current);
                		let originalValue = input.attr('original_value');
                		input.val(originalValue);
                		let img = input.prev('img');
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
    $('#rows input').on('change keyup', function(event) {
    	changedInputValue($(this));
    });
    /*
     * Change icon
     */
    $('.icon').on('click', function(event) {
    	let img = $(this);
    	let originalURL = img.attr('src');
    	let url = prompt('アイコンのURLを入力してください。', originalURL);
    	if (url == null || url == originalURL) {
    		return;
    	}
    	img.attr('src', url);
    	let input = img.next();
    	input.val(url);
    	changedInputValue(input);
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
        row.find('input').each(function(index, current) {
            let input = $(current);
            values[input.attr('name')] = input.val();
        });
        values['token'] = token;
        values['mode'] = 'save';
        $scent.post(values, url, function(result) {
            if (result['successed']) {
                alert('保存しました。');
            	location.reload();
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
        let row = $('#new_row');
        let values = {};
        values['mode'] = 'fetch_default_record';
        values['token'] = token;
        $scent.post(values, url, function(result) {
            if (result['successed']) {
                row.find('input').each(function(index, current) {
                    let input = $(current);
                    input.val(result['record'][input.attr('name')]);
                });
                let img = row.find('.left').find('img');
                img.attr('src', img.next().val());
                row.fadeIn();
                previousRow = row;
            } else {
                alert(result['message']);
            }
        }, function(result) {
            alert('通信に失敗しました。');
        });
    });
}
