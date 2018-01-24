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
			<h5 style="color:maroon;">Add Opening</h5>
		</div>
		<div class="openings widget-content nopadding">
			<div class="openings">
			<?php echo $this->Form->create('Opening'); ?>
				<fieldset>
				<div class="row">
					<span class="span6"><?php	echo $this->Form->input('user_id');?></span>
					<span class="span6"><?php	echo $this->Form->input('opening_ugx',array('type'=>'text','value'=>0,'required'=>''));?></span>
				</div>

				<?php foreach($currencies as $currency):?>
					<?php 
						if ($currency['Currency']['is_other_currency']){
							$currency['Currency']['description'] = $currency['Currency']['id'];
						}
					?>
					<div class="row">
						<span class="span6"><?php	echo $this->Form->input($currency['Currency']['id'].'a',array('type'=>'text','label'=>$currency['Currency']['description'].' opening amount','value'=>0));?></span>
						<span class="span6"><?php	echo $this->Form->input($currency['Currency']['id'].'r',array('type'=>'text','label'=>$currency['Currency']['description'].' opening rate','value'=>0));?></span>
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