<?php echo $this->Html->script(array('script_dynamic_content'));?>
<div class="flash-message"><?php echo $this->Session->flash(); ?></div>
<div class="soldReceipts view well">
<h2><?php  echo __('Sales Receipt'); ?></h2>
	<dl>
		<dt><?php echo __('Receipt No.'); ?></dt>
		<dd>
			<?php echo h($soldReceipt['SoldReceipt']['id']); ?>
			&nbsp;
		</dd>
		<dt><?php echo __('Purpose'); ?></dt>
		<dd>
			<?php echo $soldReceipt['Purpose']['description']; ?>
			&nbsp;
		</dd>		
		<dt><?php echo __('Amount'); ?></dt>
		<dd>
			<span class="ln"><?php if($soldReceipt['Currency']['id']=='c8'): ?>
				<?php echo h($soldReceipt['SoldReceipt']['orig_amount']); ?>
			<?php else: ?>
				<?php echo h($soldReceipt['SoldReceipt']['amount']); ?>
			<?php endif; ?>	</span>
			&nbsp;
		</dd>		
		<dt><?php echo __('Rate'); ?></dt>
		<dd>
			<span class="ln"><?php if($soldReceipt['Currency']['id']=='c8'): ?>
				<?php echo h($soldReceipt['SoldReceipt']['orig_rate']); ?>
			<?php else: ?>
				<?php echo h($soldReceipt['SoldReceipt']['rate']); ?>
			<?php endif; ?></span>
			&nbsp;
		</dd>
		<dt><?php echo __('Amount Ugx'); ?></dt>
		<dd>
			<span class="ln"><?php echo h($soldReceipt['SoldReceipt']['amount_ugx']); ?></span>
			&nbsp;
		</dd>
		<dt><?php echo __('Currency'); ?></dt>
		<dd>
			<?php if($soldReceipt['Currency']['id']=='c8'): ?>
				<?php echo $soldReceipt['SoldReceipt']['other_name']; ?>
			<?php else: ?>
				<?php echo $soldReceipt['Currency']['description']; ?>
			<?php endif; ?>
			&nbsp;
		</dd>
		<dt><?php echo __('Instrument'); ?></dt>
		<dd>
			<?php echo h($soldReceipt['SoldReceipt']['instrument']); ?>
			&nbsp;
		</dd>
		<dt><?php echo __('Date'); ?></dt>
		<dd>
			<?php echo h($soldReceipt['SoldReceipt']['date']); ?>
			&nbsp;
		</dd>
		<dt><?php echo __('Customer Name'); ?></dt>
		<dd>
			<?php echo h($soldReceipt['SoldReceipt']['customer_name']); ?>
			&nbsp;
		</dd>
		<dt><?php echo __('Nationality'); ?></dt>
		<dd>
			<?php echo h($soldReceipt['SoldReceipt']['nationality']); ?>
			&nbsp;
		</dd>
		<dt><?php echo __('Passport Number'); ?></dt>
		<dd>
			<?php echo h($soldReceipt['SoldReceipt']['passport_number']); ?>
			&nbsp;
		</dd>
		<dt><?php echo __('Address'); ?></dt>
		<dd>
			<?php echo h($soldReceipt['SoldReceipt']['address']); ?>
			&nbsp;
		</dd>
	</dl>
</div>
<div class="actions">
	<h3><?php echo __(''); ?></h3>
	<ul>
		<?php if($super_admin):?>
			<li><?php echo $this->Html->link(__('Edit Receipt'), array('action' => 'edit',$soldReceipt['SoldReceipt']['id'])); ?> </li>
		<?php endif; ?>
		<li><?php echo $this->Html->link(__('List Sales Receipts'), array('action' => 'index')); ?> </li>
		<li><?php echo $this->Html->link(__('New Sales Receipt'), array('action' => 'add')); ?> </li>
	</ul>
</div>
