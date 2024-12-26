<?php

use CodeIgniter\Router\RouteCollection;

/**
 * @var RouteCollection $routes
 */


// ------------------------------------  AuthAdmin ----------------------------------------------------------------------
$routes->get('admin', 'AuthAdminController::index', ['as' => 'authAdmin.index']);
$routes->get('admin/esqueci-a-senha', 'AuthAdminController::forgotPassword', ['as' => 'authAdmin.forgotPassword']);
$routes->post('admin/esqueci-a-senha', 'AuthAdminController::forgotPasswordSubmit', ['as' => 'authAdmin.forgotPassword']);
$routes->get('admin/redefinir-senha', 'AuthAdminController::resetPassword', ['as' => 'authAdmin.resetPassword']);
$routes->post('admin/redefinir-senha', 'AuthAdminController::resetPasswordSubmit', ['as' => 'authAdmin.resetPasswordSubmit']);
$routes->post('admin/login', 'AuthAdminController::store', ['as' => 'authAdmin.store']);
$routes->get('admin/sair', 'AuthAdminController::logout', ['as' => 'authAdmin.logout']);


// ------------------------------------  AuthUser ----------------------------------------------------------------------
$routes->get('login', 'AuthController::index', ['as' => 'auth.login']);
$routes->get('esqueci-a-senha', 'AuthController::forgotPassword', ['as' => 'auth.forgotPassword']);
$routes->post('esqueci-a-senha', 'AuthController::forgotPasswordSubmit', ['as' => 'auth.forgotPassword']);
$routes->get('redefinir-senha', 'AuthController::resetPassword', ['as' => 'auth.resetPassword']);
$routes->post('redefinir-senha', 'AuthController::resetPasswordSubmit', ['as' => 'auth.resetPasswordSubmit']);
$routes->post('login', 'AuthController::store', ['as' => 'auth.store']);
$routes->get('sair', 'AuthController::logout', ['as' => 'auth.logout']);


// ------------------------------------  Site --------------------------------------------------------------------------
$routes->get('/', 'SiteController::index', ['as' => 'index']);
$routes->get('/', 'SiteController::index', ['as' => 'cart']);


// ------------------------------------  Produtos ----------------------------------------------------------------------
$routes->get('/produtos', 'ProductController::index', ['as' => 'product.index']);
$routes->get('/produto/(:any)', 'ProductController::integra/$1', ['as' => 'product.integra']);
$routes->post('/add-carinho', 'CartController::store', ['as' => 'cart.store']);
$routes->post('/atualizar/carrinho', 'CartController::update', ['as' => 'cart.update']);


// ------------------------------------  Carrinho ----------------------------------------------------------------------
$routes->get('/carrinho', 'CartController::index', ['as' => 'cart.index']);
$routes->post('/add-carinho', 'CartController::store', ['as' => 'cart.store']);
$routes->delete('/limpar-carinho', 'CartController::cleanCart', ['as' => 'cart.clean']);
$routes->delete('/remove-carinho/(:num)', 'CartController::destroy/$1', ['as' => 'cart.destroy']);


// ------------------------------------  Cupons ----------------------------------------------------------------------
// $routes->post('api/cupom', 'CupomController::verifyCupom', ['as' => 'cupom.verify']);
$routes->post('api/cupom', 'CupomController::verifyCupom', ['as' => 'cupom.verify']);


// ------------------------------------  Pagamento ----------------------------------------------------------------------
$routes->post('/pegar-chave-publica', 'PaymentController::getPublicKey', ['as' => 'payment.getPublicKey']);
$routes->get('/qr_code/(:num)', 'PaymentController::getQrCode/$1', ['as' => 'payment.getQrcode']);


// ------------------------------------ User CRUD ----------------------------------------------------------------------
$routes->get('cadastro', 'UserController::create', ['as' => 'user.create']);
$routes->post('cadastro', 'UserController::store', ['as' => 'user.store']);


// Funcionalidades de usuário 
$routes->get('/pedido/recebido/(:any)', 'OrderController::setReceived/$1', ['as' => 'order.received', 'filter' => 'auth']);

$routes->group('', ['filter' => 'auth'], function($routes) {
    
    // User
    $routes->get('editar-dados', 'UserController::edit', ['as' => 'user.edit']);
    $routes->post('editar-dados/(:num)', 'UserController::update/$1', ['as' => 'user.update', 'filter' => 'csrf']);
    
    // Pedidos
    $routes->get('/pedido/resumo', 'OrderController::resumeOrder', ['as' => 'order.resume']);
    $routes->post('/pedido', 'OrderController::store', ['as' => 'order.store']);
    $routes->get('pedidos', 'OrderController::index', ['as' => 'order.index']);
    $routes->get('pedido/(:any)', 'OrderController::integra/$1', ['as' => 'order.integra']);
    $routes->get('pedido/cancelar/(:any)', 'OrderController::cancel/$1', ['as' => 'order.cancel']);

    // Pagamento 
    $routes->get('pagar-pedido/(:any)', 'PaymentController::pay/$1', ['as' => 'payment.pay']);
    $routes->get('consultar/pagamento/(:any)', 'PaymentController::consultaPayment/$1', ['as' => 'payment.consulta']);
    $routes->get('cancelar/pagamento/(:any)', 'PaymentController::cancelPayment/$1', ['as' => 'payment.cancel']);
});

// ------------------------------------ CRUDs Painel Administrativo ----------------------------------------------------
$routes->group('admin', ['filter' => 'authAdmin'], function($routes) {

    // Admins
    $routes->get('admins', 'AdminController::index', ['as' => 'admin.index']);
    $routes->post('admins/cadastro', 'AdminController::store', ['as' => 'admin.store', 'filter' => 'csrf']);
    $routes->put('admins/atualizar/(:num)', 'AdminController::update/$1', ['as' => 'admin.update', 'filter' => 'csrf']);
    $routes->delete('admins/excluir/(:num)', 'AdminController::destroy/$1', ['as' => 'admin.destroy', 'filter' => 'csrf']);
    $routes->get('admins/xlsx', 'AdminController::xlsx', ['as' => 'admin.xlsx']);
    
    // Categorias
    $routes->get('categorias', 'CategoryController::index', ['as' => 'category.index']);
    $routes->post('categorias/cadastro', 'CategoryController::store', ['as' => 'category.store', 'filter' => 'csrf']);
    $routes->put('categorias/atualizar/(:num)', 'CategoryController::update/$1', ['as' => 'category.update', 'filter' => 'csrf']);
    $routes->delete('categorias/excluir/(:num)', 'CategoryController::destroy/$1', ['as' => 'category.destroy', 'filter' => 'csrf']);
    $routes->get('categorias/xlsx', 'CategoryController::xlsx', ['as' => 'category.xlsx']);
    
    // Cupons
    $routes->get('cupons', 'CupomController::index', ['as' => 'cupom.index']);
    $routes->post('cupons/cadastro', 'CupomController::store', ['as' => 'cupom.store', 'filter' => 'csrf']);
    $routes->put('cupons/atualizar/(:num)', 'CupomController::update/$1', ['as' => 'cupom.update', 'filter' => 'csrf']);
    $routes->delete('cupons/excluir/(:num)', 'CupomController::destroy/$1', ['as' => 'cupom.destroy', 'filter' => 'csrf']);
    $routes->get('cupons/xlsx', 'CupomController::xlsx', ['as' => 'cupom.xlsx']);
    
    // Users
    $routes->get('usuarios', 'UserController::index', ['as' => 'user.index']);
    $routes->post('usuarios/cadastro', 'UserController::store', ['as' => 'user.store', 'filter' => 'csrf']);
    $routes->put('usuarios/atualizar/(:num)', 'UserController::admin_update/$1', ['as' => 'admin.user.update', 'filter' => 'csrf']);
    $routes->delete('usuarios/excluir/(:num)', 'UserController::destroy/$1', ['as' => 'user.destroy', 'filter' => 'csrf']);
    $routes->get('usuarios/xlsx', 'UserController::xlsx', ['as' => 'user.xlsx']);
    
    // Produtos
    $routes->get('produtos', 'ProductController::admin_index', ['as' => 'admin.product.index']);
    $routes->post('produtos/cadastro', 'ProductController::store', ['as' => 'product.store', 'filter' => 'csrf']);
    $routes->put('produtos/atualizar/(:num)', 'ProductController::update/$1', ['as' => 'product.update', 'filter' => 'csrf']);
    $routes->delete('produtos/excluir/(:num)', 'ProductController::destroy/$1', ['as' => 'product.destroy', 'filter' => 'csrf']);
    $routes->get('produtos/xlsx', 'ProductController::xlsx', ['as' => 'product.xlsx']);
    $routes->get('produtos/excluir-imagem/(:any)', 'ProductController::deleteImg/$1', ['as' => 'product.delete.img']);
    
    // Pedidos
    $routes->get('pedidos', 'OrderController::admin_index', ['as' => 'admin.order.index']);
    $routes->get('pedido/(:any)', 'OrderController::admin_integra/$1', ['as' => 'admin.integra.order']);
    $routes->get('pedidos/atualizar/(:any)', 'OrderController::setShipping/$1', ['as' => 'order.update']);
    $routes->delete('pedidos/excluir/(:num)', 'OrderController::destroy/$1', ['as' => 'order.destroy', 'filter' => 'csrf']);
    $routes->get('pedidos/xlsx', 'OrderController::xlsx', ['as' => 'order.xlsx']);

});


// -------------------------------- API ------------------------------------
$routes->get('api/categories', 'CategoryController::getCategories', ['as' => 'api.category']);
$routes->get('api/productsCart', 'CartController::getProductsCartCount', ['as' => 'api.productsCart']);