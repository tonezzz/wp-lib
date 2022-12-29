/*
gz_panel:
  data-event: When to action (init, click, etc.)
  data-type: 
	panel: Load panel via ajax
	data: Load data from ajax
	refresh: Reload the parent panel or data

<div class='label_row_01'><label for=''>lll</label><span class='v'>vvv</span></div>

data-event

parent = ajax load data(k,v) into array.
child = load values from parent array.
clear_date import
refresh parent
console
*/
;(function($){$.fn.gz_panel = function(options){return this.each(function(){
////

//Check initialization
if($(this).data('gz_panel')){
	//Already init, let's do the command.
	switch(options){
		case 'refresh':
		  $(this).gz_panel().init();
		  break;
	}
	return;
}else{
	//First time call, let's prepare and init
	$(this).data('gz_panel',this);
}

// Get data-default='a=1&b=2'
var data_default = Object.fromEntries(new URLSearchParams($(this).attr('data-default')));

var defaults = {
	this: this, $this: $(this),
	//ajax_count: 0
}

var vars = $.extend( {}, data_default, defaults, options ); if(vars.debug) console.log(vars);	//External & internal

init();

//data-event='init click'
function init(){
	var my_event = vars.$this.attr('data-event'); console.log(my_event);
	//switch(my_event){
	//	case 'init': do_type(); break;
	//	default: vars.$this.on(my_event,function(e){do_type();});
	//}
	vars.$this.on(my_event,function(e){do_type();});
	vars.$this.trigger('init');
}

//this.test = function(){console.log('xxx'); return 1;};
this.refresh = function(){console.log('refresh',this);
	//init();
};

function do_type_ajax(args){
	if(args.callback) console.log(args);
}
function do_type(){
	if(vars.$this.data('init')) return; else vars.$this.data('init',1);
	var _type 		= vars.$this.attr('data-type'); //console.log(_type);
	var action 		= vars.$this.attr('data-action');
	var prms 		= vars.$this.attr('data-prms');
	switch(_type){
		case 'panel':
			do_type_ajax({
				callback: function(rs){console.log(rs);}
			});
		case 'data':
		    var ajax_url = gz_stock_2.ajax_url;
			$.ajax({
				type:'get' ,dataType: 'json' ,url: ajax_url ,data: {action:action ,output: 'json' ,prms:prms}
				,success: function(rs){
					setting.data=rs; console.log(setting);
				}
				//,complete: function(rs){vars.ajax_count--; console.log(vars.ajax_count); }
			});
			break;
		//case 'refresh': vars.$this.parents('.gz_panel').data('gz_panel').refresh();
		//	break;
	}
}

//function do_update(rs){
//	if(rs.html){
//		vars.$this.html(rs.html);
//		vars.$this.find('.gz_panel').gz_panel(); //console.log(vars.$this.find('.gz_panel'));
//	}
//	if(rs.data) vars.data = rs.data;
//}

////
});}})(jQuery);