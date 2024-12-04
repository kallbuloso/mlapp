$(function(){
  $('#product_price').maskMoney({
    prefix: 'R$ ',
    thousands: '.',
    decimal: ','
 }); 

 url_system = $('meta[name="url_system"]').attr('content');
 

    
 table_transactions = $('#table_transactions').DataTable( {
      "order": [[ 4, "desc" ]],
      "processing": true,
      "serverSide": true,
      "ajax": {
          "url": 'Controller/getTransactions.php',
          "type": "POST"
      },
      "columns": [
          { "data": "id" },
          { "data": "data" },
          { "data": "valor" },
          { "data": "status" },
          { "data": "qtd_compras" },
          { "data": "comprador" },
          { "data": "opc" }

      ]
  } );
  
  table_transactions.column(4).width('20px');

})

function showMessage(id,color,message){
$(id).addClass('text-'+color);
$(id).html(message);
setTimeout(function(){
   $(id).html('');
}, 3000);
}

function setTag(tag){

let id = $("#idview_transaction").val();

$.post('Controller/Transaction.php', {type:'setTag', id: id, tag:tag}, function(data){
  try {

    var obj = JSON.parse(data);

    if(obj.erro){
      showMessage('#response_transaction_view','danger',obj.message);
      return false;
    }else{
      showMessage('#response_transaction_view','success',obj.message);

      $("#tag_transaction_modal").html(tag);

      if(tag === "Processando"){
        $("#tag_transaction_modal").removeClass('bg-success');
        $("#tag_transaction_modal").addClass('bg-warning');
      }else{
        $("#tag_transaction_modal").removeClass('bg-warning');
        $("#tag_transaction_modal").addClass('bg-success');
      }


    }

  } catch (e) {
    showMessage('#response_transaction_view','danger','Desculpe, tente novamente');
    return false;
  }
});
}

function getTransactionByDetail(id){

 $.post('Controller/Transaction.php', {type:'get_detail', id: id}, function(data){

    try {

      var obj = JSON.parse(data);

      if(obj.erro){
        showMessage('#response_sale','danger',obj.message);
        return false;
      }else{
        showMessage('#response_sale','success',obj.message);

        // transaction


        if(obj.transaction.tag !== null){
          $("#tag_transaction_modal").html(obj.transaction.tag);

          if(obj.transaction.tag === "Processando"){
            $("#tag_transaction_modal").removeClass('bg-success');
            $("#tag_transaction_modal").addClass('bg-warning');
          }else{
            $("#tag_transaction_modal").removeClass('bg-warning');
            $("#tag_transaction_modal").addClass('bg-success');
          }
        }else{
          $("#tag_transaction_modal").html('');
        }


        $("#idview_transaction").val(obj.transaction.id);
        $("#valor_transaction").html(obj.transaction.valor);
        $("#origem_transaction").html(obj.transaction.origem);
        $("#qtd_transaction").html(obj.transaction.qtd);
        $("#number_transaction").html(obj.client.number);
        $("#status_transaction").html(obj.transaction.status);
        $("#date_transaction").html(obj.transaction.created);
        $("#product_name").html(obj.product.name);
        
        

        $("#link_checkout").attr('href', url_system + '/produto/' + obj.transaction.product_id);
        if(obj.transaction.status_read == "approved" && obj.product ){
          if(obj.product.uniq_link != "not"){
              
            let nameLinkDown = obj.product.uniq_link !== null ? obj.product.uniq_link.length > 30 ? obj.product.uniq_link.substr(0, 15) + "..." + obj.product.uniq_link.substr(45) : obj.product.uniq_link : "----";

            $("#link_transaction").html(nameLinkDown + " <i class='fa fa-link' ></i>");
            $("#link_transaction").attr('href', obj.product.uniq_link);
            
          }else{
              
            let nameDown = obj.transaction.token !== null ? obj.transaction.token.length > 20 ? "..." + obj.transaction.token.substr(20) : obj.transaction.token : "----";

            $("#link_transaction").html( nameDown + " <i class='fa fa-link' ></i>");
            $("#link_transaction").attr('href', url_system + '/download/' + obj.transaction.token);
          }
        }



        if(obj.client){

          // client
          var imgClient =  'https://ui-avatars.com/api/?name='+obj.client.nome+'&size=25&background=random';
          $("#avatar_client").attr('src', imgClient);
          $("#nome_client").html(obj.client.nome);
          $("#number_transaction").html(obj.client.number);
          $("#email_client").html(obj.client.email);

          obj.text_zap ? $("#text_zap_client").val(obj.text_zap) : null;

      }


         $("#modalViewTransaction").modal('show');

      }

    } catch (e) {
      showMessage('#response_sale','danger','Desculpe, tente novamente');
      return false;
    }
 });

}
