<?php echo $this->Html->script(array('script_dynamic_content'));?>
<div class="flash-message"><?php echo $this->Session->flash(); ?></div>
<div class="actionLogs well">
	<?php if(isset($user_id)) $users_Id=$user_id;?>
	<a href="<?php echo $this->webroot.'users/view/'.$users_Id;?>"><span class="btn btn-inverse"><i class="icon-white icon-arrow-left"></i> back</span></a>
	<h2><?php echo __('ActionLog'); ?></h2>
	<?php if(isset($from) && isset($to)):?>
		<h6><?php echo 'from '.$from.', to '.$to.' ('.$this->Time->timeAgoInWords($from, array('accuracy' => array('day' => 'day'),'end' => '1 year')).')';?></h6>
	<?php endif; ?>
	<table cellpadding="0" cellspacing="0">
	<tr>
			<th><?php echo $this->Paginator->sort('action_performed'); ?></th>
			<th><?php echo $this->Paginator->sort('date_time_created'); ?></th>
			<th class="actions"><?php echo __(''); ?></th>
	</tr>
	<?php $total_amount=0;?>
	<?php foreach ($actionLogs as $actionLog): ?>
	<tr>
		<td><?php echo h($actionLog['ActionLog']['action_performed']); ?>&nbsp;</td>
		<td><?php echo h($actionLog['ActionLog']['date_time_created']); ?>&nbsp;</td>
		
		<td class="actions">
			<?php if($super_admin): ?>
				<?php if(isset($user_id)): ?>
					<?php echo $this->Html->link(__('Delete'), array('action' => 'delete', $actionLog['ActionLog']['id'],$user_id),array('class'=>'action-delete confirm-first','data-confirm-text'=>'Are you sure you want to delete it?')); ?>
				<?php else:?>
					<?php echo $this->Html->link(__('Delete'), array('action' => 'delete', $actionLog['ActionLog']['id']),array('class'=>'action-delete confirm-first','data-confirm-text'=>'Are you sure you want to delete it?')); ?>
				<?php endif;?>
				
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
