/** Listener page-connections **/
/* /wp-admin/js/plugin-install.js:4 */

var muneco_tb_position;
jQuery(document).ready(function ($) {
    muneco_tb_position = function () {
        var muneco_tbWindow = $('#TB_window'),
            width = $(window).width(),
            H = $(window).height(),
            W = ( 720 < width ) ? 720 : width,
            adminbar_height = 0;

        if ($('body.admin-bar').length) {
            adminbar_height = parseInt(jQuery('#wpadminbar').css('height'), 10);
        }

        if (muneco_tbWindow.size()) {
            muneco_tbWindow.width(W - 50).height(H - 45 - adminbar_height);
            $('#TB_ajaxContent').width(W - 80).height(H - 75 - adminbar_height);
            muneco_tbWindow.css({'margin-left': '-' + parseInt(( ( W - 50 ) / 2 ), 10) + 'px'});
            if (typeof document.body.style.maxWidth !== 'undefined')
                muneco_tbWindow.css({'top': 20 + adminbar_height + 'px', 'margin-top': '0'});
        }

        return $('a.thickbox').each(function () {
            var href = $(this).attr('href');
            if (!href)
                return;
            href = href.replace(/&width=[0-9]+/g, '');
            href = href.replace(/&height=[0-9]+/g, '');
            $(this).attr('href', href + '&width=' + ( W - 80 ) + '&height=' + ( H - 85 - adminbar_height ));
        });
    };

    $(window).resize(function () {
        muneco_tb_position();
    });

    $('body').on('click', 'a.thickbox', function () {
        tb_click.call(this);
    });
});