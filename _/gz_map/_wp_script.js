(function($){
	$('document').ready(function(){ //console.log($.fancybox);
		gz_map_init();
	});
	
	function gz_map_init(){//console.log('xxx');
		var $map_doms = $('.gz_map');
		//console.log(JSON.encode);
		//var map_init = JSON.parse("{'center':[48.8620722, 2.352047],'zoom':16}"); //console.log(map_init);
		$map_doms.each(function(idx,map_dom){//console.log(idx,map_dom,$(map_dom));
			var $map_dom = $(map_dom);
			var map_init_st = $map_dom.attr('data-init'); //console.log(map_init_st);
			var map_init = eval('('+map_init_st+')'); //console.log(map_init);
			var map_markers_st = $map_dom.attr('data-markers'); //console.log(map_markers_st);
			var map_markers = eval('('+map_markers_st+')'); //console.log(map_markers);
			var $map_obj = $map_dom.gmap3(map_init)
			.marker(map_markers);
			//.marker([{position:[13.8388272,100.7163296], icon:{url:'/wp/wp-content/themes/treebox/img/map.png'}}]);
			//console.log($map_obj);
		});
		//var $map_obj = $map_dom.gmap3(map_init);
			//center:[48.8620722, 2.352047]
		//	address:'Rayong'
		//	,zoom:16
		//});
		//*/
	}
	
})(jQuery);

