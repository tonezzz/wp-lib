(function($){
	$('document').ready(function(){
		//init_menu_lang();
		//init_tinymce_block();
	});

	function init_menu_lang(){
		$('.gz_menu_lang_switcher').html(gz_multilang.menu_lang);
	}

	function init_tinymce_block(){
		tinymce.init({
			selector: 'textarea',  // change this value according to your HTML
			plugins: 'visualblocks',
			menubar: 'view',
			toolbar: 'visualblocks',
			visualblocks_default_state: true
		  });
		}
		console.log('tinyMCE');
})(jQuery);

