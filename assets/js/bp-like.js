/* jshint undef: false, unused:false */
// AJAX Functions
var jq = jQuery;

var bp_like_ajax_request = null;                                // TODO implement this. Global variable to prevent multiple AJAX requests
var id, type;
jq(document).ready(function bpLike() {
    "use strict";
    jq('.like, .unlike').live('click', function() {

        id = jq(this).attr('id');                           // Used to get the id of the entity liked or unliked

      //  console.log('id: ' + id);
        type = jq(this).attr('class')                           //
            .replace('bp-primary-action ','')                   // end space needed to avoid double space in var type
            .replace('button', 'activity_update')               // clearer variable naming
            .replace('acomment-reply', 'activity_comment')
            .replace('blogpost', 'blog_post')
            .trim();

        jq(this).addClass('loading');

        jq.post(ajaxurl, {
            action: 'activity_like',                            // TODO this could be named clearer
            'type': type,
            'id': id
        },
            function( data ) {
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
                        getLikes(id, type);

                } else if (type == 'activity_update unlike') {

                    jq('#' + id).removeClass('unlike')
                        .addClass('like')
                        .attr('title', bplikeTerms.like_message)
                        .attr('id', id.replace("unlike", "like"));
                        getLikes(id, type);

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

                }  else if (type == 'blog_post like') {

                    jq('#' + id).removeClass('like')
                        .addClass('unlike')
                        .attr('title', bplikeTerms.unlike_message)      // may want different (smaller) message for comments
                        .attr('id', id.replace("like", "unlike") );

                } else if (type == 'blog_post unlike') {

                    jq('#' + id).removeClass('unlike')
                        .addClass('like')
                        .attr('title', bplikeTerms.like_message)
                        .attr('id', id.replace("unlike", "like") );

                } else {
                    console.log('Opps. Something went wrong');
                    console.log('type: ' + type );
                    console.log('id: ' + id );
                }

                // Nobody else liked this, so remove who likes the item
                if (data == 'Like') {
                    id = id.replace("unlike-activity-", "");
                    jq('#users-who-like-' + id ).remove();
                }

                // Show who likes the item if user is first to like
                if (data == 'Unlike <span>1</span>') {
                    id = id.replace("like-activity-", "");
                    jq('li#activity-' + id + ' .activity-meta')
                        .append('<p class="users-who-like" id="users-who-like-' + id + '"><small>' + bplikeTerms.you_like_this +'</small></p>')
                }

            });

        return false;
    });


    // this function is to get likes of a post
    function getLikes(id, type) {
      id = id
          .replace('like-activity-', '')
          .replace('unlike-activity-', '')
          .replace('un', '');
      jq('#users-who-like-' + id).addClass('loading');
      jq.post(ajaxurl, {
          action: 'bplike_get_likes',
          'type': type,
          'id': id
      }, function( response ) {
        response = response.replace('<p class="users-who-like" id="users-who-like-' + id + '">', '')
          .replace('</p>', '');
        jq('#users-who-like-' + id).html(response);
        jq('#users-who-like-' + id).removeClass('loading');

      })
    };


});
