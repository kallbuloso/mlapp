$(function(e){
    
  url_system = $('meta[name="url_system"]').attr('content');
  
});


function showMessage(id, color, message) {
$(id).addClass('text-' + color);
$(id).html(message);
setTimeout(function () {
  $(id).html('');
}, 3000);
}


$("#search_client").keypress(function (e) {
var code = (e.keyCode ? e.keyCode : e.which);
if (code == 13) {
  initSearch();
}
});

$("#initSearch").on('click', () => {
initSearch();
});


$(".copy_info").on('click', function(e){
  let info = $(this).attr('data-copy');
  copyToClipboard(info);
});


function copyToClipboard(text) {
navigator.clipboard.writeText(text)
  .then(() => {
    let configs = {
      title: "Copiado!",
      message: text,
      status: TOAST_STATUS.SUCCESS,
      timeout: 5000
    }
    Toast.setTheme(TOAST_THEME.DARK);
    Toast.create(configs);
  })
  .catch(err => {
    let configs = {
      title: "Erro ao copiar!",
      message: "Desculpe, não foi possível copiar." ,
      status: TOAST_STATUS.DANGER,
      timeout: 5000
    }
    Toast.setTheme(TOAST_THEME.DARK);
    Toast.create(configs);
  });
}

function initSearch() {
const search = $("#search_client").val();
if (search !== "") {
  location.href = url_system + '/admin/clients_search?term=' + search;
}
}

function listOrder() {
const selectElement = document.getElementById("orderList");
const selectedValue = selectElement.value;

let currentUrl = window.location.href;
let separator = currentUrl.indexOf("?") === -1 ? "?" : "&";

// Verifica se o parâmetro 'desc' já existe na URL
const descParamIndex = currentUrl.indexOf("desc=");
if (descParamIndex !== -1) {
  // Se 'desc' já existe, atualiza seu valor
  const nextAmpersandIndex = currentUrl.indexOf("&", descParamIndex + 1);
  if (nextAmpersandIndex === -1) {
    // 'desc' é o último parâmetro da URL
    currentUrl = currentUrl.slice(0, descParamIndex) + "desc=" + (selectedValue === "desc");
  } else {
    // 'desc' não é o último parâmetro da URL
    currentUrl = currentUrl.slice(0, descParamIndex) + "desc=" + (selectedValue === "desc") + currentUrl.slice(nextAmpersandIndex);
  }
} else {
  // Se 'desc' não existe, adiciona-o à URL
  currentUrl += separator + "desc=" + (selectedValue === "desc");
}

// Redireciona para a URL atualizada
window.location.href = currentUrl;
}

function statusLang(status) {
  switch (status) {
      case 'approved':
          return `<span class="badge bg-success" >Aprovado</span>`;
      case 'pending':
          return '<span class="badge bg-secondary" >Pendente</span>';
      case 'waiting':
          return '<span class="badge bg-secondary" >Aguardando</span>';
      default:
          return `<span class="badge bg-secondary" >${status}</span>`;
  }
}

function viewBuys(id, isModal=false){
  
 $("#bodyViewBuys").html(`<div class="col-md-12 text-center pt-3" ><h1><i class="fa fa-spin fa-spinner" ></i></h1></div>`);
 isModal ? null : $("#modalViewBuys").modal('show');
  
  $.post('Controller/Client.php', { type: 'viewBuys', id: id }, function (data) {

    try {

      var obj = JSON.parse(data);

      if (obj.erro) {
           let configs = {
              title: "Erro, desculpe.",
              message: obj.message,
              status: TOAST_STATUS.DANGER,
              timeout: 5000
            }
            Toast.setTheme(TOAST_THEME.DARK);
            Toast.create(configs);
            return false;
      } else {
          
          if(!isModal){
              let configs = {
                  title: "Compras localizadas.",
                  message: obj.message,
                  status: TOAST_STATUS.SUCCESS,
                  timeout: 5000
                }
              Toast.setTheme(TOAST_THEME.DARK);
              Toast.create(configs);
          }

          
          
          let tableViewBuysHtml = `<div class="col-md-12" > <div class="table-responsive"> <table id="tableViewBuys" class="table-responsive table">
                                    <thead>
                                      <tr>
                                        <th scope="col">Id</th>
                                        <th scope="col">Produto</th>
                                        <th scope="col">Status</th>
                                        <th scope="col">Data</th>
                                        <th scope="col">Downloads</th>
                                      </tr>
                                    </thead>
                                    <tbody></tbody>
                                  </table></div></div>`;
                                  
          $("#bodyViewBuys").html(tableViewBuysHtml);
          
              const tbody = document.querySelector("#tableViewBuys tbody");
      
              obj.data.forEach(buy => {
                  
                  
               let statusName = statusLang(buy.status);
                  
               let nameProd = buy.product_name !== null ? buy.product_name.length > 20 ? buy.product_name.substr(0, 20) + "..." : buy.product_name : "----";
               let titleProd = buy.product_name !== null ? buy.product_name.length : "Produto não localizado";
               let nameDown = "";
               let linkExternal = "";
               let iconBan = buy.download_active === 1 ? "fa-lock-open" : "fa-lock";
               let colorBan = buy.download_active === 1 ? "success" : "danger";
               
               if(buy.uniq_link != "not" && buy.token === null){
                  nameDown = buy.uniq_link !== null ? buy.uniq_link.length > 30 ? buy.uniq_link.substr(0, 15) + "..." : buy.uniq_link : "----";
                  linkExternal = buy.uniq_link;
               }else{
                  nameDown = buy.token !== null ? buy.token.length > 20 ? "..." + buy.token.substr(20) : buy.token : "----";
                  linkExternal = `${url_system}/download/${buy.token}`;
               }
              
                const tr = document.createElement("tr");
                tr.innerHTML = `
                  <td>${buy.id}</td>
                  <td><a title="${titleProd}" href="${url_system}/produto/${buy.product_id}" target="_blank" >${nameProd}</td>
                  <td>${statusName}</td>
                  <td>${buy.data_buy}</td>
                  <td>( <i class="fa fa-download" ></i> ${buy.count_down}) <a href="${linkExternal}" class="btn btn-secondary" target="_blank" style="box-shadow: none;padding: 5px 10px 5px 10px!important;border-radius: 0px" > <i class="fa fa-link" ></i> </a> <button onclick="lockDownload(${buy.id}, ${id});" style="padding: 5px 10px 5px 10px!important;border-radius: 0px" class="btn btn-${colorBan}" > <i class="fa ${iconBan}" ></i> </button> </td>
                `;
                tbody.appendChild(tr);
              });

      }

    } catch (e) {
    let configs = {
      title: "Erro, desculpe.",
      message: "Erro no servidor",
      status: TOAST_STATUS.DANGER,
      timeout: 5000
    }
    Toast.setTheme(TOAST_THEME.DARK);
    Toast.create(configs);
      return false;
    }
  });
}


function lockDownload(id, client_id){
   $.post('Controller/Transaction.php', { type: 'lockDownload', id: id }, function (data) {

    try {

      var obj = JSON.parse(data);

      if (obj.erro) {
        let configs = {
          title: "Erro, desculpe.",
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
        
        viewBuys(client_id, true);
        return false;

      }

    } catch (e) {
        let configs = {
          title: "Erro, desculpe.",
          message: "Erro no servidor",
          status: TOAST_STATUS.DANGER,
          timeout: 5000
        }
        Toast.setTheme(TOAST_THEME.DARK);
        Toast.create(configs);
        return false;
    }
  });
}

function removeClient(id) {
if (confirm('Deseja continuar com a remoção')) {

  $.post('Controller/Client.php', { type: 'delete', id: id }, function (data) {

    try {

      var obj = JSON.parse(data);

      if (obj.erro) {
        showMessage('#response_clients', 'danger', obj.message);
        return false;
      } else {
        showMessage('#response_clients', 'success', obj.message);
        setTimeout(function () {
          location.href = "";
        }, 2000);
      }

    } catch (e) {
      showMessage('#response_clients', 'danger', 'Desculpe, tente novamente');
      return false;
    }
  });

} else {
  return false;
}
}
