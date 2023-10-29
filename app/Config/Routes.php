<?php

namespace Config;

// Create a new instance of our RouteCollection class.
$routes = Services::routes();

/*
 * --------------------------------------------------------------------
 * Router Setup
 * --------------------------------------------------------------------
 */
$routes->setDefaultNamespace('App\Controllers');
$routes->setDefaultController('MainController');
$routes->setDefaultMethod('index');
$routes->setTranslateURIDashes(false);
$routes->set404Override();
// The Auto Routing (Legacy) is very dangerous. It is easy to create vulnerable apps
// where controller filters or CSRF protection are bypassed.
// If you don't want to define all routes, please use the Auto Routing (Improved).
// Set `$autoRoutesImproved` to true in `app/Config/Feature.php` and set the following to true.
// $routes->setAutoRoute(false);

/*
 * --------------------------------------------------------------------
 * Route Definitions
 * --------------------------------------------------------------------
 */

// We get a performance increase by specifying the default
// route since we don't have to scan directories.


// REST API
$routes->group('api', function($routes)
{
    $routes->get('url/(:any)', 'RestController::show');
    $routes->options('url', 'RestController::newUrl');
    $routes->post('url', 'RestController::newUrl');
    $routes->options('login', 'RestController::login');
    $routes->post('login', 'RestController::login');
});

// REST API Private
$routes->group('private/api', function ($routes) 
{
    $routes->options('url', 'RestPrivateController::newUrl');
    $routes->options('url/(:segment)', 'RestPrivateController::updateUrl');
    $routes->options('user/validate', 'RestPrivateController::validateUser');
    $routes->options('user/new', 'RestPrivateController::newUser');
    $routes->options('user/(:segment)', 'RestPrivateController::updateUser/$1');
});

$routes->group('private/api', ['filter' => 'jwt'], function($routes)
{
    $routes->get('url/(:any)', 'RestPrivateController::show');
    $routes->post('url', 'RestPrivateController::newUrl');
    $routes->post('url/(:segment)', 'RestPrivateController::updateUrl');
    $routes->get('users', 'RestPrivateController::users');
    $routes->get('user', 'RestPrivateController::user');
    $routes->get('user/(:segment)', 'RestPrivateController::user/$1');
    $routes->post('user/validate', 'RestPrivateController::validateUser');
    $routes->post('user/new', 'RestPrivateController::newUser');
    $routes->post('user/(:segment)', 'RestPrivateController::updateUser/$1');
    $routes->get('user/(:segment)/roles', 'RestPrivateController::userRoles/$1');
    $routes->get('user/(:segment)/group/(:segment)', 'RestPrivateController::userInGroup/$1/$2');
});

// Base Routes
$routes->get('/', 'MainController::index', ['as' => 'home']);
$routes->post('/', 'MainController::shorten');
$routes->get('/your-url', 'MainController::yourUrl');

// Private Routes
$routes->get('/private', 'PrivateController::index', ['filter' => 'login']);
$routes->get('/private/dashboard', 'PrivateController::dashboard', ['filter' => 'login']);
$routes->get('/private/delete-url/(:segment)', 'PrivateController::deleteUrl/$1', ['filter' => 'login']);
$routes->get('/private/activate-url/(:segment)', 'PrivateController::activateUrl/$1', ['filter' => 'login']);
$routes->get('/private/deactivate-url/(:segment)', 'PrivateController::deactivateUrl/$1', ['filter' => 'login']);
$routes->get('/private/users', 'PrivateController::usersDashboard', ['filter' => 'login', 'filter' => 'permission:users.manage']);
$routes->get('/private/delete-user/(:segment)', 'PrivateController::deleteUser/$1', ['filter' => 'login', 'filter' => 'permission:users.manage']);
$routes->get('/private/activate-user/(:segment)', 'PrivateController::activateUser/$1', ['filter' => 'login', 'filter' => 'permission:users.manage']);
$routes->get('/private/deactivate-user/(:segment)', 'PrivateController::deactivateUser/$1', ['filter' => 'login', 'filter' => 'permission:users.manage']);
$routes->get('/private/statistics/(:segment)', 'PrivateController::statistics/$1', ['filter' => 'login']);

// File Explorer
$routes->post('/private/fileconnector', 'FileExplorerController::connector', ['filter' => 'login', 'filter' => 'permission:files.manage']);
$routes->get('/private/fileconnector', 'FileExplorerController::connector', ['filter' => 'login', 'filter' => 'permission:files.manage']); 
$routes->get('/private/files', 'FileExplorerController::manager', ['filter' => 'login', 'filter' => 'permission:files.manage']);
$routes->get('/private/fileget/(:any)', 'FileExplorerController::getFile', ['filter' => 'login', 'filter' => 'permission:files.manage']);

// Auth
// Login/out
$routes->get('login', 'AuthController::login', ['as' => 'login']);
$routes->post('login', 'AuthController::attemptLogin');
$routes->get('logout', 'AuthController::logout');

//2fa
$routes->get('2fa', 'AuthController::twoFactor', ['as' => '2fa']);
$routes->post('2fa', 'AuthController::postTwoFactor');

// Registration
$routes->get('register', 'AuthController::register', ['as' => 'register']);
$routes->post('register', 'AuthController::attemptRegister');

// Activation
$routes->get('activate-account', 'AuthController::activateAccount', ['as' => 'activate-account']);
$routes->get('resend-activate-account', 'AuthController::resendActivateAccount', ['as' => 'resend-activate-account']);

// Forgot/Resets
$routes->get('forgot', 'AuthController::forgotPassword', ['as' => 'forgot']);
$routes->post('forgot', 'AuthController::attemptForgot');
$routes->get('reset-password', 'AuthController::resetPassword', ['as' => 'reset-password']);
$routes->post('reset-password', 'AuthController::attemptReset');

// Redirect to URL
$routes->get('/(:segment)', 'MainController::preUrl/$1');
$routes->post('/(:segment)', 'MainController::redirect/$1');


/*
 * --------------------------------------------------------------------
 * Additional Routing
 * --------------------------------------------------------------------
 *
 * There will often be times that you need additional routing and you
 * need it to be able to override any defaults in this file. Environment
 * based routes is one such time. require() additional route files here
 * to make that happen.
 *
 * You will have access to the $routes object within that file without
 * needing to reload it.
 */
if (is_file(APPPATH . 'Config/' . ENVIRONMENT . '/Routes.php')) {
    require APPPATH . 'Config/' . ENVIRONMENT . '/Routes.php';
}
