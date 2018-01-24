<?php echo $this->Html->script(array('script_dynamic_content'));?>
<div class="flash-message"><?php echo $this->Session->flash(); ?></div>
<div class="contactLists index well" style="border-left:none;width:82%;border-radius: 14px;border: px solid #ddd;display: block !important;box-shadow: 4px 4px #DDD;">
	<h2><?php echo __('My Contact Lists'); ?></h2>
	<table cellpadding="0" cellspacing="0">
	<tr>
			<th><?php echo $this->Paginator->sort('name'); ?></th>
			<th class="actions"><?php echo __(''); ?></th>
	</tr>
	<?php foreach ($contactLists as $contactList): ?>
	<tr>
		<td><?php echo h($contactList['ContactList']['name']); ?>&nbsp;</td>
		<td class="actions">
			<?php echo $this->Html->link(__('Send SMS'), array('controller'=>'contacts','action' => 'send_sms', $contactList['ContactList']['id'])); ?>
			<!--<?php echo $this->Html->link(__('Send Back Up'), array('controller'=>'contacts','action' => 'send_backup', $contactList['ContactList']['id'])); ?>-->
			<?php echo $this->Html->link(__('contacts'), array('controller'=>'contacts','action' => 'index', $contactList['ContactList']['id'])); ?>
			<?php echo $this->Html->link(__('Add Contacts'), array('controller'=>'contacts','action' => 'add', $contactList['ContactList']['id'])); ?>
			<?php echo $this->Html->link(__('Rename'), array('action' => 'edit', $contactList['ContactList']['id'])); ?>
			<?php if($super_admin): ?>
				<?php echo $this->Html->link(__('Delete'), array('action' => 'delete', $contactList['ContactList']['id']),array('class'=>'action-delete confirm-first confirm-first','data-confirm-text'=>'Are you sure you want to delete it?')); ?>
			<?php endif; ?>
			
		</td>
	</tr>
<?php endforeach; ?>
	</table>
	<p>
	<?php
	echo $this->Paginator->counter(array(
	'format' => __('Page {:page} of {:pages}, showing {:current} records out of {:count} total, starting on record {:start}, ending on {:end}')
	));
	?>	</p>
	<div class="paging">
	<?php
		echo $this->Paginator->prev('< ' . __('previous'), array(), null, array('class' => 'prev disabled'));
		echo $this->Paginator->numbers(array('separator' => ''));
		echo $this->Paginator->next(__('next') . ' >', array(), null, array('class' => 'next disabled'));
	?>
	</div>
</div>