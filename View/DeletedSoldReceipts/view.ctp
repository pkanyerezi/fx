<?php echo $this->Html->script(array('script_dynamic_content'));?>
<div class="flash-message"><?php echo $this->Session->flash(); ?></div>
<div class="deletedSoldReceipts view well">
<h2><?php  echo __('Sales Receipt'); ?></h2>
	<dl>
		<dt><?php echo __('Receipt No.'); ?></dt>
		<dd>
			<?php echo h($deletedSoldReceipt['DeletedSoldReceipt']['id']); ?>
			&nbsp;
		</dd>
		<dt><?php echo __('Purpose'); ?></dt>
		<dd>
			<?php echo $deletedSoldReceipt['Purpose']['description']; ?>
			&nbsp;
		</dd>		
		<dt><?php echo __('Amount'); ?></dt>
		<dd>
			<span class="ln"><?php if($deletedSoldReceipt['Currency']['id']=='c8'): ?>
				<?php echo h($deletedSoldReceipt['DeletedSoldReceipt']['orig_amount']); ?>
			<?php else: ?>
				<?php echo h($deletedSoldReceipt['DeletedSoldReceipt']['amount']); ?>
			<?php endif; ?>	</span>
			&nbsp;
		</dd>		
		<dt><?php echo __('Rate'); ?></dt>
		<dd>
			<span class="ln"><?php if($deletedSoldReceipt['Currency']['id']=='c8'): ?>
				<?php echo h($deletedSoldReceipt['DeletedSoldReceipt']['orig_rate']); ?>
			<?php else: ?>
				<?php echo h($deletedSoldReceipt['DeletedSoldReceipt']['rate']); ?>
			<?php endif; ?></span>
			&nbsp;
		</dd>
		<dt><?php echo __('Amount Ugx'); ?></dt>
		<dd>
			<span class="ln"><?php echo h($deletedSoldReceipt['DeletedSoldReceipt']['amount_ugx']); ?></span>
			&nbsp;
		</dd>
		<dt><?php echo __('Currency'); ?></dt>
		<dd>
			<?php if($deletedSoldReceipt['Currency']['id']=='c8'): ?>
				<?php echo $deletedSoldReceipt['DeletedSoldReceipt']['other_name']; ?>
			<?php else: ?>
				<?php echo $deletedSoldReceipt['Currency']['description']; ?>
			<?php endif; ?>
			&nbsp;
		</dd>
		<dt><?php echo __('Instrument'); ?></dt>
		<dd>
			<?php echo h($deletedSoldReceipt['DeletedSoldReceipt']['instrument']); ?>
			&nbsp;
		</dd>
		<dt><?php echo __('Date'); ?></dt>
		<dd>
			<?php echo h($deletedSoldReceipt['DeletedSoldReceipt']['date']); ?>
			&nbsp;
		</dd>
		<dt><?php echo __('Customer Name'); ?></dt>
		<dd>
			<?php echo h($deletedSoldReceipt['DeletedSoldReceipt']['customer_name']); ?>
			&nbsp;
		</dd>
		<dt><?php echo __('Nationality'); ?></dt>
		<dd>
			<?php echo h($deletedSoldReceipt['DeletedSoldReceipt']['nationality']); ?>
			&nbsp;
		</dd>
		<dt><?php echo __('Passport Number'); ?></dt>
		<dd>
			<?php echo h($deletedSoldReceipt['DeletedSoldReceipt']['passport_number']); ?>
			&nbsp;
		</dd>
		<dt><?php echo __('Address'); ?></dt>
		<dd>
			<?php echo h($deletedSoldReceipt['DeletedSoldReceipt']['address']); ?>
			&nbsp;
		</dd>
	</dl>
</div>
<div class="actions">
	<h3><?php echo __(''); ?></h3>
	<ul>
		<li><?php echo $this->Html->link(__('List Deleted Sales Receipts'), array('action' => 'index')); ?> </li>
	</ul>
</div>
