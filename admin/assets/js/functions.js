$(function(){

  page_system = $('meta[name="page_system"]').attr('content');
  $("#page_"+page_system).addClass(' bg-gradient-primary');
  
   if(page_system == "clients_search"){
      $("#page_clients").addClass('bg-gradient-primary');
      $('#search_client').focus();
   }
   
   if(page_system == "import_products_ml"){
      $("#page_products").addClass('bg-gradient-primary');
   }

});
