<?php echo $this->Html->script(array('script_dynamic_content'));?>
<div class="flash-message"><?php echo $this->Session->flash(); ?></div>
<div class="purposes view">
<h2><?php  echo __('Purpose'); ?></h2>
	<dl>
		<dt><?php echo __('Id'); ?></dt>
		<dd>
			<?php echo h($purpose['Purpose']['id']); ?>
			&nbsp;
		</dd>
		<dt><?php echo __('Description'); ?></dt>
		<dd>
			<?php echo h($purpose['Purpose']['description']); ?>
			&nbsp;
		</dd>
	</dl>
</div>
<div class="actions">
	<h3><?php echo __('Actions'); ?></h3>
	<ul>
		<li><?php echo $this->Html->link(__('Edit Purpose'), array('action' => 'edit', $purpose['Purpose']['id'])); ?> </li>
		<li><?php echo $this->Form->postLink(__('Delete Purpose'), array('action' => 'delete', $purpose['Purpose']['id']), null, __('Are you sure you want to delete # %s?', $purpose['Purpose']['id'])); ?> </li>
		<li><?php echo $this->Html->link(__('List Purposes'), array('action' => 'index')); ?> </li>
		<li><?php echo $this->Html->link(__('New Purpose'), array('action' => 'add')); ?> </li>
		<li><?php echo $this->Html->link(__('List Purchased Receipts'), array('controller' => 'purchased_receipts', 'action' => 'index')); ?> </li>
		<li><?php echo $this->Html->link(__('New Purchased Receipt'), array('controller' => 'purchased_receipts', 'action' => 'add')); ?> </li>
		<li><?php echo $this->Html->link(__('List Sold Receipts'), array('controller' => 'sold_receipts', 'action' => 'index')); ?> </li>
		<li><?php echo $this->Html->link(__('New Sold Receipt'), array('controller' => 'sold_receipts', 'action' => 'add')); ?> </li>
	</ul>
</div>
<div class="related">
	<h3><?php echo __('Related Purchased Receipts'); ?></h3>
	<?php if (!empty($purpose['PurchasedReceipt'])): ?>
	<table cellpadding = "0" cellspacing = "0">
	<tr>
		<th><?php echo __('Id'); ?></th>
		<th><?php echo __('Customer Name'); ?></th>
		<th><?php echo __('Amount'); ?></th>
		<th><?php echo __('Purpose Id'); ?></th>
		<th><?php echo __('Rate'); ?></th>
		<th><?php echo __('Amount Ugx'); ?></th>
		<th><?php echo __('Currency Id'); ?></th>
		<th><?php echo __('Instrument'); ?></th>
		<th><?php echo __('Date'); ?></th>
		<th><?php echo __('Status'); ?></th>
		<th class="actions"><?php echo __('Actions'); ?></th>
	</tr>
	<?php
		$i = 0;
		foreach ($purpose['PurchasedReceipt'] as $purchasedReceipt): ?>
		<tr>
			<td><?php echo $purchasedReceipt['id']; ?></td>
			<td><?php echo $purchasedReceipt['customer_name']; ?></td>
			<td><?php echo $purchasedReceipt['amount']; ?></td>
			<td><?php echo $purchasedReceipt['purpose_id']; ?></td>
			<td><?php echo $purchasedReceipt['rate']; ?></td>
			<td><?php echo $purchasedReceipt['amount_ugx']; ?></td>
			<td><?php echo $purchasedReceipt['currency_id']; ?></td>
			<td><?php echo $purchasedReceipt['instrument']; ?></td>
			<td><?php echo $purchasedReceipt['date']; ?></td>
			<td><?php echo $purchasedReceipt['status']; ?></td>
			<td class="actions">
				<?php echo $this->Html->link(__('View'), array('controller' => 'purchased_receipts', 'action' => 'view', $purchasedReceipt['id'])); ?>
				<?php echo $this->Html->link(__('Edit'), array('controller' => 'purchased_receipts', 'action' => 'edit', $purchasedReceipt['id'])); ?>
				<?php echo $this->Form->postLink(__('Delete'), array('controller' => 'purchased_receipts', 'action' => 'delete', $purchasedReceipt['id']), null, __('Are you sure you want to delete # %s?', $purchasedReceipt['id'])); ?>
			</td>
		</tr>
	<?php endforeach; ?>
	</table>
<?php endif; ?>

	<div class="actions">
		<ul>
			<li><?php echo $this->Html->link(__('New Purchased Receipt'), array('controller' => 'purchased_receipts', 'action' => 'add')); ?> </li>
		</ul>
	</div>
</div>
<div class="related">
	<h3><?php echo __('Related Sold Receipts'); ?></h3>
	<?php if (!empty($purpose['SoldReceipt'])): ?>
	<table cellpadding = "0" cellspacing = "0">
	<tr>
		<th><?php echo __('Id'); ?></th>
		<th><?php echo __('Customer Name'); ?></th>
		<th><?php echo __('Amount'); ?></th>
		<th><?php echo __('Purpose Id'); ?></th>
		<th><?php echo __('Rate'); ?></th>
		<th><?php echo __('Amount Ugx'); ?></th>
		<th><?php echo __('Currency Id'); ?></th>
		<th><?php echo __('Date'); ?></th>
		<th><?php echo __('Status'); ?></th>
		<th class="actions"><?php echo __('Actions'); ?></th>
	</tr>
	<?php
		$i = 0;
		foreach ($purpose['SoldReceipt'] as $soldReceipt): ?>
		<tr>
			<td><?php echo $soldReceipt['id']; ?></td>
			<td><?php echo $soldReceipt['customer_name']; ?></td>
			<td><?php echo $soldReceipt['amount']; ?></td>
			<td><?php echo $soldReceipt['purpose_id']; ?></td>
			<td><?php echo $soldReceipt['rate']; ?></td>
			<td><?php echo $soldReceipt['amount_ugx']; ?></td>
			<td><?php echo $soldReceipt['currency_id']; ?></td>
			<td><?php echo $soldReceipt['date']; ?></td>
			<td><?php echo $soldReceipt['status']; ?></td>
			<td class="actions">
				<?php echo $this->Html->link(__('View'), array('controller' => 'sold_receipts', 'action' => 'view', $soldReceipt['id'])); ?>
				<?php echo $this->Html->link(__('Edit'), array('controller' => 'sold_receipts', 'action' => 'edit', $soldReceipt['id'])); ?>
				<?php echo $this->Form->postLink(__('Delete'), array('controller' => 'sold_receipts', 'action' => 'delete', $soldReceipt['id']), null, __('Are you sure you want to delete # %s?', $soldReceipt['id'])); ?>
			</td>
		</tr>
	<?php endforeach; ?>
	</table>
<?php endif; ?>

	<div class="actions">
		<ul>
			<li><?php echo $this->Html->link(__('New Sold Receipt'), array('controller' => 'sold_receipts', 'action' => 'add')); ?> </li>
		</ul>
	</div>
</div>
