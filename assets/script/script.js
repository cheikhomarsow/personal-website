$(document).ready(function() {

    var Url = window.location.href.split("?action=")[1];
    var page = Url.split("&token")[0];
    var current_page = page.split("&from")[0];
    var token = '';


    $('header '+current_page).addClass('active');
    $('header a[href^="?action=' + current_page + '"]').addClass('active');
    if(current_page == 'reglog'){
        $('header a[href^="?action=user"]').addClass('active');
    }

    /*$('form').submit(function () {
        var article_id = $('.articleIdAjax');
        var user_id = $('.userIdAjax');
        var content_comment = $('.contentCommentAjax');

        if(url.split("&token=").length == 2){
            token = url.split("&token=")[1];
        }
        $.post('?action=article',{article_id:article_id, user_id:user_id, content:content_comment},function(data){

        });
        return false;
    });*/
    var frm = $('#formComment');

    frm.submit(function (e) {

        e.preventDefault();

        $.ajax({
            type: frm.attr('method'),
            url: frm.attr('action'),
            data: frm.serialize(),

            success: function (data) {
                console.log('Submission was successful.');
                console.log(data);
            },
            error: function (data) {
                var res = data.responseText.split('<!DOCTYPE html>')[0];
                var t0 = res.split(":")[0];
                var errorORsuccess = t0.split("\"")[1];
                if(errorORsuccess == 'error'){
                    var error_message = res.split(":")[2].split("}}")[0].replace('"',' ').replace('"','');
                    $('.error_comment').html(error_message);
                }
                if(errorORsuccess == 'success'){
                    $('.contentCommentAjax').val('');
                    $('.error_comment').html('');
                    $(".available_commentx").load(location.href + " .comment-box>*", "")
                }
            }
        });
    });

    setInterval(function() {
            $(".available_comment").load(location.href + " .available_comment>*", "");
        }, 1000
    );
});
