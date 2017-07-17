$(document).ready(function(){

    $('.position_form_login').submit(function () {
        $('.error_username').html('');
        $('.error_password').html('');
        $('.success-block-login').html('');
        var params = 'username=' + this.elements['username'].value;
        params += '&password=' + this.elements['password'].value;

        var httpLogin = new XMLHttpRequest();
        httpLogin.open("POST", "?action=demo_ajax", true);
        var url = "?action=demo_ajax";
        httpLogin.open("POST", url, true);
        httpLogin.setRequestHeader("Content-type", "application/x-www-form-urlencoded");

        httpLogin.onload = function () {
            if (httpLogin.readyState == 4 && httpLogin.status == 200) {
                $('.success-block-login').html('Formulaire valide !');
            } else {
                var errors = JSON.parse(httpLogin.responseText);
                for (var error in errors['errors']) {
                    if(error == 'error'){
                        $('.error-block-login').html(errors['errors'][error]);
                    }else if(error == 'username'){
                        $('.error_username').html(errors['errors'][error]);
                    }else if(error == 'password'){
                        $('.error_password').html(errors['errors'][error]);
                    }else{
                        console.log('...');
                    }
                }

                /*var errors = JSON.parse(httpLogin.responseText);
                for (var error in errors['errors']) {
                    errorBlockLogin.html(error + ' : ' + errors['errors'][error] + '<br>');
                }*/
            }
        };
        httpLogin.send(params);
        return false;
    });
});