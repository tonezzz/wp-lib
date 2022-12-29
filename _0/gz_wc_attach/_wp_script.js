;(function($){
	$('document').ready(function(){ //console.log($.fancybox);
		//console.log(gz_wc_attach.ajax_url);
		init_manage_order();
		init_send_mail();
	});

	function show_spinner($container){
		$container.css({position:'relative'}).append("<div class='spinner'>");
	}

	function hide_spinner($container){
		$container.find("div.spinner").remove();
	}

	function init_send_mail(){
		var $container = $(".gz_wc_attach.list_orders"); //console.log($container);
		$container.on('click','.button.send_mail',function(e){//console.log('init_send_mail');
			e.preventDefault();
			var post_id = $(this).attr('data-post_id'); //console.log($post_id);
			var $button = $(this);
			$button.text('Sending'); show_spinner($(this));
			$.ajax({
				url:gz_wc_attach.ajax_url ,data:{action:'send_mail' ,post_id:post_id}
			}).done(function(rs){//console.log($button.parent().parent());
				$button.text('Sent!'); hide_spinner($button);
				//$container.html(rs);
			});
		});
	}

	function init_manage_order(){
		var $containers = $('.gz_wc_attach.list_orders td.list_files');
		//var $items = $('.button.gz_wc_attach.add_files'); //console.log($items.first().parent());
		var file_frame; // variable for the wp.media file_frame
	
		//Get all the remove buttons
		//var $remove = $items.parent().find('.remove'); //console.log($remove);
		//$remove.on('click',function(e){//console.log($(this).parent().parent())
		$containers.on('click','.item > .remove',function(e){
			e.preventDefault();
			var $button = $(this);
			var $container = $button.closest('td.list_files'); console.log($container);
			//var $parent = $(this).parent(); console.log($parent);
			var post_id = $(this).parent().parent().attr('data-post_id'); //console.log(post_id);
			//var url = []; url.push($(this).parent().find('a').attr('href'));
			var items = []; items.push($(this).attr('data-id'));
			show_spinner($container);
			$.ajax({
				url:gz_wc_attach.ajax_url ,data:{action:'del_attach' ,post_id:post_id ,data:items}
			}).done(function(rs){//console.log($button.parent().parent());
				//console.log($(this).parent().html());
				//console.log($(this).parent().parent().html());
				//$button.parent().parent().parent().html(rs);
				//$container.find("div.spinner").remove();
				//hide_spinner($container);
				$container.html(rs);
			});
		})

		// attach a click event (or whatever you want) to some element on your page
		//$items.on( 'click', function( e ) {
		$containers.on('click','.button.gz_wc_attach.add_files',function(e){
			e.preventDefault();
			var $button = $(this); //console.log($button.parent().parent());
			var post_id = $(this).attr('data-post_id'); //console.log(post_id);
			var $container = $button.closest('td.list_files');
			// if the file_frame has already been created, just reuse it
			//if ( file_frame ) {
			//	file_frame.open();
			//	return;
			//} 
			file_frame = wp.media.frames.file_frame = wp.media({
				title: $( this ).attr( 'data-title' ),
				button: {
					text: $( this ).attr( 'data-button_text' ),
				}
				,multiple: true // set this to true for multiple file selection
			});
			file_frame.on( 'select', function() {
				//var sel = file_frame.state().get('selection').first().toJSON();
				var sel = file_frame.state().get('selection');
				//var data = [];
				var items = file_frame.state().get('selection').toJSON().map(function(item){return {id:item.id ,url:item.url};
				}); //console.log(items);
				//$container.append("<div class='spinner'>");
				show_spinner($container);
				$.ajax({
					url:gz_wc_attach.ajax_url ,data:{action:'add_attach' ,post_id:post_id ,data:items}
				}).done(function(rs){//console.log($button.parent().parent());
					//console.log($(this).parent().html());
					//console.log($(this).parent().parent().html());
					//$button.parent().parent().html(rs);
					//hide_spinner($container);
					$container.html(rs);
				});
			});
			file_frame.open();
		});		
	}
})(jQuery);

