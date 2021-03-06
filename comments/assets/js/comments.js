/**
 *
 * @param $el
 * @param context
 * @param data
 * @param textStatus
 * @param jqXHR
 *
 */
function afterSubmit($el, context, data, textStatus, jqXHR) {

    var $form = $el;
    var $to = $form.find('input.to');
    var $edit = $form.find('input.edit');

    if($to.val() && !$edit.val()) { // if reply
        var id = $to.val();
        var $newComment = $('#comments .comment').last();
        if(!$newComment.hasClass('comment-error'))
            $('#reply-' + id).append($newComment);
    }
    else if(!$to.val() && $edit.val()) { // if update comment
        var id = $edit.val();
        var $newComment = $('#comments .comment').last();
        $newComment.remove();
        var newCommentContent = $newComment.find('.comment-content');
        $('#comment-' + id + ' .comment-content').eq(0).replaceWith(newCommentContent);
    }

    $form[0].reset();

    if(checkOption($form, 'hideReplyForm') == 1) {
        $form.find('.cancel-reply-link').trigger('click');
    }
}

function checkOption($form, option) {
    var val = 0;
    var str = $form.data('options');
    var arr = str.split(', ');
    arr.forEach(function(item, i, arr) {
        var options = item.split(': ');
        if(options[0] == option) {
            val = options[1];
        }
    });
    return val;
}

$(document).ready(function () {

    /**
     * Replace comment form to initial position.
     */
    $('#comments').on('click touchstart', '.cancel-reply-link', function(e){
        e.preventDefault();
        var $this = $(this);
        var $form = $this.closest('form');

        if(checkOption($form, 'hideMainForm') == 1) {
            $form.slideUp( "slow", function() {
                $('#comment-form-wrapper').append($form);
                $form.find('.cancel-reply').hide();
                $form[0].reset();
                $form.find('input.to').val('');
                $form.find('input.edit').val('');
                $form.find('input.level').val('');
                $form.slideDown();
            });
        }
        else {
            $form.slideUp( "slow", function() {
                $form.remove();
            });
        }
        return false;
    });

    /**
     * Reply to the comment.
     */
    $('#comments').on('click touchstart', 'a.reply', function(e){
        e.preventDefault();
        var $this = $(this);
        var id = $this.data('id');
        var level = $this.data('level');
        var $comment = $('#comment-' + id);
        var $form = $('#comment-form-wrapper .comment-form');
        $form[0].reset();

        if(checkOption($form, 'hideMainForm') == 1) {
            $form.slideUp( "slow", function() {
                $comment.find('.comment-actions').eq(0).after($form);
                $form.find('#to').val(id);
                $form.find('#edit').val('');
                $form.find('#level').val(level + 1);
                $form.find('.cancel-reply').show();
                $form.slideDown();
            });
        }
        else {
            $('.comment-form').find('.cancel-reply-link').trigger('click');
            var $clone = $form.clone(true);
            $clone.css('display', 'none');
            $comment.find('.comment-actions').eq(0).after($clone);
            $clone.find('#to').val(id);
            $clone.find('#edit').val('');
            $clone.find('#level').val(level + 1);
            $clone.find('.cancel-reply').show();
            $clone.slideDown();
        }

        return false;
    });

    /**
     * Edit the comment.
     */
    $('#comments').on('click touchstart', 'a.edit', function(e){
        e.preventDefault();
        var $this = $(this);
        $this.request('onEditComment', {
            data: {id: $this.data('id')},
            success: function(data){
                var result = $.parseJSON(data.result);
                var id = result['id'];
                var content = result['content'];

                var $comment = $('#comment-' + id);
                var $form = $('.comment-form');
                $form[0].reset();

                if(checkOption($form, 'hideMainForm') == 1) {
                    $form.slideUp( "slow", function() {
                        $comment.find('.comment-actions').eq(0).after($form);
                        $form.find('#edit').val(id);
                        $form.find('#to').val('');
                        $form.find('#level').val('');
                        $form.find('#content').val(content);
                        $form.find('.cancel-reply').show();
                        $form.slideDown();
                    });
                }
                else {
                    var $clone = $form.clone(true);
                    $clone.css('display', 'none');
                    $comment.find('.comment-actions').eq(0).after($clone);
                    $clone.find('#edit').val(id);
                    $clone.find('#to').val('');
                    $clone.find('#level').val('');
                    $clone.find('#content').val(content);
                    $clone.find('.cancel-reply').show();
                    $clone.slideDown();
                }
            }
        });
        return false;
    });

    /**
     * Delete the comment.
     */
    $('#comments').on('click touchstart', 'a.delete', function(e){
        e.preventDefault();
        var $this = $(this);
        var $comment = $(this).closest('.comment');

        if($comment.has('.comment-form').length) {
            $comment.find('.cancel-reply-link').trigger('click');
        }

        $this.request('onDeleteComment', {
            data: {id: $this.data('id')},
            success: function(data){
                var id = data.result;
                var $comment = $('#comment-' + id);
                $comment.slideUp( "slow", function() {
                    $comment.remove();
                });
            }
        });
        return false;
    });

});