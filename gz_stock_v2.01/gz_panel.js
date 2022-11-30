/* reload
calling:
  If (init)
	case command: return do_commands (single object)
	default: return each.do_trigger (multiple objects)
  else
	init()
	bind_event() (data-event)

commands:
  reload: 
  load:
  refresh:
events:
  reload: Load panel & ajax data
  load: Load ajax data
  refresh: Refresh view

params:
	ajax_url:	Ajax url to use
	debug_vars: Expost vars (for debug)
gz_panel:
  data-event: When to action (init, click, etc.)
  data-type: 
	panel: Load panel via ajax
	data: Load data from ajax
	view: View data from parent
	refresh: Reload the parent panel or data
<div class='label_row_01'><label for=''>lll</label><span class='v'>vvv</span></div>

console init debug event
*/
////////////////////////////////////////////////
;(function($){$.fn.gz_panel = function(options){ console.log('[call]',options,this);
if(gz_panel=$(this).data('gz_panel')){
	switch(options){
	case 'get_store': //console.log('[get_store]',gz_panel.my,this);
		return gz_panel.my.store;
	default:
		return this.each(function(){
			if(gz_panel=$(this).data('gz_panel')) $(gz_panel).trigger(options);
		});
	}
}
return this.each(function(){//console.log('[init]',options,this);
////////////////////////////////////////////////
var my={};
this.prepare = function(){
	$(this).data('gz_panel',this);
	// Get data-default='a=1&b=2'
	var data_default = Object.fromEntries(new URLSearchParams($(this).attr('data-default')));
	my = $.extend({this: this ,$this: $(this)}, data_default);
	if(typeof options=='object') my = $.extend(my,options);
	this.my = my;

	$(this).off();
	$(this).on('reload',  function(e){this.reload(e);  return false;});
	$(this).on('load',	  function(e){this.load(e);    return false;});
	$(this).on('refresh', function(e){this.refresh(e); return false;});
}

this.reload = function(e){
	var _type = my.$this.attr('data-type');
	var prms  = my.$this.attr('data-prms');
	switch(_type){
		case 'panel':
			this.do_ajax(my.ajax_url,{action:my.$this.attr('data-action') ,prms:prms}
				,function(rs){
					my.$this.html(rs.html);
					my.$this.find('.gz_panel').gz_panel({ajax_url: my.ajax_url}).gz_panel('load');
				}
			);
			break;
	}
}

this.load = function(e){
	var _type = my.$this.attr('data-type');
	var prms  = my.$this.attr('data-prms');
	//if(my.debug.load)console.log('[load]',_type,my.$this.attr('class'));
	switch(_type){
		case 'data':
			this.do_ajax(my.ajax_url,{action:my.$this.attr('data-action') ,prms:prms}
				,function(rs){
					my.store = rs;
					//console.log('[load,data,store]',my.store,my);
					//console.log('[load,find]',my.store,my.$this.find('.gz_panel'));
					my.$this.find('.gz_panel').gz_panel('refresh');
					//my.$this.find('.gz_panel').gz_panel().gz_panel('refresh');
				}
			);
			break;
	}
}

this.refresh = function(e){
	var _type = my.$this.attr('data-type');
	var prms  = my.$this.attr('data-prms');
	//console.log('[refresh]',_type,my.$this.attr('class'));
	switch(_type){
		case 'text':
			var id = my.$this.attr('data-id');
			var parent = $(this).parents('[data-type=data]')[0];
			var store = $(parent).gz_panel('get_store');
			if((store)&&(val=store.data[id])) my.$this.text(val); else my.$this.text('n/a');
			break;
		case 'html':
			var id = my.$this.attr('data-id');
			var parent = $(this).parents('[data-type=data]')[0];
			var store = $(parent).gz_panel('get_store');
			if((store)&&(val=store.data[id])) my.$this.html(val); else my.$this.html('n/a');
			break;
	}
}

this.do_ajax = function(url,data,success=false){
	//console.log('ajax',this);
	var args = {type:'get' ,dataType: 'json' ,url: url ,output: 'json', data: data ,success :success };
	//console.log('[ajax]',args.data.action,my);
	$.ajax(args);
}

////////////////////////////////////////////////
this.prepare();
$(this).trigger('reload');
});
}})(jQuery);
////////////////////////////////////////////////
