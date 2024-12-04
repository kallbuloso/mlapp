$(function(){

     counterOrderBan();
     app_url = $("meta[name='url_site']").attr("content");

     var SPMaskBehavior = function (val) {
     return val.replace(/\D/g, '').length === 11 ? '(00) 00000-0000' : '(00) 0000-00009';
     },
     spOptions = {
       onKeyPress: function(val, e, field, options) {
           field.mask(SPMaskBehavior.apply({}, arguments), options);
         }
     };

     $('#number_buy').mask(SPMaskBehavior, spOptions);

     if($("meta[name='page_name']").attr('content') != "undefined"){

       pagename = $("meta[name='page_name']").attr('content');

       if(pagename == "compras"){

          var transactions_list = b64DecodeUnicode($("#transactions_list").val());
          let transactions = JSON.parse(transactions_list);

          for (let row in transactions) {
            var product_id = transactions[row].product_id;
            var valor      = transactions[row].valor;

            var data = new Date(transactions[row].created);
            var dia = data.getDate().toString().padStart(2, '0');
            var mes = (data.getMonth() + 1).toString().padStart(2, '0');
            var ano = data.getFullYear().toString();

            var dataFormatada = `${dia}/${mes}/${ano}`;

            var btnDow = "";

            if(transactions[row].status === "approved"){
               var status = '<span class="badge" style="background-color:green;" >Aprovado</span>';
               var btnDow = '<a target="_blank" href="'+app_url+'/down/'+transactions[row].token+'" class="text-white btn btn-sm btn-info"> Baixar <i class="fa fa-download" ></i></a>';
            }else {
               var status = '<span class="badge badge-secondary" style="background-color:gray;" >Pendente</span>';
            }

            var tr = `<tr>
                        <td>`+transactions[row].id+`</td>
                        <td>`+product_id+`</td>
                        <td>`+valor+`</td>
                        <td>`+dataFormatada+`</td>
                        <td>`+status+`</td>
                        <td>`+btnDow+`</td>
                   </tr>`;

            $("#dataListTransactions").append(tr);

          }

          table_transactions = new DataTable('#table_transactions');

       }

     }




});

function b64DecodeUnicode(str) {
     return decodeURIComponent(atob(str).split('').map(function(c) {
         return '%' + ('00' + c.charCodeAt(0).toString(16)).slice(-2);
     }).join(''));
 }


function showMsg(color,msg){
  $(".response_send").html('<span style="width:100%;" class="alert alert-'+color+'" >'+msg+'</span>');
  setTimeout(function(){
    $(".response_send").html('');
  }, 3000);
}

function counterOrderBan(){
    var countDownDate = new Date().getTime() + 600000; // 10 minutes from now

    var x = setInterval(function() {

    var now = new Date().getTime();

    var distance = countDownDate - now;

    var minutes = Math.floor((distance % (1000 * 60 * 60)) / (1000 * 60));
    var seconds = Math.floor((distance % (1000 * 60)) / 1000);

    document.getElementById("countdown").innerHTML =  (minutes < 10 ? "0" : "") + minutes + ":" + (seconds < 10 ? "0" : "") + seconds;

      if (distance < 0) {
            countDownDate = new Date().getTime() + 600000; // reset the countDownDate to 10 minutes from now
       }

    }, 1000);
}

 function disconnectMp(){

   if(confirm("Você te certeza que dejesa desconectadar sua conta?")){
     $("#btnMp").prop('disabled', true);
     $("#btnMp").prop(' <i class="fa fa-spin fa-spinner" ></i> Aguarde');

     $.post(app_url + '/disconnect' , {
       request:true
     }, function(data){
       try {

         var obj = JSON.parse(data);

         if(obj.erro){
           $("#btnMp").prop('disabled', true);
           $("#btnMp").prop(' <i class="fa fa-unlink" ></i> Desconectar Mercado Pago');
           $("#error_info").html(obj.message);
         }else{
           location.href= app_url + "/indi";
         }

       } catch (e) {
           $("#error_info").html('Desculpe, tente novamente mais tarde');
       }
     });
   }else{
     return false;
   }

 }


 function setDownload(token){
   $("#divpix").hide();
   $("#divDownload").show();
   let link = app_url + '/download/' + token;
   $("#linkDownload").attr('href', link);
 }

  function getStatus(reference){
      var intervalStatus = setInterval(function(){
          $.post(app_url + '/status', {
            reference: reference
          }, function(data){

            var obj = JSON.parse(data);

            if(obj.status == "approved"){
              clearInterval(intervalStatus);
              //setDownload(obj.token);
              location.href= app_url + '/success/' + reference;
            }

          });
      }, 2000);
  }

$("#btnCreateAccount").on('click', function(){
  let nome = $("#nome").val();
  let email = $("#email").val();
  let senha = $("#password").val();
  let request = true;
  $.post(app_url + '/create', {
    nome, email, senha, request
  }, function(data){
    try {

      var obj = JSON.parse(data);

      if(obj.erro){
        $("#error_info").html(obj.message);
      }else{
        location.href=app_url + "/conta";
      }

    } catch (e) {
      $("#error_info").html('Não foi possivel criar sua conta');
    }
  });
});

$("#btnLogin").on('click', function(){
  let email    = $("#email").val();
  let password = $("#password").val();
  let request  = true;
  $.post(app_url + '/login' , {
    email,
    password,
    request
  }, function(data){
    try {

      var obj = JSON.parse(data);

      if(obj.erro){
        $("#error_info").html(obj.msg);
      }else{
        location.href= app_url + "/conta";
      }

    } catch (e) {
        $("#error_info").html('Desculpe, tente novamente mais tarde');
    }
  });
});

$("#buyCheckout").on('click', function(){

  $("#buyCheckout").prop('disabled', true);
  $("#buyCheckout").html('Aguarde...');

  let nome       = $('#nome_buy').val();
  let id         = $("#product_id").val();
  let email      = $("#email_buy").val();
  let number     = $('#number_buy').val();
  let doc        = $('#doc_buy').val();
  let method     = $('#type_pay').val();
 
  let address_ip = $('meta[name="address_ip"]').prop('content');
  let user_agent = $('meta[name="user_agent"]').prop('content');

  if(nome == "" || email == "" || number == ""){
    showMsg('danger','Preencha todos os campos');
    $("#buyCheckout").prop('disabled', false);
    $("#buyCheckout").html('Prosseguir');
    return false;
  }

  var dados = new Object();

  dados.nome       = nome;
  dados.number     = number;
  dados.id         = id;
  dados.email      = email;
  dados.address_ip = address_ip;
  dados.user_agent = user_agent;
  dados.doc        = doc;
  dados.qtd        = 1;

  var objJson = JSON.stringify(dados);

  $.post(app_url + '/process', {method: method, buy: true, dados: objJson}, function(data){

    $("#buyCheckout").prop('disabled', false);
    $("#buyCheckout").html('Prosseguir');

    try {

      var obj = JSON.parse(data);

      if(obj.erro){
        showMsg('danger',obj.message);
        return false;
      }else{

        if(method == "pix"){
          $("#qrcodepix").attr('src', obj.qrcodepix);
          $("#pixcode").val(obj.pixcode);
          $("#divpayment").hide(100);
          $("#divpix").show(100);
          $("#fBtns").hide();
 
          getStatus(obj.reference);
        }else{
          location.href=obj.link;
        }

      }

    } catch (e) {
      showMsg('danger','Desculpe, tente novamente mais tarde');
      return false;
    }

  });

});
