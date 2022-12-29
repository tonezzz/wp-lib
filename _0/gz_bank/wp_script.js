;(function($){
	$('document').ready(function(){ //console.log(r2p_site);
		init_logo_bank_2();
	});

	/*
	//Works for older version of Woocommerce
	function init_logo_bank_1(){
		var $items = $('div.woocommerce>h3'); //console.log($items);
		if($items.length>0) $items.each(function(idx,item){
			var $item = $(item); //console.log($item.text());
			if($item.text().indexOf('Bangkok Bank')>=0) {$item.next('ul.wc-bacs-bank-details').find('li.account_number').addClass('bank_logo bbl');} //console.log('bbl')}
			if($item.text().indexOf('Kasikorn Bank')>=0) {$item.next('ul.wc-bacs-bank-details').find('li.account_number').addClass('bank_logo ksb');} //console.log('ksb')}
			if($item.text().indexOf('Siam Commercial Bank')>=0) {$item.next('ul.wc-bacs-bank-details').find('li.account_number').addClass('bank_logo scb');} //console.log('ksb')}
		});
	}
	*/

	//20170428:Tony:Modified for new version
	function init_logo_bank_2(){
		var $section = $('section.woocommerce-bacs-bank-details'); if($section.length==0) return;
		$section.find('ul.bacs_details').each(function(idx,item){
			var $item = $(item); //console.log($item.text());
			if($item.text().indexOf('Bangkok Bank')>=0) {$item.addClass('bank_logo bbl');} //console.log('bbl')}
			if($item.text().indexOf('Kasikorn Bank')>=0) {$item.addClass('bank_logo ksb');} //console.log('ksb')}
			if($item.text().indexOf('Siam Commercial Bank')>=0) {$item.addClass('bank_logo scb');} //console.log('ksb')}
		});
	}
})(jQuery);

