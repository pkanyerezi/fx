<?php echo $this->Html->script(array('script_dynamic_content'));?>
<div class="flash-message"><?php echo $this->Session->flash(); ?></div>
<div class="form" style="border-left:none;margin-top: -56px;">
	<style>
		<!--
			.row{margin-left:10px;}
			label{font-weight:bold;}
			.openings div {clear: both;margin-bottom: -23px;padding: .7em;vertical-align: text-top;}
			.form-control{width:100%;}
		-->
	</style>
	<div class="widget-box">
		<div class="widget-title">
			<h5 style="color:maroon;">Edit Report Type</h5>
		</div>
		<div>
			<?php echo $this->Form->create('ReportType'); ?>
				<fieldset>
					<?php echo $this->Form->input('id'); ?>
					<div class="row">
						<span class="span12"><?php echo $this->Form->input('name',['class'=>'form-control','readonly'=>'true']); ?></span>
					</div>
					<div class="row">
						<span class="span12"><?php echo $this->Form->input('enabled',['class'=>'form-control']); ?></span>
					</div>
				</fieldset>
			<?php echo $this->Form->end(__('Submit')); ?>
		</div>
	</div>
</div>

<div class="actions">
	<h3><?php echo __('Actions'); ?></h3>
	<ul>
		<li><?php echo $this->Html->link(__('List'), array('action' => 'index')); ?></li>
	</ul>
</div>