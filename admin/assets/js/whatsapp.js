

$("#disconnect").on('click', (e) => {

    $("#disconnect").html('Aguarde <i class="fa fa-spinner fa-spin" ></i> ');
    $("#disconnect").prop('disabled', true);

    $.post("Controller/Whatsapp.php", {type: 'disconnect'}, (res) => {
        setTimeout(() => {
            location.href="";
        }, 5000);

    });

});

$("#connectWpp").on('click', (e) => {

    $("#connectWpp").html('Aguarde <i class="fa fa-spinner fa-spin" ></i> ');
    $("#connectWpp").prop('disabled', true);

    $.post("Controller/Whatsapp.php", {type: 'connect'}, (res) => {

        try {

            let obj = JSON.parse(res);

            if(obj.erro){
                $("#response_whatsapp_api").removeClass();
                $("#response_whatsapp_api").addClasss('text-danger');
                $("#response_whatsapp_api").html(obj.message);
            }else{

                if(obj.is_connected){
                    $("#response_whatsapp_api").removeClass();
                    $("#response_whatsapp_api").addClasss('text-success');
                    $("#response_whatsapp_api").html('Você já está conectado');
                }else{

                    $("#cardConnect").hide();
                    $("#cardQrcode").show();
                    $("#qrcodeWpp").attr('src', obj.base64);

                    vStatus();

                }

            }
            
        } catch (error) {
            $("#response_whatsapp_api").addClasss('text-danger');
            $("#response_whatsapp_api").html('Erro interno.');
        }


        setTimeout(() => {
            $("#response_whatsapp_api").html('');
        }, 5000);

        $("#connectWpp").html('Conectar <i class="fa fa-plug"></i>');
        $("#connectWpp").prop('disabled', false);

    });

});


function vStatus(){
    let verifyStatus = setInterval(() => {
        $.post("Controller/Whatsapp.php", {type: 'status'}, (res) => {

            try {
    
                let obj = JSON.parse(res);
    
                if(obj.erro !== true){ 
    
                    if(obj.is_connected){
                        $("#cardQrcode").hide();
                        $("#cardConected").show();
                        clearInterval(verifyStatus);
                        location.href="";
                    }
                }
                
            } catch (error) {
                $("#response_whatsapp_api").addClasss('text-danger');
                $("#response_whatsapp_api").html('Erro interno.');
            }
    
            setTimeout(() => {
                $("#response_whatsapp_api").html('');
            }, 5000);
            
        });
    }, 2000);
}