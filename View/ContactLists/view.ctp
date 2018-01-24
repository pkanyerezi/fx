<?php echo $this->Html->script(array('script_dynamic_content'));?>
<div class="flash-message"><?php echo $this->Session->flash(); ?></div>
<div class="contactLists view">
<h2><?php  echo __('Contact List'); ?></h2>
	<dl>
		<dt><?php echo __('Name'); ?></dt>
		<dd>
			<?php echo h($contactList['ContactList']['name']); ?>
			&nbsp;
		</dd>
	</dl>
</div>
<div class="actions">
	<h3><?php echo __(''); ?></h3>
	<ul>
		<li><?php echo $this->Html->link(__('Edit Contact List'), array('action' => 'edit', $contactList['ContactList']['id'])); ?> </li>
		<li><?php echo $this->Html->link(__('List Contact Lists'), array('action' => 'index')); ?> </li>
		<li><?php echo $this->Html->link(__('New Contact List'), array('action' => 'add')); ?> </li>
	</ul>
</div>
<div class="related">
	<h3><?php echo __('Related Contacts'); ?></h3>
	<?php if (!empty($contactList['Contact'])): ?>
	<table cellpadding = "0" cellspacing = "0">
	<tr>
		<th><?php echo __('Name'); ?></th>
		<th><?php echo __('Phone Number'); ?></th>
		<th><?php echo __('Email'); ?></th>
		<th class="actions"><?php echo __(''); ?></th>
	</tr>
	<?php
		$i = 0;
		foreach ($contactList['Contact'] as $contact): ?>
		<tr>
			<td><?php echo $contact['name']; ?></td>
			<td><?php echo $contact['phone_number']; ?></td>
			<td><?php echo $contact['email']; ?></td>
			<td class="actions">
				<?php echo $this->Html->link(__('View'), array('controller' => 'contacts', 'action' => 'view', $contact['id'])); ?>
				<?php echo $this->Html->link(__('Edit'), array('controller' => 'contacts', 'action' => 'edit', $contact['id'])); ?>
			</td>
		</tr>
	<?php endforeach; ?>
	</table>
<?php endif; ?>

	<div class="actions">
		<ul>
			<li><?php echo $this->Html->link(__('New Contact'), array('controller' => 'contacts', 'action' => 'add')); ?> </li>
		</ul>
	</div>
</div>
