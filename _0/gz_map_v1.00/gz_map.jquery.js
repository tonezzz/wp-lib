/**
 * https://learn.jquery.com/plugins/basic-plugin-creation/
 */
;(function($,window,document,undefined){
	$.fn.gz_map = function(options){
		var defaults = {
			init_map:{
				center:{lat:13.8388272,lng:100.7163296}
				//center:'Bangkok'
				,zoom:8
			}
		};

		return this.each(function(){
			/**
			 * data = 
			 */
			this.clear_marker = function(){

			}

			this.add_marker = function(data){ //console.log(typeof data);
				var that = this;
				switch(typeof data.position){
					case 'string':return this.geocode(data.position,function(latlng){data.position=latlng; that.add_marker(data);});
					case 'object':
						data.map = this.options.gmap;
						var marker = new google.maps.Marker(data); //console.log(data.label,data.position);
						this.options.LatLngBounds.extend(marker.position);
						this.options.gmap.fitBounds(this.options.LatLngBounds);
						this.markers.push(marker);
						return marker;
					default: console.log(typeof data.position);
				}
			}
			/**
			 * url must return JSON
			 */
			this.add_markers_from_url = function(url){
				
			}
			
			/**
			 * data = string of address
			 */
			this.geocode = function(data,callback){//console.log(data,callback);
				var geocoder = new google.maps.Geocoder();
				geocoder.geocode({'address': data}, function(results, status) {console.log(data,results,status); //console.log(results[0].geometry.location);
					if (status == 'OK') {
						var latlng = results[0].geometry.location; //console.log('geocode',latlng);
						callback(latlng);
					}
				});
			}

		this.markers = [];
		this.options = $.extend( {}, defaults, options);
		this.options.gmap = new google.maps.Map(this,this.options.init_map);
		this.options.LatLngBounds = new google.maps.LatLngBounds();

		$(this).trigger('gz_map_after_init');
			//$obj.addClass('test');
		});


	}

	//$.fn.gz_map.add_marker = function(){ console.log('add_marker');}
}(jQuery));

/**
 *	//https://john-dugan.com/jquery-plugin-boilerplate-explained/
 */
;(function($,window,document,undefined){
	"use strict";
	var pluginName = "gz_mapx";
	var defaults = {
		init_map:{
			//center:{lat:13.8388272,lng:100.7163296}
			center:'Bangkok'
			,zoom:8
		}
	};

	function Plugin(element,options){//console.log(element,options);
		this.element = element;
		this._name = pluginName;
		//this._defaults = $.fn.gz_map.defaults;
		this._defaults = defaults;
		this.options = $.extend( {}, this._defaults, options);
		this.data = {
			LatLngBounds: new google.maps.LatLngBounds()
			,markers:[]
		}; //console.log(this.options);
		this.init();
	}

	$.extend(Plugin.prototype,{
		fitbound:function(position){//console.log(position);
			//prm.marker.each(function(idx,item){console.log(idx,item);
			//});
			//this.data.gmap.fitBounds(prm.position);
			this.data.LatLngBounds.extend(position);
			this.data.gmap.fitBounds(this.data.LatLngBounds);
		}
		,_add_marker:function(prm){//console.log(prm);
			var prm_marker = prm.marker;
			if(typeof prm_marker.map!='object') prm_marker.map = this.data.gmap; //console.log(prm_marker);
			var marker = new google.maps.Marker(prm_marker);
			this.data.markers.push(marker); //console.log(this.data.markers);
			this.data.LatLngBounds.extend(marker.position);
			this.data.gmap.fitBounds(this.data.LatLngBounds);
			//Call back with parameter
			if(typeof prm.callback=='string') eval(prm.callback + "(marker)");
			else if(typeof prm.callback=='function') prm.callback(marker);
		}
		,add_marker_2:function(prm){
			var $map_obj = prm.$map_obj;
			var marker 	= prm.marker; //console.log(marker,typeof marker.position,marker.position);
			//if(typeof prm.position=='object') console.log(typeof prm.position.lat, prm.position);
			//Preparing marker (using position or location)
			var position = false;
			if(typeof marker.position!='undefined') position = marker.position;
			else if(typeof marker.location!='undefined') position = marker.location;
			switch(typeof position){
				case 'object'://console.log(typeof marker.position,marker.position);
					if(typeof position.address=='string'){
						$map_obj.geocode({that:$map_obj ,address:position.address ,callback:function(latlng){
							//console.log(latlng,marker.position);
							position = latlng;
							//console.log(latlng,marker.position);
							$map_obj._add_marker({marker:marker,callback:prm.poi_init});
						}});
					}else if(typeof position[0]!='undefined'){//console.log(typeof marker.position[0]);
						var lat = (typeof position[0]=='string')?parseFloat(position[0]):position[0];
						var lng = (typeof position[1]=='string')?parseFloat(position[1]):position[1];
						marker.position = {lat:lat,lng:lng}; //Put it back
						$map_obj._add_marker({marker:marker,callback:prm.poi_init});
					}
					break;
				
			}
		}
		/**
		 * prm.marker	marker to add
		 * prm.that		$gz_map object
		 * prm.callback	Callback function
		 */
		,add_marker:function(prm){ //console.log(prm.marker);
			var that	= prm.that;
			var marker 	= prm.marker; //console.log(marker);
			var lat,lng;
			if(typeof marker.position=='undefined' && typeof marker.location!='undefined') marker.position = marker.location;
			//Use it
			switch(typeof marker.position){
				case 'object'://console.log(typeof marker.position,marker.position);
					if(typeof marker.position.address=='string'){
						//If address is used
						that.geocode({that:that ,address:marker.position.address ,callback:function(latlng){
							//console.log(latlng,marker.position);
							marker.position = latlng;
							//console.log(latlng,marker.position);
							that._add_marker({marker:marker,callback:prm.callback});
						}});
					}else if(typeof marker.position.lat=='function'){
						that._add_marker({marker:marker,callback:prm.callback});
					}else{
						if(typeof marker.position[0]!='undefined'){lat=marker.position[0]; lng=marker.position[1];}
						else if(typeof marker.position['lat']!='undefined'){lat=marker.position['lat']; lng=marker.position['lng'];}
						else if(typeof marker.position.lat!='undefined'){lat=marker.position.lat; lng=marker.position.lng;}
						//console.log(lat,lng)
						if(typeof lat=='string') lat = parseFloat(lat);
						if(typeof lng=='string') lng = parseFloat(lng);
						if(typeof lat=='number' && typeof lng=='number'){
							marker.position = {lat:lat,lng:lng}; //Put it back
							that._add_marker({marker:marker,callback:prm.callback});
						}
					}
				break;				
			}
		}
		,add_markers:function(prm){//console.log(prm);
			//var _debug = (prm._debug)?true:false;
			//if(prm.reset_markers) this.clear_markers(); //Clear from outside instead
			jQuery.each(prm.pois,function(idx,poi){//console.log(poi);
				prm.that.add_marker({that:prm.that ,marker:poi ,callback:prm.callback});
			}); //console.log(this.data.markers)
		}
		,clear_markers:function(){//console.log(this.data.markers);
			jQuery.each(this.data.markers,function(idx,item){ //console.log(idx,item);
				item.setMap(null);
			}); this.data.markers = []; //console.log(this.data.markers);
			this.data.LatLngBounds = null;
			this.data.LatLngBounds = new google.maps.LatLngBounds();
		}
		,geocode:function(prm){//console.log('geocode',prm);
			var geocoder = new google.maps.Geocoder();
			//address = prm.address;
			//callback = prm.callback;
			geocoder.geocode({'address': prm.address}, function(results, status) {//console.log(results,status); //console.log(results[0].geometry.location);
				if (status == 'OK') {
					var latlng = results[0].geometry.location; //console.log('geocode',latlng);
					//var latlng = {lat:location.lat(),lng:location.lng()}; //console.log(latlng);
					//var latlng = new google.maps.LatLng(lat:location.lat(),lng:location.lng());
					if(typeof prm.callback=='function'){//console.log('geocode callback',latlng);
						prm.callback(latlng);
					}
				}
			});
		}
		/**
		 * _init(): Real init with all parameters prepared.
		 */
		,_init:function(){//console.log(this,this.options.init_map);
			this.data.gmap = new google.maps.Map(this.element,this.options.init_map);
			this.data.$obj = this;
			if(typeof this.options.callback=='function'){//console.log('init callback',this,this.data);
				//this.add_marker();
				this.options.callback({that:this});
			}
		}
		/**
		 * init: use parameters from Options //zoom
		 */
		,init:function(){//console.log('init_start',this)
			var that = this; jQuery(that.element).data('gz_map',that); //console.log(that);
			if(typeof this.options.init_map.center=='string') this.geocode({address:this.options.init_map.center ,callback:function(prm){//console.log(prm);
				that.options.init_map.center = prm.latlng; //console.log(that.options.init_map);
				that._init();
			}}); else this._init();
			//console.log(this.data);
			//console.log('init end');
			//var gmap = new google.maps.Map(this[0],prm);
			/*
			this.data.map_dom = this[0];
			this.data.map_obj = this;
			console.log(prm); console.log(this.data);
			if(typeof prm.callback=='function') prm.callback({map_obj:this ,map_dom:this[0] ,gmap:gmap});
			*/
			/*
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
			*/
		}
		/*
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
		,add_type_icons:function(prm){
			this.data('type_icons',prm.icons); //console.log(this.data('type_icons'));
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
		*/
	});

	$.fn[pluginName] = function(options){//console.log(options);
		return this.each( function() {
			if ( !$.data( this, "plugin_" + pluginName ) ) {
				$.data( this, "plugin_" + pluginName, new Plugin( this, options ) );
			}
		});
	}
})(jQuery,window,document);
