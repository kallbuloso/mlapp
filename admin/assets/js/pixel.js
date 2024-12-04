$(function(){
   url_system = $('meta[name="url_system"]').attr('content');
})


 function showMessage(id,color,message){
   $(id).addClass('text-'+color);
   $(id).html(message);
   setTimeout(function(){
      $(id).html('');
   }, 3000);
 }

 $("#savePixel").on('click', function(data){

   $("#savePixel").prop('disabled', true);
   $("#savePixel").html('Aguarde...');

   let id_pixel      = $("#id_pixel").val();
   let token_pixel   = $("#token_pixel").val();

   var data = new Object();

   data.id_pixel     = id_pixel;
   data.token_pixel  = token_pixel;

   if( data.id_pixel == "" || data.token_pixel == "" ){
     showMessage('#response_pixel','danger','Preencha todos os campos');
     return false;
   }

   $.post('Controller/Pixel.php', {data: JSON.stringify(data), type: 'edit'}, function(data){

     $("#savePixel").prop('disabled', false);
     $("#savePixel").html('Adicionar');

      try {

        var obj = JSON.parse(data);

        if(obj.erro){
          showMessage('#response_pixel','danger',obj.message);
          return false;
        }else{
          showMessage('#response_pixel','success',obj.message);
          setTimeout(function(){
            location.href="";
          }, 2000);
        }

      } catch (e) {
        showMessage('#response_pixel','danger','Desculpe, tente novamente');
        return false;
      }
   });

 });

 $("#btnEditProd").on('click', function(){

   $("#btnEditProd").prop('disabled', true);
   $("#btnEditProd").html('Aguarde...');

   let id          = $("#edit_product_id").val();
   let name        = $("#edit_product_name").val();
   let price       = $("#edit_product_price").val();
   let status      = $("#edit_product_status").val();
   let description = $("#edit_product_description").val();

   var data = new Object();

   data.id          = id;
   data.name        = name;
   data.price       = price;
   data.status      = status;
   data.description = description;

   if(data.id == "" || data.name == "" || data.price == "" || data.status == "" || data.description == ""){
     showMessage('#response_edit','danger','Preencha todos os campos');
     return false;
   }

   $.post('Controller/Product.php', {data: JSON.stringify(data), type: 'edit'}, function(data){

     $("#btnEditProd").prop('disabled', false);
     $("#btnEditProd").html('Salvar');

      try {

        var obj = JSON.parse(data);

        if(obj.erro){
          showMessage('#response_edit','danger',obj.message);
          return false;
        }else{
          showMessage('#response_edit','success',obj.message);
          setTimeout(function(){
            location.href="";
          }, 2000);
        }

      } catch (e) {
        showMessage('#response_edit','danger','Desculpe, tente novamente');
        return false;
      }
   });

 });

 function getProductByEdit(id){

   $.post('Controller/Product.php', {type:'get', id: id}, function(data){

      try {

        var obj = JSON.parse(data);

        if(obj.erro){
          showMessage('#response_product','danger',obj.message);
          return false;
        }else{
          showMessage('#response_product','success',obj.message);

          $("#edit_product_id").val(obj.data.id);
          $("#edit_product_name").val(obj.data.name);
          $("#edit_product_price").val(obj.data.price);
          $("#edit_product_status").val(obj.data.status);
          $("#edit_product_description").val(obj.data.description);

          $("#moedalEditProduct").modal('show');

        }

      } catch (e) {
        showMessage('#response_product','danger','Desculpe, tente novamente');
        return false;
      }
   });

 }

 function deleteProd(id){
   if(confirm('Deseja continuar com a remoção')){

     $.post('Controller/Product.php', {type:'delete', id: id}, function(data){

        try {

          var obj = JSON.parse(data);

          if(obj.erro){
            showMessage('#response_product','danger',obj.message);
            return false;
          }else{
            showMessage('#response_product','success',obj.message);
            setTimeout(function(){
              location.href="";
            }, 2000);
          }

        } catch (e) {
          showMessage('#response_product','danger','Desculpe, tente novamente');
          return false;
        }
     });

   }else{
     return false;
   }
 }
