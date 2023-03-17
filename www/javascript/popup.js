/**
 * Set event handler.
 */
function setEventHandler(rootURL) {
    // Lazy loading of icons
    $('img.icon').each(function(index, current) {
        let img = $(current);
        let src = img.attr('data-src');
        if (typeof src !== 'undefined') {
            img.attr('src', img.attr('data-src'));
        }
    });
    // Label click event
    $('div.group div.bookmark').hide();
    $('a.label').on('click', function(event) {
        let label = $(this);
        label.parent().find('div.bookmark').each(function(index, current) {
            let bookmark = $(current);
            bookmark.css('progress', '0');
            if (bookmark.css('display') == 'none') {
                bookmark.css('transform', 'scaleY(0)');
                bookmark.show();
                setTimeout(function() {
                    bookmark.animate({'progress': 1}, {duration: 100, step: function(current) {
                        bookmark.css('transform', 'scaleY(' + current + ')');
                        bookmark.css('opacity', current);
                    }, complete: function() {
                        bookmark.css('transform', '');
                        bookmark.css('opacity', '');
                    }});
                }, index * 50);
                
            } else {
                bookmark.fadeOut(100);
            }
        });
    });
    // Bookmark click event
    $('div.bookmark a').on('click', function(event) {
        let bookmark = $(this);
        let url = bookmark.attr('url');
        let newWindow = window.open(url, 'target=_blank,oopener=yes,noreferrer=yes');
        setTimeout(function() {
            window.close();
        }, 500);
    });
    // Bookmark open in window event
    $('div.bookmark img.open_in_window').on('click', function(event) {
        let bookmark = $(this).parent().find('a');
        let values = {};
        values.mode = 'move';
        values.url = bookmark.attr('url');
        if (window.opener != null) {
            window.opener.postMessage(values, '*');
        } else {
            let newWindow = window.open(values.url, 'target=_blank,noopener=yes,noreferrer=yes');
            setTimeout(function() {
                window.close();
            }, 500);
        }
    });
    // Bookmark edit event
    $('img.edit').on('click', function(event) {
        let bookmark = $(this).parent();
        let id = bookmark.attr('id');
        let url = rootURL + '?scroll=' + id;
        let newWindow = window.open(url, 'target=_blank,oopener=yes,noreferrer=yes');
        setTimeout(function() {
            window.close();
        }, 500);
    });
    // Hide wait circle
    $('section#wait_circle').fadeOut(200);
}
