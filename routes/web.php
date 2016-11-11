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

$app->get('/{id}', function ($id) use ($app){
    return 'Welcome '.$id;
});

$app->get('/posts/{post}/comments/{comment}', function ($post,$comment) use ($app){
    return 'post is '.$post.', comment is '.$comment;
});

$app->get('/named',function () use ($app){
    // Generating URLs...
    $url = route('123');

// Generating Redirects...
    return redirect()->route('123');
});

//Named Router
$app->get('profile', ['as' => '123', function () {

    return 'Named Router';
}]);

$app->group(['middleware' => 'auth'], function () use ($app) {
    $app->get('/', function ()    {
        // Uses Auth Middleware
    });

    $app->get('user/profile', function () {
        // Uses Auth Middleware
    });
});
