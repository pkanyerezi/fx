<?php echo $this->Html->script(array('script_dynamic_content'));?>
<div class="flash-message"><?php echo $this->Session->flash(); ?></div>
<?php if($super_admin): ?>
	<?php if(file_exists(APP . 'webroot' . DS . 'fx' . DS . 'Backups' . DS . 'db.sql.zip')): ?>
		<a class="no-ajax" href="<?php echo $this->webroot.'fx/Backups/db.sql.zip'; ?>" target="_blank"><span class="btn btn-primary"><i class="icon-white icon-download"></i> download backup file</span></a>
	<?php else:?>
		<div class="alert alert-error">Error:Backup was not created.</div>
	<?php endif; ?>
<?php endif; ?>