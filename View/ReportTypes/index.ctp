<?php echo $this->Html->script(array('script_dynamic_content'));?>
<div class="flash-message"><?php echo $this->Session->flash(); ?></div>
<div>
	<h2><?php echo __('Report Types'); ?></h2>
	<table cellpadding="0" cellspacing="0">
	<tr>
			<th><?php echo $this->Paginator->sort('name'); ?></th>
			<th><?php echo $this->Paginator->sort('description'); ?></th>
			<th><?php echo $this->Paginator->sort('sort_order'); ?></th>
			<th><?php echo $this->Paginator->sort('enabled','status'); ?></th>
			<th class="actions"><?php echo __(''); ?></th>
	</tr>
	<?php $total=0;?>
	<?php foreach ($reportTypes as $reportType): ?>
	<tr>
		<td><?php echo h($reportType['ReportType']['name']); ?>&nbsp;</td>
		<td><?php echo h($reportType['ReportType']['description']); ?>&nbsp;</td>
		<td><?php echo h($reportType['ReportType']['sort_order']); ?>&nbsp;</td>
		<td>
			<?php
				$x = $reportType['ReportType']['enabled'];
				 echo (($x)?'Enabled':'Disabled'); ?>
		</td>
		<td class="actions">
			<?php if($super_admin):?>
				<?php echo $this->Html->link(__('Edit'), array('action' => 'edit', $reportType['ReportType']['id'])); ?>
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
