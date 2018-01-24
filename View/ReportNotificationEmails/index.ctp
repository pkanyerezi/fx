<?php echo $this->Html->script(array('script_dynamic_content'));?>
<div class="flash-message"><?php echo $this->Session->flash(); ?></div>
<div class="actions">
	<ul>
		<li><?php echo $this->Html->link(__('Add Notification Email'), array('action' => 'add')); ?></li>
	</ul>
</div>
<div>
	<h2><?php echo __('Report Notification Emails'); ?></h2>
	<table cellpadding="0" cellspacing="0">
	<tr>
			<th><?php echo $this->Paginator->sort('report_type_id'); ?></th>
			<th><?php echo $this->Paginator->sort('name'); ?></th>
			<th><?php echo $this->Paginator->sort('description'); ?></th>
			<th><?php echo $this->Paginator->sort('emails'); ?></th>
			<th>Frequency</th>
			<th>Data-Range</th>
			<th><?php echo $this->Paginator->sort('enabled','status'); ?></th>
			<th><?php echo $this->Paginator->sort('succeded'); ?></th>
			<th><?php echo $this->Paginator->sort('failed'); ?></th>
			<th class="actions"><?php echo __(''); ?></th>
	</tr>
	<?php $total=0;?>
	<?php foreach ($reportNotificationEmails as $reportNotificationEmail): ?>
	<tr>
		<td><?php echo h($reportNotificationEmail['ReportType']['name']); ?>&nbsp;</td>
		<td><?php echo h($reportNotificationEmail['ReportNotificationEmail']['name']); ?>&nbsp;</td>
		<td><?php echo h($reportNotificationEmail['ReportNotificationEmail']['description']); ?>&nbsp;</td>
		<td>
			<?php
				$emails = explode(',',$reportNotificationEmail['ReportNotificationEmail']['emails']);
				foreach($emails as $email)
				{
					echo '<div>'.$email.'</div>';
				}
			?>
		</td>
		<td>
			<?php echo h($reportNotificationEmail['ReportNotificationEmail']['frequency_number']); ?>
			<?php echo h($reportNotificationEmail['ReportNotificationEmail']['frequency_type']); ?>
			<?php
				$r = $reportNotificationEmail['ReportNotificationEmail']['recursive'];
				 echo (($r)?'':' - Once'); ?>
		</td>
		<td>
			<?php echo h($reportNotificationEmail['ReportNotificationEmail']['records_time_ago_number']); ?>
			<?php echo h($reportNotificationEmail['ReportNotificationEmail']['records_time_ago_type']); ?>
		</td>
		<td>
			<?php
				$x = $reportNotificationEmail['ReportNotificationEmail']['enabled'];
				 echo (($x)?'Enabled':'Disabled'); ?>
		</td>
		<td><?= $reportNotificationEmail['ReportNotificationEmail']['succeded']; ?> times</td>
		<td><?= $reportNotificationEmail['ReportNotificationEmail']['failed']; ?> times</td>
		<td class="actions">
			<?php if($super_admin):?>
				<?php echo $this->Html->link(__('Test'), array('action' => 'send_notifications', $reportNotificationEmail['ReportNotificationEmail']['id']),array('class'=>'action-delete confirm-first','data-confirm-text'=>'This helps to test the report sent and to make sure you can receive the email')); ?>
				<?php echo $this->Html->link(__('Edit'), array('action' => 'edit', $reportNotificationEmail['ReportNotificationEmail']['id'])); ?>
				<?php echo $this->Html->link(__('Delete'), array('action' => 'delete', $reportNotificationEmail['ReportNotificationEmail']['id']),array('class'=>'action-delete confirm-first','data-confirm-text'=>'Are you sure you want to delete it?')); ?>
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
