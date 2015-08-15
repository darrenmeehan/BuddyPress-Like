/* jshint undef: false, unused:false */
// AJAX Functions
var jq = jQuery;

// Global variable to prevent multiple AJAX requests
var bp_like_ajax_request = null;

jq(document).ready(function() {
    "use strict";
    jq('.like, .unlike, .like_blogpost, .unlike_blogpost').live('click', function() {
        var type = jq(this).attr('class'), id = jq(this).attr('id');

        jq(this).addClass('loading');

        jq.post(ajaxurl, {
            action: 'activity_like',
            'cookie': encodeURIComponent(document.cookie),
            'type': type,
            'id': id
        },
            function(data) {
                console.log('data: ' + data);
                jq('#' + id).fadeOut(50, function() {
                    jq(this).html(data).removeClass('loading').fadeIn(50);
                });

                console.log('type: ' + type );
                console.log('id: ' + id);
                type = type.replace('button','').replace('bp-primary-action','').trim();
                console.log('pure type:' + type);

                // Swap from like to unlike
                var newID, pureID;
                if (type == 'like') {
                    newID = id.replace("like", "unlike");
                    jq('#' + id).removeClass('like').addClass('unlike').attr('title', bplikeTerms.unlike_message).attr('id', newID).text('Unlike');
                } else if (type == 'like_blogpost') {
                    newID = id.replace("like", "unlike");
                    jq('#' + id).removeClass('like_blogpost').addClass('unlike_blogpost').attr('title', bplikeTerms.unlike_message).attr('id', newID);
                } else if (type == 'unlike_blogpost') {
                    newID = id.replace("unlike", "like");
                    jq('#' + id).removeClass('unlike_blogpost').addClass('like_blogpost').attr('title', bplikeTerms.unlike_message).attr('id', newID);
                } else if (type == 'unlike') {
                    newID = id.replace("unlike", "like");
                    jq('#' + id).removeClass('unlike').addClass('like').attr('title', bplikeTerms.like_message).attr('id', newID).text('Like');
                } else {
                    console.log('Something went wrong');
                    console.log('type: ' + type );
                    console.log('id: ' + id + 'newID: ' + newID);
                }

                // Nobody else liked this, so remove the 'View Likes'
                if (data == 'Like ') {
                    console.log('But you were the only one to like this!');
                    pureID = id.replace("unlike-activity-", "");
                    jq('#users-who-like-' + pureID ).remove();
                }

                // Show the 'View Likes' if user is first to like
                if (data == 'Unlike <span>1</span>') {
                    console.log('You\'re the first person to like this!');
                    pureID = id.replace("like-activity-", "");
                    jq('li#activity-' + pureID + ' .activity-meta').append('<p class="users-who-like" id="users-who-like-' + pureID + '">You like this.</p>');
                }

            });

        return false;
    });
        if (bplikeTerms.fav_remove == 1) {
        jq(".fav").remove();
        jq(".unfav").remove();
    }
});