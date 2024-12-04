$(function(e){
 
let ele = document.querySelector( '#template_ml_email' );
$('#template_ml_email').trumbowyg({
    btns: [
        ['viewHTML'],
        ['undo', 'redo'], // Only supported in Blink browsers
        ['formatting'],
        ['strong', 'em', 'del'],
        ['superscript', 'subscript'],
        ['link'],
        ['insertImage'],
        ['justifyLeft', 'justifyCenter', 'justifyRight', 'justifyFull'],
        ['unorderedList', 'orderedList'],
        ['horizontalRule'],
        ['removeformat'],
        ['fullscreen']
    ]
});
 
 function showMessage(id,color,message){
   $(id).addClass('text-'+color);
   $(id).html(message);
   setTimeout(function(){
      $(id).html('');
   }, 3000);
 }

 $("#saveSetting").on('click', function(data){

   $("#saveSetting").prop('disabled', true);
   $("#saveSetting").html('Aguarde...');

   let username         = $("#username").val();
   let pwd              = $("#password").val();
   let template_ml_message   = $("#template_ml_message").val();
   let title_template_email   = $("#title_template_email").val();
   let template_ml_email = $('#template_ml_email').trumbowyg('html');

   var data = new Object();

   data.username        = username;
   data.pwd             = pwd;
   data.template_ml_message = template_ml_message;
   data.title_template_email = title_template_email;
   data.template_ml_email = template_ml_email;

   if(
     data.access_token_mp == "" ||
     data.username == "" || data.template_ml_email == "" || data.title_template_email == ""){
      let configs = {
        title: "Erro, desculpe!",
        message: "Preencha todos os campos",
        status: TOAST_STATUS.DANGER,
        timeout: 5000
      }
      Toast.setTheme(TOAST_THEME.DARK);
      Toast.create(configs);
     return false;
   }

   $.post('Controller/Settings.php', {data: JSON.stringify(data), type: 'edit'}, function(data){

     $("#saveSetting").prop('disabled', false);
     $("#saveSetting").html('Editar');

      try {

        var obj = JSON.parse(data);

        if(obj.erro){
          let configs = {
            title: "Erro, desculpe!",
            message: obj.message,
            status: TOAST_STATUS.DANGER,
            timeout: 5000
          }
          Toast.setTheme(TOAST_THEME.DARK);
          Toast.create(configs);
          return false;
        }else{
          let configs = {
            title: "Sucesso!",
            message: obj.message,
            status: TOAST_STATUS.SUCCESS,
            timeout: 5000
          }
          Toast.setTheme(TOAST_THEME.DARK);
          Toast.create(configs);
        }

      } catch (e) {
        let configs = {
          title: "Erro, desculpe!",
          message: 'Desculpe, tente novamente',
          status: TOAST_STATUS.DANGER,
          timeout: 5000
        }
        Toast.setTheme(TOAST_THEME.DARK);
        Toast.create(configs);

        return false;
      }
   });

 });
    
});
