;(function($){
	$('document').ready(function(){ //console.log($.fancybox);
		gz_map_init();
	});
	
	/**
	 * Directly using Google Map API V3 instead of GMAP3 library
	 */
	function gz_map_init(){
		var $map_doms = $('.gz_map'); if($map_doms.length<1) {console.log('No .gz_map found'); return;}
		$map_doms.each(function(idx,map_dom){//console.log(map_dom);
			var $map_dom = $(map_dom);
			//Init map
			var map_init_st = $map_dom.attr('data-init'); //console.log(map_init_st);
			var map_init;
			if(typeof map_init_st=='undefined') map_init = {};
			else map_init = eval('('+map_init_st+')'); //console.log(map_init);
			map_init.callback = function(obj){//console.log(obj.that.data);
				//gz_map_init_marker(obj);
				gz_map_init_marker(obj,$map_dom);
			}
			//var $gz_map = $map_dom.gz_map('init',map_init);
			var $gz_map = $map_dom.gz_map(map_init);
		});
	}

	function gz_map_init_marker(obj,$map){//console.log(obj,$map,$map.data.$obj);
		var test_icon = [
			{
				path: "M-20,0a20,20 0 1,0 40,0a20,20 0 1,0 -40,0",
				fillColor: '#FF0000',
				fillOpacity: .6,
				anchor: new google.maps.Point(0,0),
				strokeWeight: 0,
				scale: 1
			}
			,{
				path: "M-20,0a20,20 0 1,0 40,0a20,20 0 1,0 -40,0",
				fillColor: '#0000FF',
				fillOpacity: .6,
				anchor: new google.maps.Point(0,0),
				strokeWeight: 0,
				scale: .5
			}
			,{
				path: "l -11 22 H 22 l -11 -22 Z",
				fillColor: '#0000FF',
				fillOpacity: .6,
				anchor: new google.maps.Point(0,0),
				strokeWeight: 1,
				scale: .5
			}
		];
		
		var that = obj.that; //console.log(that,that.data.gmap);
		//Test init markers
		//var $gz_markers = obj.map_obj.gz_map('add_marker',{address:'Bangkok'});
		//var $gz_markers = obj.data.gmap.add_marker({position:{address:'Bangkok'}});
		//var $gz_markers = this.add_marker({position:{address:'Bangkok'}});
		//var marker = {that:that , marker:{position:{address:'Bangkok'}}};
		//var $gz_marker = that.add_marker(marker); //console.log($gz_marker);

		//var markers_prm = that.data.$obj.attr('data-markers'); //console.log(markers_prm);
		var markers_prm = $map.attr('data-markers'); //console.log(markers_prm);
		if(markers_prm){//console.log(map_poi_st,'gz_location',st_location);
			var args = markers_prm.split(','); //console.log(args);
			var m_obj_id = args[0];
			var m_obj = eval(m_obj_id); //console.log(obj);
			var act = args[1]; //console.log(obj[act]);
			var url = m_obj[act]; //console.log(m_obj,act,url);
			var i=0;
			$.ajax({url:url ,data:{action:act ,args:args}
			,success:function(rs){//console.log('rs',rs);
				if(rs=='') return;
				var markers_st = rs;
				var markers = eval('('+markers_st+')'); //console.log(that);
				//obj.map_obj.gz_map('add_markers',{markers:markers});
				$(markers).each(function(idx,marker){//console.log(marker.icon);
					marker.icon = test_icon[i++];
					if(i>=test_icon.length) i=0;
					that.add_marker({that:that ,marker:marker});
				});
			}})
		}
	}
	
})(jQuery);

