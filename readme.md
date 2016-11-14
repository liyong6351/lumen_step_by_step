### Learn lumen from official website step by step 
## 本项目基于Lumen (5.3.2) (Laravel Components 5.3.*)

## 1. 创建项目  
运行lumen new blog

## 2. 路由  
路由信息都保存在routes/web.php文件中  
* 基本路由(不带参数版本):  
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
* 命名空间  
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
### Controllers
## 简介
Controller的作用就是存储业务逻辑。也可以将业务逻辑写入router中，但是如果太复杂不鼓励这样的情况  
Controller可以将相关的HTTP请求处理分组到某一个类中。  
Controller存储在app/Http/Controllers目录中
## 基础Controller
* 所有的Controller都应该继承Lumen提供的base controller,如下例:  
class UserController extends Controller  
{  
　　public function show($id){  
　　　　return User::findOrFail($id);  
　　}  
}  
配置router的时候如下例:  
$app->get('user/{id}', 'UserController@show');  
* 命名Controller
## Controller中间件
Middleware可以写在router中，但是有一种更方便的是写在controller的构造方法中。

## 依赖注入 和 控制器
Lumen的服务容器用于解决Controller所有的依赖关系。所以只需要在Controller的构造方法中定义即可  
* 方法注入
除了构造器注入之外，还可以使用方法注入  

### Requests
## 获取到Request
如果需要在一次Http请求中保持同一个实例，那么可以将Request声明注入即可。
## 基础的Request信息
Illuminate\Http\Request从Symfony\Component\HttpFoundation\Request中继承而来，包含了众多的信息
# 获取URI
获取到请求的URL $uri = $request->path();  
is方法可以对请求的uri进行匹配，支持通配符 if($request->is('admin/*')){}  
url或fullUrl方法可以获取到请求全路径 $url = $request->url()  $url=$request->fullUrl();  
* 获取到Request的方法
$method = $request->method();  
$isMethod = $request -> isMethod('post');
* PSR-7 请求
PSR-7标准规定了HTTP消息的家口，包括请求和响应。如果你需要使用PSR-7，那么需要安装一些库文件。Laravel使用Symfony HTTP 
Message将典型的Laravel请求和响应转化为PSR-7兼容实现。需要运行如下命令:  
composer require symfony/psr-http-message-bridge  
composer require zendframework/zend-diactoros  
一旦你安装了这些库，你就可以声明式的将请求转化为PSR-7类型的了。
use Psr\Http\Message\ServerRequestInterface;  
$app->get('/', function (ServerRequestInterface $request) {  
　　//  
});  
## 获取输入参数
* 获取输入值
$name = $request->input("name","Sally");  
如果使用的是数组形式的input，那么可以使用.操作符进行操作  
$name = $request->input('products.0.name');  
$name = $request->input('products.*.name')  
* 检测一个输入参数是否存在
$request->has('name')  
* 获取到所有的输入值
$input = $request->all();
* 检索输入数据的一部分
可以使用 only 或 except 进行操作。输入参数可以是单个数组或者动态列表  
$input = $request->only(['username','password'])  
$input = $request->only('username','password')  
$input = $request->except(['credit_card']);
$input = $request->except('credit_card');  
## 文件
* 获取上传的文件 $file = $request->file('photo');  
* 检测是否存在 $isExist = $request->hasFile('photo')  
* 检测是否成功上传:$request->file('photo')->isValid();  
* 移动上传的文件：$request->file('photo')->move($destinationPath)  $request->file('photo')->move($destinationPath,$fileName) 
* 其他的上传文件: 参见 http://api.symfony.com/3.0/Symfony/Component/HttpFoundation/File/UploadedFile.html

### response
## Basic responses
* 最简单的就是返回一个字符串 $app->get('/',function(){ return 'Hello world' })
* 返回对象 $app->get('home',function(){ return ( new Response($content,$status))->header('Content-type',$value) });  
当然，你也可以response helper  
$app->get('home',function(){ return response($content,$status)->header('Content->type',$value) })  
如果需要了解所有的Response相关的方法，请查阅 https://laravel.com/api/master/Illuminate/Http/Response.html
* 将Header附加到Response
return response($content)->header('Content-Type',$type)->header('X-Header-One','Header Value')->header('X-Header-Two','Header Value');  
我们推介使用withHeaders方法将一个数组组装起来操作  
return response($content)->withHeaders(['Content-type'=>$type,'X-Header-One'=>'Header Value','X-Header-Two'=>'Header Value'])
## 其他的Response Types
* 返回Json 
return response()->json(['name'=>'liyong','state'=>'CHN']);  
你也可以定制HTTP Code:return response()->json(['error'=>'unauthorized'],401,['X-Header-One','Header Value']);
如果你使用了jsonp response,你需要设置一个setCallBack回调  
return response()->json(['name' => 'Abigail', 'state' => 'CA'])->setCallback($request->input('callback'));
* 文件下载
return response()->download($pathToFile);  
return response()->download($pathToFile, $name, $headers);

## 重定向
Redirect responses是Illuminate\Http\RedirectResponse的实例，可以重定向到另外一个URL。重定向可以有多种实现:  
$app->get('dashboard', function () { return redirect('home/dashboard'); });  
重定向到另一个命名的routes  
return redirect()->route('login', ['id' => 1]);  
如果你的参数是一个对象，这个对象拥有id属性，调用方式可以直接传递object，id可以自动被解析出来  
return redirect()->route('profile',[$user])  