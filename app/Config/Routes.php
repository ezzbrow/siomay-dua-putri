<?php

use CodeIgniter\Router\RouteCollection;

/** @var RouteCollection $routes */
$routes->get('/', 'Home::index');

$routes->get('etalase', 'Etalase::index');

$routes->group('admin', static function ($routes): void {
    $routes->get('/', static fn () => redirect()->to('/admin/dashboard'));

    $routes->get('register',     'Admin\\Auth::register');
    $routes->post('register',    'Admin\\Auth::storeRegister');
    $routes->get('login',        'Admin\\Auth::login');
    $routes->post('login',       'Admin\\Auth::attemptLogin');
    $routes->post('logout',      'Admin\\Auth::logout');

    $routes->get('dashboard', 'Admin\\Dashboard::index', ['filter' => 'auth']);

    $routes->group('produk', ['filter' => 'auth'], static function ($routes): void {
        $routes->get('/',                 'Admin\\ProdukAdmin::index');
        $routes->get('create',            'Admin\\ProdukAdmin::create');
        $routes->post('store',            'Admin\\ProdukAdmin::store');
        $routes->get('edit/(:num)',       'Admin\\ProdukAdmin::edit/$1');
        $routes->post('update/(:num)',    'Admin\\ProdukAdmin::update/$1');
        $routes->get('delete/(:num)',     'Admin\\ProdukAdmin::delete/$1');
        $routes->post('(:num)/varian',         'Admin\\ProdukAdmin::storeVarian/$1');
        $routes->post('(:num)/varian/(:num)/delete', 'Admin\\ProdukAdmin::deleteVarian/$1/$2');
    });
});
