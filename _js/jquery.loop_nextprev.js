/**
 * jQuery LoopNextPrev
 */
;(function($){
	init_loopNextPrev();
	
	function init_loopNextPrev(){
		$.fn.loopNext = function(selector){ //console.log(this);
			var selector = selector || '';
			return this.next(selector).length ? this.next(selector) : this.siblings(selector).addBack(selector).first();
		}
		$.fn.loopPrev = function(selector){
			var selector = selector || '';
			return this.prev(selector).length ? this.prev(selector) : this.siblings(selector).addBack(selector).last();
		}
	}
})(jQuery);
//})(window.jQuery || window.Zepto);