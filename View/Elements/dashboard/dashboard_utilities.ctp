<div class="btn-group" style="width: auto;">
	<a href="<?php echo $this->params->webroot.'purchased_receipts/add'; ?>" data-original-title="Add Purchase Receipt" class="btn btn-info tip-bottom use-ajax" >
		<i class="icon-white icon-plus-sign"></i> P
	</a>
	<a href="<?php echo $this->params->webroot.'sold_receipts/add'; ?>" data-original-title="Add Sales Receipt" class="btn btn-info tip-bottom use-ajax" >
		<i class="icon-white icon-plus-sign"></i> S
	</a>
	<a href="<?php echo $this->params->webroot.'daily_returns/add'; ?>" data-original-title="Add Daily Return" class="btn btn-info tip-bottom use-ajax" >
		<i class="icon-white icon-retweet"></i>
	</a>
	<a href="<?php echo $this->params->webroot.'openings/index'; ?>" data-original-title="Opening" class="btn btn-info tip-bottom use-ajax" >
		<i class="icon-white icon-adjust"></i>
	</a>
	<?php if($super_admin || $authUser['can_download_FIA_large_cash']):?>
	<a target="_blank" id="FIA_large_cash_above_10M" href="<?php echo $this->params->webroot.'large_cash/large_cash_10_m'; ?>" data-original-title="FIA Large cash above 10M" class="btn btn-info tip-bottom no-ajax" >
		FIA
	</a>
	<?php endif;?>
	<a href="<?php echo $this->params->webroot.'expenses/index'; ?>" data-original-title="List expenses" class="btn btn-info tip-bottom use-ajax" >
		<i class="icon-white icon-list-alt"></i>
	</a>
	<?php if($super_admin || $authUser['can_view_cashflow']):?>
	<a href="<?php echo $this->params->webroot.'balancings/show_cash_flow'; ?>" data-original-title="View cash flow" class="btn btn-info tip-bottom use-ajax" >
		<i class="icon-white icon-arrow-down"></i><i class="icon-white icon-arrow-up"></i>
	</a>
	<?php endif;?>
	<a href="<?php echo $this->params->webroot.'users/view/'.($users_Id); ?>" data-original-title="Safe" class="btn btn-info tip-bottom use-ajax" >
		<i class="icon-white icon-lock"></i>
	</a>
	<a href="<?php echo $this->params->webroot.'users/view/'.($users_Id); ?>" data-original-title="my profile" class="btn btn-large tip-bottom use-ajax" >
		<?php echo $this->Html->image("pic/" . ($profile_image), array('width' => '50px', 'height' => '40px', 'alt' => 'Profile Picture')); ?>
	</a>
</div>

<script>
	$(document).ready(function(){
		$("#FIA_large_cash_above_10M").bind("click", function(e) { 
			e.preventDefault();
			var href=$(this).attr('href');
			href+='?date_from='+($('#dp_from_selected').val());
			href+='&date_to='+($('#dp_to_selected').val());
			location.href = href; 
		});
	});
</script>