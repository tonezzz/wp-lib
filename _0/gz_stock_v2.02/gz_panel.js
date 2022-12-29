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
	my = $.extend({this: this ,$this: $(this) ,callbacks: {load: {} ,show: {} }}, data_default);
	if(typeof options=='object') my = $.extend(my,options);
	this.my = my;

	$(this).off();
	//var events = my.$this.attr('data-events'); console.log('[events]',events);
	$(this).on('reload load',  function(e){this.load(e);  return false;});
	$(this).on('show', function(e){console.log('[show]',this.show(e); return false;});
	if(my.callbacks.init) return my.callbacks.init(this);
}

this.load = function(e){ //console.log('[load2]',e,this); return;
	if(!my.ajax_url) return false;
	if(!(action=my.$this.attr('data-action'))) return;
	if(e.type=='reload') my.store = null;	//Clear if reload
	this.do_ajax(my.ajax_url,{action:my.$this.attr('data-action') ,data:1 ,html:(my.store?0:1)}
		,function(rs){
			if(rs.data) my.data = rs.data;
			if(rs.html) {
				my.$this.html(rs.html);
				my.$this.find('.gz_panel').gz_panel({ajax_url: my.ajax_url ,callbacks: my.callbacks}).gz_panel('show').gz_panel('load');
			}
		}
	);
}

this.show = function(e){console.log('[show]',this);
	var _type = my.$this.attr('data-type');
	var prms  = my.$this.attr('data-prms');
	//console.log('[show]',_type,my.$this.attr('class'));
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
