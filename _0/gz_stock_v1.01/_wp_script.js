/*
parent = ajax load data(k,v) into array.
child = load values from parent array.
clear_date import
refresh parent
*/
var GZ_DATA = [];
(function($){
	var loading=false;
	var $stock_panel = $('.gz_stock_panel');
	
	function init_gz_act(){ //console.log('gz_act');
		var $con = $(document);
		$con.on('init','.gz_act',function(e){ //console.log(e.target);
			var $this = $(e.target);
			var my_event,my_type;
			if(!$this.hasClass('gz_act')) return;
			if(!(my_event=$this.attr('data-event'))) return;
			if(!(my_type=$this.attr('data-type'))) return;
			console.log(e.target);
			$this.on(my_event,function(e){
				switch(my_type){
					case 'ajax': var my_action=$this.attr('data-action'); console.log(my_action);
						$.ajax({type:'get' ,dataType: 'json' ,url: gz_stock.ajax_url ,data: {action:$this.attr('data-action') ,output: 'json' ,prms:$this.attr('data-prms')}
						,success: function(rs){ //console.log(rs);
						}});
						break;
					case 'trigger': var my_trigger=$this.attr('data-trigger'); console.log(my_trigger); console.log($this.children());
						$this.children().trigger(my_trigger);
						break;
				}
			});
			return false;
		});
	}
	
	function init_gz_ajax(){
		var $con = $(document);
		$con.on('refresh DOMNodeInserted','.gz_ajax',function(e){
			var $this = $(e.target);
			if(!$this.hasClass('gz_ajax')) return;	//No ajax = No action
			if(!$this.attr('data-action')) return;	//No action = no ajax
			//console.log(e.target);
			$.ajax({type:'get' ,dataType: 'json' ,url: gz_stock.ajax_url ,data: {action:$this.attr('data-action') ,output: 'json' ,prms:$this.attr('data-prms')}
				,success: function(rs){ //console.log(rs);
					var act = $this.attr('data-action');
					if(typeof(GZ_DATA[act])=='array') GZ_DATA[act] = GZ_DATA[act].concat([rs.data]);
					else GZ_DATA[act] = rs.data;
					$this.find('.gz_val').trigger('update'); //console.log(act,GZ_DATA);
				}
			});
			return false;
		});
		$con.on('update','.gz_val',function(e){
			var $this = $(e.target);
			if($this.parents('.gz_ajax').length==0) return;
			if(!$this.parents('.gz_ajax').attr('data-action')) return;
			//console.log(e.target);
			var act = $this.parents('.gz_ajax').attr('data-action'); //console.log(act);
			var id	= $this.attr('data-id'); //console.log(id);
			//
			//console.log(act,id,GZ_DATA);
			if(!GZ_DATA[act] || !GZ_DATA[act][id]) return;	//No data = no action
			//console.log(act,GZ_DATA[act],GZ_DATA[act][id]);
			var val = GZ_DATA[act][id]; //console.log(val);
			//var format = GZ_DATA[act][id+'_t'];
			//console.log($this.attr('data-type'));
			var val_format = false;
			switch($this.attr('data-type')){
				case 'd'	:val_format = new Intl.NumberFormat().format(val); break;
				case 'ul'	:val_format = val;
			}
			if(val_format) $(this).html(val_format);
			return false;	//20220422:Tony:Prevent event buttle up results in double update on parent gz_ajax
		});
		//Import
		$con.on('click','ul.file_list > li > .btn.import',function(e){ //console.log(e.target);
			var $this = $(e.target); //if(!$this.attr(
			var file = $this.parent().find('.file').text(); //console.log(file);
			var year = $this.parent().attr('data-year'); //console.log(year,file);
			$.ajax({type:'get' ,dataType: 'json' ,url: gz_stock.ajax_url ,data: {action:'import_date' ,output: 'json' ,year:year ,file:file}
				,success: function(rs){//console.log(rs);
					$this.parent().find('.gz_val').trigger('update');
				}
			});
			return false;
		});
		//Import all
		$con.on('click','.file_panel .file_menu .btn.import_all',function(e){//console.log($(this).parent().attr('data-path'));
			var $btns = $(e.target).parents('.file_panel').find('.btn.import').click(); return false;
		});
		//Clear
		$con.on('click','ul.file_list > li > .btn.clear',function(e){ //console.log(e.target);
			var $this = $(e.target);
			var date = $this.parent().attr('data-id'); //console.log(year,file);
			$.ajax({type:'get' ,dataType: 'json' ,url: gz_stock.ajax_url ,data: {action:'clear_date' ,output: 'json' ,date:date}
				,success: function(rs){//console.log($rs);
					$this.parent().find('.gz_val').trigger('update');
				}
			});
			return false;
		});
		//Clear all
		$con.on('click','.file_panel .file_menu .btn.clear_all',function(e){ //console.log(e.target);
			var $btns = $(e.target).parents('.file_panel').find('.btn.clear').click(); console.log($btns); return false;
		});
		$con.on('DOMNodeInserted','ul.file_list > li',function(e){//console.log(e.target);
			var $this = $(e.target);
			if (!$this.is('li')) return; //console.log(GZ_DATA);
			var id = $this.text().substring(16,16+10);
			var year = id.substring(0,4);
			//$this.attr('data-id',id).attr('data-year',year).wrapInner("<span class='file'>");
			$this.attr({'data-id':id,'data-year':year}).addClass('row').wrapInner("<span class='file cell'>");
			$this.append("<span class='gz_val btn count cell' data-type='d' data-id='" + id + "'>(?)</span>");//.trigger('update');
			$this.append("<span class='btn clear cell'>Clear</span>");
			$this.append("<span class='btn import cell'>Import</span>");
			
			var act = 'get_date_status';
			//console.log(loading,(!loading && !GZ_DATA['act']));
			if(!loading && !GZ_DATA['act']){
				loading = true; //console.log(loading,(!loading && !GZ_DATA['act']));
				$this.parents('.gz_ajax').attr('data-action',act); //console.log(act);
				$.ajax({type:'get' ,dataType: 'json' ,url: gz_stock.ajax_url ,data: {action:act ,output: 'json' ,year:year}
					,success: function(rs){
						if(typeof(GZ_DATA[act])=='array') GZ_DATA[act] = GZ_DATA[act].concat([rs.data]);
						else GZ_DATA[act] = rs.data;
						//console.log(e.target,e,act,rs.data,GZ_DATA);
						$this.parents('.file_list').find('.gz_val').trigger('update'); //console.log($this.find('.gz_val'));
						loading = false;
					}
				});
			}
			//GZ_DATA[act][id+'_t'] = 'd'; //console.log(GZ_DATA[act]);
			//$this.append("<span class='gz_val' data-id='" + dd + "'>(?)</span>");//.trigger('update');
			return false;
		});
	}
	
	init_gz_ajax();
	init_gz_act();
	//$('.gz_val').trigger('update');

	//var $btn_refresh_db = null;
	//var $menu_db = null;
	//var $import_tabs = null;
	//var saved_data = [];
	
	$('document').ready(function(){
		$('.gz_ajax').trigger('refresh');
		$('.gz_act').trigger('init');
		$('.gz_stock_panel').tabs();	//20220422:Tony:Udate existing before init tabs (and load more).
		//console.log($('.gz_val'));
		//if($('.gz_stock_panel').length==0) return; else $gz_panel = $('.gz_stock_panel');
		//$gz_panel.find('.gz_stock_tabs').tabs();
		//init_gz_ajax();
		//$gz_panel = $('.gz_stock_panel');
		//$btn_refresh_db = $gz_panel.find('.menu_db .btn.status');
		//$menu_db = $gz_panel.find('.menu_db');
		//$import_tabs = $gz_panel.find('.gz_stock_tabs').tabs();
		//init_panel_db();
		//init_panel_file();
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

