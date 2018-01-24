<?php echo $this->Html->script(array('script_dynamic_content'));?>
<div class="flash-message"><?php echo $this->Session->flash(); ?></div>
<div class="banks view">
<h2><?php  echo __('Bank'); ?></h2>
	<dl>
		<dt><?php echo __('Name'); ?></dt>
		<dd>
			<?php echo h($bank['Bank']['name']); ?>
			&nbsp;
		</dd>
	</dl>
</div>
<div class="actions">
	<h3><?php echo __(''); ?></h3>
	<ul>
		<li><?php echo $this->Html->link(__('List Banks'), array('action' => 'index')); ?> </li>
		<li><?php echo $this->Html->link(__('New Bank'), array('action' => 'add')); ?> </li>
	</ul>
</div>
