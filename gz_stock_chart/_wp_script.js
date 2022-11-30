/*
*/
(function($){ //console.log($('.gz_stock_panel').length());
	function render_chart($canvas,val){
		//var chart = new CanvasJS.Chart("chart_canvas", {
		var chart = new CanvasJS.Chart($canvas[0], {
			title: {text: "Test"},
			subtitles: [{text: "Currency in Swedish Krona"}],
			axisX: {valueFormatString: "DD MMM"},
			axisY: {suffix: " kr"},
			data: [{
				type: "candlestick",
				xValueType: "dateTime",
				yValueFormatString: "#,##0.0 kr",
				xValueFormatString: "DD MMM",
				dataPoints: val
			}]
		});
		chart.render();
	}

	function init_gz_chart(){//console.log('init_gz_chart');
		$(document).on('update','.gz_chart',function(e){//console.log(e.target);
			var $this = $(e.target); //console.log($this.parent());
			if($this.parents('.gz_ajax').length==0) return;				//No ajax parent = return
			if(!$this.parents('.gz_ajax').attr('data-action')) return;	//No data-action = return
			var act = $this.parents('.gz_ajax').attr('data-action'); //console.log(act);
			var id	= $this.attr('data-id'); //console.log(id);
			//
			//console.log(act,id,GZ_DATA);
			if(!GZ_DATA[act] || !GZ_DATA[act][id]) return;	//No data = no action
			//console.log(act,GZ_DATA[act],GZ_DATA[act][id]);
			var val = GZ_DATA[act][id]; //console.log(val);
			//console.log($this.attr('data-type'));
			switch($this.attr('data-type')){
				case 'chart'	:render_chart($this,val); break;
			}
			return false;	//20220422:Tony:Prevent event buttle up results in double update on parent gz_ajax
		});
	}
	
	init_gz_chart();
	
	$('document').ready(function(){//console.log($('.gz_stock_panel'));
		//$('.gz_ajax').trigger('refresh'); console.log($('.gz_ajax'));
	});

})(jQuery);

