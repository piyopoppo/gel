(function($) {
	var FILES = ["gelback", "geltop5", "gelbottom4", "gelmouth"];
	var IMAGES = new Object();
	var GEL_WIDTH = 500;
	var GEL_HEIGHT = 588;

	var CTX = null;
	var CTX_MAT = null;
	var CTX_PRE = null;
	var canvas = null;
	var canvas2 = null;
	var canvas3 = null;

	var loaded = false;
	var dback = null;
	var dtop = null;
	var dbottom = null;
	var dmouth = null;

	function init() {
		if (!initCanvas()) {
			return;
		}
		loadMaterials();
		initListeners();
	}
	function initCanvas() {
		canvas = $('#gel').get(0);
		if (!canvas || !canvas.getContext) {
			// canvas is not supported
			return false;
		}
		CTX = canvas.getContext('2d');
		canvas2 = $('#gel-tmp').get(0);
		CTX_MAT = canvas2.getContext('2d');
		canvas3 = $('#gel-preview').get(0);
		CTX_PRE = canvas3.getContext('2d');
		CTX.clearRect(0, 0, canvas.width, canvas.height);
		CTX_MAT.clearRect(0, 0, canvas.width, canvas.height);
		CTX_PRE.clearRect(0, 0, canvas3.width, canvas3.height);

		return true;
	}
	function initListeners() {
		$('.color-changer input[type=range]').on('change', function() {
			if ($(this).prop('init') == 1) {
				disableTweetButton();
			}

			$(this).prop('init', 1);
			if ($(this).next().val() == $(this).val()) {
				return;
			}
			$(this).next().val($(this).val());
			if (loaded) {
				draw();
			}
		});
		$('.color-changer input[type=text]').on('change keyup keypress', function() {
			if ($(this).prev().val() == $(this).val()) {
				return;
			}
			$(this).prev().val($(this).val());
			if (loaded) {
				draw();
			}
		});
		$('.color-changer input').trigger('change');

		$('#color-changers canvas').on('click', function() {
			$('#gel-wrapper').addClass('loading');
			CTX.clearRect(0, 0, canvas.width, canvas.height);
			setTimeout(function() {
				draw(0.6, CTX);
				$('#gel-wrapper').removeClass('loading');
			}, 10);
		});
		$('#gel-random').on('click', function() {
			$('.color-changer input[type=range]').each(function() {
				$(this).val(Math.floor( Math.random() * 256));
			});
			$('.color-changer input').trigger('change');
			draw(0.6, CTX);
		});
		twttr.ready(function (twttr) {
			twttr.events.bind(
				'click',
				function (ev) {
					disableTweetButton();
				}
			);
		});
		$('#gel').on('click', function() {
			if ($('#gel').hasClass('sending')) {
				return;
			}
			$('#gel').addClass('sending');

			draw();
			var buffer = convertToBinary(canvas3);
			var blob = new Blob([buffer.buffer], { type: 'image/png' });
			var formData = new FormData();
			var tr = $('.color-changer.top input[name=red]').val(),
				tg = $('.color-changer.top input[name=green]').val(),
				tb = $('.color-changer.top input[name=blue]').val(),
				br = $('.color-changer.bottom input[name=red]').val(),
				bg = $('.color-changer.bottom input[name=green]').val(),
				bb = $('.color-changer.bottom input[name=blue]').val();
			formData.append('image', blob);
			formData.append('tr', tr);
			formData.append('tg', tg);
			formData.append('tb', tb);
			formData.append('br', br);
			formData.append('bg', bg);
			formData.append('bb', bb);
			$.ajax({
				type: 'post',
				url: 'up_gel.php',
				data: formData,
				dataType: 'json',
				contentType: false,
				processData: false
			}).done(function(json) {
				if (json.success) {
					location.href = 'index.php?' +
						'tr=' + tr +
						'&tg=' + tg +
						'&tb=' + tb +
						'&br=' + br +
						'&bg=' + bg +
						'&bb=' + bb +
						'&tw=' + json.filename;
				} else {
					alert('失敗しちゃいました～～～');
				}
			}).fail(function() {
				alert('失敗しちゃいました～～～');
			}).always(function() {
				$('#gel').removeClass('sending');
			});
		});
	}
	function loadMaterials() {
		for (var i = 0; i < FILES.length; i++) {
			var img = new Image();
			img.src = 'images/' + FILES[i] + '.png?' + new Date().getTime();
			img._name = FILES[i]
			$(img).on('load', function() {
				IMAGES[$(this).get(0)._name] = $(this).get(0);

				if (Object.keys(IMAGES).length == 4) {
					loaded = true;
					draw();
					draw(0.6, CTX);
				}
			});
		}
	}

	function draw(ratio, target) {
		// default params
		if (arguments.length <= 1) {
			target = CTX_PRE;
		}
		if (arguments.length <= 0) {
			ratio = 0.2;
		}
		if (CTX == null) {
			// canvas not supported
			return;
		}
		prepareForDraw(ratio);
		target.clearRect(0, 0, canvas.width, canvas.height);

		for (var i = 0; i < canvas.width * canvas.height * 4; i += 4) {
			if (dback.data[i + 3] == 0) continue;
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

		var yoffset = 0;
		for (i = 0; i < 3; i++) {
			if (target == CTX_PRE) {
				yoffset = -10;
			}
			CTX_MAT.putImageData(dtop, 0, 0);
			target.drawImage(canvas2, 0, yoffset);
			CTX_MAT.putImageData(dbottom, 0, 0);
			target.drawImage(canvas2, 0, yoffset);
			CTX_MAT.putImageData(dmouth, 0, 0);
			target.drawImage(canvas2, 0, yoffset);
		}
	}
	function prepareForDraw(ratio) {
		var gw = GEL_WIDTH * ratio;
		var gh = GEL_HEIGHT * ratio;
		var cw = canvas.width;
		var ch = canvas.height;

		CTX_MAT.drawImage(IMAGES['gelback'], 0, 0, gw, gh);
		dback = CTX_MAT.getImageData(0, 0, cw, ch);
		CTX_MAT.clearRect(0, 0, cw, ch);

		CTX_MAT.drawImage(IMAGES['geltop5'], 0, 0, gw, gh);
		dtop = CTX_MAT.getImageData(0, 0, cw, ch);
		CTX_MAT.clearRect(0, 0, cw, ch);

		CTX_MAT.drawImage(IMAGES['gelbottom4'], 0, 0, gw, gh);
		dbottom = CTX_MAT.getImageData(0, 0, cw, ch);
		CTX_MAT.clearRect(0, 0, cw, ch);

		CTX_MAT.drawImage(IMAGES['gelmouth'], 0, 0, gw, gh);
		dmouth = CTX_MAT.getImageData(0, 0, cw, ch);
		CTX_MAT.clearRect(0, 0, cw, ch);
	}

	function convertToBinary(canvas) {
		var base64 = canvas.toDataURL('image/png');
		var bin = atob(base64.replace(/^.*,/, ''));
		var buffer = new Uint8Array(bin.length);

		for (var i = 0; i < bin.length; i++) {
			buffer[i] = bin.charCodeAt(i);
		}
		return buffer;
	}
	function disableTweetButton() {
		$('.tweet-button').html('再度ツイートするには下の大きいゲルをクリックしてください。');
	}

	// ready
	$(function() {
		init();
	});
})(jQuery);
