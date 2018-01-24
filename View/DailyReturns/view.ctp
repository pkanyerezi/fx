<?php echo $this->Html->script(array('script_dynamic_content'));?>
<div class="flash-message"><?php echo $this->Session->flash(); ?></div>
<div class="dailyReturns view well well-new-small">
<h2><?php  echo __('Daily Return'); ?></h2>
	<dl>		
		<dt><?php echo __('Date'); ?></dt>
		<dd>
			<?php echo h($dailyReturn['DailyReturn']['date']); ?>
			&nbsp;
		</dd>		
	</dl><hr/>
	<?php if(!empty($dailyReturn)): ?>
		<table>
			<tr>
				<td><b>Currency</b></td><td><b>Buying</b></td><td><b>Selling</b></td>
			</tr>
			<tr>
				<td>USD</td><td><?php echo h($dailyReturn['DailyBuyingReturn']['c1']); ?></td><td><?php echo h($dailyReturn['DailySellingReturn']['c1']); ?></td>			
			</tr>
			<tr>	
				<td>Euro</td><td><?php echo h($dailyReturn['DailyBuyingReturn']['c2']); ?></td><td><?php echo h($dailyReturn['DailySellingReturn']['c2']); ?></td>
			</tr>
			<tr>
				<td>GBP</td><td><?php echo h($dailyReturn['DailyBuyingReturn']['c3']); ?></td><td><?php echo h($dailyReturn['DailySellingReturn']['c3']); ?></td>
			</tr>
			<tr>
				<td>Kshs</td><td><?php echo h($dailyReturn['DailyBuyingReturn']['c4']); ?></td><td><?php echo h($dailyReturn['DailySellingReturn']['c4']); ?></td>
			</tr>
			<tr>
				<td>Tzshs</td><td><?php echo h($dailyReturn['DailyBuyingReturn']['c5']); ?></td><td><?php echo h($dailyReturn['DailySellingReturn']['c5']); ?></td>
			</tr>
			<tr>
				<td>SAR</td><td><?php echo h($dailyReturn['DailyBuyingReturn']['c6']); ?></td><td><?php echo h($dailyReturn['DailySellingReturn']['c6']); ?></td>
			</tr>
			<tr>
				<td>SP</td><td><?php echo h($dailyReturn['DailyBuyingReturn']['c7']); ?></td><td><?php echo h($dailyReturn['DailySellingReturn']['c7']); ?></td>
			</tr>
			<tr>
				<td>Others</td><td><?php echo h($dailyReturn['DailyBuyingReturn']['c8']); ?></td><td><?php echo h($dailyReturn['DailySellingReturn']['c8']); ?></td>
			</tr>
			
		</table>
	<?php endif; ?>
	
</div>
<div class="actions">
	<ul>
		<li><?php echo $this->Html->link(__('List Daily Returns'), array('action' => 'index')); ?> </li>
	</ul>
</div>
