jQuery(document).ready(function($){
	var newPrice;
	$('.ticket').each(function(index) {
	    $(this).on("click", function(){
	      
	      if($(this).hasClass('ticket-sold')){

	      }else{
	      	$(this).toggleClass('selected');

	       	var count_selected = $('.selected').size();
	        var id = $(this).data('id');
	        var ticket_number = $(this).data('ticket_number');
	        var current_price =  parseInt($('.ticket-price .current-price').html());
	        newPrice = current_price * count_selected;

	        $('.newPrice').html('Selected Price £' + newPrice);

	        console.log(count_selected, newPrice);
	      }

	    });
	});


	$('.single_add_to_cart_button').on('click', function(e){
		$.ajax({
	        type : "post",
	        dataType: 'JSON',
	        url : ajaxurl,
	        data : {action: "change_product_price_cart", newPrice: newPrice},
	        success: function(response) {
	 		
	        }
	    });
	});
	
});