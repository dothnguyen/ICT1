/*
 Custom javascript code for Boral pages
*/

$(document).ready(function(){

    $('.add-file').click(function () {
        $(this).parent().before("<div class='col-xs-8 file-wrapper'><input type='file' name='files[]'><img src='http://images.freescale.com/shared/images/x.gif' class='remove'></div>");
    });


});

$(document).on("click", ".remove", function () {
    $(this).closest('div').slideUp('slow', function(){$(this).remove();});
})