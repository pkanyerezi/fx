<?php echo $this->Html->script(array('script_dynamic_content'));?>
<div class="flash-message"><?php echo $this->Session->flash(); ?></div>
<?php if(isset($days)): ?>
	<a href="<?php echo $this->webroot.'dashboards/backup/'.($fox_id); ?>"><span class="btn btn-danger"><i class="icon-white icon-share"></i> backup data</span></a>	
<?php else: ?>
	<?php if($super_admin): ?>
		<a class="no-ajax" href="<?php echo $this->webroot.'fx/Backups/db.sql.zip'; ?>" target="_blank"><span class="btn btn-primary"><i class="icon-white icon-download"></i> download backup file</span></a>

		<a href="<?php echo $this->webroot.'dashboards/backup/0'; ?>"><span class="btn btn-danger"><i class="icon-white icon-share"></i> Re-Backup data</span></a>
	<?php endif; ?>
<?php endif; ?>
<div class="well alert alert-info">
<div style="text-align:center;">
=============
<h4>Main Notes (At your finger tips)</h4>
=============<br>
</div>
<div style="text-align:center;" class="alert alert-success">
	<h2>Receipts</h2>
</div>
	<h3>Adding receipts</h3>
	<h6 style="font-weight:normal">1. If you are printing them, make sure the receipt server is running. You can test this by listing either existing purchase/sales
	receipts and try to print one of it by selecting "Print" from the drop down menu beside it. If your printer is active and the receipt server 
	is up and running, the printer should print put the receipts.</h6>
	<h5>NB:</h5>
	i).  Make sure the receipt server is running in Mozila firefox to auto print the receipt.<br>
	ii). The system should be opened in a separate from the receipt server for clean results.<br>
	<br>
	<h6 style="font-weight:normal">2. Incase you are adding receipts and dont want to print them, there's a "Don't Print" button at the bottom of the form to select.</h6>
	
	<h3>Listing receipts</h3>
	<h6 style="font-weight:normal">3. Want to view only USD receipts and their total amount both in UGX and its own,
	simply list the receipts and click a link USD from the list on any receipt listed.</h6>
	
	<h6 style="font-weight:normal">4.Want to sort the receipts by any column, simply click the heading/title of the column. 
	When you click the title for the first time, the records will be sorted by the title in ASCENDING order
	and in DESCENDING or if you click for the second time and in ASCENDING order again if you click for the third time.
	</h6>
<div style="text-align:center;" class="alert alert-success">
	<h2>Closing Stock</h2>
</div>
	<h3>Saving for next working day</h3>
	<h6 style="font-weight:normal">1. Make sure you select the correct date you will open again or start working again. eg.
	If today is Monday and you are going to work again on Wednesday. Don't select Tuesday as the next opening date 
	but rather select Wednesday</h6>
	
	<h3>OnError</h3>
	<h6 style="font-weight:normal">2. If You make a mistake or if you select the next opening date as Tuesday instead of Wednesday,
	then you have to select the "Today" date as Monday(the day you closed) and click 
		"Individual balance Positions" then > "Individual Balancing" from the menu next to the name of the user account
		then scroll down and re-save it again but this time select the correct date which is Wednesday.</h6>
	<br>
	Bang!! You are back on track<br>
</div>

