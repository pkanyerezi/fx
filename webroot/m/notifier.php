<?php require "connection.php" ;?>
<!DOCTYPE html>
<html>
<head>
	<title>Forex Buerau Reports Notifier</title>
	<script type="text/javascript" src="js/jquery1.7.2mini.js"></script>
	<script type="text/javascript" src="js/notifier.js"></script>	
	<style>
		#new-receipts{font-size:12px !important;}
		.summary td{border:1px solid #999;}
	</style>
</head>

<body>
	<div id="new-receipts" style="font-size:12px;margin-top: 10%;">
		<center style="font-size:17px;">
			<?php 
				$name='';$location='';
				$sql1=$dbh->query("select name,location from foxes limit 1"); 
				if(count($sql1)){
					$ids="";
					foreach($sql1 as $row){
						$name = $row['name'];
						$location = $row['location'];
					}
				}
			?>
			<b style="font-size:17px"><?php echo $name;?></b><br/>
			<?php echo $location;?><br>
			<b style="font-size:17px">Reports Notifier</b><br/>
			<div>Status: <span class="notifier-status"></span></div>
		</center>
	</div>
</body>
</html>