<?php echo $this->Html->script(array('script_dynamic_content'));?>
<div class="flash-message"><?php echo $this->Session->flash(); ?></div>
<div class="creditors index well">
	<?php if(isset($customer_id)):?>
		<a href="<?php echo $this->webroot.'users/view/'.$customer_id;?>"><span class="btn btn-inverse"><i class="icon-white icon-arrow-left"></i> back</span></a>
	<?php endif; ?>
	<h2><?php echo __('Creditors'); ?></h2>
	<?php if(isset($from) && isset($to)):?>
		<h6><?php echo 'from '.$from.', to '.$to.' ('.$this->Time->timeAgoInWords($from, array('accuracy' => array('day' => 'day'),'end' => '1 year')).')';?></h6>
	<?php endif; ?>
	<table cellpadding="0" cellspacing="0">
	<tr>
			<th><?php echo $this->Paginator->sort('customer'); ?></th>
			<th><?php echo $this->Paginator->sort('amount'); ?></th>
			<th><?php echo $this->Paginator->sort('date'); ?></th>
			<th><?php echo $this->Paginator->sort('user_id','Created by'); ?></th>
			<th class="actions"><?php echo __(''); ?></th>
	</tr>
	<?php $total_amount=0;?>
	<?php foreach ($creditors as $creditor): ?>
	<tr>
		<td><?php echo h($creditor['Creditor']['customer']); ?>&nbsp;</td>
		<td><span class="ln"><?php echo h($creditor['Creditor']['amount']);$total_amount+=$creditor['Creditor']['amount']; ?></span>&nbsp;</td>
		<td><?php echo h($creditor['Creditor']['date']); ?>&nbsp;</td>
		<td><?php echo h($creditor['User']['name']); ?>&nbsp;</td>
		<td class="actions">
			<?php echo $this->Html->link(__('View'), array('action' => 'view', $creditor['Creditor']['id'])); ?>
			<?php if($super_admin): ?>
				<?php echo $this->Html->link(__('Edit'), array('action' => 'edit', $creditor['Creditor']['id'])); ?>
				<?php echo $this->Html->link(__('Delete'), array('action' => 'delete', $creditor['Creditor']['id']),array('class'=>'action-delete confirm-first','data-confirm-text'=>'Are you sure you want to delete it?')); ?>
			<?php endif; ?>
		</td>
	</tr>
<?php endforeach; ?>
	<tr>
		<td style="background:#2c2c2c;color:#fff">Total:</td>
		<td style="background:#2c2c2c;color:#fff" class="ln"><?php echo $total_amount; ?></td>
		<td style="background:#2c2c2c;color:#fff"></td>
		<td style="background:#2c2c2c;color:#fff"></td>
	</tr>
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
<div class="actions">
	<h3><?php echo __(''); ?></h3>
	<ul>
		<?php if(isset($customer_id)):?>
			<li><?php echo $this->Html->link(__('Add Credit'), array('action' => 'add',$customer_id)); ?></li>
		<?php else: ?>
			<li><?php echo $this->Html->link(__('Add Credit'), array('action' => 'add')); ?></li>
		<?php endif; ?>
	</ul>
</div>
