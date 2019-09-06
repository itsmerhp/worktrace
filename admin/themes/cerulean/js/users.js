$(function() {
   $(document).on('click','.popupModal',function() {
     //e.preventDefault();   
     $('#modal').find('#modalContent')
     .html("<img src='"+$(this).find("img").attr('src')+"' style='max-width:100%;border:2px solid #ccc;'/>");
     $('#modal').modal('show');
     //.load($(this).attr('href'));
   });

   $('.modal-content').css('text-align','center');
});