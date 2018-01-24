<?php require "../m/connection.php" ;?>
<?php
	$currencies=$dbh->query("SELECT * FROM currencies order by is_other_currency ASC,arrangement ASC, id ASC");
	$refresh_after = 120;//seconds
?>

<!DOCTYPE html>
<html>
<head>
<meta charset="UTF-8">
<meta http-equiv="refresh" content="<?=$refresh_after?>">
<title>Forex Currency Board</title>
<link rel="stylesheet" type="text/css" href="css/style.css">
<link rel="stylesheet" type="text/css" href="css/reset.css">
<link rel="stylesheet" type="text/css" href="css/slowautoscroll.css">
<script type="text/javascript" src="js/jquery1.7.2mini.js"></script>
<script type="text/javascript" src="js/script.js"></script>
<script type="text/javascript" src="js/slowautoscroll.js"></script>
</head>
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
<body>
	<div class="title-bar">
 		<span class="title-bar-left">
 			<h1><?php echo $name;?></h1>
 		</span>
 		<span class="title-bar-right">
 			<h1>TIME: <span class="rateTime">00:00 00</span> EAT (GMT + 3:00 HRS)</h1>
 		</span>
 	</div>
 	<div class="content" id="content">
 		<div class="currency-board left">
 			<div class="currency-titles">
 				<span class="country-title item">
	 				COUNTRY
	 			</span>
	 			<span class="code-title item">
	 				CODE
	 			</span>
	 			<span class="currency-title item">
	 				CURRENCY
	 			</span>
	 			<div class="sell-title item">
	 				BUY
	 			</div>
	 			<div class="buy-title item">
	 				SELL
	 			</div>
 			</div>
 		</div>
 		<div class="currency-board right">
 			<div class="currency-titles">
 				<span class="country-title item">
	 				COUNTRY
	 			</span>
	 			<span class="code-title item">
	 				CODE
	 			</span>
	 			<span class="currency-title item">
	 				CURRENCY
	 			</span>
	 			<div class="sell-title item">
	 				BUY
	 			</div>
	 			<div class="buy-title item">
	 				SELL
	 			</div>
 			</div>
 		</div>
 		<?php $total = 0;$i=0;?>
 		<?php if(count($currencies)):?>
	 		<?php foreach($currencies as $currency):?>
	 			<?php if($currency['id']=='c8' || $currency['id']=='c00') continue;?>
	 			<div class="currency-board <?=((!($i%2))?'right':'left')?>">
		 			<div class="currency-item">
			 			<span class="country item">
			 				<img style="width:45px;height:45px;" width="45px" height="45px" alt="<?=strtoupper($currency['id'])?>" onerror="$(this).attr('src','images/default.png');" src="images/flags/48/<?=strtoupper($currency['id'])?>.png" />
			 			</span>
			 			<span class="code item">
			 				<?=strtoupper($currency['id'])?>
			 			</span>
			 			<span class="currency item">
			 				<?=$currency['description']?>
			 			</span>
			 			<span class="buy item">
			 				<?=$currency['buy']?>
			 			</span>
			 			<span class="sell item">
			 				<?=$currency['sell']?>
			 			</span>
			 		</div>
			 	</div>
			 	<?php $i++;$total++;?>
	 		<?php endforeach;?>
 		<?php endif;?>
		
 		<?php if($total%2):?>
 			<div class="currency-board right"><div class="currency-item"></div></div>
 		<?php endif;?>

 	</div>
 	<div class="footer-bar">
 		<div>&copy; 2013 - <?=date('Y');?> by Blueprint Softwares Ltd</div><br>
 		<div style="margin-right: 25%;margin-left: 25%;">
 		<marquee scrollamount=2 behavior=alternate><p>Best rates in town at affordable prices. Our rates are negotiable for large currency amounts. Come One, Come All, Exchange All.</p></marquee>
 		</div>
 	</div>
 </body>
</html>