(function($){
	$('document').ready(function(){ //console.log($.fancybox);
		init_button_apply_2();
		init_button_apply();
	});

	function init_button_apply_2(){
		var $btn = $('a.gen_image.button.apply'); //console.log($btn);
		$btn.click(function(e){
			e.preventDefault(); e.stopPropagation();
			var $this = $(this);
			var url = $btn.attr('href'); //console.log(url);
			$this.addClass('spinner_after')
			$.ajax(url).done(function(data,textStatus,jqXHR){//console.log(data,textStatus);
				$this.removeClass('spinner_after');
				alert(textStatus + ':' + data);
			});
		});
	}
	
	function init_button_apply(){
		var $btn = $('div.prod_img>a.button.apply');
		$btn.click(function(e){
			e.preventDefault(); e.stopPropagation();
			var $div = $(this).parent();
			var prod_id = $div.attr('data-prod-id');
			var img_name = $div.attr('data-img-name');
			var site_url = $div.attr('data-site-url'); //console.log(url);
			var tpl_url = $div.attr('data-tpl-url'); //console.log(url);
			var img_url = $div.find('a.img').attr('href'); //console.log($img_url);
			//var btn_apply = $div.find('a.img').attr('href'); //console.log($img_url);
			//$.getJSON(url+'img_apply.php',{prod_id:prod_id ,img_url:img_url})
			var spin_url = site_url+'/wp-admin/images/loading.gif';
			var $spin = $("<img class='mv_spin' src='"+spin_url+"'>").insertAfter($(this));
			$.ajax(site_url+tpl_url+'img_apply.php',{
				data:{prod_id:prod_id ,img_url:img_url ,site_url:site_url ,tpl_url:tpl_url ,img_name:img_name}
			})
			.done(function(data){
				//console.log(data);
				$spin.remove();
			});
		});
	}
})(jQuery);

	function init_button_apply(){
	}
