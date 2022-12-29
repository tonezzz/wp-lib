/*
parent = ajax load data(k,v) into array.
child = load values from parent array.
clear_date import
refresh parent
*/
(function($){
	$('document').ready(function(){
		var $panel = $('.gz_stock_2_panel').gz_panel();

		//test();
		test_2();
	});
	
	function test_2()
	{
		console.log( $('.gz_stock_2_panel').data('gz_panel') );
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

