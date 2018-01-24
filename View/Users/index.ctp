<?php echo $this->Html->script(array('script_dynamic_content'));?>
<div class="flash-message"><?php echo $this->Session->flash(); ?></div>
<?php if($super_admin): ?>
<div class="actions">
	<h3><?php echo __(''); ?></h3>
	<ul>
		<?php if($userrole=='customers'):?>
			<li><?php echo $this->Html->link(__('Add Customer/Bank'), array('action' => 'add_customers')); ?></li>
		<?php else:?>
			<li><?php echo $this->Html->link(__('Add Cashier/Admin'), array('action' => 'add')); ?></li>
		<?php endif;?>
	</ul>
</div>
<?php endif; ?><br/>
<p>
<div class="users well">
	<?php if($userrole=='customers'):?>
			<h2><?php echo __('Customers/Banks'); ?></h2>
		<?php else:?>
			<h2><?php echo __('Cashiers/Admins'); ?></h2>
		<?php endif;?>
	<table cellpadding="0" cellspacing="0">
	<tr>
			<th class="actions"><?php echo __(''); ?></th>
			<th><?php echo $this->Paginator->sort('name'); ?></th>
			<?php if($the_role!='customers'):?>
				<th><?php echo $this->Paginator->sort('username'); ?></th>
			<?php endif;?>
			<?php if($the_role=='customers'):?>
				<th>Credited</th>
				<th>Debited</th>
				<th>Deposited</th>
				<th>Withdrawn</th>
				<th>(Total)</th>
			<?php endif;?>
			<th><?php echo $this->Paginator->sort('email','Phone'); ?></th>
			<th><?php echo $this->Paginator->sort('role'); ?></th>			
	</tr>
	<?php foreach ($users as $user): ?>
	<tr>
		<td class="actions">		
			<div class="btn-group" style="margin-left:10%;">
				<button class="btn dropdown-toggle" data-toggle="dropdown">
				  <i class="icon icon-certificate"></i>&nbsp;<span class="caret"></span>
				</button>
				<ul class="dropdown-menu">
					<?php if($user['User']['role']=='customer'):?>
						<li><?php echo $this->Html->link('Edit',array('action' => 'edit_customers', $user['User']['id']),array('class'=>'use-ajax','style'=>'margin-left:10px;')); ?></li>
					<?php else:?>
						<li><?php echo $this->Html->link('Individual balancing',array('controller'=>'balancings','action'=>'show_individually',0,0,0,$user['User']['id']),array('class'=>'use-ajax','style'=>'margin-left:10px;')); ?></li>					
						<?php if($super_admin):?>
							<li><?php echo $this->Html->link('Currency Summary',array('controller'=>'reports','action'=>'currency_summary',$user['User']['id']),array('class'=>'use-ajax','style'=>'margin-left:10px;')); ?></li>
							<li><?php echo $this->Html->link('All Openings',array('controller'=>'openings','action'=>'index',$user['User']['id']),array('class'=>'use-ajax','style'=>'margin-left:10px;')); ?></li>					
						<?php endif;?>
						<li><?php echo $this->Html->link('View',array('action' => 'view', $user['User']['id']),array('class'=>'use-ajax','style'=>'margin-left:10px;')); ?></li>
						<li><?php echo $this->Html->link('Edit',array('action' => 'settings', $user['User']['id']),array('class'=>'use-ajax','style'=>'margin-left:10px;')); ?></li>
					<?php endif; ?>
					<li class="divider"></li>					
					<li><?php echo $this->Html->link(__('Delete'), array('action' => 'delete', $user['User']['id']), array('class'=>'confirm-first use-ajax','style'=>'margin-left:10px;','data-confirm-text'=>__('Are you sure you want to delete # %s?', $user['User']['id']))); ?></li>
				</ul>												
			</div>			
		</td>
		<td><a href="<?php echo $this->webroot;?>users/view/<?php echo h($user['User']['id']);?>"><?php echo h($user['User']['name']); ?></a>&nbsp;</td>
		<?php if($the_role!='customers'):?>
		<td>
			
				<?php echo h($user['User']['username']); ?>
			
		&nbsp;</td>
		<?php endif; ?>			
		<?php if($the_role=='customers'):?>
			<td><span class="ln"><?php echo h($user['User']['total_credited']); ?></span></td>
			<td><span class="ln"><?php echo h($user['User']['total_debited']); ?></span></td>
			<td><span class="ln"><?php echo h($user['User']['total_deposited']); ?></span></td>
			<td><span class="ln"><?php echo h($user['User']['total_withdrawn']); ?></span></td>
			<?php $total = ($user['User']['total_credited']-$user['User']['total_debited']) + ($user['User']['total_deposited']-$user['User']['total_withdrawn']); ?>
			<td><span class="ln"><?php echo $total; ?></span></td>
		<?php endif;?>
		<td><?php echo h($user['User']['email']); ?>&nbsp;</td>
		<td>
			<?php if($user['User']['role']=='regular'): ?>
				<?php echo 'cashier';?>
			<?php else:?>
				<?php echo h($user['User']['role']); ?>
			<?php endif;?>
		&nbsp;</td>		
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
</p>