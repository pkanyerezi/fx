<?php echo $this->Html->script(array('script_dynamic_content'));?>
<div class="flash-message"><?php echo $this->Session->flash(); ?></div>
<div class="purchasedReceipts view well">
<h2><?php  echo __('Deleted Purchase Receipt'); ?></h2>
	<dl>
		<dt><?php echo __('Receipt No.'); ?></dt>
		<dd>
			<?php echo h($deletedPurchasedReceipt['DeletedPurchasedReceipt']['id']); ?>
			&nbsp;
		</dd>
		<dt><?php echo __('Source of funds'); ?></dt>
		<dd>
			<?php echo $deletedPurchasedReceipt['PurchasedPurpose']['description']; ?>
			&nbsp;
		</dd>
		<dt><?php echo __('Amount'); ?></dt>
		<dd>
			<span class="ln"><?php if($deletedPurchasedReceipt['Currency']['id']=='c8'): ?>
				<?php echo h($deletedPurchasedReceipt['DeletedPurchasedReceipt']['orig_amount']); ?>
			<?php else: ?>
				<?php echo h($deletedPurchasedReceipt['DeletedPurchasedReceipt']['amount']); ?>
			<?php endif; ?></span>		
			&nbsp;
		</dd>
		
		<dt><?php echo __('Rate'); ?></dt>
		<dd>
			<span class="ln"><?php if($deletedPurchasedReceipt['Currency']['id']=='c8'): ?>
				<?php echo h($deletedPurchasedReceipt['DeletedPurchasedReceipt']['orig_rate']); ?>
			<?php else: ?>
				<?php echo h($deletedPurchasedReceipt['DeletedPurchasedReceipt']['rate']); ?>
			<?php endif; ?></span>		
			&nbsp;
		</dd>
		<dt><?php echo __('Amount Ugx'); ?></dt>
		<dd>
			<span class="ln"><?php echo h($deletedPurchasedReceipt['DeletedPurchasedReceipt']['amount_ugx']); ?></span>
			&nbsp;
		</dd>
		<dt><?php echo __('Currency'); ?></dt>
		<dd>
			<?php if($deletedPurchasedReceipt['Currency']['id']=='c8'): ?>
				<?php echo $deletedPurchasedReceipt['DeletedPurchasedReceipt']['other_name']; ?>
			<?php else: ?>
				<?php echo $deletedPurchasedReceipt['Currency']['description']; ?>
			<?php endif; ?>
			&nbsp;
		</dd>
		<dt><?php echo __('Date'); ?></dt>
		<dd>
			<?php echo h($deletedPurchasedReceipt['DeletedPurchasedReceipt']['date']); ?>
			&nbsp;
		</dd>
		<dt><?php echo __('Customer Name'); ?></dt>
		<dd>
			<?php echo h($deletedPurchasedReceipt['DeletedPurchasedReceipt']['customer_name']); ?>
			&nbsp;
		</dd>
		<dt><?php echo __('Nationality'); ?></dt>
		<dd>
			<?php echo h($deletedPurchasedReceipt['DeletedPurchasedReceipt']['nationality']); ?>
			&nbsp;
		</dd>
		<dt><?php echo __('Passport Number'); ?></dt>
		<dd>
			<?php echo h($deletedPurchasedReceipt['DeletedPurchasedReceipt']['passport_number']); ?>
			&nbsp;
		</dd>
		<dt><?php echo __('Address'); ?></dt>
		<dd>
			<?php echo h($deletedPurchasedReceipt['DeletedPurchasedReceipt']['address']); ?>
			&nbsp;
		</dd>
	</dl>
</div>
<div class="actions">
	<h3><?php echo __(''); ?></h3>
	<ul>
		<li><?php echo $this->Html->link(__('List Deleted Purchase Receipt'), array('action' => 'index')); ?> </li>
	</ul>
</div>
