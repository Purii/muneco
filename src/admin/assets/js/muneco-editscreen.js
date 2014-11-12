/** Listener page-connections **/
/* /wp-admin/js/post.js:839 */

jQuery(document).ready(function ($) {


    var $postConnectionBox = $('#post-status-connections');
    updateConnectionsCounterText();
    function updateConnectionsCounterText() {
        var counter = 0;
        $postConnectionBox.find('ul li').each(function (index, connectionelm) {
            var langcounter = $(connectionelm).children('input').val();
            if (parseInt(langcounter) != 0) counter++;
        });
        $('#post-connections-counter').html(counter);
    }

    $postConnectionBox.siblings('a.edit-post-connections').click(function (event) {
        if ($postConnectionBox.is(':hidden')) {
            $postConnectionBox.slideDown('fast');
            $(this).hide();
        }
        event.preventDefault();
    });

    $postConnectionBox.find('.save-post-connections').click(function (event) {
        $postConnectionBox.slideUp('fast').siblings('a.edit-post-connections').show();
        updateConnectionsCounterText();
        event.preventDefault();
    });

    $postConnectionBox.find('.cancel-post-connections').click(function (event) {
        $('#post-status-connections').slideUp('fast').siblings('a.edit-post-connections').show().focus();
        $postConnectionsBox.find('input.post-connection-hidden').attr('value', 0);
        $postConnectionsBox.find('.post-connection-previewtext').html('');
        updateConnectionsCounterText();
        event.preventDefault();
    });

    $('.updateConnections').on('click', function () {
        $('#connected-page-list-container .inside input:checked').each(function (index, languagelist) {
            var id = $(languagelist).val();
            var siteid = $(languagelist).data('siteid');
            var title = $(languagelist).data('title');
            $postConnectionBox.find('li[data-siteid="' + siteid + '"] input.post-connection-hidden').attr('value', id);
            $postConnectionBox.find('li[data-siteid="' + siteid + '"] .post-connection-previewtext').html(title);
        });
        updateConnectionsCounterText();
        $('#TB_closeWindowButton').trigger('click');
    });

    $('.cancelConnections').on('click', function () {
        $('#TB_closeWindowButton').trigger('click');
    });

});
/** /Listener page-connections **/
/** Listener toggle-element **/

jQuery(document).ready(function ($) {

    $('.muneco-expand-container').on('click keydown', '.muneco-expand-toggle', function (e) {
        if (e.type === 'keydown' && 13 !== e.which) // "return" key
            return;
        e.preventDefault(); // Keep this AFTER the key filter above

        $(this).closest('.muneco-expand-container').toggleClass('open');
    });
});
/** /Listener toggle-element **/
/** Listener check-element **/
jQuery(document).ready(function ($) {
    $('.wp-admin').on('change', '#tb-connectedpage-container .post-connection > input', function () {
        muneco_validateConnections();
    });
});
/** /Listener check-element **/
/** Validate-connection **/
function muneco_validateConnections() {
    jQuery('#tb-connectedpage-container .connectionbox').each(function () {
        if (jQuery(this).find('input:checked').val() != '0') {
            jQuery(this).removeClass('incomplete').addClass('complete');
            var titleSpan = jQuery(this).find('h3 > span');
            if (titleSpan.length <= 0) {
                jQuery(this).find('h3').append('<span></span>');
                titleSpan = jQuery(this).find('h3 > span');
            }
            var title = jQuery(this).find('input:checked').data('title');
            titleSpan.html(title);
            return true;
        }
        ;
        jQuery(this).addClass('incomplete').removeClass('complete');
    });
};
/** /Validate-connection **/

/** link-search **/
jQuery(document).ready(function ($) {
    $('.wp-admin').on('change', '#tb-connectedpage-container .muneco-search', function () {
        var searchvalue = $(this).find('input[type=search]').val();
        var siteid = $(this).find('input.siteid').val();
        var ajax_nonce = $(this).find('input.ajax_nonce').val();
        /* TODO */
        var post_type = 'post';
        var searchResult = muneco_searchPosts(searchvalue, siteid, post_type, ajax_nonce, muneco_listPostsearches);
    });
});
function muneco_listPostsearches(data) {
    data = jQuery.parseJSON(data);
    data = data.data;
    console.log(data);
}
function muneco_searchPosts(searchvalue, siteid, post_type, ajax_nonce, callback) {
    if ('undefined' == typeof ajax_nonce || 'undefined' == typeof searchvalue || 'undefined' == typeof siteid || 'function' != typeof callback) console.log('Invalid Request');
    var data = {
        'action': 'muneco_linkajax',
        'searchvalue': searchvalue,
        'siteid': siteid,
        'ajax_nonce': ajax_nonce,
        'post_type': post_type
    }

    jQuery.post(ajaxurl, data, function (response) {
        if (true == response.success) {
            callback(response);
        }
        callback(response);
    });
}
/** /link-search **/