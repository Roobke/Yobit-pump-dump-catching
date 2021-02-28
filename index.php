<?php
	include('includes/config.php');
	include('classes/TradeAPI.php');
	
	$api = new YoBitNetAPI($api_key, $secret_key);
	$balances = $api->getInfo();
?>
<!DOCTYPE html>
<html>
	<head>
		<meta charset="UTF-8">
		<title>Yobit TRADE BOT</title>
		<link rel="stylesheet" type="text/css" href="assets/styles.css?r=<?=rand(0, 999999999)?>">
		<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.3.1/jquery.min.js"></script>
		<script src="assets/scripts.js?r=<?=rand(0, 999999999)?>"></script>
	</head>
	<body>
		<div class="center">
			<div class="big_text">
				Yobit TRADE BOT
			</div>
			<div class="info_text">
				This BOT automatically buys currency in Market for <b><?=$min_purchase_sum?></b> <?=strtoupper($main_crypto_currency)?>(when you click Buy) and sell all yours haved currency when you click Sell.
				<br/>
				Button Price showing crypto price every 1.5 second.
			</div>
			<div class="info_text2">
				You have <b><?=$balances['return']['funds'][$main_crypto_currency]?></b> <?=strtoupper($main_crypto_currency)?>.
			</div>
			<div class="info_block">
				Crypto name:
				<br/>
				<input type="text" name="name" id="name" class="currency_name"/>
				<br/>
				<input type="submit" id="buy" value="Buy"/> <input type="submit" id="buy2" value="Buy and sell with <?=($desired_profit * 100 - 100)?>%"/> <input type="submit" id="price" value="Price"/> <input type="submit" id="stop" value="Stop"/>
				<div class="response"></div>
			</div>
		</div>
	</body>
</html>