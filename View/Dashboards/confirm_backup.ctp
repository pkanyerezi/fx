<?php echo $this->Html->script(array('script_dynamic_content'));?>
<div class="flash-message"><?php echo $this->Session->flash(); ?></div>
<div class="well">
	<?php if(file_exists(APP . 'webroot' . DS . 'fx' . DS . 'Backups' . DS . 'db.sql.zip')): ?>
		<br><br>
		<div class="alert alert-info">NB. If you have done a system restore, Please download the previous backup file first</div>
		<a class="no-ajax" href="<?php echo $this->webroot.'fx/Backups/db.sql.zip'; ?>" target="_blank"><span class="btn btn-primary"><i class="icon-white icon-download"></i> download previous backup</span></a>
	<?php endif;?>
	<a href="<?php echo $this->webroot.'dashboards/backup'; ?>" class="confirm-first btn btn-danger" data-confirm-text="Warning!! The old backup will be replace if it exists!"><span><i class="icon-white icon-share"></i> backup data</span></a>
</div>
