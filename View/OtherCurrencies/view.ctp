<?php echo $this->Html->script(array('script_dynamic_content'));?>
<div class="flash-message"><?php echo $this->Session->flash(); ?></div>
<div class="otherCurrencies view well">
<h2><?php  echo __('Other Currency'); ?></h2>
	<dl>
		<dt><?php echo __('Id'); ?></dt>
		<dd>
			<?php echo h($otherCurrency['OtherCurrency']['id']); ?>
			&nbsp;
		</dd>
		<dt><?php echo __('Name'); ?></dt>
		<dd>
			<?php echo h($otherCurrency['OtherCurrency']['name']); ?>
			&nbsp;
		</dd>
		<dt><?php echo __('Description'); ?></dt>
		<dd>
			<?php echo h($otherCurrency['OtherCurrency']['description']); ?>
			&nbsp;
		</dd>
	</dl>
</div>