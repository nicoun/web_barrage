<!DOCTYPE html>
<html lang="zh-CN">
	<head>
	    <meta charset="utf-8">
		<meta http-equiv="X-UA-Compatible" content="IE=edge">
		<meta name="viewport" content="width=device-width, initial-scale=1">
		<meta name="description" content="Demo">
		<meta name="keywords" content="">
		<!-- 新 Bootstrap 核心 CSS 文件 -->
		<link rel="stylesheet" href="//cdn.bootcss.com/bootstrap/3.3.5/css/bootstrap.min.css">
		<!-- 可选的Bootstrap主题文件（一般不用引入） -->
		<link rel="stylesheet" href="//cdn.bootcss.com/bootstrap/3.3.5/css/bootstrap-theme.min.css">

		<link rel="stylesheet" type="text/css" href="css/barrager.css">
		<!-- socket IO  -->
		<script src='//cdn.bootcss.com/socket.io/1.3.7/socket.io.js'></script>
		<!-- jQuery文件。务必在bootstrap.min.js 之前引入 -->
		<script src="//cdn.bootcss.com/jquery/1.11.3/jquery.min.js"></script>
		<!-- 最新的 Bootstrap 核心 JavaScript 文件 -->
		<script src="//cdn.bootcss.com/bootstrap/3.3.5/js/bootstrap.min.js"></script>

		<script src="//cdn.bootcss.com/json2/20150503/json2.min.js"></script>

		<script src="js/jquery.barrager.js"></script>

		<title>弹幕Demo</title>
	</head>
	<body style="background-color:#CCCC99">
		<div class="container navbar-fixed-bottom">

			<div class="row">
				<p></p>
				<div class="col-lg-12">
					<div class="input-group input-group-lg">
						<input type="text" id="announcement" class="form-control" placeholder="chat...">
						  <span class="input-group-btn">
							<button class="btn btn-success announcement" type="button">
								我要吐槽
							</button>
						  </span>
					</div>
				</div>
			</div>

			<div class="row">
				<p></p>
				<div class="col-lg-12">
					<div class="alert alert-info" id="online_count" style="text-align: center;"></div>
				</div>
			</div>

		</div>
	<script>
		$(document).ready(function () {
			// 连接服务端
			var http   = 'http://'+document.domain;
			var socket = io(http+':2120/');

			var uid = Date.parse(new Date());

			// 连接后登录
			socket.on('connect', function(){
				socket.emit('login', uid);
			});


			socket.on('update_online_count', function(online_stat){
				$('#online_count').html(online_stat);
			});

			$(".announcement").click(function(){
				_sendMsg();
				$("#announcement").val('');
			});

			document.onkeydown = function(e){
				var ev = document.all ? window.event : e;
				if(ev.keyCode==13) {
					$(".announcement").click();
				}
			};

			String.prototype.format = function(args) {
				var result = this;
				if (arguments.length < 1) {
					return result;
				}
				var data = arguments;
				if (arguments.length == 1 && typeof (args) == "object") {
					data = args;
				}
				for (var key in data) {
					var value = data[key];
					if (undefined != value) {
						result = result.replace("{" + key + "}", value);
					}
				}
				return result;
			};

			var  barrager_code=
					'var item={\n'+
					"   img:'{img}', //图片 \n"+
					"   info:'{info}', //文字 \n"+
					"   href:'{href}', //链接 \n"+
					"   close:{close}, //显示关闭按钮 \n"+
					"   speed:{speed}, //延迟,单位秒,默认6 \n"+
					"   bottom:{bottom}, //距离底部高度,单位px,默认随机 \n"+
					"   color:'{color}', //颜色,默认白色 \n"+
					"   old_ie_color:'{old_ie_color}', //ie低版兼容色,不能与网页背景相同,默认黑色 \n"+
					" }\n"+
					"$('body').barrager(item);";

			var  default_item={
				'img':'img/avatar.png',
				'info':'弹幕文字信息',
				'href':'',
				'close':true,
				'speed':5,
				'color':'#fff' ,
				'old_ie_color':'#000000'
			};

			socket.on('new_msg', function(rs){

				var obj = JSON.parse(rs);

				var item = {
					'img':'img/avatar.png',
					'info' :obj.data,
					'close':true
				};

				$('#barrager-code').val(barrager_code.format(default_item));

				$('body').barrager(item);
			});


			/**
			 * 发送信息
			 * @returns {boolean}
             * @private
             */
			function _sendMsg()
			{
				var content = $("#announcement").val();
				if (!content) {
					alert('请输入内容');
					return false;
				}
				var _url = http+':2121/?type=send_msg';
				$.ajax({
					url : _url, // 请求url
					type : "get", // 提交方式
					dataType : "json", // 数据类型
					data : {
						'content' : content
					},
					beforeSend:function(){
					},
					success : function(data) { // 提交成功的回调函数
					},
					error : function(){
					}
				});
			}

		});
	</script>
	</body>
</html>
