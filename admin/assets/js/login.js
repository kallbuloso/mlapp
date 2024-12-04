
 function showMessage(id,color,message){
   $(id).addClass('text-'+color);
   $(id).html(message);
   setTimeout(function(){
      $(id).html('');
   }, 3000);
 }


$("#loginForm").on('click', function(e){

  $("#loginForm").prop('disabled', true);
  $("#loginForm").html('Aguarde...');

  let username = $("#username").val();
  let password = $("#password").val();

  var dados = new Object();
  dados.username = username;
  dados.password = password;

  var dataJson = JSON.stringify(dados);

  $.post('Controller/Login.php', {type:'login', data: dataJson}, function(data){

    $("#loginForm").prop('disabled', false);
    $("#loginForm").html('ENTRAR');

     try {

       var obj = JSON.parse(data);

       if(obj.erro){
         showMessage('#response_login','danger',obj.message);
         return false;
       }else{
         showMessage('#response_login','success',obj.message);
          location.href="dashboard";
       }

     } catch (e) {
       showMessage('#response_login','danger','Desculpe, tente novamente');
       return false;
     }
  });


});
