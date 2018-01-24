<?php echo $this->Html->script(array('script_dynamic_content'));?>
<div class="flash-message"><?php echo $this->Session->flash(); ?></div>
<div id="use">
    <div class="users view well" style="text-align:left;border-radius: 10px;cursor:auto;">
        <div class="related">
            
            <ul class="nav nav-tabs" id="myTab">
                <li><a href="#a" class='no-ajax'><i class="icon-user"></i> Profile</a></li>
				<?php if($user['User']['role']=='regular' || $user['User']['role']=='super_admin'):?>
					<li><a href="#b" class='no-ajax'><i class="icon-file"></i> Safe</a></li>
					<li><a href="#c" class='no-ajax'><i class="icon-arrow-right"></i> Transfer</a></li>
				<?php endif;?>
            </ul>
                
            <div class="tab-content">
                <div id="a" class="tab-pane active">
                    <dl><?php $i = 0; $class = ' class="altrow"'; ?>
                        <dt<?php if ($i % 2 == 0)
                            echo $class; ?>><?php echo __('Name'); ?></dt>
                        <dd<?php if ($i++ % 2 == 0)
                                echo $class; ?>>
                                <?php echo $user['User']['name']; ?>
                            &nbsp;
                        </dd>

                        <dt<?php if ($i % 2 == 0)
                                    echo $class; ?>><?php echo __('Phone'); ?></dt>
                        <dd<?php if ($i++ % 2 == 0)
                                echo $class; ?>>
                                <?php echo $user['User']['email']; ?>
                            &nbsp;
                        </dd>

						<?php if($user['User']['role']!='customer'):?>
							<dt<?php if ($i % 2 == 0)
										echo $class; ?>><?php echo __('Username'); ?></dt>
							<dd<?php if ($i++ % 2 == 0)
									echo $class; ?>>
									<?php echo $user['User']['username']; ?>
								&nbsp;
							</dd>
						<?php else:?>
							<dt<?php if ($i % 2 == 0)
										echo $class; ?>><?php echo __('Identification'); ?></dt>
							<dd<?php if ($i++ % 2 == 0)
									echo $class; ?>>
									<?php echo $user['User']['identication_type']; ?> - <?php echo $user['User']['identication_number']; ?>
								&nbsp;
							</dd>

							<dt<?php if ($i % 2 == 0)
										echo $class; ?>><?php echo __('Address'); ?></dt>
							<dd<?php if ($i++ % 2 == 0)
									echo $class; ?>>
									<?php echo $user['User']['address']; ?>
								&nbsp;
							</dd>

							<dt<?php if ($i % 2 == 0)
										echo $class; ?>><?php echo __('Other Details'); ?></dt>
							<dd<?php if ($i++ % 2 == 0)
									echo $class; ?>>
									<?php echo $user['User']['other_details']; ?>
								&nbsp;
							</dd>
						<?php endif;?>
                        
						<?php if($user['User']['role']=='customer'): ?>
							<!--
							<dt<?php if ($i % 2 == 0)
										echo $class; ?>><?php echo __('Total Credit'); ?></dt>
							<dd<?php if ($i++ % 2 == 0)
									echo $class; ?>>UGX
									<?php echo $total_credit=(!isset($credit[0][0]['total_amount']))?0:$credit[0][0]['total_amount']; ?>
								&nbsp;
							</dd>
							
							<dt<?php if ($i % 2 == 0)
										echo $class; ?>><?php echo __('Total Debt'); ?></dt>
							<dd<?php if ($i++ % 2 == 0)
									echo $class; ?>>UGX
									<?php echo $total_debt=(!isset($debt[0][0]['total_amount']))?0:$debt[0][0]['total_amount']; ?>
								&nbsp;
							</dd>
							-->
							<dt<?php if ($i % 2 == 0)
										echo $class; ?>><?php echo __('Total Receivables'); ?></dt>
							<dd<?php if ($i++ % 2 == 0)
									echo $class; ?>>UGX
									<?php echo $total_receivable=(!isset($receivable[0][0]['total_amount']))?0:$receivable[0][0]['total_amount']; ?>
								&nbsp;
							</dd>
							<dt<?php if ($i % 2 == 0)
										echo $class; ?>><?php echo __('Total Withdrawal'); ?></dt>
							<dd<?php if ($i++ % 2 == 0)
									echo $class; ?>>UGX
									<?php echo $total_withdrawal=(!isset($withdrawal[0][0]['total_amount']))?0:$withdrawal[0][0]['total_amount']; ?>
								&nbsp;
							</dd>
							<hr/>
							
							<dt style="width:100%;" <?php if ($i % 2 == 0)
										echo $class; ?>>
										<?php echo __('Total Balance ((Deposit-Withdrawal))'); ?>: <span class="btn btn-inverse">UGX <?php echo ($total_receivable-$total_withdrawal); ?></span></dt>
							<dd<?php if ($i++ % 2 == 0)
									echo $class; ?>>
								   &nbsp;
							</dd>
							<!--
							<dt style="width:100%;" <?php if ($i % 2 == 0)
										echo $class; ?>>
										<?php echo __('Total Balance ((Credit-Debt)+(Receivable-Withdrawal))'); ?>: <span class="btn btn-inverse">UGX <?php echo ($total_credit-$total_debt)+($total_receivable-$total_withdrawal); ?></span></dt>
							<dd<?php if ($i++ % 2 == 0)
									echo $class; ?>>
								   &nbsp;
							</dd>
							-->
						<?php endif; ?>
                    </dl>
                </div>
				
				
				<?php if($user['User']['role']=='regular' || $user['User']['role']=='super_admin'):?>
				 <div id="b" class="tab-pane">
					
					<div class="well">
					<?php if($user['User']['role']=='regular'): ?>
						<h5>Your opening considered today (<?php echo date('M-d-Y',strtotime($date_today)); ?>)</h5><hr/>

						<?php if(count($opening)):?>
						
						<table cellpadding="0" cellspacing="0" class="table">
							<tr>
								<?php foreach($currencies as $currency):?>
									<th><?=$currency['Currency']['description']?></th>
								<?php endforeach;?>

								<?php foreach($otherCurrencies as $currency):?>
									<th><?=$currency['Currency']['id']?></th>
								<?php endforeach;?>
							</tr>
							<tr>
								<td><span class="ln"><?php echo h($opening['Opening']['opening_ugx']); ?></span>&nbsp;</td>
								<?php foreach($currencies as $currency):?>
									<?php if ($currency['Currency']['id']=='ugx') {continue;}?>
									<td>
										<span class="ln">
											<?php echo h($opening['Opening'][$currency['Currency']['id'] . 'a']); ?>
										</span>
									</td>
								<?php endforeach;?>
								
								<?php ?>
								<?php 
									$data=json_decode($opening['Opening']['other_currencies'],true);
									$data_other_currencies = [];
									foreach ($data['data'] as $value) {
										$data_other_currencies[$value['CID']] = $value;
									}
								?>
								
								<?php foreach($otherCurrencies as $other_currency):?>
									<?php $_amount=$_rate=0;?>
									<?php
										@$_amount=$data_other_currencies[$other_currency['Currency']['id']]['CAMOUNT'];
									?>
									<?php
										echo '<td><span class="ln">'.($_amount).'</span></td>';
									?>
								<?php endforeach;?>
							</tr>
							</table>
							<?php else:?>
								Either you closed for Today (<?php echo date('M-d-Y',strtotime($date_today)); ?>), OR 
								No opening found. You probably didn't save work of the previous working day.
								Please do it as soon as possible. Thanks.
							
							<?php endif; ?>
						<?php endif;?>	
							
							
						
						
						
						
						
						
						
						<!-- Next tym's openning-->
						<?php if($user['User']['role']=='regular'): ?>
						<h5>Your next opening
								<?php if(count($next_opening)):?>
									due on <?php echo date ('M-d-Y',strtotime($next_opening['Opening']['date'])); ?> saved/created by 
									<?php echo date('M-d-Y',strtotime($date_today)); ?>
								<?php endif; ?>
						</h5><hr/>

						<?php if(count($next_opening)):?>
						
						<table cellpadding="0" cellspacing="0">
							<tr>
									<th>UGX</th>
									<th>USD</th>
									<th>Euro</th>
									<th>GBP</th>
									<th>Kshs</th>
									<th>Tzsh</th>
									<th>SAR</th>
									<th>SP</th>
									<?php foreach($otherCurrencies as $other_currency):?>
										<th><?php echo $other_currency['Currency']['id']; ?></th>
									<?php endforeach;?>
							</tr>
							<tr>
								<td><span class="ln"><?php echo h($next_opening['Opening']['opening_ugx']); ?></span>&nbsp;</td>
								<td><span class="ln"><?php echo h($next_opening['Opening']['c1a']); ?></span>&nbsp;</td>
								<td><span class="ln"><?php echo h($next_opening['Opening']['c2a']); ?></span>&nbsp;</td>
								<td><span class="ln"><?php echo h($next_opening['Opening']['c3a']); ?></span>&nbsp;</td>
								<td><span class="ln"><?php echo h($next_opening['Opening']['c4a']); ?></span>&nbsp;</td>
								<td><span class="ln"><?php echo h($next_opening['Opening']['c5a']); ?></span>&nbsp;</td>
								<td><span class="ln"><?php echo h($next_opening['Opening']['c6a']); ?></span>&nbsp;</td>
								<td><span class="ln"><?php echo h($next_opening['Opening']['c7a']); ?></span>&nbsp;</td>
									

								<?php $data=json_decode($next_opening['Opening']['other_currencies'],true);?>
								<?php 
									$data_other_currencies = [];
									foreach ($data['data'] as $value) {
										$data_other_currencies[$value['CID']] = $value;
									}

								?>
								
								<?php foreach($otherCurrencies as $other_currency):?>
									<?php $_amount=$_rate=0;?>
									<?php
										@$_amount=$data_other_currencies[$other_currency['Currency']['id']]['CAMOUNT'];
									?>
									<?php
										echo '<td><span class="ln">'.($_amount).'</span></td>';
									?>
								<?php endforeach;?>
								
							</tr>
							</table>
							<?php else:?>
								You have no opening for the next day/time. Please save your work at the end of the day after balancing.
							
							<?php endif; ?>
						<?php endif;?>	
							






















						<?php if($super_admin || $authUser['can_view_safe']):?>
						<h5>Cash In the safe</h5><hr/>

						<?php if(count($safe)):?>
						<div style="overflow-x: auto;">
						<table cellpadding="0" cellspacing="0" class="well">
							<tr>
									<th>UGX</th>
									<th>USD</th>
									<th>Euro</th>
									<th>GBP</th>
									<th>Kshs</th>
									<th>Tzsh</th>
									<th>SAR</th>
									<th>SP</th>
									<?php foreach($otherCurrencies as $other_currency):?>
										<th><?php echo $other_currency['Currency']['id']; ?></th>
									<?php endforeach;?>
							</tr>
							<tr>
								<td><span class="ln"><?php echo h($safe['Safe']['opening_ugx']); ?></span>&nbsp;</td>
								<td><span class="ln"><?php echo h($safe['Safe']['c1a']); ?></span>&nbsp;</td>
								<td><span class="ln"><?php echo h($safe['Safe']['c2a']); ?></span>&nbsp;</td>
								<td><span class="ln"><?php echo h($safe['Safe']['c3a']); ?></span>&nbsp;</td>
								<td><span class="ln"><?php echo h($safe['Safe']['c4a']); ?></span>&nbsp;</td>
								<td><span class="ln"><?php echo h($safe['Safe']['c5a']); ?></span>&nbsp;</td>
								<td><span class="ln"><?php echo h($safe['Safe']['c6a']); ?></span>&nbsp;</td>
								<td><span class="ln"><?php echo h($safe['Safe']['c7a']); ?></span>&nbsp;</td>
								
								<?php 
									$data=json_decode($safe['Safe']['other_currencies'],true);
									$data_other_currencies = [];
									foreach ($data['data'] as $value) {
										$data_other_currencies[$value['CID']] = $value;
									}

								?>
								
								<?php foreach($otherCurrencies as $other_currency):?>
									<?php $_amount=$_rate=0;?>
									<?php
										@$_amount=$data_other_currencies[$other_currency['Currency']['id']]['CAMOUNT'];
									?>
									<?php
										echo '<td><span class="ln">'.($_amount).'</span></td>';
									?>
								<?php endforeach;?>

							</tr>
							</table>
							<?php else:?>
								Safe has not been initialized
							
							<?php endif; ?>
							</div>
						<?php endif; ?>
					</div>
					<hr>
					
					
					
				 </div>

				 <div id="c" class="tab-pane">
				 	<?php foreach($currencies as $currency):?>
						<div class="row" style="margin-left:10px;">
							<div class="span4">
								<div class="btn btn-info" style="width:100px"><?php echo $currency['Currency']['description'];?></div>
							</div>
							<div class="span4">
								<input name="data[User][amount_input]" maxlength="50" type="text" value="0" id="UserAmountInput_<?php echo $currency['Currency']['id'];?>" required="required">
							</div>
							<div class="span4">
								<div class="btn-group" style="margin-left:10%;">
									<button class="btn dropdown-toggle" data-toggle="dropdown">
									  <i class="icon icon-certificate"></i> Action
									  <span class="caret"></span>
									</button>
									<ul class="dropdown-menu">
										<?php if(($user['User']['role']=='regular' and $super_admin)):?>
											
											<!--
											<li><a href="#" currency_id='<?php echo $currency['Currency']['id'];?>' class="no-ajax safe-action" style="margin-left:10px;" data-target="users/safe_withdrawal_from_safe" onclick="return false;" title="This will withdraw cash from safe and add it to today's opening cash... Continue?">Withdraw from safe </a></li>
											-->

											<li><a href="#" currency_id='<?php echo $currency['Currency']['id'];?>' class="no-ajax safe-action" style="margin-left:10px;" data-target="users/safe_return_to_safe/<?=$user['User']['id']?>" onclick="return false;" title="This will return cash from today's opening back to the safe... Continue?">Return to safe </a></li>

											<!--
											<li><a href="#" currency_id='<?php echo $currency['Currency']['id'];?>' class="no-ajax safe-action" style="margin-left:10px;" data-target="users/safe_deposit_into_safe/<?=$user['User']['id']?>" onclick="return false;" title="This will deposit cash from the opening created when you saved your work after balancing.. Continue?">Deposit into safe</a></li>
											-->

										<?php endif;?>
										<?php if($cashier):?>
											<li class="divider"></li>
											<?php foreach($users as $user):?>
												<li><a href="#" currency_id='<?php echo $currency['Currency']['id'];?>' class="no-ajax safe-action" style="margin-left:10px;" data-target="users/safe_send_to/<?php echo $user['User']['id'];?>" onclick="return false;" title="This will send cash from today's opening to cashier's today's opening... Continue?">Send to <?php echo $user['User']['name'];?></a></li>
											<?php endforeach;?>	
										<?php endif;?>
										<?php if($super_admin):?>											
											<li><a href="#" currency_id='<?php echo $currency['Currency']['id'];?>' class="no-ajax safe-action" style="margin-left:10px;" data-target="users/safe_deposit" onclick="return false;" title="This will directly increase cash in the safe...Continue?">Deposit</a></li>
											<li><a href="#" currency_id='<?php echo $currency['Currency']['id'];?>' class="no-ajax safe-action" style="margin-left:10px;" data-target="users/safe_withdraw" onclick="return false;" title="This will directly reduce cash in the safe...Continue??">Withdraw</a></li>											
										<?php endif;?>
									</ul>												
								</div>
							</div>
						</div><hr/>
					<?php endforeach; ?>
					
					
					
					<!--Other Currencies-->
					<?php foreach($otherCurrencies as $otherCurrency):?>
						<div class="row" style="margin-left:10px;">
							<div class="span4">
								<div class="btn btn-info" style="width:100px"><?php echo $otherCurrency['Currency']['id'];?></div>
							</div>
							<div class="span4">
								<input name="data[User][amount_input]" maxlength="50" type="text" value="0" id="UserAmountInput_<?php echo $otherCurrency['Currency']['id'];?>" required="required">
							</div>
							<div class="span4">
								<div class="btn-group" style="margin-left:10%;">
									<button class="btn dropdown-toggle" data-toggle="dropdown">
									  <i class="icon icon-certificate"></i> Action
									  <span class="caret"></span>
									</button>
									<ul class="dropdown-menu">
										<?php if(($user['User']['role']=='regular' and $super_admin)):?>
											<!--
											<li><a href="#" currency_id='<?php echo $otherCurrency['Currency']['id'];?>' class="no-ajax safe-action" style="margin-left:10px;" data-target="users/other_safe_withdrawal_from_safe" onclick="return false;" title="This will withdraw cash from safe and add it to today's opening cash... Continue?">Withdraw from safe </a></li>
											-->

											<li><a href="#" currency_id='<?php echo $otherCurrency['Currency']['id'];?>' class="no-ajax safe-action" style="margin-left:10px;" data-target="users/other_safe_return_to_safe/<?=$user['User']['id']?>" onclick="return false;" title="This will return cash from today's opening back to the safe... Continue?">Return to safe </a></li>

											<!--
											<li><a href="#" currency_id='<?php echo $otherCurrency['Currency']['id'];?>' class="no-ajax safe-action" style="margin-left:10px;" data-target="users/other_safe_deposit_into_safe" onclick="return false;" title="This will deposit cash from the opening created when you saved your work after balancing.. Continue?">Deposit into safe</a></li>
											-->
											
										<?php endif;?>

										<?php if($cashier):?>
											<li class="divider"></li>
											<?php foreach($users as $user):?>
												<li><a href="#" currency_id='<?php echo $otherCurrency['Currency']['id'];?>' class="no-ajax safe-action" style="margin-left:10px;" data-target="users/other_safe_send_to/<?php echo $user['User']['id'];?>" onclick="return false;" title="This will send cash from today's opening to cashier's today's opening... Continue?">Send to <?php echo $user['User']['name'];?></a></li>
											<?php endforeach;?>	
										<?php endif;?>
										
										<?php if($super_admin):?>
											<li class="divider"></li>
											<li><a href="#" currency_id='<?php echo $otherCurrency['Currency']['id'];?>' class="no-ajax safe-action" style="margin-left:10px;" data-target="users/other_safe_deposit" onclick="return false;" title="This will directly increase cash in the safe...Continue?">Deposit</a></li>
											<li><a href="#" currency_id='<?php echo $otherCurrency['Currency']['id'];?>' class="no-ajax safe-action" style="margin-left:10px;" data-target="users/other_safe_withdraw" onclick="return false;" title="This will directly reduce cash in the safe...Continue??">Withdraw</a></li>											
										<?php endif;?>
									</ul>												
								</div>
							</div>
						</div><hr/>
					<?php endforeach; ?>
				 </div>
				 <?php endif; ?>
		
                <hr/>
                <script>
					$(document).ready(function(){
						$('#myTab a').click(function (e){
							e.preventDefault();
							$(this).tab('show');
						});
						$('.safe-action').click(function(e){
							e.preventDefault();
							
							if(!confirm($(this).attr('title'))) return;
							
							showLoading();
							var my_href=('<?php echo $this->webroot;?>'+($(this).attr('data-target'))+'/'+($(this).attr('currency_id'))+'/'+($('#UserAmountInput_'+($(this).attr('currency_id'))).val()));
							data={
								'date_today':$('#dp_today_selected').val(),
								'date_from':$('#dp_from_selected').val(),
								'date_to':$('#dp_to_selected').val()
							};
							$.ajax({
								url: my_href,
								data:data,
								success: function(data) {
									$('.dynamic-content').html(data);
									removeLoading();
									$('html, body').animate({scrollTop:0}, 'slow');
									return false;
								},
								error: function() {},
								complete : function () {removeLoading();},
								statusCode: {403: function (response) {window.location.href='<?php echo $this->webroot;?>users/login';}}
							});
						});
					});
                </script>
                    
            </div>
        </div>

    </div>
    
    
    <div class="actions btn btn-small" style="border-radius: 10px;">
       <ul>
			<?php if($super_admin && $user['User']['role']!='customer'): ?>
				<li><?php echo $this->Html->link('Individual Balancing',array('controller'=>'balancings','action'=>'show_individually',0,0,0,$user['User']['id'])); ?></li>
				<li><?php echo $this->Html->link('Safe Transactions',array('controller'=>'safe_transactions','action'=>'index')); ?></li>
				<li><?php echo $this->Html->link('Log file',array('controller'=>'action_logs','action'=>'index',$user['User']['id'])); ?></li>
			<?php elseif($user['User']['role']!='customer'):?>
				<li><?php echo $this->Html->link('Safe Transactions',array('controller'=>'safe_transactions','action'=>'index')); ?></li>
			<?php elseif($user['User']['role']=='customer'):?>
				<h6 style="float:left;margin-bottom:10px;">Add Record</h6>
				<!--
				<li><?php echo $this->Html->link('Credit',array('controller'=>'creditors','action'=>'add',$user['User']['id'])); ?></li>
				<li><?php echo $this->Html->link('Debit',array('controller'=>'debtors','action'=>'add',$user['User']['id'])); ?></li>
				-->
				<li><?php echo $this->Html->link('Deposit cash',array('controller'=>'receivables','action'=>'add',$user['User']['id'])); ?></li>
				<li><?php echo $this->Html->link('Withdraw cash',array('controller'=>'withdrawals','action'=>'add',$user['User']['id'])); ?></li>
				<li><?php echo $this->Html->link('Transfer cash',array('controller'=>'users','action'=>'transfer',$user['User']['id'])); ?></li>
				
				<hr/>
				<h6 style="float:left;margin-bottom:10px;margin-top:-10px;">Transactions</h6>
				<li><?php echo $this->Html->link('Summary',array('controller'=>'users','action'=>'transaction_summary',$user['User']['id']),['class'=>'no-ajax']); ?></li>
				<li><?php echo $this->Html->link('Summary ByDate',array('controller'=>'users','action'=>'transaction_summary',$user['User']['id']),['class'=>'use-ajax']); ?></li>
				<hr>
				
				<h6 style="float:left;margin-bottom:10px;margin-top:-10px;">History</h6>
				<!--
				<li><?php echo $this->Html->link('Credits',array('controller'=>'creditors','action'=>'index',$user['User']['id'])); ?></li>
				<li><?php echo $this->Html->link('Debits',array('controller'=>'debtors','action'=>'index',$user['User']['id'])); ?></li>
				-->
				<li><?php echo $this->Html->link('Deposits',array('controller'=>'receivables','action'=>'index',$user['User']['id'])); ?></li>
				<li><?php echo $this->Html->link('Withdraws',array('controller'=>'withdrawals','action'=>'index',$user['User']['id'])); ?></li>
			
			<?php elseif($user['User']['role']=='regular'):?>
				<li><?php echo $this->Html->link('Balance',array('controller'=>'balancings','action'=>'show_individually',0,0,0,$user['User']['id'])); ?></li>
				<li><?php echo $this->Html->link('Log file',array('controller'=>'action_logs','action'=>'index',$user['User']['id'])); ?></li>
			<?php endif; ?>
		</ul>
    </div>
    