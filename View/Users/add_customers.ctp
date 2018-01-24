<?php echo $this->Html->script(array('script_dynamic_content'));?>
<div class="flash-message"><?php echo $this->Session->flash(); ?></div>
<div class="users form well">
<?php echo $this->Form->create('User');?>
	<legend><?php echo ('Add Customer/Bank/Director'); ?></legend>
	<?php
		$id_options = [
			'None'=>'None',
			'Passport'=>'Passport',
			'EA-Passport'=>'East African Passport',
			'NationalID'=>'National ID',
			'CompanyID'=>'Company ID',
			'Other'=>'Other'
		];
	?>
	<span class="row">
		<span class="span6"><?php echo $this->Form->input('name');?></span>
		<span class="span6"><?php echo $this->Form->input('email',array('label'=>'Phone'));?></span>
	</span>
	<span class="row">
		<span class="span6"><?php echo $this->Form->input('identication_type',['type'=>'select','options'=>$id_options]);?></span>
		<span class="span6"><?php echo $this->Form->input('identication_number');?></span>
	</span>

	<span class="row">
		<span class="span6"><?php echo $this->Form->input('address');?></span>
		<span class="span6"><?php echo $this->Form->input('other_details');?></span>
	</span>
<?php echo $this->Form->end(__('Submit', true));?>
</div>
<div class="actions">
	<h3><?php __('Actions'); ?></h3>
	<ul>
		<li><?php echo $this->Html->link(__('List customers', true), array('action' => 'index','customers'));?></li>
	</ul>
</div>