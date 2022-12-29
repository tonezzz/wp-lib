/*
parent = ajax load data(k,v) into array.
child = load values from parent array.
clear_date import
refresh parent
*/
(function($){
	$('document').ready(function(){
		var $panel = $('.gz_stock_panel').gz_panel({ajax_url: gz_stock.ajax_url
			//,debug: {init: 1 ,my: 1 ,prepare: 1 ,reload: 1 ,load: 1 ,ajax: 1 ,reload_panel: 0 ,refresh: 1 ,get_store: 1 ,refresh_text: 1}
		});

		//test();
		test_2();
	});
	
	function test_2()
	{
		console.log( $('.gz_stock_panel').data('gz_panel') );
		//console.log( $('.gz_stock_2_panel').data('gz_panel').refresh() );
	}
	
	function test(){
		$.widget( "nmk.progressbar", {
			_create: function() {
				var progress = this.options.value + "%";
				this.element.addClass( "progressbar" ).text( progress );
			}
 
		});
		$( "<div />" ).appendTo( "body" ).progressbar({ value: 20 });
	}
})(jQuery);

