$(function(){
    url_system = $('meta[name="url_system"]').attr('content');
});


$('#modalAddAccountMKT').on('hide.bs.modal', function (event) {
    location.href="";
});


function saveAccount(id){
    
    let access_token = $("#access_token_" + id).val();
    
    if(access_token === "" || access_token === null){
         let configs = {
            title: "Erro, desculpe!",
            message: 'O campo Access Token não pode ser vazio',
            status: TOAST_STATUS.DANGER,
            timeout: 5000
          }
          Toast.setTheme(TOAST_THEME.DARK);
          Toast.create(configs);
          return false;
    }
    
    
    $("#btnSave_" + id ).prop('disabled', true);
    $("#btnSave_" + id ).html('<i class="fa fa-spinner fa-spin" ></i>');
    
          
    $.post('Controller/Marketplace.php', {id, access_token, type:'save'}, function(res){
            
          try {
    
            var obj = JSON.parse(res);
    
            if(obj.erro){
                
              $("#btnSave_" + id).prop('disabled', false);
              $("#btnSave_" + id).html('<i class="fa fa-save" ></i>');
        
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
            
              $("#btnSave_" + id).prop('disabled', false);
              $("#btnSave_" + id).html('<i class="fa fa-save" ></i>');
    
            }
    
          } catch (e) {
              
                          
             $("#btnSave_" + id).prop('disabled', false);
             $("#btnSave_" + id).html('<i class="fa fa-save" ></i>');
             
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
    
}

function removeAccount(id){
    if(confirm("Deseja realmente remover está conta?")){
        
        $("#btnRemove_" + id).prop('disabled', true);
        $("#btnRemove_" + id).html('<i class="fa fa-spin fa-spinner" ></i>');
        
        $.post('Controller/Marketplace.php', {id, type:'delete'}, function(res){
            
          try {
    
            var obj = JSON.parse(res);
    
            if(obj.erro){
                
              $("#btnRemove_" + id).prop('disabled', false);
              $("#btnRemove_" + id).html('<i class="fa fa-trash" ></i>');
        
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
            
              setTimeout(() => {
                location.href="";
              }, 2000);
    
            }
    
          } catch (e) {
              
                          
             $("#btnRemove_" + id).prop('disabled', false);
             $("#btnRemove_" + id).html('<i class="fa fa-trash" ></i>');
             
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
        
        
    }else{
        return false;
    }
}

function modalAddAccountMKT(t=false){
    if(!t){
        $("#modalAddAccountMKT").modal('show');
        return false;
    }
    
    
    let access_token = $("#access_token_new_account").val();
    
    if(access_token === "" || access_token === null){
         let configs = {
            title: "Erro, desculpe!",
            message: 'O campo Access Token não pode ser vazio',
            status: TOAST_STATUS.DANGER,
            timeout: 5000
          }
          Toast.setTheme(TOAST_THEME.DARK);
          Toast.create(configs);
          return false;
    }
    
    
    $("#btnAddAccountMKT").prop('disabled', true);
    $("#btnAddAccountMKT").html('Aguarde');
    
    $.post('Controller/Marketplace.php', {access_token, type:'add'}, function(res){
        
      try {

        var obj = JSON.parse(res);

        if(obj.erro){
            
         $("#btnAddAccountMKT").prop('disabled', false);
         $("#btnAddAccountMKT").html('Adicionar');
    
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
          
          let webhook = `${url_system}/callback/${obj.id}`;
          $("#webhook_lasted_inserted").val(webhook);
          $("#divWebhookNewAccount").show();
          
          $("#btnAddAccountMKT").prop('disabled', false);
          $("#btnAddAccountMKT").html('Adicionar');
    
          $("#btnAddAccountMKT").hide();

        }

      } catch (e) {
          
         $("#btnAddAccountMKT").prop('disabled', false);
         $("#btnAddAccountMKT").html('Adicionar');
         
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
    
    

}



 