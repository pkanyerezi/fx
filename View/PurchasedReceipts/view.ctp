<?php echo $this->Html->script(array('script_dynamic_content'));?>
<div class="flash-message"><?php echo $this->Session->flash(); ?></div>
<div class="purchasedReceipts view well">
<h2><?php  echo __('Purchase Receipt'); ?></h2>
	<dl>
		<dt><?php echo __('Receipt No.'); ?></dt>
		<dd>
			<?php echo h($purchasedReceipt['PurchasedReceipt']['id']); ?>
			&nbsp;
		</dd>
		<dt><?php echo __('Source of funds'); ?></dt>
		<dd>
			<?php echo $purchasedReceipt['PurchasedPurpose']['description']; ?>
			&nbsp;
		</dd>
		<dt><?php echo __('Amount'); ?></dt>
		<dd>
			<span class="ln"><?php if($purchasedReceipt['Currency']['id']=='c8'): ?>
				<?php echo h($purchasedReceipt['PurchasedReceipt']['orig_amount']); ?>
			<?php else: ?>
				<?php echo h($purchasedReceipt['PurchasedReceipt']['amount']); ?>
			<?php endif; ?></span>		
			&nbsp;
		</dd>
		
		<dt><?php echo __('Rate'); ?></dt>
		<dd>
			<span class="ln"><?php if($purchasedReceipt['Currency']['id']=='c8'): ?>
				<?php echo h($purchasedReceipt['PurchasedReceipt']['orig_rate']); ?>
			<?php else: ?>
				<?php echo h($purchasedReceipt['PurchasedReceipt']['rate']); ?>
			<?php endif; ?></span>		
			&nbsp;
		</dd>
		<dt><?php echo __('Amount Ugx'); ?></dt>
		<dd>
			<span class="ln"><?php echo h($purchasedReceipt['PurchasedReceipt']['amount_ugx']); ?></span>
			&nbsp;
		</dd>
		<dt><?php echo __('Currency'); ?></dt>
		<dd>
			<?php if($purchasedReceipt['Currency']['id']=='c8'): ?>
				<?php echo $purchasedReceipt['PurchasedReceipt']['other_name']; ?>
			<?php else: ?>
				<?php echo $purchasedReceipt['Currency']['description']; ?>
			<?php endif; ?>
			&nbsp;
		</dd>
		<dt><?php echo __('Date'); ?></dt>
		<dd>
			<?php echo h($purchasedReceipt['PurchasedReceipt']['date']); ?>
			&nbsp;
		</dd>
		<dt><?php echo __('Customer Name'); ?></dt>
		<dd>
			<?php echo h($purchasedReceipt['PurchasedReceipt']['customer_name']); ?>
			&nbsp;
		</dd>
		<dt><?php echo __('Nationality'); ?></dt>
		<dd>
			<?php echo h($purchasedReceipt['PurchasedReceipt']['nationality']); ?>
			&nbsp;
		</dd>
		<dt><?php echo __('Passport Number'); ?></dt>
		<dd>
			<?php echo h($purchasedReceipt['PurchasedReceipt']['passport_number']); ?>
			&nbsp;
		</dd>
		<dt><?php echo __('Address'); ?></dt>
		<dd>
			<?php echo h($purchasedReceipt['PurchasedReceipt']['address']); ?>
			&nbsp;
		</dd>
		<dt><?php echo __('Phone'); ?></dt>
		<dd>
			<?php echo h($purchasedReceipt['PurchasedReceipt']['phone_number']); ?>
			&nbsp;
		</dd>
	</dl>
</div>
<div class="actions">
	<h3><?php echo __(''); ?></h3>
	<ul>
		<?php if($authUser['can_edit_receipt']): ?>
			<li><?php echo $this->Html->link(__('Edit Receipt'), array('action' => 'edit',$purchasedReceipt['PurchasedReceipt']['id'])); ?> </li>
		<?php endif; ?>
		<li><?php echo $this->Html->link(__('List Purchase Receipt'), array('action' => 'index')); ?> </li>
		<li><?php echo $this->Html->link(__('New Purchase Receipt'), array('action' => 'add')); ?> </li>
	</ul>
</div>
