/*
 Custom javascript code for Boral pages
*/

$(document).ready(function(){

    $('.add-file').click(function () {
        $(this).parent().before("<div class='col-xs-8 file-wrapper'><input type='file' name='files[]'  accept='.png, .jpg, .jpeg'><a class='btn btn-sm remove'><span class='fa fa-times'></span></a></div>");
    });


});

$(document).on("click", ".remove", function () {
    $(this).closest('div').slideUp('slow', function(){$(this).remove();});
})