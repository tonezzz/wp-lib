;(function($){
	$('document').ready(function(){ //console.log($.fancybox);
		gz_map_init();
	});
	
	/**
	 * Directly using Google Map API V3 instead of GMAP3 library
	 */
	function gz_map_init(){//console.log('gz_map_init');
		var $map_doms = $('.gz_map'); if($map_doms.length<1) return; //{console.log('No .gz_map found'); return;}
		$map_doms.each(function(idx,map_dom){//console.log(map_dom);
			var $map_dom = $(map_dom);
			//Init map
			var map_init; var map_init_st = $map_dom.attr('data-init');
			if(typeof map_init_st=='undefined') map_init = {};
			else map_init = eval('('+map_init_st+')'); //console.log(map_init);
			map_init.callback = function(obj){//console.log(obj.that.data);
				obj.$map_dom = $map_dom;
				gz_map_init_2(obj);
				//obj.$map_obj
				//var init_script = $map_dom.attr('data-init-script'); //console.log(init_script);
				//eval(init_script);
				var gz_map_init = $map_dom.attr('data-map_init'); if(gz_map_init) eval(gz_map_init+'($map_dom)');
			}
			//var $gz_map = $map_dom.gz_map('init',map_init);
			//var dom_init = $map_dom.attr('data-dom_init'); if(dom_init) {var js = dom_init+'($map_dom)'; eval(js);}
			$map_dom.children('.map').gz_map(map_init);
		});
	}

	function gz_map_init_2(obj){
		var map_type = obj.$map_dom.attr('data-map_type'); //console.log('xx',obj.$map_dom,map_type);
		var poi_init = obj.$map_dom.attr('data-poi-init');
		switch(map_type){
			case 'heatmap':gz_map_init_heatmap(obj,poi_init); break;
			default:gz_map_init_poi(obj,poi_init);
		}
	}

	function gz_map_init_heatmap(obj,poi_init=false){//console.log(obj.that.data.gmap);
		//var that = obj.that; //console.log(that,that.data.gmap);
		var url_prm = new URLSearchParams(window.location.search); //console.log(url_prm);
		var area = url_prm.get('area'); //console.log(area);
		var pois_prm = obj.$map_dom.attr('data-markers'); //console.log(pois_prm);
		if(pois_prm){//console.log(map_poi_st,'gz_location',st_location);
			var args = pois_prm.split(','); //console.log(args);
			var m_obj_id = args[0];
			var m_obj = eval(m_obj_id); //console.log(obj);
			var act = args[1]; //console.log(obj[act]);
			var url = m_obj[act]; //console.log(m_obj,act,url);
			var i=0;
			$.ajax({url:url ,data:{action:act ,args:args ,area:area}
			,success:function(rs){//console.log('rs',rs);
				if(rs=='') return;
				var pois_st = rs;
				var pois = eval('('+pois_st+')'); //console.log(that);
				var locs = [];
				$(pois.data).each(function(idx,poi){ //console.log(poi.data.DO);
					var loc = {};
					loc.location = new google.maps.LatLng(poi.position[0],poi.position[1]); //console.log(loc.location.lat(),loc.location.lng());
					loc.weight = parseFloat(poi.data.DO);
					locs.push(loc); //console.log(loc.weight);
					obj.that.fitbound(loc.location); 
				}); //console.log(obj.that);
				//google.load("visualization", "1", {packages: ["heatmap"]});
				//google.setOnLoadCallback(function(){
					var heatmap = new google.maps.visualization.HeatmapLayer({
						data: locs
						,radius:60
						,dissipating:true
					});
					heatmap.setMap(obj.that.data.gmap);
					if(poi_init) poi_init();
				//});
			}})
		}
	}


	function gz_map_init_poi(obj,poi_init=false){//console.log(obj,poi_init);
		var url_prm = new URLSearchParams(window.location.search); //console.log(url_prm);
		var area = url_prm.get('area'); //console.log(area);
		$map = obj.$map_dom;
		var that = obj.that; //console.log(that,that.data.gmap);
		var pois_prm = $map.attr('data-markers'); //console.log(markers_prm);
		if(pois_prm){//console.log(map_poi_st,'gz_location',st_location);
			var args = pois_prm.split(','); //console.log(args);
			var m_obj_id = args[0];
			var m_obj = eval(m_obj_id); //console.log(obj);
			var act = args[1]; //console.log(obj[act]);
			var url = m_obj[act]; //console.log(m_obj,act,url);
			var i=0;
			$.ajax({url:url ,data:{action:act ,args:args ,area:area}
			,success:function(rs){//console.log('rs',rs);
				if(rs=='') return;
				var pois_st = rs;
				var pois = eval('('+pois_st+')'); //console.log(pois);
				//Adding pois at once
				that.add_markers({pois:pois.data, that:that, callback:poi_init});
			}})
		}
	}
	/*
	function gz_map_init_marker(obj){//console.log(obj,$map,$map.data.$obj);
		$map = obj.$map_dom;
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
		//Test init pois
		//var $gz_pois = obj.map_obj.gz_map('add_marker',{address:'Bangkok'});
		//var $gz_pois = obj.data.gmap.add_marker({position:{address:'Bangkok'}});
		//var $gz_markers = this.add_marker({position:{address:'Bangkok'}});
		//var marker = {that:that , marker:{position:{address:'Bangkok'}}};
		//var $gz_marker = that.add_marker(marker); //console.log($gz_marker);

		//var markers_prm = that.data.$obj.attr('data-markers'); //console.log(markers_prm);
		var pois_prm = $map.attr('data-markers'); //console.log(markers_prm);
		if(pois_prm){//console.log(map_poi_st,'gz_location',st_location);
			var args = pois_prm.split(','); //console.log(args);
			var m_obj_id = args[0];
			var m_obj = eval(m_obj_id); //console.log(obj);
			var act = args[1]; //console.log(obj[act]);
			var url = m_obj[act]; //console.log(m_obj,act,url);
			var i=0;
			$.ajax({url:url ,data:{action:act ,args:args}
			,success:function(rs){//console.log('rs',rs);
				if(rs=='') return;
				var pois_st = rs;
				var pois = eval('('+pois_st+')'); //console.log(that);
				//obj.map_obj.gz_map('add_markers',{markers:markers});
				$(pois.data).each(function(idx,poi){//console.log(poi);
					//marker.icon = test_icon[i++];
					//if(i>=test_icon.length) i=0;
					that.add_marker({that:that ,marker:poi});
				});
			}})
		}
	}
	*/
	
})(jQuery);

