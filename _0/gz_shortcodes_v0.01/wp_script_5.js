;(function($){//
	var scroll_duration = 2000;
	
	$('document').ready(function(){ console.log(3);
		init_smooth_scroll_3();
		init_nsp_tech_dropdown_2();
	});
	
	function init_smooth_scroll_3(){//console.log('init_smooth_scroll_3'); 
		$(document).on('click','a[href^="#"]',function(e){
			var target = $(this.hash);
			if (target.length) { //console.log(document.body.scrollTop); console.log(scroll_duration);
				$('html, body').animate({
				  scrollTop: target.offset().top - 150
				}, scroll_duration);
				//target.focus(); // Setting focus
			} //console.log(document.body.scrollTop, target.offset().top);
		});
	}

	function init_nsp_tech_dropdown_2(){
		//var test = "a b a"; console.log(test.match(/[\s\w]+/g));
		var $ct = $('.nsp_tech_dropdown'); if($ct.length<1) return;
		$ct.closest('.vc_row').addClass('overflow_visible').attr('style','margin-top:0 !important'); console.log($ct.closest('.vc_row').css('margin-top')); //Fix menu floating on top issue.
		var menu_data = $ct.attr('data-menu'); //console.log(menu_data);
		//This will go for all
		scroll_duration = parseInt($ct.attr('data-scroll_duration')); 
		var regex = /([\s\w\-.+>]+)\([\s\w\-.+>,]+\)/g;
		var list = menu_data.match(regex); //console.log(list);
		var regex = /([\s\w\-.+>]+)/g;
		for(const i in list){
			var ll = list[i].split('(');
			var l2 = ll[1].match(regex);
			$ct.append(render_dropdown(ll[0],l2));
		}
		$ct.show();
	}
	function render_dropdown(label,options){//console.log(label,options);
		var html = '';
		html+= "<ul class='nsp_tech " + label + "'>";
		html+= "<li class='item label'>" + render_link(label) + "</li>";
		for(const i in options){
			html += "<li class='item'>";
			html += render_link(options[i]);
			html += "</li>";
		}
		html+= "</ul>"; //console.log(html);
		return html;
	}
	function render_link(label){
		var labels = label.split('>'); if(labels.length==1) labels[1] = labels[0]; //console.log(label,labels);
		var html = "<a href='#" + labels[0] + "'>" + labels[1] + "</a>";
		return html;
	}
})(jQuery);

