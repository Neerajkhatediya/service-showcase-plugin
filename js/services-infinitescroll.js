/* Services plugin infinite scroll js */
jQuery(document).ready(function($){
	var count = 2;
	var display_data = $(window).height() / 2;
	var flag = true;
	var load_data = eis_infinitescroll.loadFunc;
		
	//Load data on scroll
	if( load_data == 'scroll' ){
		$(window).scroll(function(){
			if( flag ){
				
				if( $(window).scrollTop() > display_data ){
					flag = false;
					loadServices(count);
					count++;
				} else {
					//Do nothing on scroll up 
				}			
				
				//Disabling ajax call for scroll up
				display_data = $(window).scrollTop();
			}
		}); 
	}
	
	//Load data on button click
	if( load_data == 'load_btn' ){
		$('.loadMore_btn').click(function(){
			var page_num = $(this).attr('data-pagenum');
			loadServices(page_num);
		});
	}
	
	var pageNum = 1;
	
	function loadServices(pageNumber){    
		var ajaxurl = eis_infinitescroll.ajaxurl;
		var services_order = eis_infinitescroll.services_order;
		var posts_per_page = eis_infinitescroll.posts_per_page;
		pageNum = pageNumber;
		
		$.ajax({
			url: ajaxurl,
			type:'POST',
			async: true,
			data: "action=eis_infinite_scroll&services_order=" + services_order + "&pageNum=" + pageNum + "&postPerPage=" + posts_per_page,
			beforeSend: function(){
				$('.loadMore_btn').hide();
				$(".no_data").text('');
				$('.loader_img').css('display','block');
			}, 
			success: function(data){
				if( data.length ){
					$('.main_services').hide();
					$(".more_content").html(data);
					$('.loader_img').css('display','none');
					$('.loadMore_btn').show();
					flag = true;
				} else {
					$(".no_data").text('');
					$(".no_data").append('No more posts found');
					$('.loadMore_btn').hide();
					$('.loader_img').css('display','none');
				}
				
			}
		});
		
		return false;
		
	}
});
