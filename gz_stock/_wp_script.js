/*

value by id
*/
(function($){ //console.log('xxx');
	var $gz_panel = null;
	var $btn_refresh_db = null;
	var $menu_db = null;
	var $import_tabs = null;
	var saved_data = [];
	
	$('document').ready(function(){
		$gz_panel = $('.gz_stock_panel');
		$btn_refresh_db = $gz_panel.find('.menu_db .btn.status');
		$menu_db = $gz_panel.find('.menu_db');
		$import_tabs = $gz_panel.find('.gz_stock_tabs').tabs();
		init_panel_db();
		init_panel_file();
	});

	function init_panel_db(){
		$gz_panel.find('.menu_db .btn.status').on('click',function(e){
			$.ajax({type:'get' ,dataType: 'html' ,url: gz_stock.ajax_url ,data: {action:'get_db_status' ,output: 'html'}
				,success: function(rs){$gz_panel.find('.db_status').html(rs);}
			});
		});
		$gz_panel.find('.menu_db .btn.clear_db').on('click',function(e){
			$.ajax({type:'get' ,dataType: 'html' ,url: gz_stock.ajax_url ,data: {action:'clear_db' ,output: 'html'}
				,success: function(rs){$('.db_status').html(rs);}
			});
		});
		$btn_refresh_db.click();
	}

	function init_panel_file(){//console.log('3'); //console.log($gz_panel.find('.file_menu .btn.status'));
		//Auto init for file_panel AJAX load
		$(document).on('DOMNodeInserted','.file_panel',function(e){console.log(this);
			//var d=1000;
			//$(e.target).find('.info .value').each(function(e){ $(this).delay(10*d++).click() });
			//$(e.target).find('.file_list .btn.status').each(function(){ $(this).delay(10*d++).click() });
			//console.log($(this).find('.value'));
			//File status
			//$(this).find('.value').trigger('update');
			//Import all 
			//$(this).find('.btn.import_all').click();
			$(this).find('.file_menu .btn.refresh').click();
		});
		//Import all
		$(document).on('click','.file_panel .file_menu .btn.refresh',function(e){console.log($(this).parents('.file_panel').find('.info .value'));
			$(this).parents('.file_panel').find('.info .value')
			//(this).parent();
			//var $btn = $(this);
			//$.ajax({type:'get' ,dataType:'json' ,url: gz_stock.ajax_url ,data: {action: 'get_status_year' ,src: $(this).parent().attr('data-path') ,clear: 1 }
			//	,success: function(msg){
			//		//console.log(msg);
			//		$btn.parent().find('.btn.status').click();
			//		//$menu_db.find('.btn.status').click();
			//	}
			//});
		});
		//Import all
		$(document).on('click','.file_panel .btn.import_all',function(e){//console.log($(this).parent().attr('data-path'));
			var $btn = $(this);
			$.ajax({type:'get' ,dataType:'json' ,url: gz_stock.ajax_url ,data: {action: 'import_date' ,src: $(this).parent().attr('data-path') ,clear: 1 }
				,success: function(msg){
					//console.log(msg);
					$btn.parent().find('.btn.status').click();
					//$menu_db.find('.btn.status').click();
				}
			});
		});
		//Attach click event to $('.file_list .btn.status')
		//Clear date data
		var $file_list = $('xx');
		$file_list.find('.btn.clear').on('click',function(e){//console.log($(this).parent().attr('data-path'));
			var $btn = $(this);
			$.ajax({type:'get' ,dataType: 'html' ,url: gz_stock.ajax_url ,data: {action:'clear_date' ,date:$(this).parent().attr('data-date') ,output: 'html'}
				,success: function(rs){
					$btn.parent().find('.btn.status').click();
					$menu_db.find('.btn.status').click();
				}
			});
		})
		//Each file status
		$(document).on('click','.gz_stock_panel .file_list .btn.status',function(e){//console.log(e);
			var d = $(this).parent().attr('data-date'); console.log(d);
			var $btn = $(this);
			$.ajax({type:'get' ,dataType: 'html' ,url: gz_stock.ajax_url ,data: {action:'get_date_status' ,date:$(this).parent().attr('data-date') ,output: 'html'}
				,success: function(rs){//console.log(rs,this);
					$btn.html(rs);}
			});
		});
		/*
		$(document).on('update','.value',function(){
			var $obj = $(this);
			var val=false;
			if(saved_data[$obj.attr('data-from')] && saved_data[$obj.attr('data-from')][$obj.attr('data-key')]){
				val = saved_data[$obj.attr('data-from')][$obj.attr('data-key')];
			} else {
				$.ajax({type:'get' ,dataType: 'array' ,url: gz_stock.ajax_url ,data: {action:$obj.attr('data-from') ,year:'2021' ,output: 'array'}
					,success: function(rs){console.log(rs,this);
						$btn.html(rs);
					}
				});
			}
		});
		
		var $file_menu = $gz_panel.find('.file_menu');
		var $file_list = $gz_panel.find('.file_list');
		$file_menu.find('.btn.status').on('click',function(e){//console.log($(this).parent().parent().find('.btn.import'));
			$file_list.find('.btn.status').click();
		});
		$file_menu.find('.btn.status').on('click',function(e){//console.log($(this).parent().parent().find('.btn.import'));
			$(this).parent().parent().find('.files').each();
		});
		$file_menu.find('.btn.import').on('click',function(e){//console.log($(this).parent().parent().find('.btn.import'));
			$(this).parent().parent().find('.btn.status').click();
		});
		//Init (add click) to $('.file_list .btn.status') after AJAX loadeded.
		//Attach click event to $('.info .value')
		$(document).on('click','.file_panel .info .value',function(e){console.log(e);
			//var $btn = $(this);
			//$.ajax({type:'get' ,dataType: 'html' ,url: gz_stock.ajax_url ,data: {action:'get_date_status' ,date:$(this).parent().attr('data-date') ,output: 'html'}
			//	,success: function(rs){//console.log(rs,this);
			//		$btn.html(rs);}
			//});
		});
		//$file_menu.find('.btn.status').click();
		*/
	}

})(jQuery);

