/**
 *	//https://john-dugan.com/jquery-plugin-boilerplate-explained/
 */
;(function($,window,document,undefined){
	"use strict";
	var pluginName = "gz_map";
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
		}; //console.log(this.options);
		this.init();
	}

	$.extend(Plugin.prototype,{
		_add_marker:function(prm){//console.log(prm);
			var prm_marker = prm.marker;
			if(typeof prm_marker.map!='object') prm_marker.map = this.data.gmap; //console.log(prm_marker);
			var marker = new google.maps.Marker(prm_marker);
			this.data.LatLngBounds.extend(marker.position);
			this.data.gmap.fitBounds(this.data.LatLngBounds);
		}
		,add_marker:function(prm){//console.log('add_marker',prm,this.options);
			var that	= prm.that;
			var marker 	= prm.marker; //console.log(marker,typeof marker.position,marker.position);
			//if(typeof prm.position=='object') console.log(typeof prm.position.lat, prm.position);
			switch(typeof marker.position){
				case 'object'://console.log(typeof marker.position,marker.position);
					if(typeof marker.position.address=='string'){
						that.geocode({that:that ,address:marker.position.address ,callback:function(latlng){
							//console.log(latlng,marker.position);
							marker.position = latlng;
							//console.log(latlng,marker.position);
							that._add_marker({marker:marker});
						}});
					}else if(typeof marker.position[0]!='undefined'){//console.log(typeof marker.position[0]);
						var lat = (typeof marker.position[0]=='string')?parseFloat(marker.position[0]):marker.position[0];
						var lng = (typeof marker.position[1]=='string')?parseFloat(marker.position[1]):marker.position[1];
						marker.position = {lat:lat,lng:lng};
						that._add_marker({marker:marker});
					}
					break;
			}
			/*
			if(typeof marker.position=='object' && typeof marker.position[0]=='string' && typeof marker.position[1]=='string'){//console.log(typeof prm.position[0], prm.position[0]);
				var lat = parseFloat(marker.position[0]);
				var lng = parseFloat(marker.position[1]);
				that._add_marker();
			}else if(typeof marker.position=='object' && typeof marker.position[0]=='number' && typeof marker.position[1]=='number'){//console.log(typeof prm.position[0], prm.position[0]);
				var lat = marker.position[0];
				var lng = marker.position[1];
				that._add_marker();
			}else if(typeof marker.position=='object' && typeof marker.position.lat=='number' && typeof marker.position.lng=='number'){//console.log(typeof prm.position[0], prm.position[0]);
				var lat = marker.position.lat;
				var lng = marker.position.lng;
				that._add_marker();
			}else if(typeof marker.address=='string') this.gz_map('geocode',{that:that ,address:marker.address ,callback:function(latlng){
				console.log(latlng);
				//var marker = new google.maps.Marker({position:latlng ,map:this.data('gmap')});
				that._add_marker();
			}}).bind(this); else console.log("add_marker: Unknown position format.")
			*/
			/*
			if(typeof lat=='number' && typeof lng=='number'){console.log(this.data);
				//var position = new google.maps.LatLng(lat,lng); console.log(position);
				//var marker = new google.maps.Marker({position:{lat:lat,lng:lng} ,map:this.data('gmap')});
				var prm_marker = marker; //console.log(prm);
				prm_marker.position = {lat:lat,lng:lng};
				prm_marker.map = this.data.gmap;
				//prm.icon = this.render_icon(prm.icon).bind(this);
				//prm.icon = render_icon(prm.icon);//.bind(this);
				var marker = new google.maps.Marker(prm_marker);
				this.data.LatLngBounds.extend(marker.position);
				this.data.gmap.fitBounds(this.data.LatLngBounds);
			}
			*/
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
			var that = this; //console.log(that);
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
