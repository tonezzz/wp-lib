(function($){
	$('document').ready(function(){ //console.log($.fancybox);
		gz_map_init();
	});
	
	function gz_map_init(){//console.log('xxx');
		var $map_doms = $('.gz_map'); if($map_doms.length<1) return;
		//console.log(JSON.encode);
		//var map_init = JSON.parse("{'center':[48.8620722, 2.352047],'zoom':16}"); //console.log(map_init);
		$map_doms.each(function(idx,map_dom){//console.log(idx,map_dom,$(map_dom));
			var $map_dom = $(map_dom);
			var map_init_st = $map_dom.attr('data-init'); //console.log(map_init_st);
			var map_init = eval('('+map_init_st+')'); //map_init.options = {}; console.log(map_init);

			//var map_icon = $map_dom.attr('data-icon'); //console.log(map_icon_st);
			//if(map_icon) map_init.options.icon = map_icon; console.log(map_init.options);
			
			var $map_obj = $map_dom.gmap3(map_init);
			//console.log($map_obj); console.log($map_obj.marker);

			//var map_icon = $map_dom.attr('data-icon'); //console.log(map_icon_st);
			var marker_options = {}; if(typeof map_icon!='undefined') marker_options.icon = map_icon;

			var map_markers_st = $map_dom.attr('data-markers'); //console.log(map_markers_st);
			if(map_markers_st){
				var map_markers = eval('('+map_markers_st+')'); //console.log('map_markers',map_markers);
				//map_markers.options = marker_options;
				$map_obj.marker(map_markers).fit();
			}
			var map_poi_st =  $map_dom.attr('data-poi'); //console.log(map_poi_st);
			if(typeof gz_location=='undefined') return;
			if(map_poi_st){//console.log(map_poi_st,'gz_location',st_location);
				var args = map_poi_st.split(','); //console.log(args);
				var obj_id = args[0];
				var obj = eval(obj_id); //console.log(obj);
				var act = args[1]; //console.log(obj[act]);
				var url = obj[act];
				$.ajax({url:url ,data:{action:act ,args:args}
				,success:function(rs){//console.log('rs',rs);
					if(rs=='') return;
					var pois_st = rs;
					var pois = eval('('+pois_st+')'); //console.log('pois',pois);
					$map_obj.marker(pois).fit();
				}});
			}
		});
		//var $map_obj = $map_dom.gmap3(map_init);
			//center:[48.8620722, 2.352047]
		//	address:'Rayong'
		//	,zoom:16
		//});
		//*/
	}
	
})(jQuery);

