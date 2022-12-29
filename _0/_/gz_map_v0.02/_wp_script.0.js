/**
 *	//https://john-dugan.com/jquery-plugin-boilerplate-explained/
 */
;(function($,window,document,undefined){
	"use strict";
	var pluginName = "gz_map";
	var defaults = {
		LatLngBounds:new google.maps.LatLngBounds()
	};

	function Plugin(element,options){
		this.element = element;
		this._name = pluginName;
		//this._defaults = $.fn.gz_map.defaults;
		this._defaults = defaults;
		this.options = $.extend( {}, this._defaults, options );
		this.options.LatLngBounds = new google.maps.LatLngBounds(); //console.log(this.options);
		this.init();
	}

	$.extend(Plugin.prototype,{
		init:function(prm){//console.log(prm);
			//console.log(this.LatLngBounds);
			//this.data('LatLngBounds',new google.maps.LatLngBounds());
			//if(typeof prm=='undefined') prm = {center:{lat:-34.397,lng:150.644} ,zoom:8}
			if(typeof prm=='undefined') prm = {address:'Bangkok' ,zoom:8} //Set default to BKK
			if(typeof prm=='object'){
				if(typeof prm.center=='object'){//If it's latlng then let's creat map
					var gmap = new google.maps.Map(this[0],prm);
					this.data('gmap',gmap); 		//Google Map object
					this.data('map_dom',this[0]);	//Map DOM
					this.data('map_obj',this);		//Map Object (jQuery)
					//console.log(prm.callback);
					if(typeof prm.callback=='function') prm.callback({map_obj:this ,map_dom:this[0] ,gmap:gmap});
				//}else if(typeof prm.address=='string') this.gz_map('geocode',{obj:this ,address:prm.address ,callback:function(latlng){
				}else if(typeof prm.address=='string') this.geocode({obj:this ,address:prm.address ,callback:function(latlng){
					//If it's a string then let's find latlng the init
					if(typeof prm.zoom!='number') prm.zoom = 8; //console.log(this); console.log(self);
					//prm.address = undefined;
					prm.center = latlng;
					//this.init(prm);
				}});
			}
		}
		,render_icon:function(){
			if(typeof icon.type=='string'){
				switch(icon.type){
					case 'google.maps.SymbolPath':
						var icon_url = eval(icon.type + '.' + icon.name); //console.log(icon_url);
						var icon_scale = (icon.scale)?parseFloat(icon.scale):10;
						//icon = {path:icon_url, scale:icon_scale}
						icon.path = icon_url;
						icon.scale = icon_scale;
					break;
				}
			}
		}
		,get_gmap:function(){return this.data('gmap');}
		,get_map_obj:function(){return this.data('map_obj');}
		,get_map_dom:function(){return this.data('map_dom');}
		,geocode:function(prm){
			var geocoder = new google.maps.Geocoder();
			//address = prm.address;
			//callback = prm.callback;
			geocoder.geocode({'address': prm.address}, function(results, status) {//console.log(results,status); //console.log(results[0].geometry.location);
				if (status == 'OK') {
					var latlng = results[0].geometry.location; //console.log(latlng);
					//var latlng = {lat:location.lat(),lng:location.lng()}; //console.log(latlng);
					//var latlng = new google.maps.LatLng(lat:location.lat(),lng:location.lng());
					if(typeof prm.callback=='function') prm.callback(latlng);
				}
			});
		}
		,add_type_icons:function(prm){
			this.data('type_icons',prm.icons); //console.log(this.data('type_icons'));
		}
		,add_marker:function(prm){
			//console.log(prm,typeof prm.position, prm.position instanceof google.maps.LatLng);
			//if(typeof prm.position=='object') console.log(typeof prm.position.lat, prm.position);
			if(typeof prm.position=='object' && typeof prm.position[0]=='string' && typeof prm.position[1]=='string'){//console.log(typeof prm.position[0], prm.position[0]);
				var lat = parseFloat(prm.position[0]);
				var lng = parseFloat(prm.position[1]);
			}else if(typeof prm.address=='string') this.gz_map('geocode',{address:prm.address ,callback:function(latlng){
				//console.log(latlng);
				//var marker = new google.maps.Marker({position:latlng ,map:this.data('gmap')});
			}.bind(this)}); else console.log("add_marker: Unknown position format.")
		/*
			if(prm.position instanceof google.maps.LatLng){//Correct type, just use it
				var marker = new google.maps.Marker({position:prm.position ,map:this.data('gmap')});
			}else if(typeof prm.position=='array' && typeof prm.position[0]=='number' && typeof prm.position[1]=='number'){
				var position = new google.maps.LatLng(prm.position.lat,prm.position.lng);
				var marker = new google.maps.Marker({position:position ,map:this.data('gmap')});
			}else if(typeof prm.position=='object' && typeof prm.lat=='number' && typeof prm.lng=='number'){
				var position = new google.maps.LatLng(prm.position.lat,prm.position.lng);
				var marker = new google.maps.Marker({position:position ,map:this.data('gmap')});
			}else if(typeof prm.position=='array' && typeof prm.position.lat=='number' && typeof prm.position.lng=='number'){
				var position = new google.maps.LatLng(prm.position.lat,prm.position.lng);
				var marker = new google.maps.Marker({position:position ,map:this.data('gmap')});
			}else if(typeof prm.position=='array'){
				var position = new google.maps.LatLng(prm.position[0],prm.position[1]);
				var marker = new google.maps.Marker({position:position ,map:this.data('gmap')});
			*/
			if(typeof lat=='number' && typeof lng=='number'){//console.log(typeof lat, lat);
				//var position = new google.maps.LatLng(lat,lng); console.log(position);
				//var marker = new google.maps.Marker({position:{lat:lat,lng:lng} ,map:this.data('gmap')});
				var prm_marker = prm; //console.log(prm);
				prm_marker.position = {lat:lat,lng:lng};
				prm_marker.map = this.data('gmap');
				//prm.icon = this.render_icon(prm.icon).bind(this);
				prm.icon = render_icon(prm.icon);//.bind(this);
				var marker = new google.maps.Marker(prm_marker);
				this.data('LatLngBounds').extend(marker.position);
				this.data('gmap').fitBounds(this.data('LatLngBounds'));
			}
		}
		,add_markers:function(prm){
			if(typeof prm.position=='object'){
				var marker = new google.maps.Marker({position:prm.position ,map:this.data('gmap')});
				//position: {lat: 13.75, lng: 100.50}
			}else if(typeof prm.address=='string') this.gz_map('geocoding',{obj:this ,address:prm.address ,callback:function(latlng){
				prm.position = latlng;
				this.obj.gz_map('add_marker',prm);
			}});
		}
	});

	$.fn[pluginName] = function(options){console.log(options);
		return this.each( function() {
			if ( !$.data( this, "plugin_" + pluginName ) ) {
				$.data( this, "plugin_" + pluginName, new Plugin( this, options ) );
			}
		});
	}
})(jQuery,window,document);

;(function($){
	$.fn.gz_map2 = function(action,prm){//console.log('gz_map',action,prm);
		switch(action){
			case 'init':
				this.data('LatLngBounds',new google.maps.LatLngBounds());
				//if(typeof prm=='undefined') prm = {center:{lat:-34.397,lng:150.644} ,zoom:8}
				if(typeof prm=='undefined') prm = {address:'Bangkok' ,zoom:8} //Set default to BKK
				if(typeof prm=='object'){
					if(typeof prm.center=='object'){//If it's latlng then let's creat map
						var gmap = new google.maps.Map(this[0],prm);
						this.data('gmap',gmap); 		//Google Map object
						this.data('map_dom',this[0]);	//Map DOM
						this.data('map_obj',this);		//Map Object (jQuery)
						//console.log(prm.callback);
						if(typeof prm.callback=='function') prm.callback({map_obj:this ,map_dom:this[0] ,gmap:gmap});
					}else if(typeof prm.address=='string') this.gz_map('geocode',{obj:this ,address:prm.address ,callback:function(latlng){
						//If it's a string then let's find latlng the init
						if(typeof prm.zoom!='number') prm.zoom = 8; //console.log(this); console.log(self);
						//prm.address = undefined;
						prm.center = latlng;
						this.obj.gz_map('init',prm);
					}});
				}
				break;
			case 'get_gmap': return this.data('gmap');
			case 'get_map_obj': return this.data('map_obj');
			case 'get_map_dom': return this.data('map_dom');
			case 'geocode':{
				var geocoder = new google.maps.Geocoder();
				//address = prm.address;
				//callback = prm.callback;
				geocoder.geocode({'address': prm.address}, function(results, status) {//console.log(results,status); //console.log(results[0].geometry.location);
					if (status == 'OK') {
						var latlng = results[0].geometry.location; //console.log(latlng);
						//var latlng = {lat:location.lat(),lng:location.lng()}; //console.log(latlng);
						//var latlng = new google.maps.LatLng(lat:location.lat(),lng:location.lng());
						if(typeof prm.callback=='function') prm.callback(latlng);
					}
				});
			} break;
			case 'add_type_icons':{
				this.data('type_icons',prm.icons); //console.log(this.data('type_icons'));
			} break;
			case 'add_marker':
				//console.log(prm,typeof prm.position, prm.position instanceof google.maps.LatLng);
				//if(typeof prm.position=='object') console.log(typeof prm.position.lat, prm.position);
				if(typeof prm.position=='object' && typeof prm.position[0]=='string' && typeof prm.position[1]=='string'){//console.log(typeof prm.position[0], prm.position[0]);
					var lat = parseFloat(prm.position[0]);
					var lng = parseFloat(prm.position[1]);
				}else if(typeof prm.address=='string') this.gz_map('geocode',{address:prm.address ,callback:function(latlng){
					//console.log(latlng);
					//var marker = new google.maps.Marker({position:latlng ,map:this.data('gmap')});
				}.bind(this)}); else console.log("add_marker: Unknown position format.")
			/*
				if(prm.position instanceof google.maps.LatLng){//Correct type, just use it
					var marker = new google.maps.Marker({position:prm.position ,map:this.data('gmap')});
				}else if(typeof prm.position=='array' && typeof prm.position[0]=='number' && typeof prm.position[1]=='number'){
					var position = new google.maps.LatLng(prm.position.lat,prm.position.lng);
					var marker = new google.maps.Marker({position:position ,map:this.data('gmap')});
				}else if(typeof prm.position=='object' && typeof prm.lat=='number' && typeof prm.lng=='number'){
					var position = new google.maps.LatLng(prm.position.lat,prm.position.lng);
					var marker = new google.maps.Marker({position:position ,map:this.data('gmap')});
				}else if(typeof prm.position=='array' && typeof prm.position.lat=='number' && typeof prm.position.lng=='number'){
					var position = new google.maps.LatLng(prm.position.lat,prm.position.lng);
					var marker = new google.maps.Marker({position:position ,map:this.data('gmap')});
				}else if(typeof prm.position=='array'){
					var position = new google.maps.LatLng(prm.position[0],prm.position[1]);
					var marker = new google.maps.Marker({position:position ,map:this.data('gmap')});
				*/
				if(typeof lat=='number' && typeof lng=='number'){//console.log(typeof lat, lat);
					//var position = new google.maps.LatLng(lat,lng); console.log(position);
					//var marker = new google.maps.Marker({position:{lat:lat,lng:lng} ,map:this.data('gmap')});
					var prm_marker = prm; //console.log(prm);
					prm_marker.position = {lat:lat,lng:lng};
					prm_marker.map = this.data('gmap');
					//prm.icon = this.render_icon(prm.icon).bind(this);
					prm.icon = render_icon(prm.icon);//.bind(this);
					var marker = new google.maps.Marker(prm_marker);
					this.data('LatLngBounds').extend(marker.position);
					this.data('gmap').fitBounds(this.data('LatLngBounds'));
				}
				break;
			case 'add_markers':
				if(typeof prm.position=='object'){
					var marker = new google.maps.Marker({position:prm.position ,map:this.data('gmap')});
					//position: {lat: 13.75, lng: 100.50}
				}else if(typeof prm.address=='string') this.gz_map('geocoding',{obj:this ,address:prm.address ,callback:function(latlng){
					prm.position = latlng;
					this.obj.gz_map('add_marker',prm);
				}});
				break;
		}

		return this;
	} //console.log($.fn.gz_map);

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
			var map_init = eval('('+map_init_st+')'); //map_init.options = {}; console.log(map_init);
			map_init.callback = function(obj){
				gz_map_init_marker(obj);
			}
			//var $gz_map = $map_dom.gz_map('init',map_init);
			var $gz_map = $map_dom.gz_map(map_init);
		});
	}

	function gz_map_init_marker(obj){//console.log(obj);
		//Test init markers
		var $gz_markers = obj.map_obj.gz_map('add_marker',{address:'Bangkok'});

		var markers_prm = obj.map_obj.attr('data-markers'); console.log(markers_prm);
		if(markers_prm){//console.log(map_poi_st,'gz_location',st_location);
			var args = markers_prm.split(','); //console.log(args);
			var m_obj_id = args[0];
			var m_obj = eval(m_obj_id); //console.log(obj);
			var act = args[1]; //console.log(obj[act]);
			var url = m_obj[act]; //console.log(m_obj,act,url);
			$.ajax({url:url ,data:{action:act ,args:args}
			,success:function(rs){//console.log('rs',rs);
				if(rs=='') return;
				var markers_st = rs;
				var markers = eval('('+markers_st+')'); //console.log(markers);
				//obj.map_obj.gz_map('add_markers',{markers:markers});
				$(markers).each(function(idx,marker){obj.map_obj.gz_map('add_marker',marker);}); //console.log(marker);})
			}})
		}
	}

	function gz_map_init_0(){//console.log('xxx');
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
				,success:function(rs){console.log('rs',rs);
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

