(function($){
	$('document').ready(function(){ //console.log($.fancybox);
		init_control();
	});
	
	function init_control(){
		var default_scale = {x:1,y:1};
		var $obj = $('.gz_zoom_mask');
		var $mask = $obj.find('.gz_mask'); console.log($mask);
		var $img = $obj.find('.gz_img_list'); console.log($img);
		var $control = $obj.find('.kk_control');
		var is_mousedown=false;
		var md=false;
		//$img.data('translate',{x:.5,y:.5}); //console.log($obj.data('translate'));
		$img.data('translate',{x:0,y:0}); //console.log($obj.data('translate'));
		$img.data('scale',default_scale); //console.log($obj.data('scale'));
		$control.find('.button.fit').click();	//Fit the image
		$mask.on({
			'mousedown touchstart':function(e){e.preventDefault(); md={x:e.clientX,y:e.clientY,tx:$img.data('translate')}; is_mousedown=true;}
			,'mouseup touchmove':function(e){e.preventDefault(); md=false; is_mousedown=false;}
			//mouseout:function(e){
			//	$img[0].style.transform = 'none'
			//}
			,mousemove:function(e){if(!is_mousedown) return; //console.log(e,is_mousedown);
				e.preventDefault(); //console.log(md);
				gz_zoom_bg_translate($img,e,md);
			}
			,wheel:function(e){ //console.log(e.originalEvent.deltaY, e.originalEvent);
				e.preventDefault();
				gz_zoom_bg_scale($img,e);
			}
		});
		
		$control.find('.button.zoom_in').on('click',function(){
			sc = $img.data('scale');
			sc.x*=1.1; sc.y*=1.1;
			$img.data('scale',sc);
			gz_zoom_bg_transform($img);
		});
		$control.find('.button.zoom_out').on('click',function(){
			sc = $img.data('scale');
			sc.x*=0.9; sc.y*=0.9;
			$img.data('scale',sc);
			gz_zoom_bg_transform($img);
		});
		$control.find('.button.fit').on('click',function(){
			//sc = $img.data('scale');
			//sc.x=1.0; sc.y=1.0;
			$img.data('scale',default_scale);
			gz_zoom_bg_transform($img);
		});
	}
	
	function gz_zoom_bg_scale($obj,e){//console.log(e.originalEvent.deltaY);
		var sc = $obj.data('scale');
		if(e.originalEvent.deltaY<0) {sc.x*=1.1; sc.y*=1.1;} else {sc.x*=.9; sc.y*=.9;}
		$obj.data('scale',sc);
		gz_zoom_bg_transform($obj);
	}
	
	function gz_zoom_bg_translate($obj,e,md){//console.log($obj,$obj[0]);
		var bd = e.target.getBoundingClientRect(); //Position of the container related to the page. //console.log(rect); console.log(e.clientX, e.clientY);
		var delta = {x:md.x-e.clientX, y:md.y-e.clientY}; //console.log(delta);
		var dp = {x:delta.x/bd.width, y:delta.y/bd.height}; //Calculate position (0..1) //console.log(px,py);
		var tx = {x:md.tx.x-dp.x, y:md.tx.y-dp.y}; //Calculation transform position
		//var tx = {x:md.tx.x, y:tx.y-dp.y}; //Calculation transform position
		console.log(dp,tx);
		$obj.data('translate',tx);
		gz_zoom_bg_transform($obj);
		//$obj.data('translate',{x:tx,y:ty});
		//var px = (e.clientX-bd.x)/bd.width, py = (e.clientY-bd.y)/bd.height; //Calculate position (0..1) //console.log(px,py);
		//console.log(e.clientX,e.clientY,px,py,bd);
		//var tx = 0.5-px, ty = 0.5-py; //Calculation transform position
		//var px = (e.clientX-bd.x)/bd.width, py = (e.clientY-bd.y)/bd.height; //Calculate position (0..1) //console.log(px,py);
		//var tx = px, ty = py; //Calculation transform position
		//$obj.data('translate',{x:tx,y:ty});
		//gz_zoom_bg_transform($obj);
	}
	
	function gz_zoom_bg_translate_0($obj,e){//console.log($obj,$obj[0]);
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
})(jQuery);

