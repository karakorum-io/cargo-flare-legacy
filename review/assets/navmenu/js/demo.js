$(document).ready(function() {
	
	var effect = '';
	var style = 'lgray';
	
	$('#menu').addClass(style + " " + effect);
	
	$('#effects a').click(function() {
		var new_effect = $(this).attr('href').substring(1);
		if (new_effect != effect)
		{
			$('#menu').removeClass(effect).addClass(new_effect);
			effect = new_effect;
		}
		setDemoInfo();
	});
	
	$('#styles a ').click(function() {
		var new_style = $(this).attr('href').substring(1);
		if (new_style != style)
		{
			$('#menu').removeClass(style).addClass(new_style);
			style = new_style;
		}
		setDemoInfo();
	});
	
	function setDemoInfo() {
		var head_code = '';
		head_code += '&lt;link rel="stylesheet" href="css/menu/core.css" type="text/css" media="screen"&gt;\n';
		head_code += '&lt;link rel="stylesheet" href="css/menu/styles/' + style + '.css" type="text/css" media="screen"&gt;\n';
		if (effect != '') {
			head_code += '&lt;!--[if (gt IE 9)|!(IE)]&gt;&lt;!--&gt;\n';
			head_code += '&nbsp;&nbsp;&lt;link rel="stylesheet" href="css/menu/effects/' + effect + '.css" type="text/css" media="screen"&gt;\n';
			head_code += '&lt;!--&lt;![endif]--&gt;';
		}
		$('#head_code').html(head_code);
		
		var html_code = '';
		
		var classes = 'menu';
		classes += ' ' + style;
		if (effect != '') {
			classes += ' ' + effect;
		}
		
		html_code += '&lt;ul class="' + classes + '"&gt;\n';
		html_code += '&nbsp;&nbsp;&lt;li&gt;&lt;a href="#"&gt;First item&lt;/a&gt;&lt;/li&gt;\n'
		html_code += '&lt;ul&gt;';
		$('#html_code').html(html_code);
	}
	
	setDemoInfo();
	
});