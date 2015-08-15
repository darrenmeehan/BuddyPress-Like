jQuery(document).ready(function() {
    "use strict";
    jQuery('.like, .unlike, .like_blogpost, .unlike_blogpost').live('click', function() {
        var type = jQuery(this).attr('class'), id = jQuery(this).attr('id');

        jQuery(this).addClass('loading');

        jQuery.post(bplikeTerms.ajaxurl, {
            action: 'activity_like',
            'cookie': encodeURIComponent(document.cookie),
            'type': type,
            'id': id
        },
            function(data) {
                console.log('data: ' + data);
                jQuery('#' + id).fadeOut(50, function() {
                    jQuery(this).html(data).removeClass('loading').fadeIn(50);
                });

                console.log('type: ' + type );
                console.log('id: ' + id);
                type = type.replace('button','').replace('bp-primary-action','').trim();
                console.log('pure type:' + type);

                // Swap from like to unlike
                var newID, pureID;
                if (type == 'like') {
                    newID = id.replace("like", "unlike");
                    jQuery('#' + id).removeClass('like').addClass('unlike').attr('title', bplikeTerms.unlike_message).attr('id', newID).text('Unlike');
                } else if (type == 'like_blogpost') {
                    newID = id.replace("like", "unlike");
                    jQuery('#' + id).removeClass('like_blogpost').addClass('unlike_blogpost').attr('title', bplikeTerms.unlike_message).attr('id', newID);
                } else if (type == 'unlike_blogpost') {
                    newID = id.replace("unlike", "like");
                    jQuery('#' + id).removeClass('unlike_blogpost').addClass('like_blogpost').attr('title', bplikeTerms.unlike_message).attr('id', newID);
                } else if (type == 'unlike') {
                    newID = id.replace("unlike", "like");
                    jQuery('#' + id).removeClass('unlike').addClass('like').attr('title', bplikeTerms.like_message).attr('id', newID).text('Like');
                } else {
                    console.log('Something went wrong');
                    console.log('type: ' + type );
                    console.log('id: ' + id + 'newID: ' + newID);
                }

                // Nobody else liked this, so remove the 'View Likes'
                if (data == 'Like ') {
                    console.log('But you were the only one to like this!');
                    pureID = id.replace("unlike-activity-", "");
                    jQuery('#users-who-like-' + pureID ).remove();
                }

                // Show the 'View Likes' if user is first to like
                if (data == 'Unlike <span>1</span>') {
                    console.log('You\'re the first person to like this!');
                    pureID = id.replace("like-activity-", "");
                    jQuery('li#activity-' + pureID + ' .activity-meta').append('<p class="users-who-like" id="users-who-like-' + pureID + '">You like this.</p>');
                }

            });

        return false;
    });
        if (bplikeTerms.fav_remove == 1) {
        jQuery(".fav").remove();
        jQuery(".unfav").remove();
    }
});