$(document).ready(function() {

    function CKupdateCommentary(){
        for ( instance in CKEDITOR.instances )
            CKEDITOR.instances['editor1'].updateElement();
    }

    var Url = window.location.href.split("?action=")[1];
    var page = Url.split("&token")[0];
    var current_page = page.split("&from")[0];
    var token = '';


    $('header '+current_page).addClass('active');
    $('header a[href^="?action=' + current_page + '"]').addClass('active');
    if(current_page == 'reglog'){
        $('header a[href^="?action=user"]').addClass('active');
    }

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
                }
            }
        });
    });

    setInterval(function() {
            $(".available_comment").load(location.href + " .available_comment>*", "");
        }, 1000
    );

    var frmBis = $('#formContactMe');
    frmBis.submit(function (e) {

        e.preventDefault();

        $.ajax({
            type: frmBis.attr('method'),
            url: frmBis.attr('action'),
            data: frmBis.serialize(),

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
                    $('.error_contact_me').html(error_message);
                }
                if(errorORsuccess == 'success'){
                    $('.waiting').html("Traitement en cours ...");
                    setInterval(function() {
                        $('.contentMeVal').val('');
                        $('.error_contact_me').html('');
                        $('.waiting').html("<img src='assets/img/checkmark-for-verification.png' alt='img'>");
                        }, 5000
                    );
                }
            }
        });
    });



    var frmTer = $('#formASkQuestion');
    frmTer.submit(function (e) {

        e.preventDefault();
        CKupdateCommentary();

        $.ajax({
            type: frmTer.attr('method'),
            url: frmTer.attr('action'),
            data: frmTer.serialize(),


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
                    $('.error_block_ask_question').html(error_message);
                }
                if(errorORsuccess == 'success'){
                    $('.success_block_ask_question').html('Message envoyé !');
                    $('.error_block_ask_question').html('');
                    $('.sujet').val('');
                    $('#cke_1_contents').html('');


                    $(".availables_questions").load(location.href + " .availables_questions>*", "");
                    setInterval(function() {
                        $('.container_form').css('display','none');
                        }, 3000
                    );
                }
            }
        });
    });

    $('#goCom').click(function(){
        var page = $('#commentBox'); // Page cible
        var speed = 3000; // Durée de l'animation (en ms)
        $('html, body').animate( { scrollTop: $(page).offset().top }, speed ); // Go        return false;
    });

    $('.ask_question').on('click', function () {
        $('.container_form').css('display','block');
    });

    var frmAnswer = $('#formAnswer');

    frmAnswer.submit(function (e) {

        e.preventDefault();
        CKupdateCommentary();


        $.ajax({
            type: frmAnswer.attr('method'),
            url: frmAnswer.attr('action'),
            data: frmAnswer.serialize(),

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
                    $('.error_answer').html(error_message);
                }
                if(errorORsuccess == 'success'){
                    $('.contentCommentAjax').val('');
                    $('.error_answer').html('');
                    $('#cke_1_contents').html('');

                    $(".answers_box").load(location.href + " .answers_box>*", "");

                }
            }
        });
    });
});
