<?php echo $this->Html->script(array('script_dynamic_content'));?>
<div class="flash-message"><?php echo $this->Session->flash(); ?></div>

<div class="widget-box" style="border-left:none;margin-top: -40px;width:80%;margin-left:15%;">
	<div class="widget-title">
		<span class="icon">
			<i class="icon-align-justify"></i>									
		</span>
		<h5 style="color:maroon;">Daily Returns</h5>
	</div>
	<div class="widget-content">	
		<div class="dailyReturns">
			<?php if(isset($from) && isset($to)):?>
				<h6><?php echo 'from '.$from.', to '.$to.' ('.$this->Time->timeAgoInWords($from, array('accuracy' => array('day' => 'day'),'end' => '1 year')).')';?></h6>
			<?php endif; ?>
			<table cellpadding="0" cellspacing="0">
			<tr>
					<th><?php echo $this->Paginator->sort('date'); ?></th>
					<th><?php echo $this->Paginator->sort('name','Created by'); ?></th>
					<th class="actions"><?php echo __(''); ?></th>
			</tr>
			<?php foreach ($dailyReturns as $dailyReturn): ?>
			<tr>
				<td><?php echo h(date("M jS, Y", strtotime($dailyReturn['DailyReturn']['date'])).' ('.($dailyReturn['DailyReturn']['date']).')'); ?>&nbsp;</td>
				<td><?php echo h($dailyReturn['DailyReturn']['name']); ?>&nbsp;</td>
				<td class="actions">
					<?php echo $this->Html->link(__('Send'), array('action' => 'send', $dailyReturn['DailyReturn']['id'])); ?>
					<?php echo $this->Html->link(__('View'), array('action' => 'view', $dailyReturn['DailyReturn']['id'])); ?>
					<?php if($super_admin): ?>
						<?php echo $this->Html->link(__('Delete'), array('action' => 'delete', $dailyReturn['DailyReturn']['id']),array('class'=>'action-delete confirm-first','data-confirm-text'=>'Are you sure you want to delete it?')); ?>
					<?php endif;?>
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
	</div>
</div>