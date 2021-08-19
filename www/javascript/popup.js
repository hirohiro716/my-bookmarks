/**
 * Set event handler.
 */
function setEventHandler() {
    // Label click event
    $('a.label').on('click', function(event) {
        let label = $(this);
        label.parent().find('.bookmark').each(function(index, current) {
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
    $('a.bookmark').on('click', function(event) {
        let bookmark = $(this);
        let url = bookmark.attr('url');
        if (window.opener == null) {
            window.location.href = url;
        } else {
            window.opener.postMessage(url, '*');
        }
    });
}
