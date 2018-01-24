<?php echo $this->Html->script(array('script_dynamic_content'));?>
<div class="flash-message"><?php echo $this->Session->flash(); ?></div>
<style type="text/css">
	tbody td:nth-child(4n+1),
	tbody td:nth-child(4n),
	thead th:nth-child(4n+1),
	thead th:nth-child(4n){
	  background:rgba(255,255,136,0.5);
	}

	tbody td:nth-child(1),
	thead th:nth-child(1),
	tbody td:nth-child(2),
	thead th:nth-child(2),
	tbody td:nth-child(3),
	thead th:nth-child(3),
	tbody td:nth-child(4),
	thead th:nth-child(4)
	{
		background:inherit;
	}
</style>
<div class="" style="border-left:none;margin-top: -56px;">
	<div class="widget-box">
		<div class="widget-title">
			<span class="icon">
				<i class="icon-align-justify"></i>									
			</span>
			<h5 style="color:maroon;">Openings</h5>
		</div>
		<div class="openings widget-content nopadding">
			<div class="actions">
				<ul>
					<li>
						<a href="<?php echo $this->webroot;?>openings/add"><i class="icon icon-plus-sign"></i> New Opening</a>
					</li>
				</ul>
			</div>
			<table cellpadding="0" cellspacing="0" class="well">
			<thead>
			<tr>
					<?php if($super_admin):?> <th></th> <?php endif; ?>
					<th></th>
					<th>Cashier</th>
					<th>Date</th>
					<th>Opening UGX</th>
					<?php foreach($currencies as $currency):?>
						<th><?=strtoupper($currency['Currency']['id']).'<br>Amount'; ?></th>
						<th><?=strtoupper($currency['Currency']['id']).'<br>Rate'; ?></th>
					<?php endforeach;?>
					<th class="actions"><?php echo __(''); ?></th>
			</tr>
			</thead>
			<?php foreach ($openings as $opening): ?>
			<tr>
				<?php if($super_admin):?>
				<td>
					<div class="btn-group">
						<button class="btn dropdown-toggle" data-toggle="dropdown">
						  <i class="icon icon-certificate"></i>
						  <span class="caret"></span>
						</button>
						<ul class="dropdown-menu">
							<?php if(!$opening['Opening']['status']):?>													
									<li>
										<?php echo $this->Html->link(__('Edit'), array('action' => 'edit', $opening['Opening']['id'],$opening['Opening']['user_id'])); ?>
									</li>						
								<li class="divider"></li>
							<?php endif;?>
							<li><?php echo $this->Html->link(__('Delete'), array('action' => 'delete', $opening['Opening']['id']),array('class'=>'action-delete confirm-first','data-confirm-text'=>'Are you sure you want to delete it?')); ?></li>					
						</ul>
					</div>
				</td>
				<?php endif; ?>	
				
				<td>
					<?php if($opening['Opening']['status']): ?>
						<?php echo $this->Html->image('test-pass-icon.png',array('style'=>'max-width: 15px;')); ?>
					<?php else: ?>
						<?php echo $this->Html->image('test-fail-icon.png',array('style'=>'max-width: 15px;')); ?>
					<?php endif; ?>&nbsp;
				</td>
				<td>
					<?php echo $this->Html->link($opening['User']['name'], array('controller' => 'users', 'action' => 'view', $opening['User']['id'])); ?>
				</td>
				<td><?php echo h($opening['Opening']['date']); ?>&nbsp;</td>
				<td><span class="ln"><?php echo h($opening['Opening']['opening_ugx']); ?></span>&nbsp;</td>

				<?php $currency_details = json_decode($opening['OpeningDetail']['currency_details'],true);?>
				<?php foreach($currencies as $currency):?>
					<td><span><?php @$v=$currency_details[$currency['Currency']['id']]['CAMOUNT']; echo $v; ?></span></td>
					<td><span class="ln"><?php @$v=$currency_details[$currency['Currency']['id']]['CRATE']; echo $v; ?></span></td>
				<?php endforeach;?>
				
				<?php if($super_admin):?>
				<td class="actions">
					<?php echo $this->Html->link(__('Edit'), array('action' => 'edit', $opening['Opening']['id'],$opening['Opening']['user_id'])); ?>
					<?php echo $this->Html->link(__('Delete'), array('action' => 'delete', $opening['Opening']['id']),array('class'=>'action-delete confirm-first','data-confirm-text'=>'Are you sure you want to delete it?')); ?>
				</td>
				<?php endif; ?>
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
	</div>
</div>