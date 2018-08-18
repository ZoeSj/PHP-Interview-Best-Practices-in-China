# API 篇

## RESTful 是什么

> RESTful是一种软件设计风格，全称是：Representational State Transfer（表现层状态转移/用URL定位资源，用HTTP动词描述操作）
有哪些RESTful风格的接口？
[Github API](https://developer.github.com/v3/)

**why choose RESTful ？**

### 接口基本原则：
1、安全可靠，高效易扩展
2、简单明了，可读性强，没有歧义
3、API风格统一，调用规则，传入参数和返回数据有统一的标准

### RESTful的设计理念基于HTTP协议，设计原则：
** 1、HTTPS **
HTTPS为接口的安全提供了保障，可以有效防止通信被窃听和篡改，可以通过
[cerbot](https://certbot.eff.org/)等工具。
Attention：非HTTPS的API调用，不要重定向到HTTPS。而要直接返回调用错误以禁止不安全的调用。

** 2、域名 **
应当尽可能的将API和主域名区分开，可以使用专用的域名：
`https://api.zoe.com`
Or 
`https://www.zoe.com/api`

** 3、版本控制 **
第一种：将版本号直接加入到URL中
```
https://api.zoe.com/v1
https://api.zoe.com/v2
```

第二种：使用http请求头的accept字段进行区分（推荐）
```
Https://api.zoe.com/
		Accept:application/prs.zoe.va+json
		Accept:application/prs.zoe.va+json
```
![postman test](http://ol1lfoh6e.bkt.clouddn.com/594affbbd269aee9ac07f6ecd51f50ba.png)

** 4、用URL定位资源 **
在REST福利的架构中，所有的一切都表示资源，每个URL都代表一个资源（名词），而且大部分情况下资源是名词的复数，尽量不要在URL中出现动词。
Such as：(冒号开始的代表变量)
```
GET /issues                          列出所有的 issue
GET /orgs/:org/issues                列出某个项目的 issue
GET /repos/:owner/:repo/issues/:number   获取某个项目的某个 issue
POST /repos/:owner/:repo/issues          为某个项目创建 issue
PATCH /repos/:owner/:repo/issues/:number         修改某个 issue
PUT /repos/:owner/:repo/issues/:number/lock      锁住某个 issue
DELETE /repos/:owner/:repo/issues/:number/lock   接收某个 issue
```
总结：
1、资源的设计可以嵌套，表明资源与资源之间的关系
2、大部分情况下访问的是某个资源集合，想要得到单个资源，可以通过资源的id或者number等唯一标识获取。
3、某些情况下，资源会是单数形式，例如某个项目某个issue的锁，每个issue只会有一把锁，所以是单数
错误的例子：
```
POST https://api.larabbs.com/createTopic
GET https://api.larabbs.com/topic/show/1
POST https://api.larabbs.com/topics/1/comments/create
POST https://api.larabbs.com/topics/1/comments/100/delete
```
正确的例子:
```
POST https://api.larabbs.com/topics
GET https://api.larabbs.com/topics/1
POST https://api.larabbs.com/topics/1/comments
DELETE https://api.larabbs.com/topics/1/comments/100
```

** 5、用http动词描述操作 **
http设计了很多动词来表示不同的操作，RESTful吧这些利用的很好，来表明如何操作资源。
幂等性：指一次和多次请求某一个资源应该具有同样的副作用，也就是一次访问和多次访问，对这个资源带来的变化是相同的。
常见的动词及幂等性：
|动词|描述|是否幂等性|
|:----    |:---|:----- |
|GET|获取资源，单个或多个|是|
|POST|创建资源|否|
|PUT|更新资源，客户端提供完整的资源数据|是|
|PATCH|更新资源。客户端提供部分的资源数据|否|
|DELETE|删除资源|是|

为什么PUT是幂等而patch不是呢？因为put是根据客户端提供了完整的资源数据，客户端提交什么就更新什么，而patch有可能是根据客户端提供的参数，动态的计算出某个值，例如每次请求后资源的某个参数减1，所以多次调用，资源会有不同的变化。

Attention：GET请求对于资源来说是安全的，不允许GET请求改变（更新或创建）资源，但是实际中，为了方便统计类的数据，会有一些例外，例如帖子详情，记录访问次数，每调用一次，访问次数加一。

** 6、资源过滤 **
需要提供合理的参数供客户端过滤资源，such as：
```
?state=closed:不同的状态
?page=2&per_page=100:访问第几页数据，每页多少条
?sortby=name&order=asc:指定返回结果按照哪个属性排序，以及排序顺序。
```

** 7、正确使用状态码 **
```
	200 OK - 对成功的 GET、PUT、PATCH 或 DELETE 操作进行响应。也可以被用在不创建新资源的 POST 操作上
	201 Created - 对创建新资源的 POST 操作进行响应。应该带着指向新资源地址的 Location 头
	202 Accepted - 服务器接受了请求，但是还未处理，响应中应该包含相应的指示信息，告诉客户端该去哪里查询关于本次请求的信息
	204 No Content - 对不会返回响应体的成功请求进行响应（比如 DELETE 请求）
	304 Not Modified - HTTP缓存header生效的时候用
	400 Bad Request - 请求异常，比如请求中的body无法解析
	401 Unauthorized - 没有进行认证或者认证非法
	403 Forbidden - 服务器已经理解请求，但是拒绝执行它
	404 Not Found - 请求一个不存在的资源
	405 Method Not Allowed - 所请求的 HTTP 方法不允许当前认证用户访问
	410 Gone - 表示当前请求的资源不再可用。当调用老版本 API 的时候很有用
	415 Unsupported Media Type - 如果请求中的内容类型是错误的
	422 Unprocessable Entity - 用来表示校验错误
	429 Too Many Requests - 由于请求频次达到上限而被拒绝访问

```

** 8、数据响应格式 **
默认使用json作为数据响应格式，如果客户端需求使用其他的响应格式，例如xml，需要在accept头中指定需要的格式。
```
Https://api.zoe.com/
		Accept:application/prs.zoe.v1+json
		Accept:application/prs.zoe.v1+xml
```
对于错误数据，默认使用如下结构：
```
‘message’ => ‘:message’,    //错误的具体描述
‘errors’ => ‘:errors’,				//参数的具体错误描述，422等状态码提供
‘code’ =>’:code’ 						//自定义的异常码
‘status_code’ => ‘:status_code’,//http状态码
‘debug’=>’:debug’,          //debug信息，非生产环境提供
```
例如：
```
{
		“message”:”422 Unprocessable Entity”,
		“errors”:{
				“name”:[
					“姓名必须好听“
				]
		},
		“status+code”:422
}

{
		“message”:”您无权访问该订单”,
		“status_code”:403
}
```

** 9、调用频率限制 **
为了防止服务器被攻击，减少服务器压力。需要对接口进行合适的限流控制，在响应头信息中加入合适的信息，告知客户端当前的限流情况：
```
		X-RateLimit-Limit :100 最大访问次数
		X-RateLimit-Remaining :93 剩余的访问次数
		X-RateLimit-Reset :1513784506 到该时间点，访问次数会重置为 X-RateLimit-Limit
```
超过限流次数后，需要返回 **429 Too Many Requests** 错误。

** 10、编写文档 **

为了方便用户使用，我们需要提供清晰的文档，尽可能包括以下几点
	•	包括每个接口的请求参数，每个参数的类型限制，是否必填，可选的值等。
	•	响应结果的例子说明，包括响应结果中，每个参数的释义。
	•	对于某一类接口，需要有尽量详细的文字说明，比如针对一些特定场景，接口应该如何调用。


* 如何在不支持 `DELETE` 请求的浏览器上兼容 `DELETE` 请求

## 常见 API 的 `APP_ID` `APP_SECRET` 主要作用是什么？阐述下流程 

- 客户端（app/浏览器）将用户导向第三方认证服务器
- 用户在第三方认证服务器，选择是否给予客户端授权
- 用户同意授权后，认证服务器将用户导向客户端事先指定的重定向URI，同时附上一个授权码。
- 客户端将授权码发送至服务器，服务器通过授权码以及APP_SECRET向第三方服务器申请access_token
- 服务器通过access_token，向第三方服务器申请用户数据，完成登陆流程，

	1.	APP_SECRET 存储在客户端，客户端获取授权码之后，直接通过授权码和 APP_SECRET 去第三方换取 access_token。
	2.	APP_SECRET 存储在服务端，客户端获取授权码之后，将授权码发送给服务器，服务器通过授权码和 APP_SECRET 去第三方换取 access_token。（推荐）

* API 请求如何保证数据不被篡改？

## JSON 和 JSONP 的区别

谈到ajax会面临两个问题，一个是：AJAX以何种格式来交换数据？另一个：跨域的需求如何解决？
比较推崇的方案：用json来传数据，靠jsonp来实现跨域。

** json示例：**
```
// 描述一个人

var person = {
    "Name": "Bob",
    "Age": 32,
    "Company": "IBM",
    "Engineer": true
}

// 获取这个人的信息

var personAge = person.Age;

// 描述几个人

var members = [
    {
        "Name": "Bob",
        "Age": 32,
        "Company": "IBM",
        "Engineer": true
    },
    {
        "Name": "John",
        "Age": 20,
        "Company": "Oracle",
        "Engineer": false
    },
    {
        "Name": "Henry",
        "Age": 45,
        "Company": "Microsoft",
        "Engineer": false
    }
]

// 读取其中John的公司名称

var johnsCompany = members[1].Company;

// 描述一次会议

var conference = {
    "Conference": "Future Marketing",
    "Date": "2012-6-1",
    "Address": "Beijing",
    "Members": 
    [
        {
            "Name": "Bob",
            "Age": 32,
            "Company": "IBM",
            "Engineer": true
        },
        {
            "Name": "John",
            "Age": 20,
            "Company": "Oracle",
            "Engineer": false
        },
        {
            "Name": "Henry",
            "Age": 45,
            "Company": "Microsoft",
            "Engineer": false
        }
    ]
}

// 读取参会者Henry是否工程师

var henryIsAnEngineer = conference.Members[2].Engineer;
```

** JSONP **
>Ajax直接请求普通文件存在跨域无权限访问的问题，无论是静态页面还是动态页面，web服务，WCF（？），但是在web页面上调用js文件时不受到跨域的影响（凡是yongyousrc属性的都有跨域的神奇能力），所以可以通过在远程服务器上设法把数据装进js格式的文件里，供客户端调用和进一步处理，而处理这些数据的格式可以是json，而且json还被原生js支持，很完美了。

方案如下：
Web客户端通过与调用脚本一模一样的方式，来调用跨域服务器上动态生成的js格式文件，客户端在对json文件成功调用之后，获得了自己所需的数据，这就是jsonp，该协议的一个要点就是允许用户传递一个callback参数给服务端，然后服务端返回数据时会将这个callback参数作为函数名来包裹住json数据，这样客户端就可以随意定制函数来自动处理返回数据。

具体实现示例：
喵喵：远程服务器。cat.com
汪汪：本地服务器.dag.com
1  miaomiao.js是cat.com根目录下的一个js文件。
代码如下：
```
alert(‘喵呜，我是喵喵’);
```
Jsonp.html是dag.com下的一个页面：
```
<!DOCTYPE html>
<html>
<head>
    <title></title>
    <script type="text/javascript" src="http://cat.com/miaomiao.js"></script>
</head>
<body>
</body>
</html>
```
这里会弹出弹窗，现实跨域成功。

2  在jsonp.html页面定义一个函数。然后在远程文件miaomiao.js中传入数据进行调用。
```
<!DOCTYPE html>
<head>
    <title></title>
    <script type="text/javascript">
    var localHandler = function(data){
        alert('我是dog函数，可以被跨域猫的miaomiao.js文件调用，远程js带来的数据是：' + data.result);
    };
    </script>
    <script type="text/javascript" src="http://cat.com/miaomiao.js”></script>
</head>
<body>

</body>
</html>
```

miaomiao.js:
```
localHandler({"result":"我是远程猫js带来的数据"});
```
运行之后，显示本地调用成功，并且获取数据。但是如何让miaomiao知道它调用的dog函数叫什么呢？毕竟附近的dog太多了。

3 喵喵和汪汪想了一个办法，如果汪汪想要调用喵喵，就在返回的骨头上加一个标志，说我想调用XXX函数的js代码，你丫别给我传错了啊。于是喵喵就按照骨头上的需求来生成js脚本并且给汪汪一个响应“拿好你的骨头，别搞丢了”
汪汪的jsonp.html
```
<!DOCTYPE html>
<head>
    <title></title>
    <script type="text/javascript">
    // 得到骨头信息查询结果后的回调函数
    var boneHandler = function(data){
        alert('你查询的肉骨头结果是：骨头价格 ' + data.price + ' 元，' + '余骨 ' + data.nums + ' 块。’);
    };
    // 提供jsonp服务的url地址（不管是什么类型的地址，最终生成的返回值都是一段javascript代码）
    var url = "http://cat.com/jsonp/boneResult.aspx?code=pig&callback=boneHandler";
    // 创建script标签，设置其属性
    var script = document.createElement('script');
    script.setAttribute('src', url);
    // 把script标签加入head，此时调用开始
    document.getElementsByTagName('head')[0].appendChild(script); 
    </script>
</head>
<body>

</body>
</html>
```
上面实现的是编码动态查询，也是jsonp客户端实现的核心。下面是如何完成jsonp调用的全过程。
上面url中的code参数表示dog告诉cat我要查询附近猪骨的信息，并且把一个叫callback的骨头给cat，说这是我们的暗号（boneHandler），别和其他狗子搞混了，你把结果都放到这块骨头的这个暗号中给我传过来。于是这个叫做boneResult.aspx的页面生成了一段这样的代码提供给jsonp.html
```
boneHandler({
    "code": "CA1998",
    "price": 1780,
    "tickets": 5
});

```

4 如何用jquery实现？
Attention:jquery在处理jsonp类型的ajax时（虽然jquery也把jsonp归入了ajax，但其实它们真的不是一回事儿），自动帮你生成回调函数并把数据取出来供success属性方法来调用
```
<!DOCTYPE html>
 <head>
     <title>Untitled Page</title>
      <script type="text/javascript" src=jquery.min.js"></script>
      <script type="text/javascript">
     jQuery(document).ready(function(){ 
        $.ajax({
             type: "get",
             async: false,
             url: "http://cat.com/jsonp/boneResult.aspx?code=pig”,
             dataType: "jsonp",
             jsonp: "callback",//传递给请求处理程序或页面的，用以获得jsonp回调函数名的参数名(一般默认为:callback)
             jsonpCallback:”boneHandler",//自定义的jsonp回调函数名称，默认为jQuery自动生成的随机函数名，也可以写"?"，jQuery会自动为你处理数据
             success: function(json){
                 alert('您查询到骨头信息：骨头价格： ' + json.price + ' 元，余骨： ' + json.nums + ' 块。’);
             },
             error: function(){
                 alert('fail');
             }
         });
     });
     </script>
     </head>
  <body>
  </body>
 </html>
```

1、ajax和jsonp这两种技术在调用方式上“看起来”很像，目的也一样，都是请求一个url，然后把服务器返回的数据进行处理，因此jquery和ext(?)等框架都把jsonp作为ajax的一种形式进行了封装；
2、但ajax和jsonp其实本质上是不同的东西。ajax的核心是通过XmlHttpRequest获取非本页内容，而jsonp的核心则是动态添加<script>标签来调用服务器提供的js脚本。
3、其实ajax与jsonp的区别不在于是否跨域，ajax通过服务端代理一样可以实现跨域，jsonp本身也不排斥同域的数据的获取。
4、还有就是，jsonp是一种方式或者说非强制性协议，如同ajax一样，它也不一定非要用json格式来传递数据，如果你愿意，字符串都行，只不过这样不利于用jsonp提供公开服务。
总而言之，jsonp不是ajax的一个特例，哪怕jquery等巨头把jsonp封装进了ajax，也不能改变着一点！


* 数据加密和验签的区别
* RSA 是什么
* API 版本兼容怎么处理
* 限流（木桶、令牌桶）
* OAuth 2 主要用在哪些场景下
* JWT
* PHP 中 `json_encode(['key'=>123]);` 与 `return json_encode([]);` 区别，会产生什么问题？如何解决


