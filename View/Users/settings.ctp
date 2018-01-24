<?php echo $this->Html->script(array('script_dynamic_content'));?>
<div class="flash-message"><?php echo $this->Session->flash(); ?></div>
<div id="use">
    <div class="users view">
        <div class="related">
            
            <ul class="nav nav-tabs" id="myTab">
                <li><a href="#b" class='no-ajax'><i class="icon-cog"></i> Settings</a></li>
				<li><a href="#c" class='no-ajax'><i class="icon-refresh"></i> Password Reset</a></li>
            </ul>
                
            <div class="tab-content">      
                <div id="b" class="tab-pane active">
                    <div class="users ">
                    <?php echo $this->Form->create('User',array('type'=>'file','id'=>'user_edit_form','controller'=>'users','action'=>'edit'));?>
                            <?php
                                    echo $this->Form->input('id');
                                    echo $this->Form->input('name');
                                    echo $this->Form->input('email');
                                    echo $this->Form->input('username');
                                    //echo $this->Form->input('password',array('value'=>''));
                                    //echo $this->Form->input('password_confirmation',array('type'=>'password'));
                                    if($super_admin && $this->data['User']['id']!=$users_Id){		
                                            echo $this->Form->input('role',array('type'=>'select','options'=>array('regular'=>'Regular user','admin'=>'Administrator'),'selected'=>1));
                                            		
                                    }
                                    
                                    echo $this->Form->input('printing_place',array('type'=>'select','options'=>['1'=>'Main/Server Computer','2'=>'This Computer ('.$_SERVER['REMOTE_ADDR'].')']));
                                    if($super_admin){
                                        echo '<p class="alert alert-info">Other Permissions</p>';
                                        echo $this->Form->input('require_board_rate',array());

                                        echo '<p class="alert alert-info">Access Permissions</p>';
                                        echo $this->Form->input('can_edit_receipt',array());
                                        echo $this->Form->input('can_delete_receipt',array());
                                        echo $this->Form->input('can_view_safe',array());
                                        
                                        echo '<p class="alert alert-info">Reports/Summary Permissions</p>';
                                        echo $this->Form->input('balance_with_all_purchases_from_other_cashiers',array());
                                        echo $this->Form->input('can_download_receipts_excelfile',array());
                                        echo $this->Form->input('can_download_FIA_large_cash',array());
                                        echo $this->Form->input('can_view_large_cash_receipts',array());
                                        echo $this->Form->input('can_view_sales_and_purchase_returns',array());
                                        echo $this->Form->input('can_view_currency_summary',array());
                                        echo $this->Form->input('can_view_cashflow',array());
                                        echo $this->Form->input('can_view_closing_balance_summary',array());
                                        echo $this->Form->input('is_safe',array());

                                        echo '<p class="alert alert-info">Choose below if either bank or director is being added. But not both.</p>';
                                        echo $this->Form->input('is_bank',array('label'=>'Is Bank'));
                                        echo $this->Form->input('is_director',array('label'=>'Is Director'));
                                    }

                            ?>
                            </fieldset>
                    <?php echo $this->Form->end(__('Update', true));?>
                    </div> 
                </div>
				
				<div id="c" class="tab-pane">
						<?php echo $this->Form->create('User',array('type'=>'file','id'=>'user_edit_form','controller'=>'users','action'=>'edit'));?>
								<?php
										echo $this->Form->input('id');										
										echo $this->Form->input('password',array('value'=>'','label'=>'New Password'));
										echo $this->Form->input('password_confirmation',array('type'=>'password','label'=>'Confirm password'));								

								?>
								</fieldset>
						<?php echo $this->Form->end(__('Change Password', true));?>
					</div>
				
                <hr/>
                <script>
                    $('#myTab a').click(function (e){
                        e.preventDefault();
                        $(this).tab('show');
                    });
                </script>
                    
            </div>
        </div>

    </div>
    
    
    <div class="actions">
        <h3><?php echo'<strong>';
__('My account');
echo'</strong>'; ?></h3>
        <ul id="imge">
            <?php
				echo $this->Html->image("pic/" . $user['User']['profile_image'], array('width' => '50px', 'height' => '40px', 'alt' => 'Profile Picture'));
            ?>
        </ul>

    </div>
    