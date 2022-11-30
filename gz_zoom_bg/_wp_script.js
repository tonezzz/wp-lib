(function($){ //data
	$('document').ready(function(){ //console.log($.fancybox);
		gz_zoom_bg_init();
	});
	
	function gz_zoom_bg_scale($obj,e){console.log(e.originalEvent.deltaY);
		//var bd = e.target.getBoundingClientRect(); //Position of the container related to the page. //console.log(rect); console.log(e.clientX, e.clientY);
		//var px = (e.clientX-bd.x)/bd.width, py = (e.clientY-bd.y)/bd.height; //Calculate position (0..1) //console.log(px,py);
		var sx = 2, sy = 2; //Calculation transform position
		var sc = $obj.data('scale');
		if(e.originalEvent.deltaY<0) {sc.x*=1.1; sc.y*=1.1;} else {sc.x*=.9; sc.y*=.9;}
		$obj.data('scale',sc);
		gz_zoom_bg_transform($obj);
	}
	
	function gz_zoom_bg_translate($obj,e){//console.log($obj,$obj[0]);
		var bd = e.target.getBoundingClientRect(); //Position of the container related to the page. //console.log(rect); console.log(e.clientX, e.clientY);
		var px = (e.clientX-bd.x)/bd.width, py = (e.clientY-bd.y)/bd.height; //Calculate position (0..1) //console.log(px,py);
		var tx = 0.5-px, ty = 0.5-py; //Calculation transform position
		$obj.data('translate',{x:tx,y:ty});
		gz_zoom_bg_transform($obj);
	}
	
	function gz_zoom_bg_transform($obj){
		var ts = $obj.data('translate');
		var sc = $obj.data('scale');
		$obj[0].style.transform = "translate(" + sc.x*ts.x*100 + "%," + sc.y*ts.y*100 + "%) scale(" + sc.x*100 + "%," + sc.y*100 + "%)";
	}
	
	function log(obj){
		var $log_obj = $('.log_obj');
		if($log_obj.length==0){
			$('body').append("<div class='log_obj'></div>");
			$log_obj = $('.log_obj').css({width:'300px' ,height:'auto' ,position:'absolute' ,left:0 ,top:0 ,zIndex:1});
		}
		$log_obj.html($log_obj.html()+'<br/>'+obj);
		//console.log($log_obj);
	}
	
	function gz_zoom_bg_init(){//log('xx');
		var $items = $('.gz_zoom_bg'); //console.log($obj);
		$items.each(function(){
			//Mouse control initialization
			//Use mouse events on the $mask to control the container's zoom and position
			var $obj = $(this); //console.log($obj);
			$obj.data('translate',{x:.5,y:.5}); //console.log($obj.data('translate'));
			$obj.data('scale',{x:1,y:1}); //console.log($obj.data('scale'));
			var touch_dist_0 = touch_dist = false;
			var mask_sel = $obj.attr('data-mask'); var $mask = $(mask_sel).addClass('zoomable'); //console.log($mask);
			//console.log($mask.offset());
			$mask.on({
				mousemove:function(e){//console.log(e.clientX-$obj.offsetLeft ,e.clientY-$obj.offsetTop);
					e.preventDefault();
					gz_zoom_bg_translate($obj,e);
				}
				//Wheel scroll to zoom in/out
				,wheel:function(e){ //console.log(e.originalEvent.deltaY, e.originalEvent);
					e.preventDefault();
					gz_zoom_bg_scale($obj,e);
				}
				/*
				,click:function(e){ //console.log(e.clientX,e.clientY);
					//e.preventDefault();
					//gz_zoom_bg_scale($obj,e);
				}
				,doubletap:function(e){
					e.preventDefault();
					gz_zoom_bg_moveto($obj,$obj.width()/2,$obj.height()/2);
					gz_zoom_bg_do_zoom({obj:$obj ,abs_zoom:1}); //log(zoom_factor);
				}
				,dblclick:function(e){//log('dblclick');
					e.preventDefault();
					gz_zoom_bg_moveto($obj,$obj.width()/2,$obj.height()/2);
					gz_zoom_bg_do_zoom({obj:$obj ,abs_zoom:1}); //log(zoom_factor);
				}
				//,mouseleave:function(e){//console.log('mouseleave'); //When mouse out of container, put it back in the center.
				//	e.preventDefault();
				//	$obj.css({backgroundPosition:'center'});
				//}
				,touchstart:function(e){//log('touchstart');
					e.preventDefault();
					var touches = e.originalEvent.touches; //log();
					if((typeof touches[0]!='undefined')&&(typeof touches[1]!='undefined')){
						//2 touches let's record the distance
						//log(touches[0].pageX); log(touches[1].pageX); log(touches[0].pageY); log(touches[1].pageY);
						//touch_dist_0 = sqrt(pow(touches[0].pageX-touches[1].pageX,2)+pow(touches[0].pageY-touches[1].pageY,2));
						touch_dist_0 = Math.hypot(touches[0].pageX-touches[1].pageX ,touches[0].pageY-touches[1].pageY);
						//log(touch_dist_0);
					}else touch_dist_0 = false;
				}
				,touchmove:function(e){//log(touch_dist_0);
					e.preventDefault();
					var touches = e.originalEvent.touches; //console.log(touches[0]);
					if(typeof touches[1]=='undefined'){
						//One touch only move it
						var px = touches[0].pageX;
						var py = touches[0].pageY;
						gz_zoom_bg_moveto($obj,px,py);
					}else if (touch_dist_0!==false){
						//2 touches, let's move & zoom/pinch
						var px = touches[0].pageX+touches[1].pageX;
						var py = touches[0].pageY+touches[1].pageY;
						//gz_zoom_bg_moveto($obj,px,py);
						//touch_dist = sqrt(pow(touches[0].pageX-touches[1].pageX,2)+pow(touches[0].pageY-touches[1].pageY,2));
						touch_dist = Math.hypot(touches[0].pageX-touches[1].pageX ,touches[0].pageY-touches[1].pageY);
						var zoom_factor = (touch_dist/touch_dist_0-1)/8+1;
						gz_zoom_bg_do_zoom({obj:$obj ,factor:zoom_factor}); //log(zoom_factor);
					}
				}
				,touchend:function(e){
					touch_dist_0 = false;
				}
				*/
			});
			//Mouse move to scroll bckground
			//$mask.on(
			
			//Control bar initialization
			var control_sel = $obj.attr('data-control'); var $control = $(control_sel); //console.log(control_sel,$control);
			$control.find('.button.zoom_out').click(function(){
				gz_zoom_bg_do_zoom({obj:$obj ,factor:0.9});
			});
			$control.find('.button.zoom_in').click(function(){//console.log('ZoomIn');
				gz_zoom_bg_do_zoom({obj:$obj ,factor:1.1});
			});
			$control.find('.button.fit').click(function(){
				$obj.css({'background-size':'cover' ,'background-position':'0 0'});
			});
		});
	}
})(jQuery);

