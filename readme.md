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

### Authentication 认证
在Lumen中的认证，和Laravel使用的是相同的哭，但是配置却完全不同。因为Lumen不支持Session，所以认证的时候必须使用
基于API的token机制  
## 开始吧
* Authentication 服务提供者
备注:在使用认证之前，需要先将bootstrap/app.php中的AuthServiceProvider服务注册的注释取消  
AuthServiceProvider 在app/Providers目录中，包含单独的Auth::viaRequest方法.viaRequest方法是一个闭包，当
有请求需要认证的时候会调用它们。在这个闭包中，你可以按照你的意愿撰写App\User的逻辑。如果没有通过认证的用户，那么需要返回null  
$this->app['auth']->viaRequest('api', function ($request) {// Return User or null...});  
再次强调,认证的时候可以使用用户、在request中的token、query string、单独的token或者其他的方式。  
* 访问已验证的用户
和在Laravel中一样，可以使用Auth::user()方法获取到当前用户。另外，也可以通过$request->user()方法来获取用户  
$user = $request->user();
### 授权Authorization
* 介绍:除了自身提供的authentication服务之外，Lumen也提供了一个简单的方式来组织授权逻辑和控制访问资源。并且有
多种的方法和helper来协助你完成授权逻辑.  
一般情况下，Lumen和Laravel可以按照相同的方式完成授权。下面仅仅讲解不同点，其他的需要参考https://laravel.com/docs/5.3/authorization进行了解  


### Cache 缓存
缓存支持多种方式，其中值得推介的是Memcached和Redis  
* Lumen的使用的方式和Laravel是一样的，请参考"https://laravel.com/docs/5.3/cache"文档  
* Redis支持：需要安装Redis相关的库并且启用Eloquent。

###database 数据库
* 使用Lumen链接数据库以及查询都会非常简单，当前支持四种数据库:Mysql、Postgres、SQLite、SQL Server，需要在.env
文件中配置数据库信息
* 基础使用
$results = app('db')->select('select * from users');  //不使用facade的情况  
$results = DB::select('select * from users'); //使用facade的情况  
* 基础查询
http://laravel.com/docs/database
* Query Builder
http://laravel.com/docs/queries
* Eloquent ORM
https://laravel.com/docs/5.3/eloquent
## 数据迁移
关于怎么创建数据表并且迁移数据，请参考: https://laravel.com/docs/5.3/migrations

### 数据加密
* 配置:在使用数据加密之前，必须配置.env配置文件中的APP_KEY(32个随机字符)
## 基本用法
* 加密一个值 
 public function storeSecret(Request $request, $id)  
 {  
 　　$user = User::findOrFail($id);  
 　　$user->fill(['secret' => Crypt::encrypt($request->secret)  
 　　])->save();  
 }
* 解密一个值
有加密当然有解密，但是如果输入的值不支持解密的话，将会抛出一个Illuminate\Contracts\Encryption\DescryptionException  
$decrypted = Crypt::decrypt($encryptionValue);

###  Errors 和 Logging
## 介绍
* 当你创建一个Lumen项目的时候，已经为你配置error和exception的处理机制。另外，Lumen整合了Monolog的日志库，这个库
提供了强大并且多种多样的日志处理模式。Monolog: https://github.com/Seldaek/monolog
## 配置
* Error Detail
.env文件中的APP_DEBUG配置项决定了日志输出的细节  
在本地开发环境中，你应该设置其为true.在生产环境中应该设置为false。
* 自定义Monolog的配置项
如果你想在你的应用程序中完全控制Monolog，那么你需要使用应用的configureMonologUsing方法。
## 错误处理机制
所有的exception都能被App\Exceptions\Handler捕获并处理。这个类包括两个方法:report和render.我们将对这两个方法
进行深入的解析。  
* Report方法
这个方法用于记录exception或者将其发送给第三方服务。默认的这个方法仅仅是将数据传送给父类进行处理。但是，你可以
按照自己的想法对其进行定制化。  
按照类型忽略错误:  
$dontReport属性设置忽略的错误类型。默认情况，404不会被记录。你可以添加其他需要忽略的错误类型
* Render方法
Render方法用于渲染HTTP错误并且返回给浏览器。默认情况，这个方法仅仅是将数据传递给父类进行处理。但是，你可以按照
自己的医院进行有针对的处理。
## Http Exceptions
有些Exceptions描述Http error Code。比如:404、401等等。为了渲染应用中所有这样的错误，你可以使用如下方式:  
abort(404);  
abort方法会立即抛出一个错误给Exception handler执行。同时，你可以提供一个描述信息，如下:  
abort(403,'Unauthorized action.')
## Logging
Lumen的logging也是使用的Monolog库。默认情况下，Lumen会为应用生成一个日志文件，这个文件被存储在storage/logs
目录中。你可以使用Log门面写这个日志文件。 Log::info('Showing user: '.$id);  
当前的日志等级分为emergency>alert>critical>error>warning>notice>info>debug.
* 上下文信息
上下文信息也可以传递给log方法。这些信息能和日志信息一起被打印出来
Log::info('User failed to login.', ['id' => $user->id]);
### 事件
## 介绍
Lumen的事件是一个观察者对象，可以监听应用中发生的事件。Event classes 默认存储在app/Event目录中，他们监听
位于app/Listener目录中的被观察者
* 和Laravel的不同
https://laravel.com/docs/5.3/events  
Lumen也支持事件广播，这允许你在客户端的JS代码中监听服务器端的事件。但是在Lumen和Laravel中有一些差异值得讨论
* 生成
在Lumen中，没有生成和监听的命令，所以你必须copyExampleEvent和ExampleListener类去定义你的事件和监听者。
* 注册事件/监听者
和Laravel一样，EventServiceProvider提供了一个方便的方式去注册事件监听。EventServiceProvider有一个listen
属性用于存储时间的key和监听者。当然，你可以注册你想监听的任何事件
protected $listen = ['App\Events\ExampleEvent' => [ 'App\Listeners\ExampleListener',],];
* 触发事件
你可以使用Event helper或Event facade去触发事件。  
event(new ExampleEvent);  
Event::fire(new ExampleEvent);

### 队列
## 简介
Lumen的队列服务通过提供一个统一的API进行操作。队列服务允许你延时处理耗时的任务，这样会加快应用的响应速度
同时提升系统的友好度  
Lumen的队列任务和Laravel的队列任务一样，请参考  
https://laravel.com/docs/queues
## 配置
队列任务的配置项在.env配置文件中。  
如果你想从根本上去定制任务，你必须拷贝整个vendor/laravel/lumen-framework/config/queue.php 到config的
根目录，并且调整你需要修改的配置项。如果config目录不存在，那么你需要创建它。
## 驱动程序的必备条件
*  Database
如果想要使用database序列的驱动，你需要数据库和数据表去保存这些任务  
Schema::create('jobs', function (Blueprint $table) {  
　　$table->bigIncrements('id');  
　　$table->string('queue');  
　　$table->longText('payload');  
　　$table->tinyInteger('attempts')->unsigned();  
　　$table->tinyInteger('reserved')->unsigned();  
　　$table->unsignedInteger('reserved_at')->nullable();  
　　$table->unsignedInteger('available_at');  
　　$table->unsignedInteger('created_at');  
　　$table->index(['queue', 'reserved', 'reserved_at']);  
});  
* 其他的队列依赖
下面的依赖项是List队列的驱动:  
Amazon SQS:aws/aws-sdk-php ~3.0  
Beanstalkd:pda/pheanstalk ~3.0  
Redis:predis/predis ~1.0  
## 和Laravel的不同点
其实和Laravel大部分都是一样的，参考:https://laravel.com/docs/5.3/queues  
然而，还是有一些区别，我们下面将要讨论  
* 生成器
Lumen没有包括自动生成新任务类的生成器。开发者需要拷贝ExampleJob，ExampleJob提供了所有Job都应该有的结构，
同时使用了Job类，这个类提供了InteractsWithQueue，Queueable和SerializesModels。
这个类的路径在App\jobs\ExampleJob  
* 调度的Job
和Laravel一样，你需要使用dispatch方法完成任务分发。  
dispatch(new ExampleJob);  
同时，也可以使用Queue facade进行操作。  
Queue::push(new ExampleJob);

### 服务容器
## 简介
服务容器是一个管理类依赖并且完成依赖注入的强大的工具。
## 和Laravel的区别
Lumen几乎使用和Laravel一样的服务容器，所以你可以使用Laravel所有的强大的特性。  
https://laravel.com/docs/5.3/container  
*  Laravel\Lumen\Application实例是Illuminate\Container\Container的扩展，所以几乎可以代表Lumen项目的
服务容器。  
通常，你需要使用Service Providers将绑定注册到容器中。当然，你可能使用bind、singleton、instance或者
其他container提供的方法。所有的方法都在Laravel的文档中有记录的: https://laravel.com/docs/container  
* 解析实例 
从容器中解析出实例，你可以使用声明式完成注入的操作  
$instance = app(Something::class);
### 服务提供者
##简介
服务提供者是Lumen应用的引导程序的核心。你的Lumen应用都是通过服务提供程序完成引导的。
但是，什么是引导呢?简单的说，我们把注册，包括注册服务容器，事件监听，中间件、事件路由等。服务提供者是配置
应用的中心环节。  
如果你打开bootstrap/app.php,你可以看到$app->register()方法的调用。你可以通过调用添加你自定义的服务提供
者。
## 撰写服务提供者
所有的服务提供者都需要继承Illuminate\Support\ServiceProvider类。这个类要求你在你的服务提供者中至少提供
一个register方法。你只能将事务绑定到服务容器中。你绝对不能使用register方法绑定事件监听、路由或者其他模块
的组件。
* Register方法
就像刚才说的，你使用register方法应该只能绑定事务到你的服务容器中。决不能绑定事件监听、路由或者其他功能模块的
组件。否则，你可能会使用到尚未加载的服务提供商的服务而导致程序错误。
好吧，我们先来看看基础的service provider吧～  
namespace App\Providers;   
use Riak\Connection;  
use Illuminate\Support\ServiceProvider;  
class RiakServiceProvider extends ServiceProvider  
{  
　　/**  
　　* Register bindings in the container.  
　　*  
　　* @return void  
　　*/  
　　public function register()  
　　{  
　　　　$this->app->singleton(Connection::class, function ($app) {  
　　　　　　return new Connection(config('riak'));  
　　　　});  
　　}  
}  
这个服务提供商仅仅定义了一个register方法并且注入了一个Riak\Connect到服务容器中。
## 引导方法(The Boot Method)
那么，如果我们需要使用服务提供者注入一个视图组件呢?这样的需求需要在boot方法中完成。<b> 这个方法将在所有的服务
提供商完成注册之后被调用 <b> 也就是说这样你可以使用所有已经注册的服务
namespace App\Providers;  
use Queue;  
use Illuminate\Support\ServiceProvider;  
class AppServiceProvider extends ServiceProvider  
{  
　　// Other Service Provider Properties...  
　　/**  
　　 * Bootstrap any application services.  
　　 *  
　　 * @return void  
　　 */  
　　public function boot()  
　　{  
　　　　Queue::failing(function ($event) {  
　　　　});  
　　}  
}  
## 注入Provider
所有的服务提供商都需要在bootstrap\app.php中完成注册。这个文件包括了所有的$app->register()方法的调用。你可以
按照自己的需要添加register方法。
### 单元测试
##简介
Lumen是有考虑到单元测试的。实际上，使用PHPUnit进行测试是开箱即用的，并且为你提供了phpunit.xml文件。框架也
提供了方便的helper方法以供你的Json返回测试。  
框架提供了一个ExampleTest.php的样例。当你新创建了一个Lumen项目的时候，简单的运行phpunit命令就可以完成测试。
* 测试环境
当测试的时候，Lumen会自动关闭Cache，也就是说，测试期间不会保留缓存数据  
你也可以自己配置测试环境，测试环境的配置在phpunit.xml文件中。
* 定义或运行测试
如果需要测试，那么就需要在tests目录中创建测试文件。测试文件需要继承TestCase。然后你需要自己撰写测试方法。  
class FooTest extends TestCase  
{  
　　public function testSomethingIsTrue()  
　　{  
　　　　$this->assertTrue(true);  
　　}  
}
## 应用测试
Lumen提供了非常流畅的API，用于你对测试HTTP请求并检查输出数据。  
* 测试Json的API
Lumen提供了一些helper用于测试JSON的API。比如:post\get\put\patch和delete等方法。你也可以将数据或者头部文件轻松的传递
传递给这些方法。  
class ExampleTest extends TestCase  
{  
　　/**  
　　* A basic functional test example.  
　　*  
　　* @return void  
　　*/  
　　public function testBasicExample()  
　　{  
　　　　$this->json('POST', '/user', ['name' => 'Sally'])  
　　　　　　->seeJson([  
　　　　　　'created' => true,  
　　　　]);  
　　}  
}  
seeJson方法将给定的数组转化为Json,然后检查是否有测试的Json片段。所以如果Response返回了多的Json片段，也是
可以通过的。
* 验证完全的json匹配
可以使用seeJsonEquals方法验证json完全匹配  
public function testBasicExample()  
{  
　　$this->post('/user', ['name' => 'Sally'])  
　　　　->seeJsonEquals([  
　　　　'created' => true,  
　　]);  
}
* 验证
actingAs助手方法提供一种简单的方式去验证给定的用户作为当前用户。  
class ExampleTest extends TestCase  
{  
　　public function testApplication()  
　　{  
　　　　$user = factory('App\User')->create();  
　　　　$this->actingAs($user)->get('/user');  
　　}  
}
* 定制HTTP请求
如果你需要模拟一个http请求并且获取到Response的话，那么可以使用call方法
public function testApplication(){  
　　$response = $this->call('get','/');  
　　$this->assertEquals(200,$response->status());  
}
## 使用Databases
Lumen也提供了很多方便的对数据库应用的帮助方法。首先，你可以似乎用seeInDatabase帮助方法去
确认数据是否存在。比如，我们认定数据库中user表中存在email值为"sally@example.com"的数据。可以测试如下:  
public function testDatabase(){  
    //Make call to application  
    $this->seeInDatabase('users',['email'=>'sally@example.com']);  
}  
…………………………………………………………………………

### 数据校验
## 简介
Lumen提供杜仲机制去验证输入参数。默认情况下，Lumen的base controller类使用ProvidesConvenienceMethods，
他提供了一中哦你方便的方法来使用各种强大的验证规则验证传入的HTTP请求。  
一般情况下，Lumen使用的验证机制和Laravel十分类似，请参考: https://laravel.com/docs/5.3/validation  
然后，也存在一些不同:  
## 和Laravel的不同点
* from request: Lumen不支持From Request。如果你使用form requests,你需要查看Laravel的文档。
* $this->validate方法：Lumen的$this->validate方法会提供一个Json格式的响应消息。这与Laravel版本的方法形
成对比，如果请求不是Ajax方法，Laravel的方法会重定向到response。因为Lumen是无状态的，并且不支持sessions,
刷新Session中的错误信息是不可能的。如果你需要使用重定向并且刷新error数据的话，你应该使用Laravel框架。  
和Laravel不同的是，Lumen提供了从Router闭包中访问validate的方法。  
use Illuminate\Http\Request;  
$app->post('/user', function (Request $request) {  
    $this->validate($request, [  
        'name' => 'required',  
        'email' => 'required|email|unique:users'  
    ]);  
    // Store User...  
});  
当然，你可以使用Validate:make去创建你想要的验证文件。  
## $error 视图变量
Lumen不能支持Session的开箱即用，所以$error视图变量在Lumen中并不能正常使用。如果验证失败，$this->validate
会抛出Illuminate\Validation\ValidationException 的Json信息。