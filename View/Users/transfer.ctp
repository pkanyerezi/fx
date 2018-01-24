<?php echo $this->Html->script(array('script_dynamic_content'));?>
<div class="flash-message"><?php echo $this->Session->flash(); ?></div>
<div class="users form well">
<?php echo $this->Form->create('User',array('type'=>'file','id'=>'user_add_form'));?>
	<fieldset>
		<legend><?php echo ('Make transfer'); ?></legend>
	<?php
		echo $this->Form->input('amount',array('label'=>'Amount(UGX)'));
	?>
	<select name="data[User][user_id]">
		<?php foreach($users as $user):?>
			<option value="<?=$user['User']['id'];?>"><?=$user['User']['name'];?></option>
		<?php endforeach;?>
	</select>
	</fieldset>
<?php echo $this->Form->end(__('Submit', true));?>
</div>
<div class="actions">
	<h3><?php __('Actions'); ?></h3>
	<ul>
		<li><?php echo $this->Html->link(__('List Users', true), array('action' => 'index'));?></li>
	</ul>
</div>