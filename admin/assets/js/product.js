$(function () {

  app_url = $("meta[name='url_system']").attr("content");

  $('#product_price').maskMoney({
    prefix: 'R$ ',
    thousands: '.',
    decimal: ','
  });

  $('#edit_product_price').maskMoney({
    prefix: 'R$ ',
    thousands: '.',
    decimal: ','
  });
      
   url_system = $('meta[name="url_system"]').attr('content');

});



$("#search_product").keypress(function (e) {
var code = (e.keyCode ? e.keyCode : e.which);
if (code == 13) {
   initSearchProd();
}
});

$("#initSearchProds").on('click', () => {
    initSearchProd();
});


function initSearchProd() {
  const search = $("#search_product").val();
    let currentUrl = window.location.href;
    let separator = currentUrl.indexOf("?") === -1 ? "?" : "&";

    // Se houver parâmetros na URL, adicione "&" ao invés de "?"
    if (currentUrl.indexOf("?") !== -1) {
        separator = "&";
    }

    if (search !== "") {
        location.href = url_system + '/admin/products' + separator + 'term=' + search;
    }
}

function listOrderProds() {
  const selectElement = document.getElementById("orderListProds");
  const selectedValue = selectElement.value;

  let currentUrl = window.location.href;
  let separator = currentUrl.indexOf("?") === -1 ? "?" : "&";

  // Verifica se o parâmetro 'order' já existe na URL
  const orderParamIndex = currentUrl.indexOf("order=");
  if (orderParamIndex !== -1) {
    // Se 'order' já existe, atualiza seu valor
    const nextAmpersandIndex = currentUrl.indexOf("&", orderParamIndex + 1);
    if (nextAmpersandIndex === -1) {
      // 'order' é o último parâmetro da URL
      currentUrl = currentUrl.slice(0, orderParamIndex) + "order=" + selectedValue;
    } else {
      // 'order' não é o último parâmetro da URL
      currentUrl = currentUrl.slice(0, orderParamIndex) + "order=" + selectedValue + currentUrl.slice(nextAmpersandIndex);
    }
  } else {
    // Se 'order' não existe, adiciona-o à URL
    currentUrl += separator + "order=" + selectedValue;
  }

  // Redireciona para a URL atualizada
  window.location.href = currentUrl;
}

function copyToClipboard(text) {
  var $temp = $("<input>");
  $("body").append($temp);
  $temp.val(text).select();
  if (document.execCommand("copy")) {
    showMessage('#response_product', 'success', 'Link copiado!');
    $temp.remove();
    return true;
  } else {
    $temp.remove();
    return false;
  }
}

function showMessage(id, color, message) {
  $(id).addClass('text-' + color);
  $(id).html(message);
  setTimeout(function () {
    $(id).html('');
  }, 3000);
}

$("#edit_product_type_download").on('change', function (e) {
  let type_down = $("#edit_product_type_download").val();
  const iptnRe = $("#edit_product_recycle_file");
  const linkDo = $("#edit_product_link_download");
  type_down == "link" ? (linkDo.prop('disabled', false), linkDo.focus(), iptnRe.prop('disabled', true)) : (linkDo.prop('disabled', true), iptnRe.prop('disabled', false));
});


$("#product_type_download").on('change', function (e) {
  let type_down = $("#product_type_download").val();
  const iptnRe = $("#product_recycle_file");
  const linkDo = $("#product_link_download");
  type_down == "link" ? (linkDo.prop('disabled', false), linkDo.focus(), iptnRe.prop('disabled', true)) : (linkDo.prop('disabled', true), iptnRe.prop('disabled', false));
});


$("#btnAddItem").on('click', function (data) {

  $("#btnAddItem").prop('disabled', true);
  $("#btnAddItem").html('Aguarde...');

  let product = $("#product_id").val();
  let items = $("#items_product").val();


  var data = new Object();

  data.product = product;
  data.items = items;

  if (data.items == "") {
    showMessage('#response_add', 'danger', 'Preencha todos os campos');
    return false;
  }


  $.post('Controller/Items.php', { data: JSON.stringify(data), type: 'add' }, function (data) {

    $("#btnAddItem").prop('disabled', false);
    $("#btnAddItem").html('Adicionar');

    try {

      var obj = JSON.parse(data);

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
        setTimeout(function () {
          location.href = "";
        }, 2000);
      }

    } catch (e) {
      let configs = {
        title: "Erro, desculpe!",
        message: 'Erro interno no servidor. Tente mais tarde.',
        status: TOAST_STATUS.DANGER,
        timeout: 5000
      }
      Toast.setTheme(TOAST_THEME.DARK);
      Toast.create(configs);
      return false;
    }
  });

});

$("#btnAddCate").on('click', function (data) {

  $("#btnAddCate").prop('disabled', true);
  $("#btnAddCate").html('Aguarde...');

  let nome = $("#categoria_name").val();
  let description = $("#description_price").val();

  var data = new Object();

  data.nome = nome;
  data.description = description;

  if (data.nome == "" || data.description == "") {
    showMessage('#response_add', 'danger', 'Preencha todos os campos');
    return false;
  }

  $.post('Controller/Categorys.php', { data: JSON.stringify(data), type: 'add' }, function (data) {

    $("#btnAddCate").prop('disabled', false);
    $("#btnAddCate").html('Adicionar');

    try {

      var obj = JSON.parse(data);

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
        setTimeout(function () {
          location.href = "";
        }, 2000);
      }

    } catch (e) {
      let configs = {
        title: "Erro, desculpe!",
        message: "Erro interno no servidor, tente mais tarde.",
        status: TOAST_STATUS.DANGER,
        timeout: 5000
      }
      Toast.setTheme(TOAST_THEME.DARK);
      Toast.create(configs);
      return false;
    }
  });

});


function linkProduct(linkProduct) {

  // Tenta copiar o texto para a área de transferência
  navigator.clipboard.writeText(linkProduct)
    .then(() => {
      let configs = {
        title: "Copiado!",
        message: linkProduct,
        status: TOAST_STATUS.SUCCESS,
        timeout: 5000
      }
      Toast.setTheme(TOAST_THEME.DARK);
      Toast.create(configs);
    })
    .catch(err => {
      let configs = {
        title: "Erro ao copiar!",
        message: "Desculpe, não foi possível copiar. Mas o link é: " + linkProduct,
        status: TOAST_STATUS.DANGER,
        timeout: 5000
      }
      Toast.setTheme(TOAST_THEME.DARK);
      Toast.create(configs);
    });
}

$("#btnAddProd").on('click', function (data) {

  $("#btnAddProd").prop('disabled', true);
  $("#btnAddProd").html('Aguarde...');

  let name = $("#product_name").val();
  let image = $("#product_image").val();
  let price = $("#product_price").val();
  let status = $("#product_status").val();
  let description = $("#product_description").val();
  let typeDown = $("#product_type_download").val();
  let recycle_file = $("#product_recycle_file").val();
  let link_download = $("#product_link_download").val();
  let account_mkt = $("#account_mkt").val();
  let identifier = $("#product_identifier").val();

  var data = new Object();

  data.name = name;
  data.price = price;
  data.image = image;
  data.status = status;
  data.typeDown = typeDown;
  data.recycle_file = recycle_file;
  data.link_download = link_download;
  data.description = description;
  data.account_mkt = account_mkt;
  data.identifier = identifier === "" ? null : identifier;
 
  if (data.description == "" || data.name == "" || data.price == "" || data.status == "" || data.typeDown == "" || data.account_mkt == "") {
    let configs = {
      title: "Oops!",
      message: 'Preencha todos os campos',
      status: TOAST_STATUS.DANGER,
      timeout: 5000
    }
    Toast.setTheme(TOAST_THEME.DARK);
    Toast.create(configs);
    $("#btnAddProd").prop('disabled', false);
    $("#btnAddProd").html('Adicionar');
    return false;
  }

  if (typeDown == "link") {
    if (data.link_download == "") {
      let configs = {
        title: "Oops!",
        message: 'Informe um link para download',
        status: TOAST_STATUS.DANGER,
        timeout: 5000
      }
      Toast.setTheme(TOAST_THEME.DARK);
      Toast.create(configs);

      $("#btnAddProd").prop('disabled', false);
      $("#btnAddProd").html('Adicionar');
      return false;
    }
  }

  $.post('Controller/Product.php', { data: JSON.stringify(data), type: 'add' }, function (data) {

    $("#btnAddProd").prop('disabled', false);
    $("#btnAddProd").html('Adicionar');

    try {

      var obj = JSON.parse(data);

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

        if (typeDown == "upload") {
          setTimeout(function () {
            location.href = app_url + "/admin/items?product_id=" + obj.id;
          }, 1500);
        } else {
          setTimeout(function () {
            location.href = "";
          }, 1500);
        }

      }

    } catch (e) {
      showMessage('#response_add', 'danger', 'Desculpe, tente novamente');
      return false;
    }
  });

});

$("#btnEditCategory").on('click', function () {

  $("#btnEditCategory").prop('disabled', true);
  $("#btnEditCategory").html('Aguarde...');

  let id = $("#edit_category_id").val();
  let description = $("#edit_category_description").val();
  let name = $("#edit_category_name").val();


  var data = new Object();

  data.id = id;
  data.name = name;
  data.description = description;

  if (data.id == "" || data.name == "" || data.description == "") {

    let configs = {
      title: "Oops!",
      message: "Preencha todos os campos!",
      status: TOAST_STATUS.DANGER,
      timeout: 5000
    }
    Toast.setTheme(TOAST_THEME.DARK);
    Toast.create(configs);
    return false;
  }

  $.post('Controller/Categorys.php', { data: JSON.stringify(data), type: 'edit' }, function (data) {

    $("#btnEditCategory").prop('disabled', false);
    $("#btnEditCategory").html('Salvar');

    try {

      var obj = JSON.parse(data);

      if (obj.erro) {

        let configs = {
          title: "Erro, desculpa!",
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

        setTimeout(function () {
          location.href = "";
        }, 2000);
      }

    } catch (e) {
      showMessage('#response_edit', 'danger', 'Desculpe, tente novamente');
      return false;
    }
  });

});

$("#btnEditProd").on('click', function () {

  $("#btnEditProd").prop('disabled', true);
  $("#btnEditProd").html('Aguarde...');

  let id = $("#edit_product_id").val();
  let image = $("#edit_product_image").val();
  let name = $("#edit_product_name").val();
  let price = $("#edit_product_price").val();
  let status = $("#edit_product_status").val();
  let description = $("#edit_product_description").val();
  let typeDown = $("#edit_product_type_download").val();
  let recycle_file = $("#edit_product_recycle_file").val();
  let link_download = $("#edit_product_link_download").val();
  let account_mkt = $("#edit_account_mkt").val();
  let identifier = $("#edit_product_identifier").val();
  
  var data = new Object();

  data.id = id;
  data.name = name;
  data.price = price;
  data.status = status;
  data.image = image;
  data.description = description;
  data.typeDown = typeDown;
  data.recycle_file = recycle_file;
  data.link_download = link_download;
  data.account_mkt = account_mkt;
  data.identifier = identifier === "" ? null : identifier;

  if (data.id == "" || data.name == "" || data.price == "" || data.status == "" || data.description == "" || data.typeDown == "" || data.account_mkt == "") {

    let configs = {
      title: "Oops!",
      message: "Preencha todos os campos",
      status: TOAST_STATUS.SUCCESS,
      timeout: 5000
    }
    Toast.setTheme(TOAST_THEME.DARK);
    Toast.create(configs);
    $("#btnEditProd").prop('disabled', false);
    $("#btnEditProd").html('Salvar');
    return false;
  }

  if (typeDown == "link") {
    if (data.link_download == "") {

      let configs = {
        title: "Oops!",
        message: "Informe um link para download",
        status: TOAST_STATUS.DANGER,
        timeout: 5000
      }
      Toast.setTheme(TOAST_THEME.DARK);
      Toast.create(configs);
      $("#btnEditProd").prop('disabled', false);
      $("#btnEditProd").html('Salvar');
      return false;
    }
  }

  $.post('Controller/Product.php', { data: JSON.stringify(data), type: 'edit' }, function (data) {

    $("#btnEditProd").prop('disabled', false);
    $("#btnEditProd").html('Salvar');

    try {

      var obj = JSON.parse(data);

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
      }

    } catch (e) {
      let configs = {
        title: "Erro, desculpe!",
        message: "Erro interno no servidor, tente mais tarde.",
        status: TOAST_STATUS.DANGER,
        timeout: 5000
      }
      Toast.setTheme(TOAST_THEME.DARK);
      Toast.create(configs);
      return false;
    }
  });

});

function getCategoryByEdit(id) {
  $.post('Controller/Categorys.php', { type: 'get', id: id }, function (data) {

    try {

      var obj = JSON.parse(data);

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

        $("#edit_category_id").val(obj.data.id);
        $("#edit_category_name").val(obj.data.nome);
        $("#edit_category_description").val(obj.data.description);

        $("#moedalEditCategory").modal('show');

      }

    } catch (e) {
      showMessage('#response_product', 'danger', 'Desculpe, tente novamente');
      return false;
    }
  });
}

function setAtentionInput(input, time) {
  let intervalInputAtention = setInterval(() => {
    input.addClass('input-atention');
    setTimeout(() => {
      input.removeClass('input-atention');
    }, 400)
  }, 800);
  setTimeout(() => {
    clearInterval(intervalInputAtention);
  }, time);
}

function getProductByEdit(id) {

  $.post('Controller/Product.php', { type: 'get', id: id }, function (data) {

    try {

      var obj = JSON.parse(data);

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
          title: "Sucesso",
          message: obj.message,
          status: TOAST_STATUS.SUCCESS,
          timeout: 5000
        }
        Toast.setTheme(TOAST_THEME.DARK);
        Toast.create(configs);

        $("#edit_product_id").val(obj.data.id);
        $("#edit_product_name").val(obj.data.name);
        $("#edit_product_price").val(obj.data.price);
        $("#edit_product_status").val(obj.data.status);
        $("#edit_product_image").val(obj.data.image);
        $("#edit_product_description").val(obj.data.description);
        $("#edit_account_mkt").val(obj.data.account_mkt);
        $("#edit_product_identifier").val(obj.data.identifier);

        const iptndown = $("#edit_product_type_download");
        const linkDo = $("#edit_product_link_download");
        const iptnRe = $("#edit_product_recycle_file");

        if (obj.data.uniq_link === "not") {
          iptndown.val("upload");
          linkDo.prop('disabled', true);
          linkDo.val('');
          iptnRe.val(obj.data.recycle_file);
          iptnRe.prop('disabled', false);
        } else {
          iptndown.val("link");
          linkDo.prop('disabled', false);
          obj.data.uniq_link == "imported" ? (linkDo.val(""), setAtentionInput(linkDo,8000)) : linkDo.val(obj.data.uniq_link);
          iptnRe.prop('disabled', true);
        }

        $("#moedalEditProduct").modal('show');

      }

    } catch (e) {
      let configs = {
        title: "Erro, desculpe!",
        message: "Erro interno no servidor, tente mais tarde.",
        status: TOAST_STATUS.DANGER,
        timeout: 5000
      }
      Toast.setTheme(TOAST_THEME.DARK);
      Toast.create(configs);
      return false;
    }
  });

}

function deleteItem(id) {
  if (confirm('Deseja continuar com a remoção')) {

    $.post('Controller/Items.php', { product: true, type: 'delete', id: id }, function (data) {

      try {

        var obj = JSON.parse(data);

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

          setTimeout(function () {
            location.href = "";
          }, 2000);

        }

      } catch (e) {
        showMessage('#response_items', 'danger', 'Desculpe, tente novamente');
        return false;
      }
    });

  } else {
    return false;
  }
}

function itensSelected() {
  $("#response_add").addClass('alert alert-success text-white');
  $("#response_add").html('Itens selecionados');
}

function deleteCategory(id) {
  if (confirm('Deseja continuar com a remoção')) {

    $.post('Controller/Categorys.php', { type: 'delete', id: id }, function (data) {

      try {

        var obj = JSON.parse(data);

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
          setTimeout(function () {
            location.href = "";
          }, 2000);
        }

      } catch (e) {
        let configs = {
          title: "Erro, desculpe!",
          message: "Erro interno no servidor, tente mais tarde",
          status: TOAST_STATUS.DANGER,
          timeout: 5000
        }
        Toast.setTheme(TOAST_THEME.DARK);
        Toast.create(configs);
        return false;
      }
    });

  } else {
    return false;
  }
}


function deleteProd(id) {
  if (confirm('Deseja continuar com a remoção')) {

    $.post('Controller/Product.php', { type: 'delete', id: id }, function (data) {

      try {

        var obj = JSON.parse(data);

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
          setTimeout(function () {
            location.href = "";
          }, 2000);
        }

      } catch (e) {
        let configs = {
          title: "Erro, desculpe!",
          message: "Erro interno no servidor, tente mais tarde.",
          status: TOAST_STATUS.DANGER,
          timeout: 5000
        }
        Toast.setTheme(TOAST_THEME.DARK);
        Toast.create(configs);
        return false;
      }
    });

  } else {
    return false;
  }
}
