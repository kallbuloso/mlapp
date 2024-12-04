$(function () {
    app_url = $('meta[name="url_system"]').attr('content');
});


function importMlProducts() {
    
    let account = $("#account_import").val();
    
    if(account == ""){
        let configs = {
            title: "Erro, desculpe!",
            message: "Escolha qual conta deseja importar os produtos.",
            status: TOAST_STATUS.DANGER,
            timeout: 5000
        }
        Toast.setTheme(TOAST_THEME.DARK);
        Toast.create(configs);
        return false;
    }

    $("#btnimport").prop('disabled', true);
    $("#btnimport").html('Aguarde <i class="fa fa-spinner fa-spin" ></i> ');

    $.post("Controller/MercadoLivre.php", {account, type: "getProducts" }, function (res) {

        $("#btnimport").prop('disabled', false);
        $("#btnimport").html('Iniciar Importação');

        try {

            const obj = JSON.parse(res);

            if (obj.erro) {
                let configs = {
                    title: "Erro, desculpe!",
                    message: obj.message,
                    status: TOAST_STATUS.DANGER,
                    timeout: 5000
                }
                Toast.setTheme(TOAST_THEME.DARK);
                Toast.create(configs);
                return false;
            } else {

                let configs = {
                    title: "Sucesso!",
                    message: obj.message,
                    status: TOAST_STATUS.SUCCESS,
                    timeout: 5000
                }
                Toast.setTheme(TOAST_THEME.DARK);
                Toast.create(configs);

                const table = document.getElementById("tableProductsImport");
                const data = obj.data;

                $("#tableProductsImport").html('');

                data.forEach((e) => {

                    let price = e.price.toLocaleString('pt-br', { style: 'currency', currency: 'BRL' });
                    let iconImport = app_url + (e.import.erro ? "/admin/assets/img/icon-error.png" : "/admin/assets/img/icon-check.png");
                    let messageImport = e.import.erro ? e.import.message : "Produto importado com sucesso!";
                    let colorMessage = e.import.erro ? 'text-danger' : "text-success";

                    table.innerHTML += `
                    <tr>
                           <td class="text-center" > <img src="${iconImport}" width="40" > </td>
                           <td> <img src="${e.thumbnail}" /> </td>
                           <td> ${e.id} </td>
                           <td> 
                                ${e.title} <br />
                                <span class="${colorMessage}" >${messageImport}</span> 
                           </td>
                           <td> ${price} </td>
                           <td> 
                              <a href="${e.permalink}" class="text-white btn btn-info" target="_blank" >Ver <i class="fa fa-external-link" ></i> </a>
                           </td>
                    </tr>`;

                });

            }

        } catch (error) {

        }
    });
}