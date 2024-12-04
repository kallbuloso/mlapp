<?php

 require_once 'class/Conn.class.php';
 require_once 'class/Product.class.php';

 $products     = new Product();
 $dashboard    = new Dashboard;

 $getCategorysProduct = $products->getCategorysProduct();
 $pageViewsToday      = $dashboard->pageViewsToday();

 if(!$pageViewsToday){
   // insert
   $dashboard->insertPageViewToday();
 }else{
   //update
   $dashboard->setPageViewToday();
 }

 $cateId = 1;

 if(isset($_GET['cate'])){
   if(is_numeric($_GET['cate'])){
     $cateId = trim($_GET['cate']);
   }
 }

 $getProductsByCate = $products->getProductsByCate($cateId);
 $getCategoryById   = $products->getCategoryById($cateId);


 $nome_client = "";
 $mail_client = "";

 if(isset($_COOKIE['client_mail'])){
   $mail_client = $_COOKIE['client_mail'];
 }

 if(isset($_COOKIE['client_nome'])){
   $nome_client = $_COOKIE['client_nome'];
 }

?>

<!DOCTYPE html>
<html lang="pt-br">
   <head>
      <!-- basic -->
      <meta charset="utf-8">
      <meta http-equiv="X-UA-Compatible" content="IE=edge">
      <!-- mobile metas -->
      <meta name="viewport" content="width=device-width, initial-scale=1">
      <meta name="viewport" content="initial-scale=1, maximum-scale=1">
      <!-- site metas -->
      <title><?= APP_NAME; ?></title>

      <meta name="address_ip" content="<?= ADDRESS_IP; ?>">
      <meta name="user_agent" content="<?= USER_AGENT; ?>">
      <meta name="url_site" content="<?= APP_URL; ?>">

      <meta name="keywords" content="">
      <meta name="description" content="">
      <meta name="author" content="">
      <!-- bootstrap css -->
      <link rel="stylesheet" href="public/css/bootstrap.min.css">
      <!-- style css -->
      <link rel="stylesheet" href="public/css/style.css">
      <!-- Responsive-->
      <link rel="stylesheet" href="public/css/responsive.css">
      <!-- fevicon -->
      <link rel="icon" href="public/images/fevicon.png" type="image/gif" />
      <!-- Scrollbar Custom CSS -->
      <link rel="stylesheet" href="public/css/jquery.mCustomScrollbar.min.css">
      <!-- Tweaks for older IEs-->
      <link rel="stylesheet" href="https://netdna.bootstrapcdn.com/font-awesome/4.0.3/public/css/font-awesome.css">
      <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/fancybox/2.1.5/jquery.fancybox.min.css" media="screen">
      <!--[if lt IE 9]>
      <script src="https://oss.maxcdn.com/html5shiv/3.7.3/html5shiv.min.js"></script>
      <script src="https://oss.maxcdn.com/respond/1.4.2/respond.min.js"></script><![endif]-->

      <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.3.0/css/all.min.css"  />

   </head>
   <!-- body -->
   <body class="main-layout">
      <!-- loader  -->

      <!-- end loader -->
      <div class="wrapper">
      <!-- end loader -->

      <div id="content">
         <!-- header -->
         <header>
            <!-- header inner -->
            <div class="header">
               <div class="container-fluid">
                  <div class="row">
                     <div class="col-xl-3 col-lg-3 col-md-3 col-sm-3 col logo_section">
                        <div class="full">
                           <div class="center-desk">
                              <div class="logo">
                                 <a href="index.html"><img src="public/images/logo.png" alt="#" /></a>
                              </div>
                           </div>
                        </div>
                     </div>
                  </div>
               </div>
            </div>
         </header>
         <!-- end header inner -->
         <!-- end header -->
         <!-- banner -->
         <div id="myCarousel" class="carousel slide banner_main" data-ride="carousel">
            <ol class="carousel-indicators">
               <li data-target="#myCarousel" data-slide-to="0" class="active"></li>
            </ol>
            <div class="carousel-inner">
               <div class="carousel-item active">
                  <div class="container-fluid">
                     <div class="carousel-caption">
                        <div class="row">
                           <div class="col-xl-5 col-lg-5 col-md-5 col-sm-12">
                              <div class="text-bg">
                                 <h3 style="font-size: 34px;font-weight: bold;color: #fff;" >Contas de qualidade estão com você com a garantia da <span style="color:#003a52;" ><?= APP_NAME; ?></span> </h3>
                                 <p>Você pode comprar contas em massa e baixá-las em formato xlsx ou txt.</p>
                                 <a class="read_more" href="#buy">Continuar</a>
                              </div>
                           </div>
                           <div class="col-xl-7 col-lg-7 col-md-7 col-sm-12">
                              <div class="images_box">
                                 <figure><img src="public/images/img2.png"></figure>
                              </div>
                           </div>
                        </div>
                     </div>
                  </div>
               </div>

            </div>
            <a class="carousel-control-prev" href="#myCarousel" role="button" data-slide="prev">
            <i class="fa fa-long-arrow-left" aria-hidden="true"></i>
            </a>
            <a class="carousel-control-next" href="#myCarousel" role="button" data-slide="next">
            <i class="fa fa-long-arrow-right" aria-hidden="true"></i>
            </a>
         </div>
         <!-- end banner -->


         <!-- about -->
         <div id="buy"  class="about">
            <div class="container-fluid">
               <div class="row d_flex pl-5 pr-5">

                 <?php if($getProductsByCate){ foreach($getProductsByCate as $key => $prod){

                   $qtdItems = $products->countItemsProduct($prod->id);

                  ?>

                   <!-- Modal -->
                    <div class="modal fade" id="modalDescription_<?= $prod->id; ?>" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
                      <div class="modal-dialog" role="document">
                        <div class="modal-content">
                          <div class="modal-header">
                            <h5 class="modal-title" id="exampleModalLabel">Descrição de <?= $prod->name; ?></h5>
                            <button type="button" class="close" data-dismiss="modal" aria-label="Fechar">
                              <span aria-hidden="true">&times;</span>
                            </button>
                          </div>
                          <div class="modal-body">
                            <?= $prod->description; ?>
                          </div>
                          <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-dismiss="modal">Fechar</button>
                          </div>
                        </div>
                      </div>
                    </div>

                     <!-- Product card -->
                     <div class="col-md-4 mb-5">
                       <div style="background-color: #f5f5ff;" class="card h-100 text-center">
                         <!-- "Sale"/"new" badge -->

                         <i onclick="$('#modalDescription_<?= $prod->id; ?>').modal('show');" style="cursor: pointer;margin: 5px;position: absolute;width: 22px;float: right;right: 0;" class="fa fa-question-circle" ></i>

                         <!-- Product image -->
                         <center>
                           <img onclick="setPaymentModal(<?= $prod->id; ?>);" style="cursor:pointer;width: 70px;border-radius: 100%;margin-top: 15px;" class="card-img-top" src="<?= $prod->image; ?>" alt="...">
                         </center>


                         <!-- Product details  p-4 -->
                         <div  style="cursor:pointer;" onclick="setPaymentModal(<?= $prod->id; ?>);" class="card-body text-center">
                           <!-- Product name -->
                           <a href="#" style="font-size:20px;color:#010483!important;" class="h4 text-decoration-none"><?= $prod->name; ?></a>

                          <div class="mt-3 row">

                             <div class="col-6">
                               <b style="font-size:20px;" ><?= $qtdItems; ?></b>
                               <p style="font-size:13px;" >
                                 Quantidade em estoque
                               </p>
                             </div>

                             <div style="border-left: 2px solid #80808069;" class="col-6">
                               <b style="font-size:20px;" >R$ <?= $prod->price; ?></b>
                               <p style="font-size:13px;" >
                                 Preço unitário
                               </p>
                             </div>

                           </div>
                         </div>

                       </div>
                     </div>


               <?php } } ?>

               </div>
            </div>
         </div>
         <!-- end about -->


         <div id="buy"  class="about">
            <div class="container-fluid">
               <div class="row d_flex pl-5 pr-5">
                   <div class="col-md-6">
                      <div class="about_img">
                         <figure><img src="public/images/about_img.jpg" alt="#"/></figure>
                      </div>
                   </div>
                   <div class="col-md-6">
                      <div class="titlepage">
                         <h3 style="font-size:35px;">Algumas das funcionalidades do sistema</h3>
                         <p>

                           "<?= APP_NAME; ?>" é uma plataforma de venda de contas muito avançada. Gostaríamos de falar sobre alguns de nossos recursos muito avançados para oferecer a melhor experiência do usuário.

                           As informações das contas que você comprou aparecerão instantaneamente em seu painel.
                           Você pode recarregar seu saldo com um cartão de crédito automático 24 horas por dia, 7 dias por semana.
                           Com a tela de pedidos simples, você pode obter contas individuais ou coletivas.
                           Você pode ver diretamente as informações das contas que recebeu ou baixá-las como xlsx, txt.
                           Ao enviar uma solicitação de suporte, você pode obter suporte para qualquer problema que vier à sua mente.
                           Além disso, todas as contas que você comprou em nosso sistema são mantidas criptografadas em nosso banco de dados e seus dados são armazenados com segurança.

                         </p>

                      </div>
                   </div>
               </div>
             </div>
          </div>


         <!-- work -->
         <div id="work"  class="work">
            <div class="container-fluid">
               <div class="row d_flex">
                  <div class="col-md-7">
                     <div class="titlepage">
                        <h3  style="font-size:35px;" >Por que eu deveria obter uma conta de você?</h3>
                        <p>

                          A primeira pergunta que vem à mente dos contas a receber é "as contas estão seguras, como são abertas?" está acontecendo.

                          As contas que você compra são criadas não rastreáveis ​​usando um IP diferente e um agente de usuário a cada vez. Suas contas são apenas para você e as informações de login das contas que você comprou não são fornecidas a outro usuário.

                          Contas cuidadosamente criadas com quase nenhum risco de encerramento.
                          As informações de uma conta vendida não são vendidas para outro usuário.
                          É especial para você! Garantimos contas de alta qualidade e acessíveis!




                        </p>
                     </div>
                  </div>
                  <div class="col-md-5">
                     <div class="work_img">
                        <figure><img src="public/images/work_img.jpg" alt="#"/></figure>
                     </div>
                  </div>
               </div>
            </div>
         </div>
         <!-- end work -->


         <div class="about">
           <div class="container-fluid">
              <div class="row d_flex p-4">
                <div class="col-12">
                  <h1 style="font-size:40px;">Perguntas frequentes</h1>
                </div>
                <div class="col-12">
                  <div class="accordion" id="accordionExample">
                      <div class="card">
                        <div class="card-header" id="headingOne">
                          <h5 class="mb-0">
                            <button class="btn" type="button" data-toggle="collapse" data-target="#collapseOne" aria-expanded="true" aria-controls="collapseOne">
                             <i class="fa fa-question-circle" ></i>  Minhas contas compradas serão encerradas?
                            </button>
                          </h5>
                        </div>

                        <div id="collapseOne" class="collapse show" aria-labelledby="headingOne" data-parent="#accordionExample">
                          <div class="card-body">
                          As contas que você compra são criadas usando um IP diferente e um agente de usuário específico a cada vez. Portanto, o risco de fechamento é muito baixo. Ele permanecerá seguro desde que você não faça login em locais muito diferentes das contas que você comprou.
                          </div>
                        </div>
                      </div>
                      <div class="card">
                        <div class="card-header" id="headingTwo">
                          <h5 class="mb-0">
                            <button class="btn btn collapsed" type="button" data-toggle="collapse" data-target="#collapseTwo" aria-expanded="false" aria-controls="collapseTwo">
                              <i class="fa fa-question-circle" ></i> Quando receberei as informações de login da conta que comprei?
                            </button>
                          </h5>
                        </div>
                        <div id="collapseTwo" class="collapse" aria-labelledby="headingTwo" data-parent="#accordionExample">
                          <div class="card-body">
                            As informações de login de uma conta que você comprou são transmitidas automaticamente para o seu perfil pelo sistema. Não está sujeito a nenhum processo extra de aprovação.
                          </div>
                        </div>
                      </div>
                      <div class="card">
                        <div class="card-header" id="headingThree">
                          <h5 class="mb-0">
                            <button class="btn btn collapsed" type="button" data-toggle="collapse" data-target="#collapseThree" aria-expanded="false" aria-controls="collapseThree">
                            <i class="fa fa-question-circle" ></i> Outra pessoa pode comprar a conta que comprei?
                            </button>
                          </h5>
                        </div>
                        <div id="collapseThree" class="collapse" aria-labelledby="headingThree" data-parent="#accordionExample">
                          <div class="card-body">
                            A conta que você comprou é exclusiva para você e não pode ser vendida para outro usuário. Ele será excluído automaticamente do sistema assim que você comprá-lo.
                          </div>
                        </div>
                      </div>

                    </div>
                </div>
              </div>
           </div>
         </div>


         <!--  footer -->
         <footer>
            <div class="footer">

               <div class="copyright">
                  <div class="container">
                     <div class="row">
                        <div class="col-md-12">
                           <p>Copyright 2023 Todos os diretos reservados</a></p>
                        </div>
                     </div>
                  </div>
               </div>
            </div>
         </footer>
         <!-- end footer -->
      </div>



       <!-- Modal -->
       <div class="modal fade" id="modalPayment" tabindex="-1" role="dialog" aria-labelledby="exampleModalLongTitle" aria-hidden="true">
         <div class="modal-dialog" role="document">
           <div class="modal-content">
             <div class="modal-header">
               <h5 class="modal-title" id="exampleModalLongTitle">Finalize sua compra</h5>
             </div>
             <div class="modal-body">


               <div class="col-md-12" id="divDownload" style="display:none;" >
                 <center>
                   <h2> <i class="fa fa-check-circle text-success"></i> Seu pagamento foi aprovado!</h2>
                 </center>
                 <p style="font-size: 12px;color: gray;">
                   Te enviamos um email com os detalhes da transação e com o link de download do seu produto. <br>
                   Você pode também fazer o download pelo botão abaixo.
                 </p>
                 <p class="text-center">
                   <a href="javascript:void();" id="linkDownload" target="_blank" class="text-white btn btn-info" > <i class="fa fa-download"></i> Download </a>
                 </p>

                <div class="col-md-12 mt-4 mb-2">
                  <a href="javascript:voide();" onclick="$('#modalPayment').modal('toggle');$('#divDownload').hide();$('#divpayment').show();$('#qtd_itens').val(0);" >Concluir</a>
                </div>
               </div>

               <div style="display:none;" class="row text-center" id="divpix">

                 <div class="col-md-12 mb-2">
                   <img src="https://media.tenor.com/On7kvXhzml4AAAAj/loading-gif.gif" width="20" alt=""> Aguardando pagamento
                 </div>

                 <div class="col-md-12 mb-2">
                    <img id="qrcodepix" style="width:200px;" src="https://dummyimage.com/300x300/dee2e6/6c757d.jpg" class="img-thumbnail" alt="">
                 </div>

                 <div class="col-md-12">
                   <textarea id="pixcode" class="form-control" name="pixcode" placeholder="Pix copia e cola" rows="2" cols="80"></textarea>
                 </div>

                 <div class="col-md-12 mb-2 mt-2">
                   <button onclick="$('#pixcode').select();document.execCommand('copy');" type="button" class="text-white btn btn-sm btn-info" name="button"> <i class="fa fa-copy"></i> Copiar </button>
                 </div>


               </div>

                <div id="divpayment" class="row">

                  <input type="hidden" id="product_id" name="product_id" value="">

                  <div class="mb-4 col-md-12 pb-2">
                    <p>
                      Informe dados para processamento do pagamento
                    </p>
                    <p class="text-info" id="countItens"></p>
                  </div>

                   <div class="col-md-12 pb-2">
                      <div class="form-group">
                         <input type="text" class="form-control" placeholder="Informe seu nome" id="nome_buy" name="nome_buy" value="<?= $nome_client; ?>">
                      </div>
                   </div>

                   <div class="col-md-12 pb-2">
                      <div class="form-group">
                         <input type="email" class="form-control" placeholder="Informe seu email" id="email_buy" name="email_buy" value="<?= $mail_client; ?>">
                         <small>Um link de download será enviado para este e-mail</small>
                      </div>
                   </div>

                   <div class="col-md-12 pb-2">
                     <div class="form-group">
                       <input type="number" class="form-control" min="1" id="qtd_itens" name="qtd_itens" value="0">
                       <small>Quantidade da compra.</small>
                     </div>
                   </div>

                   <div class="mt-4 col-md-12 response_send">
                   </div>

                </div>
             </div>
             <div id="fBtns" class="modal-footer">
               <button onclick="$('#modalPayment').modal('toggle');" type="button" class="btn btn-secondary" data-dismiss="modal">Cancelar</button>
               <button type="button" id="buyCheckout" class="btn btn-primary">Prosseguir</button>
             </div>
           </div>
         </div>
       </div>


      <!-- Javascript files-->
      <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.6.3/jquery.min.js"></script>
      <script src="public/js/popper.min.js"></script>
      <script src="public/js/bootstrap.bundle.min.js"></script>
      <script src="public/js/jquery-3.0.0.min.js"></script>

      <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.2.3/js/bootstrap.min.js" ></script>
      <script src="<?= APP_URL; ?>/assets/js/app.js"></script>

      <script type="text/javascript">
        function setPaymentModal(prodId){

          $('#modalPayment').modal('show');
          $('#product_id').val(prodId);

          var dados   = new Object();
          dados.id    = prodId;
          dados.nome  = 'null';
          dados.email = 'null';
          dados.qtd   = 1;

          $.post( '<?= APP_URL; ?>/process', {
            buy: true,
            dados: JSON.stringify(dados),
            calc: true
          }, function(data){
            let nItens = parseInt(data);
            $("#qtd_itens").attr("max", nItens);
            $("#countItens").html("Existe "+data+" itens disponíveis para este produto");
            if(nItens === 0){
              $("#countItens").removeClass("text-info");
              $("#countItens").addClass("text-danger");
            }else{
              $("#countItens").removeClass("text-danger");
              $("#countItens").addClass("text-info");
            }
          });

        }
      </script>

   </body>
</html>
