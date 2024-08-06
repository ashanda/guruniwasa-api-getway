<?php


$router->get('users', 'UserController@index');



//Login Endpoints
$router->post('students-login', 'AuthController@Studentlogin');
$router->post('teachers-login', 'AuthController@Teacherlogin');
$router->post('staff-login', 'AuthController@Stafflogin');
$router->post('admins-login', 'AuthController@Adminlogin');
$router->post('super-admins-login', 'AuthController@SupedAdminlogin');


    // Add more routes as needed


$router->group(['middleware' => 'auth.token'], function () use ($router) {
   //Logout Endpoints
    $router->post('students-logout', 'AuthController@Studentlogout');
    $router->post('teachers-logout', 'AuthController@Teacherlogout');
    $router->post('staff-logout', 'AuthController@Stafflogout');
    $router->post('admins-logout', 'AuthController@Adminlogout');
    $router->post('super-admins-logout', 'AuthController@SupedAdminlogout');


    //check if user is authenticated
    $router->get('students-check-auth', 'AuthController@checkAuthStudent');
    $router->get('teacher-check-auth', 'AuthController@checkAuthTeacher');
    $router->get('staff-check-auth', 'AuthController@checkAuthStaff');
    $router->get('admin-check-auth', 'AuthController@checkAuthAdmin');
    $router->get('superadmin-check-auth', 'AuthController@checkAuthSuperAdmin');

    
});

//Common Endpoints
    $router->get('grades', 'GradeController@index');
    $router->get('subjects', 'SubjectController@index');