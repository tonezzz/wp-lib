/*
*/
(function($){
	var Map = false;
	
	$('document').ready(function(){
		init_gz_map();
	});
	
	function init_gz_map(){
		if($('#map_windsurf').length==0) return;

		Map = {opt:{} ,layers:{}};
		
		var map_init = {center:[13.736717,100.523186] ,zoom:3 ,zoomControl:false}
		Map.$ = $('#map_windsurf');
		Map.map = L.map('map_windsurf',map_init);
		init_layers(Map);
		init_poi_cat(Map);
	}
	
	function init_poi_cat(Map){
		var poi_cat = Map.$.attr('data-poi_cat');
		if(poi_cat) {load_poi_cat(Map,poi_cat,function(){set_bound(Map,poi_cat);});}
	}
	
	function set_bound(Map,layer){
		var bounds = Map.layers[layer].getBounds(); //console.log(bounds);
		Map.map.flyToBounds(bounds);
	}
	
	function init_layers(Map){
		Map.layers.street 		= L.tileLayer('https://{s}.google.com/vt/lyrs=m&x={x}&y={y}&z={z}',{maxZoom: 20,subdomains:['mt0','mt1','mt2','mt3']});
		Map.layers.hybrid 		= L.tileLayer('https://{s}.google.com/vt/lyrs=s,h&x={x}&y={y}&z={z}',{maxZoom: 20, subdomains:['mt0','mt1','mt2','mt3']});
		Map.layers.satellite 	= L.tileLayer('https://{s}.google.com/vt/lyrs=s&x={x}&y={y}&z={z}',{maxZoom: 20,subdomains:['mt0','mt1','mt2','mt3']});
		Map.layers.terrain 		= L.tileLayer('https://{s}.google.com/vt/lyrs=p&x={x}&y={y}&z={z}',{maxZoom: 20,subdomains:['mt0','mt1','mt2','mt3']});
		Map.layers.street.addTo(Map.map);		
	}

	/*
	 * layer=null: Create a new layer name "poi_cat"
	 */
	function load_poi_cat(Map,poi_cat,callback=false){
		var layer;
		if(!(poi_cat in Map.layers)) {layer = Map.layers[poi_cat] = new L.featureGroup(); layer.addTo(Map.map);} else layer = Map.layers[poi_cat];
		//test_add_marker(Map,layer);
	
        jQuery.ajax(gz_map.ajax ,{dataType:'json' ,data:{action:'get_pois' ,'cat':poi_cat}
            ,success:function(data){
                jQuery.each(data,function(idx,item){
					add_poi(layer,item);
                });
				if(callback) callback();
            }
        });
	}

	function add_poi(layer,item){
		var marker = new L.marker([item.lat,item.lng] ,{title:item.title}).addTo(layer); //Testing
	}
	
	/*Add test markers to a layer*/
	function test_add_marker(Map,layer){
		var marker = new L.marker([0,0]).addTo(layer);
		console.log(marker,layer,Map);
	}

	/*
    add_marker(item,layer){
        var loading = '<div class="animate__animated animate__flip">Loading ...</div>';
		var icon = L.divIcon({className:'gz_poi_icon_wrap' ,html:item.data.div_icon});
        var $marker = L.marker([item.lat,item.lng] ,{icon:icon ,iconAnchor:[54,50]});
		var that = this;
        $marker.data = item.data;
        $marker.addTo(layer);
		var that = this;
        $marker.on('click',function(e){that.popup_marker(that,this);}); //if(item.data.id==8622) $marker.click();
		L.marker([item.lat,item.lng] ,{icon:this.icons.poi}).addTo(layer); //Testing
    }
	*/

})(jQuery);

