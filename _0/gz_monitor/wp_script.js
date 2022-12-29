/*
*/
var data_test = {
	type: 'pie',
	data: {
		datasets: [{
			data: [10, 20, 30]
		}],

		// These labels appear in the legend and in the tooltips when hovering different arcs
		labels: [
			'Red',
			'Yellow',
			'Blue'
		]
	}
};

var Test_actions = [
	{
		name: 'xxx',
		handler(chart){
			//
		}
	}
 ];

(function($){
	
	$('document').ready(function(){//console.log($('.gz_stock_panel'));
		init_gz_monitor();
	});
	
	function init_gz_monitor(){//console.log($('.gz_highlight > video'));
		if($('.gz_monitor').length==0) return;
		
		console.log(gz_monitor);
		
		//init graph
		//$('.gz_monitor').on('init,DOMNodeInserted','.gz_graph',function(){
		$('.gz_monitor').on('init','.gz_graph',function(){
			var $this = $(this); //console.log($this);
			if($this.data('init')) return; else $this.data('init',true);
			//
			var data_st = $this.attr('data-graph');
			var chart_data = jQuery.parseJSON(data_st);
			chart_data.options = {actions: Test_actions}; console.log(chart_data);
			//chart_data.actions = Test_actions; 
			var $obj = $('<canvas>'); $this.append($obj);
			$this.append("<div class='button'>xxx</div>");
			
			const chart = new Chart($obj,chart_data);
		});

		//Init tabs
		$('.gz_monitor').tabs({
			load: function(e,ui){
				ui.panel.find('.gz_graph').trigger('init');
			}
		});
	}

})(jQuery);

