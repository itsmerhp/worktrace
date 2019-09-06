$.fn.stars = function() {
    return $(this).each(function() {
        // Get the value
        var val = parseFloat($(this).html());
        // Make sure that the value is in 0 - 5 range, multiply to get width
        var size = Math.max(0, (Math.min(5, val))) * 16;
        // Create stars holder
        var $span = $('<span />').width(size);
        // Replace the numerical value with stars
        $(this).html($span);
    });
}

$(document).ready(function(){
   //Date picker
    $('.crityk-datepicker').datepicker({
        autoclose: true,
        format: 'mm/dd/yyyy',
        endDate: 'now'
    }); 
    $('span.stars').stars();
});

//To view uploaded images
function readURL(input,id) {
    if (input.files && input.files[0]) {
        var reader = new FileReader();

        reader.onload = function (e) {
            $('#'+id).attr('src', e.target.result);
        }

        reader.readAsDataURL(input.files[0]);
    }
}
function handle_selected_restaurant(places){
    $('.get-menu-items-loader').show();
    if (places.length == 0) {
        $('#publisherpost-restaurant_id').val('');
        $("#publisherpost-menu_item_id").html('');
        $('.get-menu-items-loader').hide();
        return;
    }else{
        $('#publisherpost-restaurant_id').val(places[0].place_id);
        $.ajax({
            type: 'POST',
            url: $('.get_menu_items_url').val(),
            data: {'restaurant_id': places[0].place_id},
            success: function (response) {
                $('.get-menu-items-loader').hide();
                $("#publisherpost-menu_item_id").html(response);
                $("#publisherpost-menu_item_id").trigger('change');
            },
            error: function () {
                $('.get-menu-items-loader').hide();
                alert("Something went wrong! Please try again.");
                return false;
            }
        });
    }
};
$(document).on('blur', "#restaurant_id",function(){
    $('.get-menu-items-loader').show();
    $("#publisherpost-restaurant_id").val($('#restaurant_id-hidden').val());
});
$(document).ready(function(){
    if($("#publisherpost-post_restaurant").length>0){        
        //Trigger change function if menu item image hidden field do not have value
        if($.trim($("#publisherpost-menu_item_image").val())==""){
            $('#publisherpost-menu_item_id').trigger('change');
        }else{
            $('.supporting-img-div').slideUp(300);  
        }
    }        
});
$('#publication-form').on('keyup keypress', function(e) {
    if ($(':focus').attr('id') == 'publications-address') {
      var keyCode = e.keyCode || e.which;
        if (keyCode === 13) { 
            e.preventDefault();
            return false;
        }
    };
});
$('#pub-articles').on('keyup keypress', function(e) {
    if ($(':focus').attr('id') == 'publisherpost-post_restaurant') {
      var keyCode = e.keyCode || e.which;
        if (keyCode === 13) { 
            e.preventDefault();
            return false;
        }
    };
});
$('#publisherpost-menu_item_id').change(function(){ 
    $("#publisherpost-menu_item_image").val("");
    if ($('#publisherpost-menu_item_id').val() == "") {
        $('.supporting-img-div').slideUp(300);  
        $('.menu-item-img-div').slideUp(300);  
    }else{
        $('.get-menu-items-loader').show();
        $.ajax({
            type: 'POST',
            url: $('.get_menu_item_image_url').val(),
            data: {'menu_item_id': $('#publisherpost-menu_item_id').val()},
            success: function (menuItemImg) {
                $('.get-menu-items-loader').hide();
                if($.trim(menuItemImg) != ""){                    
                    $("#publisherpost-menu_item_image").val(menuItemImg);
                    $(".menu-item-img-div a").attr('data-target',menuItemImg);
                    $(".menu-item-img-div img").attr('src',menuItemImg);
                    $('.menu-item-img-div').slideDown(300);
                    $('.supporting-img-div').slideUp(300);  
                }else{
                    $('.menu-item-img-div').slideUp(300);
                    $('.supporting-img-div').slideDown(300);
                }
            },
            error: function () {
                $('.get-menu-items-loader').hide();
                alert("Something went wrong! Please try again.");
                return false;
            }
        });        
    }
});