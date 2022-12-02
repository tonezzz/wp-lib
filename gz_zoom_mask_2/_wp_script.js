;(function($){
	$('document').ready(function(){ //console.log($.fancybox);
		init_control(); //Must init control first.
		init_tabs(); //Always init after control
	});

	var $active_list;
	function init_tabs(){
		//if($prm.tabs){//console.log($prm.tabs);
		var $tabs = $('.gz_img_tabs'); //console.log($tabs);
		$tabs.each(function(){
			$(this).tabs({
				create:function(event,ui){//console.log(ui.panel);
					$active_list = ui.panel; //console.log($active_list);
					$active_list.find('ul.items>li.item').first().click();
				}
			}).find('ul.tabs_nav>li.tab_label').on('click',function(e){//console.log(e);
				var tab_name=$(e.currentTarget).attr('aria-controls'); //console.log(tab_name);
				$active_list = $tabs.find('div#'+tab_name); //console.log($active_list);
				$active_list.find('ul.items>li.item').first().click();
			});
			//$control.data('active_list',$active_list);
		});
	}
	
	function init_control(){
		/*
		*	Thumbnail click action
		*/
		$('.gz_img_list>ul.items>li.item').click(function(){
			//$active_list.removeClass('active');
			var $item = $(this); //console.log($item);
			var $active_group = $item.parent();	$active_group.find('.item').removeClass('active');
			$item.addClass('active');
			var data_img = $item.attr('data-img'); //console.log(data_img);
			var $data_img = JSON.parse(data_img); //console.log($data);
			//var $gz_zoom_mask = $item.parent().parent().parent().parent().parent(); //console.log($gz_zoom_mask);
			var $gz_zoom_mask = $item.closest('.gz_zoom_mask_2'); //console.log($gz_zoom_mask);
			$gz_zoom_mask.find('.panel_img>.img')//.addClass('xxx')
				//.css('background-image',$data.source)
				.css({backgroundImage:'url('+$data_img.source+')' ,backgroundSize:'100%' ,backgroundPosition:'center'}).data('img',$data_img);
		});
		/*
		*	Album control
		*/
		$('.gz_zoom_mask_2 .control.album_next').click(function(){//console.log($active_list.find('.item.active'));
			var $tabs = $(this).closest('.gz_zoom_mask_2').find('.gz_img_tabs'); //console.log($tabs.html());
			var $active_tab = $tabs.find('ul.tabs_nav>li.tab_label.ui-tabs-active').loopNext().find('a.tab_label').click(); //console.log($active_tab.html());
		});
		$('.gz_zoom_mask_2 .control.album_prev').click(function(){//console.log($active_list);
			var $tabs = $(this).closest('.gz_zoom_mask_2').find('.gz_img_tabs'); //console.log($tabs.html());
			var $active_tab = $tabs.find('ul.tabs_nav>li.tab_label.ui-tabs-active').loopPrev().find('a.tab_label').click(); //console.log($active_tab.html());
		});
		/*
		*	tmb control
		*/
		$('.gz_zoom_mask_2 .control.img_next').click(function(){//console.log($active_list);
			$active_list.find('.item.active').loopNext().click();
		});
		$('.gz_zoom_mask_2 .control.img_prev').click(function(){//console.log($active_list);
			$active_list.find('.item.active').loopPrev().click();
		});
		/*
		*	Zoom control
		*/
		$('.gz_zoom_mask_2 .control.zoom_in').click(function(){//console.log($active_list);
			var $img = $(this).closest('.gz_zoom_mask_2').find('.panel_img>.img');
			var scale = $img.data('scale'); if(!scale) scale = 1; //console.log(scale);
			scale*=1.1; $img.data('scale',scale);
			$img.css('transform','scale('+scale+')'); //console.log(scale,$img.css('transform'));
		});
		$('.gz_zoom_mask_2 .control.zoom_out').click(function(){//console.log($active_list);
			var $img = $(this).closest('.gz_zoom_mask_2').find('.panel_img>.img');
			var scale = $img.data('scale'); if(!scale) scale = 1; //console.log(scale);
			scale*=.9; $img.data('scale',scale);
			$img.css('transform','scale('+scale+')'); //console.log(scale,$img.css('transform'));
		});
		/*
		*	Move control
		*/
		$('.gz_zoom_mask_2 .button.up').click(function(){//console.log($active_list);
			var $img = $(this).closest('.gz_zoom_mask_2').find('.panel_img>.img');
			//var top = $img.data('scale'); if(!scale) scale = 1; //console.log(scale);
			//scale*=1.1; $img.data('scale',scale);
			$img.css('transform','translate('+'0'+','+'-10px'+')'); //console.log(scale,$img.css('transform'));
		});
	}
	
	/*
	*	function set_img_prm()
	*		- Use CSS transform (scale, translate) to zoom and pan image.
	*	Parameters:
	*		- set_scale, set_x, set_y set the value.
	*		- rel_scale, rel_x, rel_y set the value relatively.
	*/
	function set_img_param($prm){
		var scale, pos_x, pos_y;
		if($prm.set_scale) scale = $prm.set_scale else scale = $img.data('scale'); if(!scale) scale = 1;
		if($prm.set_pos_x) pos_x = $prm.set_pos_x else pos_x = $img.data('pos_x'); if(!pos_x) pos_x = 0;
		if($prm.set_pos_y) pos_y = $prm.set_pos_y else pos_y = $img.data('pos_x'); if(!pos_y) pos_y = 0;
	}
})(jQuery);

