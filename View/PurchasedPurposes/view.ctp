<div class="purchasedPurposes view">
<h2><?php  echo __('Purchased Purpose'); ?></h2>
	<dl>
		<dt><?php echo __('Id'); ?></dt>
		<dd>
			<?php echo h($purchasedPurpose['PurchasedPurpose']['id']); ?>
			&nbsp;
		</dd>
		<dt><?php echo __('Description'); ?></dt>
		<dd>
			<?php echo h($purchasedPurpose['PurchasedPurpose']['description']); ?>
			&nbsp;
		</dd>
	</dl>
</div>
<div class="actions">
	<h3><?php echo __('Actions'); ?></h3>
	<ul>
		<li><?php echo $this->Html->link(__('Edit Purchased Purpose'), array('action' => 'edit', $purchasedPurpose['PurchasedPurpose']['id'])); ?> </li>
		<li><?php echo $this->Form->postLink(__('Delete Purchased Purpose'), array('action' => 'delete', $purchasedPurpose['PurchasedPurpose']['id']), null, __('Are you sure you want to delete # %s?', $purchasedPurpose['PurchasedPurpose']['id'])); ?> </li>
		<li><?php echo $this->Html->link(__('List Purchased Purposes'), array('action' => 'index')); ?> </li>
		<li><?php echo $this->Html->link(__('New Purchased Purpose'), array('action' => 'add')); ?> </li>
		<li><?php echo $this->Html->link(__('List Purchased Receipts'), array('controller' => 'purchased_receipts', 'action' => 'index')); ?> </li>
		<li><?php echo $this->Html->link(__('New Purchased Receipt'), array('controller' => 'purchased_receipts', 'action' => 'add')); ?> </li>
	</ul>
</div>
<div class="related">
	<h3><?php echo __('Related Purchased Receipts'); ?></h3>
	<?php if (!empty($purchasedPurpose['PurchasedReceipt'])): ?>
	<table cellpadding = "0" cellspacing = "0">
	<tr>
		<th><?php echo __('Id'); ?></th>
		<th><?php echo __('Fox Id'); ?></th>
		<th><?php echo __('Customer Name'); ?></th>
		<th><?php echo __('Amount'); ?></th>
		<th><?php echo __('Purchased Purpose Id'); ?></th>
		<th><?php echo __('Rate'); ?></th>
		<th><?php echo __('Amount Ugx'); ?></th>
		<th><?php echo __('Currency Id'); ?></th>
		<th><?php echo __('Date'); ?></th>
		<th><?php echo __('Status'); ?></th>
		<th><?php echo __('Is Uploaded'); ?></th>
		<th><?php echo __('Nationality'); ?></th>
		<th><?php echo __('Address'); ?></th>
		<th><?php echo __('Passport Number'); ?></th>
		<th><?php echo __('User Id'); ?></th>
		<th><?php echo __('Name'); ?></th>
		<th class="actions"><?php echo __('Actions'); ?></th>
	</tr>
	<?php
		$i = 0;
		foreach ($purchasedPurpose['PurchasedReceipt'] as $purchasedReceipt): ?>
		<tr>
			<td><?php echo $purchasedReceipt['id']; ?></td>
			<td><?php echo $purchasedReceipt['fox_id']; ?></td>
			<td><?php echo $purchasedReceipt['customer_name']; ?></td>
			<td><?php echo $purchasedReceipt['amount']; ?></td>
			<td><?php echo $purchasedReceipt['purchased_purpose_id']; ?></td>
			<td><?php echo $purchasedReceipt['rate']; ?></td>
			<td><?php echo $purchasedReceipt['amount_ugx']; ?></td>
			<td><?php echo $purchasedReceipt['currency_id']; ?></td>
			<td><?php echo $purchasedReceipt['date']; ?></td>
			<td><?php echo $purchasedReceipt['status']; ?></td>
			<td><?php echo $purchasedReceipt['is_uploaded']; ?></td>
			<td><?php echo $purchasedReceipt['nationality']; ?></td>
			<td><?php echo $purchasedReceipt['address']; ?></td>
			<td><?php echo $purchasedReceipt['passport_number']; ?></td>
			<td><?php echo $purchasedReceipt['user_id']; ?></td>
			<td><?php echo $purchasedReceipt['name']; ?></td>
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
