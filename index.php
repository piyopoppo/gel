<!DOCTYPE html>
<html>
	<head>
	<meta http-equiv="X-UA-Compatible" content="IE=edge">
	<title>Gel Generator</title>
	<meta charset="utf-8">
	<meta name="viewport" content="width=device-width, initial-scale=1">
<?php
	$validParam = false;
	$imageSrc = '';
	$tr = 226; $tg = 83; $tb = 0;
	$br = 0; $bg = 255; $bb = 255;
	if (isset($_GET['tr']) && isset($_GET['tg']) && isset($_GET['tb']) && 
		isset($_GET['br']) && isset($_GET['bg']) && isset($_GET['bb'])) {
		if (isRGBData($_GET['tr']) && isRGBData($_GET['tg']) && isRGBData($_GET['tb']) &&
			isRGBData($_GET['br']) && isRGBData($_GET['bg']) && isRGBData($_GET['bb'])) {
			$tr = $_GET['tr']; $tg = $_GET['tg']; $tb = $_GET['tb'];
			$br = $_GET['br']; $bg = $_GET['bg']; $bb = $_GET['bb'];
			$validParam = true;

			if (isset($_GET['tw']) && isFilename($_GET['tw'])) {
				$tweetUrl = sprintf('http://azurine.pupu.jp/miscellaneous/gel/?tr=%d&tg=%d&tb=%d&br=%d&bg=%d&bb=%d&tw=%s&fromtw=1', $tr, $tg, $tb, $br, $bg, $bb, $_GET['tw']);
				$imageSrc = sprintf('http://azurine.pupu.jp/miscellaneous/gel/gel_images/%s.png', $_GET['tw']);
			}
		}
	}

	function isRGBData($param) {
		return preg_match('/^[0-9][0-9]?[0-9]?$/', $param);
	}
	function isFilename($param) {
		return preg_match('/^[0-9]+_[0-9]+_[0-9]+_[0-9]+_[0-9]+_[0-9]+$/', $param);
	}
?>

<?php if (isset($_GET['tw']) && isFilename($_GET['tw']) && $validParam): ?>
	<meta name="twitter:card" content="summary">
	<meta name="twitter:creator" content="@piyopoppo">
	<meta name="twitter:title" content="オリジナルゲル">
	<meta name="twitter:description" content="オリジナルゲルです。">
	<meta name="twitter:image:src" content="<?php echo $imageSrc; ?>">
<?php endif ?>
	<link rel="stylesheet" href="./style.css" />
	</head>
	<body>
		<div id="wrapper">
			<h1><a href="./index.php"><img src="./images/logo.png" alt="Gel Generator" /></a></h1>
<?php if (isset($_GET['tw']) && isFilename($_GET['tw']) && $validParam && !isset($_GET['fromtw'])): ?>
			<p>
				下のボタンを押してゲルツイートをしよう！
			</p>
			<div class="tweet-button">
				<span>このゲルをツイートする→</span>
				<a href="https://twitter.com/share" class="twitter-share-button"{count} data-url="<?php echo $tweetUrl; ?>" data-text="オリジナルゲルです！" data-hashtags="gel_generator">Tweet</a>
			</div>
<?php else: ?>
			<p>
				君だけのオリジナルゲルを作ろう！<br />
				できたゲルは右クリックして画像保存できるぞ！
			</p>
<?php endif ?>
			<div id="gel-wrapper">
				<canvas id="gel" width="300" height="360"></canvas>
			</div>
			<canvas id="gel-tmp" width="300" height="360"></canvas>
			<div id="color-changers">
				<canvas id="gel-preview" width="100" height="118"></canvas>
				<div class="color-changer top">
					<div>top</div>
					<div><span class="red">red:</span><input type="range" name="red" value="<?php echo $tr; ?>" max="255" min="0" /><input type="text" name="red-text" value="" /></div>
					<div><span class="green">green:</span><input type="range" name="green" value="<?php echo $tg; ?>" max="255" min="0" /><input type="text" name="green-text" value="" /></div>
					<div><span class="blue">blue:</span><input type="range" name="blue" value="<?php echo $tb; ?>" max="255" min="0" /><input type="text" name="blue-text" value="" /></div>
				</div>
				<div class="color-changer bottom">
					<div>bottom</div>
					<div><span class="red">red:</span><input type="range" name="red" value="<?php echo $br; ?>" max="255" min="0" /><input type="text" name="red-text" value="" /></div>
					<div><span class="green">green:</span><input type="range" name="green" value="<?php echo $bg; ?>" max="255" min="0" /><input type="text" name="green-text" value="" /></div>
					<div><span class="blue">blue:</span><input type="range" name="blue" value="<?php echo $bb; ?>" max="255" min="0" /><input type="text" name="blue-text" value="" /></div>
				</div>
			</div>
			<div class="notice">
				<p>
					小さいゲルをクリックすると大きくなります！<br>
					大きいゲルをクリックするとTwitterにツイートできる形式で表示します！<br>
					ゲルおみくじは連打すると動作が重くなる事があります。
				</p>
				<button id="gel-random">ゲルおみくじをする</button>
			</div>
			<div id="author">
				元ネタ：世界樹の迷宮<br />
				つくったひと：piyopoppo<br />
				素材の提供：なしきさん
			</div>
		</div>
	<script type="text/javascript" src="//ajax.googleapis.com/ajax/libs/jquery/1.11.1/jquery.min.js"></script>
	
	<script>window.twttr = (function(d, s, id) {
  var js, fjs = d.getElementsByTagName(s)[0],
    t = window.twttr || {};
  if (d.getElementById(id)) return t;
  js = d.createElement(s);
  js.id = id;
  js.src = "https://platform.twitter.com/widgets.js";
  fjs.parentNode.insertBefore(js, fjs);
 
  t._e = [];
  t.ready = function(f) {
    t._e.push(f);
  };
 
  return t;
}(document, "script", "twitter-wjs"));</script>
	<script type="text/javascript" src="./main.js"></script>
	</body>
</html>