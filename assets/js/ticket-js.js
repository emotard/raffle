jQuery(document).ready(function($){
	/* Encrypting the price not needed any more due to price being set via php on quantiy */ 
	$('.woocommerce-cart .qty').prop('disabled', true);
	var newPrice;
	var newPrice2;
	var count_selected;
	var ticket_numbers = [];
	$('.ticket').each(function(index) {
	    $(this).on("click", function(){
	      
	      if($(this).hasClass('ticket-sold')){

	      }else{
	      	$(this).toggleClass('selected');

	       	count_selected = $('.selected').size();
	        var id = $(this).data('id');
	        var ticket_number = $(this).data('ticket_number');

	        /* check if ticket is already in array if so remove it from array */ 
	        
	        if(jQuery.inArray(ticket_number, ticket_numbers) != -1) {
			    ticket_numbers = jQuery.grep(ticket_numbers, function(value) {
				  return value != ticket_number;
				});
			} else {
			     ticket_numbers.push(ticket_number);
			} 
	       
	        var current_price =  parseInt($('.ticket-price .current-price').html());
	        newPrice = current_price * count_selected;
            newPrice2 = String(newPrice);
	       	var key = CryptoJS.enc.Hex.parse(specialObj.key);
			var iv  = CryptoJS.enc.Hex.parse('101112131415161718191a1b1c1d1e1f');

			var encrypted = CryptoJS.AES.encrypt(newPrice2, key, { iv: iv }).toString();

	        $('.newPrice').html('Selected Price £' + newPrice);
	        $.ajax({
		        type : "POST",
		        dataType: 'JSON',
		        url : ajaxurl,
		        data : {action: "change_product_price_cart", 
		        custom: encrypted, 
		        security: specialObj.security, 
		        count: count_selected, 
		        ticket_numbers: ticket_numbers},
		        success: function(response) {
			       $('.qty').val(count_selected);
		        }
	    	});
	        //console.log(count_selected, newPrice);
	      }

	    });
	});

});