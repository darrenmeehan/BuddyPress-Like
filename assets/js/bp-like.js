/* jshint undef: false, unused:false */
// AJAX Functions
var jq = jQuery;

var bp_like_ajax_request = null;                                // TODO implement this. Global variable to prevent multiple AJAX requests

jq(document).ready(function() {
    "use strict";
    jq('.like, .unlike').live('click', function() {             // TODO ensure all links only use .like or .unlike
        
        var id = jq(this).attr('id');                           // Used to get the id of the entity liked or unliked

        var type = jq(this).attr('class')                       // 
            .replace('bp-primary-action ','')                   // end space needed to avoid double space in var type
            .replace('button', 'activity_update')               // clearer variable naming
            .replace('acomment-reply', 'activity_comment')
            .trim();
        
        jq(this).addClass('loading');                           

        jq.post(ajaxurl, {
            action: 'activity_like',                            // TODO this could be named clearer
            'type': type,
            'id': id
        },
            function(data) {
                console.log('type: ' + type);
                console.log('data: ' + data);
                jq('#' + id).fadeOut(100, function() {
                    jq(this).html(data).removeClass('loading').fadeIn(100);
                });

                // may only need one if and else if
                // if (like) {} else if (unlike) {} else {oops()}
                // leave for now as may need something for messages
                if (type == 'activity_update like') {

                    jq('#' + id).removeClass('like')
                        .addClass('unlike')
                        .attr('title', bplikeTerms.unlike_message)
                        .attr('id', id.replace("like", "unlike") );
                
                } else if (type == 'activity_update unlike') {

                    jq('#' + id).removeClass('unlike')
                        .addClass('like')
                        .attr('title', bplikeTerms.like_message)
                        .attr('id', id.replace("unlike", "like"));
 
                } else if (type == 'activity_comment like') {

                    jq('#' + id).removeClass('like')
                        .addClass('unlike')
                        .attr('title', bplikeTerms.unlike_message)      // may want different (smaller) message for comments
                        .attr('id', id.replace("like", "unlike") );

                } else if (type == 'activity_comment unlike') {

                    jq('#' + id).removeClass('unlike')
                        .addClass('like')
                        .attr('title', bplikeTerms.like_message)
                        .attr('id', id.replace("unlike", "like") );
                
                } else {
                    console.log('Opps. Something went wrong');
                    console.log('type: ' + type );
                    console.log('id: ' + id );
                }

                // Nobody else liked this, so remove the 'View Likes'
                if (data == 'Like ') {
                    console.log('But you were the only one to like this!');
                    id = id.replace("unlike-activity-", "");
                    jq('#users-who-like-' + id ).remove();
                }

                // Show the 'View Likes' if user is first to like
                if (data == 'Unlike <span>1</span>') {
                    console.log('You\'re the first person to like this!');
                    id = id.replace("like-activity-", "");
                    jq('li#activity-' + id + ' .activity-meta')
                        .append('<p class="users-who-like" id="users-who-like-' + id + '">' + bplikeTerms.you_like_this +'</p>')
                        .slideDown(1000); // quick attempt as some animation. Needs worked properly
                }

            });

        return false;
    });
});