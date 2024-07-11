<?php


$router->get('users', 'UserController@index');

//Login Endpoints
$router->post('students-login', 'AuthController@Studentlogin');
$router->post('teachers-login', 'AuthController@Teacherlogin');
$router->post('staff-login', 'AuthController@Stafflogin');
$router->post('admins-login', 'AuthController@Adminlogin');
$router->post('super-admins-login', 'AuthController@SupedAdminlogin');


$router->post('students-logout', 'AuthController@Studentlogout');
$router->post('teachers-logout', 'AuthController@Teacherlogout');
$router->post('staff-logout', 'AuthController@Stafflogout');
$router->post('admins-logout', 'AuthController@Adminlogout');
$router->post('super-admins-logout', 'AuthController@SupedAdminlogout');
    // Add more routes as needed


$router->group(['middleware' => 'auth.token'], function () use ($router) {
    $router->post('logout', 'AuthController@logout');
    // Add more routes as needed
});