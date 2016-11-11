### Learn lumen from official website step by step 
## 本项目基于Lumen (5.3.2) (Laravel Components 5.3.*)

## 1. 创建项目
运行lumen new blog

## 2. 路由
路由信息都保存在routes/web.php文件中
基本路由(不带参数版本):
$app->get('foo', function () {
    return 'Hello World';
});
* 基本路由(带参数版本)
$app->get('user/{id}', function ($id) {
    return 'User '.$id;
});
Lumen支持的Http方法包括get post put patch delete options

* 命名路由:其实简单理解就是给路由起了一个名字,使得他们可以在其他路由中使用,比如:
$app->get('profile', ['as' => 'profileAlias', function () {
    //
}]);
就是将profile路由起了一个别名为profileAlias,那么其他路由需要跳转到这个路由可以使用别名进行跳转
$app->get('/named',function () use ($app){
    // Generating URLs...
    $url = route('profileAlias');
    // Generating Redirects...
    return redirect()->route('123');
});

* 路由分组:简单理解,就是将路由的公用部分提出来形成公用信息,常用的场景包括中间件 命名空间 
1. 中间件:比如如果需要在访问接口的时候验证是否有权限,那么就可以使用middleware路由分组,中间件按照定义的数组顺序依次执行
$app->group(['middleware' => 'auth'], function () use ($app) {
    $app->get('/', function ()    {
        // Uses Auth Middleware
    });
    $app->get('user/profile', function () {
        // Uses Auth Middleware
    });
});

2. 命名空间


路由前缀

