<?php echo $this->Html->script(array('script_dynamic_content'));?>
<div class="flash-message"><?php echo $this->Session->flash(); ?></div>
<div class="form" style="border-left:none;margin-top: -56px;">
	<style>
		<!--
			.row{margin-left:10px;}
			label{font-weight:bold;}
			.openings div {clear: both;margin-bottom: -23px;padding: .7em;vertical-align: text-top;}
			.form-control{width:100%;}
			#ReportNotificationEmailStartAtMonth{width:127px;}
			#ReportNotificationEmailStartAtDay{width:60px;}
			#ReportNotificationEmailStartAtHour,#ReportNotificationEmailStartAtMin,#ReportNotificationEmailStartAtMeridian{width:127px;}
		-->
	</style>
	<div class="widget-box">
		<div class="widget-title">
			<h5 style="color:maroon;">Edit Report Notification Email</h5>
		</div>
		<div>
			<?php echo $this->Form->create('ReportNotificationEmail'); ?>
				<fieldset>
					<?php echo $this->Form->input('id'); ?>
					<div class="row">
						<span class="span6"><?php echo $this->Form->input('report_type_id',['class'=>'form-control']); ?></span>
						<span class="span6"><?php echo $this->Form->input('name',['class'=>'form-control']); ?></span>
					</div>
					<div class="row">
						<span class="span12"><?php echo $this->Form->input('description',['label'=>'Description/Comment','class'=>'form-control']); ?></span>
					</div>
					<div class="row>
						<span class="span12"><?php echo $this->Form->input('emails',['label'=>'Emails(Comma seperated)','class'=>'form-control']); ?></span>
					</div>
					<div class="row">
						<span class="span6"><?php echo $this->Form->input('records_time_ago_number',['label'=>'Data Ranges(Time) from','class'=>'form-control']); ?></span>
						<?php $records_time_ago_options = [
							'Days'=>'Days',
							'Weeks'=>'Weeks',
							'Month'=>'Month',
							'Year'=>'Year'
						];?>
						<span class="span6"><?php echo $this->Form->input('records_time_ago_type',['label'=>'.','options'=>$records_time_ago_options,'class'=>'form-control']); ?></span>
					</div>
					<div class="row">
						<span class="span6"><?php echo $this->Form->input('frequency_number',['label'=>'Repeat/Send Every After','class'=>'form-control']); ?></span>
						<?php $frequency_type_options = [
							'Minutes'=>'Minutes',
							'Hours'=>'Hours',
							'Days'=>'Days',
							'Weeks'=>'Weeks',
							'Month'=>'Month'
						];?>
						<span class="span6"><?php echo $this->Form->input('frequency_type',['label'=>'.','options'=>$frequency_type_options,'class'=>'form-control']); ?></span>
					</div>
					<div class="row">
						<span class="span2"><?php echo $this->Form->input('recursive',['label'=>'Repeating','class'=>'form-control']); ?></span>
						<span class="span2"><?php echo $this->Form->input('enabled',['class'=>'form-control']); ?></span>
						<span class="span8"><?php echo $this->Form->input('start_at',['class'=>'']); ?></span>
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