<?php echo $this->Html->script(array('script_dynamic_content'));?>
<div class="flash-message"><?php echo $this->Session->flash(); ?></div>
<div class="users form well">
<?php echo $this->Form->create('User',array('type'=>'file','id'=>'user_add_form'));?>
	<fieldset>
		<legend><?php echo ('Add Cashier'); ?></legend>
	<?php
		echo $this->Form->input('name');
		echo $this->Form->input('email');
		echo $this->Form->input('username');
		echo $this->Form->input('password');
		echo $this->Form->input('password_confirmation',array('type'=>'password'));
		if($admin){
		echo $this->Form->input('role',array('type'=>'select','options'=>array('regular'=>'Regular user','admin'=>'Administrator'),'class'=>'role'));
		}
		if($super_admin){
			echo 'Choose below if either bank or director is being added. But not both.';
			echo $this->Form->input('is_bank',array('label'=>'Is Bank'));
			echo $this->Form->input('is_director',array('label'=>'Is Director'));
		}
		
	?>
	</fieldset>
<?php echo $this->Form->end(__('Submit', true));?>
</div>
<div class="actions">
	<h3><?php __('Actions'); ?></h3>
	<ul>
		<li><?php echo $this->Html->link(__('List Users', true), array('action' => 'index'));?></li>
	</ul>
</div>