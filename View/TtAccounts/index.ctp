<?php echo $this->Html->script(array('script_dynamic_content'));?>
<div class="flash-message"><?php echo $this->Session->flash(); ?></div>
<div class=" well well-new-small">
	<h2><?php echo __('TT'); ?></h2>
	<table cellpadding="0" cellspacing="0">
	<tr>
			<th><?php echo $this->Paginator->sort('currency_name'); ?></th>
			<th><?php echo $this->Paginator->sort('balance'); ?></th>
			<th class="actions"><?php echo __(' '); ?></th>
	</tr>
	<?php foreach ($tTAccounts as $account): ?>
	<tr>
		<td><?php echo h($account['TtAccount']['currency_name']); ?>&nbsp;</td>
		<td><?php echo h($account['TtAccount']['balance']); ?>&nbsp;</td>
		<td class="actions">
			<?php echo $this->Html->link(__('Edit'), array('action' => 'edit', $account['TtAccount']['id'])); ?>
		</td>
	</tr>
<?php endforeach; ?>
	</table>
</div>