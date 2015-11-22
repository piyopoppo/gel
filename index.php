<!DOCTYPE html>
<html>
	<head>
	<meta http-equiv="X-UA-Compatible" content="IE=edge">
	<title>gel generator</title>
	<meta charset="utf-8">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<style><!--
body {
	font-family: "HG創英角ﾎﾟｯﾌﾟ体", "ヒラギノ角ゴ Pro W3", "Hiragino Kaku Gothic Pro", "メイリオ", Meiryo, Osaka, "ＭＳ Ｐゴシック", "MS PGothic", sans-serif;
}
#gel-preview {
	box-shadow: 1px 1px 5px rgba(0,0,0, 0.6);
	margin: auto;
	width: 300px;
	display: block;
}
#gel-tmp {
	display: none;
}
#color-changers {
	margin-top: 10px;
	text-align: center;
}
.color-changer {
	display: inline-block;
}
.color-changer > div > span {
	width: 4em;
	display: inline-block;
}
.color-changer input[type=range] {
	width: 100px;
}
.color-changer .red { color: red; }
.color-changer .green { color: green; }
.color-changer .blue { color: blue; }
#wrapper {
	width: 620px;
	margin: auto;
}
#author {
	margin-top: 30px;
	text-align: right;
}
	--></style>
	</head>
	<body>
		<div id="wrapper">
			<h1><img src="./images/logo.png" alt="Gel Generator" /></h1>
			<p>
				君だけのオリジナルゲルを作ろう！<br />
				できたゲルは右クリックして画像保存できるぞ！
			</p>
			<canvas id="gel-preview" width="300" height="360"></canvas>
			<canvas id="gel-tmp" width="300" height="360"></canvas>
			<div id="color-changers">
				<div class="color-changer top">
					<div>top</div>
					<div><span class="red">red:</span><input type="range" name="red" value="226" max="255" min="0" /><span>0</span></div>
					<div><span class="green">green:</span><input type="range" name="green" value="83" max="255" min="0" /><span>0</span></div>
					<div><span class="blue">blue:</span><input type="range" name="blue" value="0" max="255" min="0" /><span>0</span></div>
				</div>
				<div class="color-changer bottom">
					<div>bottom</div>
					<div><span class="red">red:</span><input type="range" name="red" value="0" max="255" min="0" /><span>0</span></div>
					<div><span class="green">green:</span><input type="range" name="green" value="255" max="255" min="0" /><span>0</span></div>
					<div><span class="blue">blue:</span><input type="range" name="blue" value="255" max="255" min="0" /><span>0</span></div>
				</div>
			</div>
			<div id="author">
				元ネタ：世界樹の迷宮<br />
				つくったひと：piyopoppo<br />
				素材の提供：なしきさん
			</div>
		</div>
	<script src="//ajax.googleapis.com/ajax/libs/jquery/1.11.1/jquery.min.js"></script>
	<script><!--
(function($) {
	var FILES = ["gelback", "geltop5", "gelbottom4", "gelmouth"];
	var IMAGES = new Object();
	var canvas = null;
	var canvas2 = null;
	var CTX = null;
	var CTX2 = null;
	$(function() {
		$('.color-changer input').each(function() {
			$(this).next().html($(this).val());
		});
		$('.color-changer input').on('change', function() {
			$(this).next().html($(this).val());
			draw();
		});
		canvas = $('#gel-preview').get(0);
		if (!canvas || !canvas.getContext) {
			return;
		}
		canvas2 = $('#gel-tmp').get(0);
		CTX = canvas.getContext('2d');
		CTX2 = canvas2.getContext('2d');
		CTX.clearRect(0, 0, canvas.width, canvas.height);
		CTX2.clearRect(0, 0, canvas.width, canvas.height);
		for (var i = 0; i < FILES.length; i++) {
			var img = new Image();
			img.src = 'images/' + FILES[i] + '.png?' + new Date().getTime();
			img._name = FILES[i]
			$(img).on('load', function() {
				IMAGES[$(this).get(0)._name] = $(this).get(0);

				if (Object.keys(IMAGES).length == 4) {
					onLoaded();
				}
			});
		}

		var dback = null;
		var dtop = null;
		var dbottom = null;
		var dmouth = null;
		function onLoaded() {

			draw();
		}
		
		function draw() {
			if (CTX == null) {
				return;
			}

			var w = 500;
			var h = 588;
			var ratio = 0.6;
			CTX2.drawImage(IMAGES['gelback'], 0, 0, w * ratio, h * ratio);
			dback = CTX2.getImageData(0, 0, canvas.width, canvas.height);
			CTX2.clearRect(0, 0, canvas.width, canvas.height);

			CTX2.drawImage(IMAGES['geltop5'], 0, 0, w * ratio, h * ratio);
			dtop = CTX2.getImageData(0, 0, canvas.width, canvas.height);
			CTX2.clearRect(0, 0, canvas.width, canvas.height);

			CTX2.drawImage(IMAGES['gelbottom4'], 0, 0, w * ratio, h * ratio);
			dbottom = CTX2.getImageData(0, 0, canvas.width, canvas.height);
			CTX2.clearRect(0, 0, canvas.width, canvas.height);

			CTX2.drawImage(IMAGES['gelmouth'], 0, 0, w * ratio, h * ratio);
			dmouth = CTX2.getImageData(0, 0, canvas.width, canvas.height);
			CTX2.clearRect(0, 0, canvas.width, canvas.height);
			
			for (var i = 0; i < canvas.width * canvas.height * 4; i += 4) {
				if (dback.data[i + 3] == 0) continue;
				//if (dback.data[i + 3] == 0 && dtop.data[i + 3] == 0) continue;
				if (dtop.data[i + 3] > 0) {
					dtop.data[i] -= 255 - $('.color-changer.top input[name=red]').val();
					dtop.data[i + 1] -= 255 - $('.color-changer.top input[name=green]').val();
					dtop.data[i + 2] -= 255 - $('.color-changer.top input[name=blue]').val();
				}
				if (dbottom.data[i + 3] > 0) {
					dbottom.data[i] -= 255 - $('.color-changer.bottom input[name=red]').val();
					dbottom.data[i + 1] -= 255 - $('.color-changer.bottom input[name=green]').val();
					dbottom.data[i + 2] -= 255 - $('.color-changer.bottom input[name=blue]').val();
				}
			}
			
			//CTX2.putImageData(dback, 0, 0);
			//CTX.drawImage(canvas2, 0, 0);
			CTX2.putImageData(dtop, 0, 0);
			CTX.drawImage(canvas2, 0, 0);
			CTX2.putImageData(dbottom, 0, 0);
			CTX.drawImage(canvas2, 0, 0);
			CTX2.putImageData(dmouth, 0, 0);
			CTX.drawImage(canvas2, 0, 0);
		}
	});
})(jQuery);
	--></script>
	</body>
</html>