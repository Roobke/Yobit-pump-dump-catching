<?php
	function round_up($value, $places = 0) {
		if ($places < 0) $places = 0;
		
		$mult = pow(10, $places);
		
		return ceil($value * $mult) / $mult;
	}
	
	function round_down($value, $precision) {   
		$value = (float) $value;
		$precision = (int) $precision;
		
		if ($precision < 0) $precision = 0;
		
		$decPointPosition = strpos($value, '.');
		
		if ($decPointPosition === false) return $value;
		
		return (float) (substr($value, 0, $decPointPosition + $precision + 1));        
	}
?>