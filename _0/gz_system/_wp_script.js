(function($){
	$('document').ready(function(){ //console.log($.fancybox);
		//init_fullscreen();
		//init_debug();
	});
	
	function init_fullscreen(){//console.log('init_fullscreen()');
		window.scrollTo(0,1);
	}

	function init_debug(){//console.log('debug');
		$('body').append("<div id='debug' style='background-color:white; width:100%;'></div>");
		$('#debug').append("<div id='body'>body.class="+$('body').attr('class')+'</div>');
		$('#debug').append("<div id='login'>body.class="+$('body').attr('class')+'</div>');
		//$('#debug').append("<div id='width'></div>"); setInterval(function(){$('#debug>#width').append('<div>window.width='+$(window).width()+'</div>');},1000)
	}
})(jQuery);

