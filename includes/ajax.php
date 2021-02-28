<?php
	include('config.php');
	include('../classes/TradeAPI.php');
	include('functions.php');
	
	$api = new YoBitNetAPI($api_key, $secret_key);
	$balances = $api->getInfo();
	$exchangeInfo = $api->getExchangeInfo();
	$minQty = 1;
	$currency_exists = false;
	$tab = 8;
	
	if (isset($_POST['currency'])) {
		$currency = strtolower(preg_replace("/[^A-Z]+/", "", strtoupper($_POST['currency'])));
		
		foreach ($exchangeInfo['pairs'] as $key => $info) {
			if ($key == $currency.'_'.$main_crypto_currency) {
				$ticker = $api->getPairTicker($currency.'_'.$main_crypto_currency);
				$price = number_format($ticker[$currency.'_'.$main_crypto_currency]['last'], 8, '.', '');
				$market_buy_price = number_format($price * 1.01, 8, '.', '');
				#$minQty = round_up($info['min_amount'] / $price, $tab);
				$minQty = round_up($min_purchase_sum / $price, $tab);
				$currency_exists = true;
				
				break;
			}
		}
	}
	
	if (!$currency_exists) {
		echo 'Currency not exists.';
		
		exit;
	}
	
	if (isset($_POST['action']) && $_POST['action'] == 'buy') {
		$order = $api->makeOrder($minQty, $currency.'_'.$main_crypto_currency, 'buy', $market_buy_price);
		
		echo 'Currency price: <b>'.$market_buy_price.'</b>
		<br/>
		Answer: <b>'.print_r($order, true).'</b>';
	}
	
	if (isset($_POST['action']) && $_POST['action'] == 'buy2') {
		$order = $api->makeOrder($minQty, $currency.'_'.$main_crypto_currency, 'buy', $market_buy_price);
		
		sleep(5);
		
		$order2 = $api->makeOrder($minQty, $currency.'_'.$main_crypto_currency, 'sell', number_format($market_buy_price * $desired_profit, 8, '.', ''));
		
		echo 'Currency price: <b>'.$price.'</b>
		<br/>
		Buy answer: <b>'.print_r($order, true).'</b>
		<br/>
		Sell answer: <b>'.print_r($order2, true).'</b>';
	}
	
	if (isset($_POST['action']) && $_POST['action'] == 'price') {
		echo '<small><small><b>'.$price.'</b></small></small>
		<br/>';
	}
?>