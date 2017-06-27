$(document).ready(function() {
    var current_page = window.location.href.split("?action=")[1];
    $('header '+current_page).addClass('active');
    $('header a[href^="?action=' + current_page + '"]').addClass('active');
    if(current_page == 'reglog'){
        $('header a[href^="?action=user"]').addClass('active');
    }
});