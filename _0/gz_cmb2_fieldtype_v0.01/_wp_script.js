(function($){
	$('document').ready(function(){//console.log(gz_qrcode);
		//init_gz_password();
		init_gz_date();
		init_gz_datetime();
	});

	function init_gz_password(){
		$(".cmb-type-gz-date").on("click", "input.gz_password", function(event){//console.log(this,'init_gz_date',event)
			var $obj = $(this); $obj.parent().css('position','relative');
			var value = $obj.val(); //console.log(value);
			console.log($obj,value);
		});
	}

	function init_gz_datetime(){
		$(".cmb-type-gz-datetime").on("click", "input.gz_datetime", function(event){//console.log(this,'init_gz_date',event)
			var $obj = $(this); $obj.parent().css('position','relative');
			var value = $obj.val(); //console.log(value);
			//if(typeof value == 'string') value = value.replace('-',' '); //Make it work on FF
			if(typeof value == 'string') value = value.replace(/-/g,' '); //Make it work on FF
			var ms = Date.parse(value);
			var value_date=false;
			if(Number.isInteger(ms)) value_date = new Date(ms); else value_date = new Date();
			//console.log(value,ms,value_date);
			if($obj.parent().find('.container').length==0){//Prevent the container re-creation
				var $container = $("<div class='container' style='position:absolute;'>");
				$container.html(
					render_gz_date_option_day(value_date)
					+render_gz_date_option_month(value_date)
					+render_gz_date_option_year(value_date)
					+' '+render_gz_datetime_option_hour(value_date)
					+':'+render_gz_datetime_option_minute(value_date)
				)
				$container.append("<input type='button' class='btn_ok' value='OK' style='margin:0;padding:4px 10px;'/><input type='button' class='btn_cancel' value='X' style='margin:0;padding:4px 10px;'/>");
				$container.appendTo($obj.parent());
				$container.css('top',-$container.height());
				$container.find('.btn_ok').click(function(e){ //console.log('btn_ok');
					e.preventDefault(); e.stopPropagation();
					var val = $container.find('.day').val() + '-' + $container.find('.month').val() + '-' + $container.find('.year').val();
					$obj.val(val);
					//$container.remove();
					$(this).parent().remove();
				});
				$container.find('.btn_cancel').click(function(e){ //console.log('btn_ok');
					e.preventDefault(); e.stopPropagation();
					$(this).parent().remove();
				});
				$obj.keypress(function(e){$container.remove();});
			}
		});
	}

	function init_gz_date(){
		$(".cmb-type-gz-date").on("click", "input.gz_date", function(event){//console.log(this,'init_gz_date',event)
			var $obj = $(this); $obj.parent().css('position','relative');
			var value = $obj.val(); //console.log(value);
			//if(typeof value == 'string') value = value.replace('-',' '); //Make it work on FF
			if(typeof value == 'string') value = value.replace(/-/g,' '); //Make it work on FF
			var ms = Date.parse(value);
			var value_date=false;
			if(Number.isInteger(ms)) value_date = new Date(ms); else value_date = new Date();
			//console.log(value,ms,value_date);
			if($obj.parent().find('.container').length==0){//Prevent the container re-creation
				var $container = $("<div class='container'>");
				$container.html(render_gz_date_option_day(value_date)+render_gz_date_option_month(value_date)+render_gz_date_option_year(value_date))
				$container.append("<input type='button' class='btn_ok' value='ok'/>");
				$container.appendTo($obj.parent());
				$container.css('top',-$container.height());
				$container.find('.btn_ok').click(function(e){ //console.log('btn_ok');
					e.preventDefault(); e.stopPropagation();
					var val = $container.find('.day').val() + '-' + $container.find('.month').val() + '-' + $container.find('.year').val();
					$obj.val(val);
					//$container.remove();
					$(this).parent().remove();
				});
				$obj.keypress(function(e){$container.remove();});
			}
		});
	}

	function render_gz_datetime_option_minute(sel_date){
		var html = '';
		var sel_val = sel_date.getMinutes(); //console.log('min='+sel_val);
		html+= "<select class='minute'>";
		for(var i=0;i<=59;i++){
			var selected = (i==sel_val)?'selected':'';
			var val = String('0'+i).slice(-2); //console.log(i,val);
			html+= "<option value='"+val+"' "+selected+">"+val+"</option>";
		}
		html+= "</select>";
		return html;
	}

	function render_gz_datetime_option_hour(sel_date){
		var html = '';
		var sel_val = sel_date.getHours(); //console.log('hour='+sel_val);
		html+= "<select class='hour'>";
		for(var i=0;i<=23;i++){
			var selected = (i==sel_val)?'selected':'';
			var val = String('0'+i).slice(-2); //console.log(i,val);
			html+= "<option value='"+val+"' "+selected+">"+val+"</option>";
		}
		html+= "</select>";
		return html;
	}

	function render_gz_date_option_day(sel_date){
		var html = '';
		var sel_day = sel_date.getDate();
		html+= "<select class='day'>";
		for(var i=1;i<=31;i++){
			var selected = (i==sel_day)?'selected':'';
			html+= "<option value='"+i+"' "+selected+">"+i+"</option>";
		}
		html+= "</select>";
		return html;
	}

	function render_gz_date_option_month(sel_date){
		var m_th = ['','ม.ค.', 'ก.พ.', 'มี.ค.', 'เม.ย.', 'พ.ค.', 'มิ.ย.', 'ก.ค.', 'ส.ค.', 'ก.ย.', 'ต.ค.', 'พ.ย.', 'ธ.ค.'];
		var m_en = ['','Jan', 'Feb', 'Mar', 'Apr', 'May', 'June', 'July', 'Aug', 'Sept', 'Oct', 'Nov', 'Dec'];
		var html = '';
		var sel_month = sel_date.getMonth()+1;
		html+= "<select class='month'>";
		for(var i=1;i<=12;i++){
			var selected = (i==sel_month)?'selected':'';
			html+= "<option value='"+m_en[i]+"' "+selected+">"+m_th[i]+' / '+m_en[i]+"</option>";
		}
		html+= "</select>";
		return html;
	}

	function render_gz_date_option_year(sel_date){
		var html = '';
		var sel_year = sel_date.getFullYear();
		var year = (new Date()).getFullYear(); //console.log(year);
		html+= "<select class='year'>";
		for(var i=year-100;i<=year;i++){
			var selected = (i==sel_year)?'selected':'';
			html+= "<option value='"+i+"' "+selected+">"+ (i+543) +' / '+ (i) +"</option>";
		}
		html+= "</select>";
		return html;
	}
})(jQuery);

