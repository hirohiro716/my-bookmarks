/**
 * Adjustment for each device.
 */
$(window).bind('load', function() {
    adjustForDevice();
});
$(window).bind('resize', function() {
    adjustForDevice();
});

/**
 * Adjustment for window.
 */
function adjustForDevice() {
    let width = $(window).width();
    let height = $(window).height();
    if (width > 900) {
        $('link.device_css').each(function() {
            $(this).attr('href', $(this).attr('href').replace('/mobile/', '/desktop/'));
        });
    } else {
        $('link.device_css').each(function() {
            $(this).attr('href', $(this).attr('href').replace('/desktop/', '/mobile/'));
        });
    }
    let base = width;
    if (base > 500) {
        base = 500;
    }
    let rate = base / 350;
    $('body').css('fontSize', rate + 'em');
}

/**
 * Disable form submission with enter.
 */
$(window).bind('load', function() {
    $('form').on('submit', function() {
        return false;
    });
});
