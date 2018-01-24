<style>
<!--
	.daily-returns tbody tr:first-child td {background:#2e335b;color:#fff;}
	.wooden_bg{background-image:url("<?php echo $this->webroot;?>img/wooden_bg.jpg");width:50%;border-radius:10px;};
	.daily-returns{width:50%;}
	table tr td {border-bottom: none;}
	table tr:nth-child(even) {background: none;}
	form div {clear: both;margin-bottom: -20px;padding: .5em;vertical-align: text-top;}
	.row{margin-left:10px;color:#fff;}
	.row span:first-child{margin-top: 20px;}
	
-->
</style>
<?php echo $this->Html->script(array('script_dynamic_content'));?>
<div class="flash-message"><?php echo $this->Session->flash(); ?></div>
<div class="dailyReturns" style="margin-top: -55px;">
<center>
<?php echo $this->Form->create('DailyReturn'); ?>
	<fieldset>
		<div class="wooden_bg">
			<div class="widget-title">
				<span class="icon">
					<i class="icon-retweet"></i>									
				</span>
				<h5>Daily Returns</h5>
			</div><hr/>
			<table>
				<tr>
					<td>&nbsp;</td>
					<td style="font-weight:bold;font-size:14px;"><i class="icon icon-arrow-down"></i>Buying</td>
					<td style="font-weight:bold;font-size:14px;"><i class="icon icon-arrow-up"></i>Selling</td>
				</tr>
			<?php foreach($currencies as $currency):
					if ($currency['Currency']['is_other_currency']) {
						$currency['Currency']['description'] = $currency['Currency']['id'];
					}
			 ?>
				<tr style="border-bottom:1px solid #555;">
					<td style="vertical-align: middle;font-weight:bold;font-size:14px;"><?php echo $currency['Currency']['description'];?></td>
					<td style="vertical-align: middle;"><?php echo $this->Form->input('',array('name'=>'data[DailyBuyingReturn]['.($currency['Currency']['id']).']','label'=>'','type'=>'text','value'=>0)); ?></td>
					<td style="vertical-align: middle;"><?php echo $this->Form->input('',array('name'=>'data[DailySellingReturn]['.($currency['Currency']['id']).']','label'=>'','type'=>'text','value'=>0)); ?></td>
				</tr>
			<?php endforeach; ?>
			</table>
			<?php echo $this->Form->end(__('Submit')); ?>
		</div>
	</fieldset>
</center>
</div>