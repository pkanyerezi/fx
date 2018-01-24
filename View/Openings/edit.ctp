<?php echo $this->Html->script(array('script_dynamic_content'));?>
<div class="flash-message"><?php echo $this->Session->flash(); ?></div>
<div class="" style="border-left:none;margin-top: -56px;width:82%;margin-left:9%;">
	<style>
		<!--
			.row{margin-left:10px;}
			label{font-weight:bold;}
			.openings div {clear: both;margin-bottom: -23px;padding: .7em;vertical-align: text-top;}
		-->
	</style>
	<div class="widget-box">
		<div class="widget-title">
			<span class="icon">
				<i class="icon-align-justify"></i>									
			</span>
			<h5 style="color:maroon;">Edit Opening</h5>
		</div>
		<div class="openings widget-content nopadding">
			<?php echo $this->Form->create('Opening'); ?>
				<?php 	echo $this->Form->input('id');?>
				<fieldset>
				<div class="row">
					<span class="span6"><?php	echo $this->Form->input('user_id');?></span>
					<span class="span6"><?php	echo $this->Form->input('opening_ugx',array('required'=>''));?></span>
				</div>

				<?php $currency_details = json_decode($opening['OpeningDetail']['currency_details'],true);?>
				<?php foreach($currencies as $currency):?>
					<?php 
						@$CAMOUNT = $currency_details[$currency['Currency']['id']]['CAMOUNT'];
						@$CRATE = $currency_details[$currency['Currency']['id']]['CRATE'];

						$CAMOUNT = empty($CAMOUNT)?0:$CAMOUNT;
						$CRATE = empty($CRATE)?0:$CRATE;
						// pr($currency_details[$currency['Currency']['id']]);
					?>
					<div class="row">
						<span class="span6"><?php	echo $this->Form->input($currency['Currency']['id'].'a',array('type'=>'text','label'=>$currency['Currency']['id'].' opening amount','value'=>$CAMOUNT));?></span>
						<span class="span6"><?php	echo $this->Form->input($currency['Currency']['id'].'r',array('type'=>'text','label'=>$currency['Currency']['id'].' opening rate','value'=>$CRATE));?></span>
					</div>
				<?php endforeach;?>
				<div class="row">
					<span class="span10"><?php echo $this->Form->input('date',array('required'=>''));?></span>
				</div>
				</fieldset>
			<?php echo $this->Form->end(__('Submit')); ?>
		</div>
	</div>
</div>