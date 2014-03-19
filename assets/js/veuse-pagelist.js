(function ($) {
	
	'use strict';
	
	$.fn.veusePagelist = function(options) {
		
		var defaults = {
			handle: '.pageselector-wrapper'
		}
			
		var options = $.extend({}, defaults,options);
		
		
		var actives,
			id ;
			
		$(options.handle).each(function(){
			
			actives = $(this).next().val();
	
			var arr = actives.split(',');
	
					
			$(this).find('a[data-page-id]').each(function(){
			
				id = $(this).attr('data-page-id');
			
				if( jQuery.inArray(id, arr) >= 0 ){
					$(this).addClass('active');
				}
			
			});	
			
			actives = '';
			arr = '';
		});
	
		
		jQuery(document).on('click','.pageselector-wrapper a', function(){
			
			var ids = '';
			
			$(this).toggleClass('active');
			
			$(this).parent().find('a.active').each(function(){			
				ids += $(this).attr('data-page-id') + ',';
			});
			
			$(this).parent().next().val(ids);
			
			ids = '';
			return false;
		});

	
	}
	
	
	jQuery(document).ready(function($){
		
		$(document).veusePagelist();
		
	});


}( jQuery ));
