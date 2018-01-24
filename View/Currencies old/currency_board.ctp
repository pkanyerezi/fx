<?php echo $this->Html->script(array('script_dynamic_content'));?>
<div class="flash-message"><?php echo $this->Session->flash(); ?></div>
<?php if($super_admin):?>
<center>
	<div class="actions" style="width: 100%;">
		<ul>
			<li><a class="no-ajax" href="http://localhost/fx/webroot/rates" target="_blank">Screen Currency Board</a></li>
		</ul>
	</div>
</center>
<?php endif;?>
<div class="currencies">
	<center>
		<?=$this->Form->create('Currency');?>
			<div class="wooden_bg">
				<table>
					<tr>
						<td>&nbsp;</td>
						<td style="font-weight:bold;font-size:14px;"><i class="icon icon-arrow-down"></i>Buy</td>
						<td style="font-weight:bold;font-size:14px;"><i class="icon icon-arrow-up"></i>Sell</td>
					</tr>
				<?php foreach($currencies as $currency): ?>
					<?php if(in_array($currency['Currency']['id'],array('c00','c8'))) continue;?>
					<tr style="border-bottom:1px solid #555;">
						<td style="vertical-align: middle;font-weight:bold;font-size:14px;"><?php echo $currency['Currency']['id'];?></td>
						<td style="vertical-align: middle;"><?php echo $this->Form->input('',array('name'=>'data[Currency][buy]['.($currency['Currency']['id']).']','readonly'=>($authUser['role']!='super_admin'),'label'=>'','type'=>'text','value'=>$currency['Currency']['buy'])); ?></td>
						<td style="vertical-align: middle;"><?php echo $this->Form->input('',array('name'=>'data[Currency][sell]['.($currency['Currency']['id']).']','readonly'=>($authUser['role']!='super_admin'),'label'=>'','type'=>'text','value'=>$currency['Currency']['sell'])); ?></td>
					</tr>
				<?php endforeach; ?>
				</table>
			</div>
			<?php if($super_admin): ?>
				<?php echo $this->Form->end(__('Submit')); ?>
			<?php endif;?>
		</form>
	</center>
</div>
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