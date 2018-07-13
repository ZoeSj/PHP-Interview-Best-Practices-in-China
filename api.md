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
**1、HTTPS**
HTTPS为接口的安全提供了保障，可以有效防止通信被窃听和篡改，可以通过
[cerbot](https://certbot.eff.org/)等工具。
Attention：非HTTPS的API调用，不要重定向到HTTPS。而要直接返回调用错误以禁止不安全的调用。

**2、域名**
应当尽可能的将API和主域名区分开，可以使用专用的域名：
`https://api.zoe.com`
Or 
`https://www.zoe.com/api`

**3、版本控制**
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

**4、用URL定位资源**
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

**5、用http动词描述操作**
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

**6、资源过滤**
需要提供合理的参数供客户端过滤资源，such as：
```
?state=closed:不同的状态
?page=2&per_page=100:访问第几页数据，每页多少条
?sortby=name&order=asc:指定返回结果按照哪个属性排序，以及排序顺序。
```

**7、正确使用状态码**
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

**8、数据响应格式**
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

**9、调用频率限制**
为了防止服务器被攻击，减少服务器压力。需要对接口进行合适的限流控制，在响应头信息中加入合适的信息，告知客户端当前的限流情况：
```
		X-RateLimit-Limit :100 最大访问次数
		X-RateLimit-Remaining :93 剩余的访问次数
		X-RateLimit-Reset :1513784506 到该时间点，访问次数会重置为 X-RateLimit-Limit
```
超过限流次数后，需要返回 **429 Too Many Requests** 错误。

**10、编写文档**

为了方便用户使用，我们需要提供清晰的文档，尽可能包括以下几点
	•	包括每个接口的请求参数，每个参数的类型限制，是否必填，可选的值等。
	•	响应结果的例子说明，包括响应结果中，每个参数的释义。
	•	对于某一类接口，需要有尽量详细的文字说明，比如针对一些特定场景，接口应该如何调用。


## 如何在不支持 `DELETE` 请求的浏览器上兼容 `DELETE` 请求

* 常见 API 的 `APP_ID` `APP_SECRET` 主要作用是什么？阐述下流程 
* API 请求如何保证数据不被篡改？
* JSON 和 JSONP 的区别
* 数据加密和验签的区别
* RSA 是什么
* API 版本兼容怎么处理
* 限流（木桶、令牌桶）
* OAuth 2 主要用在哪些场景下
* JWT
* PHP 中 `json_encode(['key'=>123]);` 与 `return json_encode([]);` 区别，会产生什么问题？如何解决