<section id="tickets">
<?php 

foreach ($results as $result) { ?>
	<?php 
	 if($result->ticket_status === "sold"){
	 	$sold = "ticket-sold";
	 }else{
	 	$sold = "";
	 }
	?>
	<div class="ticket_number">
		
		<div class="ticket <?php echo $sold ?>" data-ticket_number="<?php echo $result->ticket_number ?>" data-id="<?php echo $result->id ?>">
			<?php echo $result->ticket_number ?>
		</div>

	</div>

<?php  } ?>

</section>

<section id="total-tickets-purchased">
	<div class="ticket-price">
 	 <h4><div class="newPrice" data-setprice="100" >Selected Price £0</div></h4>
	 <div class="current-price"><?php echo $price ?></div>
	</div>
</section>