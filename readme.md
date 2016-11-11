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
中间件:比如如果需要在访问接口的时候验证是否有权限,那么就可以使用middleware路由分组,中间件按照定义的数组顺序依次执行
$app->group(['middleware' => 'auth'], function () use ($app) {
    $app->get('/', function ()    {
        // Uses Auth Middleware
    });
    $app->get('user/profile', function () {
        // Uses Auth Middleware
    });
});

命名空间


* 路由前缀

## 中间件MiddleWare
1. Middleware简单说就是过滤器,比如:你可以添加Authenticated过滤器,当请求到达的时候会校验是否认证过了,没有认证过的跳转到认证页面,
否则继续后面的操作.
用户可以自定义Middleware,Response也可以添加附加信息,比如可以添加header信息,logging对应的中间件可以添记录日志
# 所有的middleware都必须存储到app/Http/Middleware目录中

## 定义Middleware
* 定义一个MiddleWare 
你可以简单的拷贝app/Http/Middleware中的ExampleMiddleware类然后写自己的逻辑

* 执行Middleware的顺序
Middleware是在请求之前执行还是在请求之后执行取决于Middle自身的结构
  下面的结构是请求之前执行(执行逻辑之后才执行$next)  
class OldMiddleware  
{  
    public function handle($request, Closure $next){  
        if($request->input('age') <= 200){  
            return redirect('home');  
        }  
        return $next($request);  
    }  
}  
下面的结构是请求之后执行  
class OldMiddleware  
{  
    public function handle($request, Closure $next){  
        $response = $next($request);  
        //Perform some business  
        return $response;  
    }  
}

## 注册Middleware
* 全局Middleware
如果需要全局middleware,那么使用bootstrap/app.php中的$app->middleware()方法中直接注册
$app->middleware([
    App\Http\Middleware\OldMiddleware::class
]);

* 非全局MiddleWare
首先需要在bootstrap/app.php中注册key,注册的方法为routeMiddleware,比如
$app->routeMiddleware([
     'auth' => App\Http\Middleware\Authenticate::class,
]);
然后在配置router的时候添加middle配置即可
$app->get('/', ['middleware' => ['first', 'second'], function () {
    //
}]);

* Middleware的参数
很多时候Middleware也需要使用到参数,比如如果你需要验证用户属于某种角色才能访问某一种资源,那么角色需要事先作为参数传递给Middleware
比如:$role就是一个被传入的参数
public function handle($request, Closure $next, $role)
{
    if(!$request->user()->hasRole($role)){
        return redirect('home');
    }
    return $next($request);
}
然后在调用这个Middleware的时候就需要传递参数,如下(其中'role:editor'就是传入参数):
$app->put('post/{id}', ['middleware' => 'role:editor', function ($id) {
}]);

* 终止Middleware
终止Middleware的意思是当请求已经结束了,还需要继续进行处理的工作.比如写入Session的操作.需要注意的是terminate方法需要$request和$response
参数,同时当你定义了terminable Middleware之后需要在全局Middleware中注册.注册之后每次调用Middleware的时候都是新创建一个对象进行处理,
如果你需要使用singleton模式,也就是单独的实例进行处理的话,那么直接使用singleton方法注册就好了
class SessionMiddleware
{
    public function handle($request, Closure $next)
    {
        return $next($request);
    }

    public function terminate($request,$response){
        //do somebusiness
    }
}