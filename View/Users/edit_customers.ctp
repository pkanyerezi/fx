<?php echo $this->Html->script(array('script_dynamic_content'));?>
<div class="flash-message"><?php echo $this->Session->flash(); ?></div>
<div class="users form well">
<?php echo $this->Form->create('User');?>
	<fieldset>
		<legend><?php echo ('Edit customer'); ?></legend>
	<?php
		echo $this->Form->input('id');
		echo $this->Form->input('name');
		echo $this->Form->input('email',array('label'=>'Phone'));
		if($super_admin){
			echo $this->Form->input('is_bank',array('label'=>'Is Bank'));
		}
	?>
	</fieldset>
<?php echo $this->Form->end(__('Submit', true));?>
</div>
<div class="actions">
	<h3><?php __('Actions'); ?></h3>
	<ul>
		<li><?php echo $this->Html->link(__('List customers', true), array('action' => 'index','customers'));?></li>
	</ul>
</div>