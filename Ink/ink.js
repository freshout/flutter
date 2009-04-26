Event.observe(window, 'load', watchInkFields, false);
Event.observe(window, 'unload', Event.unloadCache, false);

function watchInkFields() {
	if($('enableInk').checked == false) { toggleForm('ink'); }
	if($('restore')) { Event.observe('restore', 'click', restoreInk, false); }
	updateInkColors();
	Event.observe('enableInk', 'click', function(){ toggleForm('ink'); }, false);
	new Form.Element.EventObserver('red', function(){ saveInkVariables($F('activeField')); ink_rgb2hex(); setSliders($F($F('activeField'))) });
	new Form.Element.EventObserver('green', function(){ saveInkVariables($F('activeField')); ink_rgb2hex(); setSliders($F($F('activeField'))) });
	new Form.Element.EventObserver('blue', function(){ saveInkVariables($F('activeField')); ink_rgb2hex(); setSliders($F($F('activeField'))) });
	Form.getElements('ink').each(function(item){
		new Form.Element.EventObserver(item, function(item){ saveInkVariables(item.id); updateInkColor(item); });
		Event.observe(item.id, 'keyup', function(){ saveInkVariables(item.id), updateInkColor(item.id); }, false)
	});
	document.getElementsByClassName('color').each(function(item){
		Event.observe(item.id, 'click', function(){ toggleColorSwatch(item.id); }, false);
	});
	redSlider = new Control.Slider('red_slider','red_bar', {axis: 'horizontal', onSlide:function(v){$('red').value = (v*255).toFixed(); ink_rgb2hex();}, onChange:function(){ if($F('activeField') != '') saveInkVariables($F('activeField')); }} );
	greenSlider = new Control.Slider('green_slider','green_bar', {axis: 'horizontal', onSlide:function(v){$('green').value = (v*255).toFixed(); ink_rgb2hex();}, onChange:function(){ if($F('activeField') != '') saveInkVariables($F('activeField')); }} );
	blueSlider = new Control.Slider('blue_slider','blue_bar', {axis: 'horizontal', onSlide:function(v){$('blue').value = (v*255).toFixed(); ink_rgb2hex();}, onChange:function(){ if($F('activeField') != '') saveInkVariables($F('activeField')); }} );
}

function toggleForm(form) {
	if($('activeField').disabled == false) {
		$(form).style.color = '#777777';
		Form.disable(form);
		updateInkOption('enableInk', 'false');
	} else {
		updateInkOption('enableInk', 'true');
		$(form).style.color = '#000000';
		Form.enable(form);
	}
}

function toggleColorSwatch(element) {
	if($F('activeField') != '') { $($F('activeField')).style.backgroundImage = 'url(JS_FLUTTER_URI + "images/colorswatch.png")';
		if($F($F('activeField')) == '') { $($F('activeField')).style.backgroundColor = ''; }
	}
	$('activeField').value = element;
	$(element).style.backgroundImage = 'url(JS_FLUTTER_URI + "images/colorswatch-active.png")';
	if($F($F('activeField')) != '') { setSliders($F($F('activeField'))); }
		else { resetSliders(); }
}

function setSliders(value) {
	ink_hex2rgb(value);
	redSlider.setValue($F('red')/255);
	greenSlider.setValue($F('green')/255);
	blueSlider.setValue($F('blue')/255);
	ink_rgb2hex();
}

function resetSliders() {
	$('red').value = '0';
	$('green').value = '0';
	$('blue').value = '0';
	redSlider.setValue(0);
	greenSlider.setValue(0);
	blueSlider.setValue(0);
	$('colorfield').style.backgroundColor = '#000000';
}

function saveInkVariables(element) {
	if($F(element).length == 6 || $F(element) == '') {
		var value = escape($F(element));
		var string = element.split('__');
		var pars = 'element='+string[0]+'&definition='+string[1]+'&value='+value;
		var url = JS_FLUTTER_URI + 'Ink/ink-ajax.php';
		var myAjax = new Ajax.Request(url, 
			{method: 'get', 
			parameters: pars} 
			);
	}
}

function updateInkColors() {
	document.getElementsByClassName('color').each(function(item){
		if($F(item) != '') $(item).style.backgroundColor = '#'+$F(item);
	});
}

function updateInkColor(item) {
	if($F(item) != '' && ($F(item).length == 6)) { 
		$(item).style.backgroundColor = '#'+$F(item);
		setSliders($F(item));
	}
}

function ink_rgb2hex() {
	var hexindex = "0123456789ABCDEF";
	var R = $F('red');
	var G = $F('green');
	var B = $F('blue');
	R = hexindex.charAt((R - R % 16) / 16) + hexindex.charAt(R % 16);
	G = hexindex.charAt((G - G % 16) / 16) + hexindex.charAt(G % 16);
	B = hexindex.charAt((B - B % 16) / 16) + hexindex.charAt(B % 16);
	$('colorfield').style.backgroundColor = '#'+R+G+B;
	if($F('activeField') != '') {
		$($F('activeField')).style.backgroundColor = '#'+R+G+B;
		$($F('activeField')).value = R+G+B;
	}
}

function ink_hex2rgb(hex) {
    var hexindex = "0123456789ABCDEF";
	var R = hexindex.indexOf(hex.charAt(0).toUpperCase()) * 16 + hexindex.indexOf(hex.charAt(1).toUpperCase());
	var G = hexindex.indexOf(hex.charAt(2).toUpperCase()) * 16 + hexindex.indexOf(hex.charAt(3).toUpperCase());
	var B = hexindex.indexOf(hex.charAt(4).toUpperCase()) * 16 + hexindex.indexOf(hex.charAt(5).toUpperCase());
	$('red').value = R;
	$('green').value = G;
	$('blue').value = B;
}

function updateInkOption(option, value) {
	var pars = 'option='+option+'&value='+value;
	var url = JS_FLUTTER_URI + 'Ink/ink-ajax.php';
	var myAjax = new Ajax.Request(url, 
		{method: 'get', 
		parameters: pars} 
		);
}

function restoreInk() {
	if(window.confirm('Are you sure you restore default values? All changes will be lost.')) {
		var pars = 'restore=true';
		var url = JS_FLUTTER_URI + 'Ink/ink-ajax.php';
		var myAjax = new Ajax.Request(url, 
			{method: 'get', 
			parameters: pars,
			onComplete: function() { window.location.href=window.location.href; }
			});
	}
}