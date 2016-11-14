<?php

/*
|--------------------------------------------------------------------------
| Application Routes
|--------------------------------------------------------------------------
|
| Here is where you can register all of the routes for an application.
| It is a breeze. Simply tell Lumen the URIs it should respond to
| and give it the Closure to call when that URI is requested.
|
*/

$app->get('/version', function () use ($app) {
    return $app->version();
});

/**
 * This is default response
 */
$app->get('/', function () use ($app) {
    return 'Welcome,I do not know you name';
});

$app->get('/home', ['as' => 'home',function (){
    return 'Welcome home';
}]);

$app->get('/{id}', function ($id) use ($app){
    return 'Welcome '.$id;
});

$app->get('/posts/{post}/comments/{comment}', function ($post,$comment) use ($app){
    return 'post is '.$post.', comment is '.$comment;
});

$app->get('admin/profile',['middleware'=>'old',function(){
    return 'admin/profile';
}]);

$app->get('admin/role',['middleware'=>'role:100',function(){
    return 'admin/role';
}]);

$app->get('user/{id}', 'UserController@show');