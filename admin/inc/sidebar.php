<aside
  class="sidenav navbar navbar-vertical navbar-expand-xs border-0 border-radius-xl my-3 fixed-start ms-3   bg-gradient-dark"
  id="sidenav-main">
  <div class="sidenav-header text-center">
    <i class="fas fa-times p-3 cursor-pointer text-white opacity-5 position-absolute end-0 top-0 d-none d-xl-none"
      aria-hidden="true" id="iconSidenav"></i>
    <a class="navbar-brand m-0" href="dashboard">
      <img src="https://www.cbvj.org.br/index/wp-content/uploads/2017/10/default-logo.png" class="navbar-brand-img" alt="main_logo">
    </a>
  </div>
  <hr class="horizontal light mt-0 mb-2">
  <div class="collapse navbar-collapse  w-auto " id="sidenav-collapse-main">
    <ul class="navbar-nav">

      <li class="nav-item">
        <a class="nav-link text-white" id="page_dashboard" href="dashboard">
          <div class="text-white text-center me-2 d-flex align-items-center justify-content-center">
            <i class="material-icons opacity-10">business_center</i>
          </div>
          <span class="nav-link-text ms-1">Dashboard</span>
        </a>
      </li>

      <!-- <li class="nav-item">
        <a class="nav-link text-white " id="page_categorys" href="categorys">
          <div class="text-white text-center me-2 d-flex align-items-center justify-content-center">
            <i class="material-icons opacity-10">apps</i>
          </div>
          <span class="nav-link-text ms-1">Categorias</span>
        </a>
      </li> -->

      <li class="nav-item">
        <a class="nav-link text-white " id="page_products" href="products">
          <div class="text-white text-center me-2 d-flex align-items-center justify-content-center">
            <i class="material-icons opacity-10">add_shopping_cart</i>
          </div>
          <span class="nav-link-text ms-1">Produtos</span>
        </a>
      </li>

      <li class="nav-item">
        <a class="nav-link text-white " id="page_sales" href="sales">
          <div class="text-white text-center me-2 d-flex align-items-center justify-content-center">
            <i class="material-icons opacity-10">loyalty</i>
          </div>
          <span class="nav-link-text ms-1">Vendas</span>
        </a>
      </li>

      <li class="nav-item">
        <a class="nav-link text-white " id="page_clients" href="clients">
          <div class="text-white text-center me-2 d-flex align-items-center justify-content-center">
            <i class="material-icons opacity-10">person</i>
          </div>
          <span class="nav-link-text ms-1">Clientes</span>
        </a>
      </li>
      
      <li class="nav-item">
        <a class="nav-link text-white " id="page_marketplace" href="marketplace">
          <div class="text-white text-center me-2 d-flex align-items-center justify-content-center">
            <i class="fa fa-handshake"></i>
          </div>
          <span class="nav-link-text ms-1">Marketplace</span>
        </a>
      </li>

      <?php if (ENABLE_PIXEL) { ?>
        <li class="nav-item">
          <a class="nav-link text-white " id="page_pixel" href="pixel">
            <div class="text-white text-center me-2 d-flex align-items-center justify-content-center">
              <i class="material-icons opacity-10">facebook</i>
            </div>
            <span class="nav-link-text ms-1">Pixel</span>
          </a>
        </li>
      <?php } ?>

      <?php if (ENABLE_API_WPP) { ?>

        <li class="nav-item">
          <a class="nav-link text-white " id="page_whatsapp" href="whatsapp">
            <div class="text-white text-center me-2 d-flex align-items-center justify-content-center">
              <i class="fa fa-brands fa-whatsapp"></i>
            </div>
            <span class="nav-link-text ms-1">Whatsapp</span>
          </a>
        </li>
      <?php } ?>
      
      

      <li class="nav-item mt-3">
        <h6 class="ps-4 ms-2 text-uppercase text-xs text-white font-weight-bolder opacity-8">Conta</h6>
      </li>

      <li class="nav-item">
        <a class="nav-link text-white " id="page_settings" href="settings">
          <div class="text-white text-center me-2 d-flex align-items-center justify-content-center">
            <i class="material-icons opacity-10">settings</i>
          </div>
          <span class="nav-link-text ms-1">Configurações</span>
        </a>
      </li>

      <li class="nav-item">
        <a class="nav-link text-white " href="logout">
          <div class="text-white text-center me-2 d-flex align-items-center justify-content-center">
            <i class="material-icons opacity-10">login</i>
          </div>
          <span class="nav-link-text ms-1">Sair</span>
        </a>
      </li>

    </ul>
  </div>

</aside>