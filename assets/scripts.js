var delay = (function() {
	var timer = 0;

	return function(callback, ms) {
		clearTimeout (timer);
		timer = setTimeout(callback, ms);
	};
})();

$(document).ready(function () {
	$(document).on("click", "#buy", function () {
		var currency = $('#name').val();
		
		$('.response').empty();
		$.ajax({
			type: "POST",
			url: 'includes/ajax.php',
			data: {action: 'buy', currency: currency},
			success: function(data) {
				$('.response').html(data);
			}
		});
	});
	
	$(document).on("click", "#buy2", function () {
		var currency = $('#name').val();
		
		$('.response').empty();
		$.ajax({
			type: "POST",
			url: 'includes/ajax.php',
			data: {action: 'buy2', currency: currency},
			success: function(data) {
				$('.response').html(data);
			}
		});
	});
	
	$(document).on("click", "#price", function () {
		var currency = $('#name').val();
		
		$('.response').empty();
		
		timer = setInterval(function() {
			$.ajax({
				type: "POST",
				url: 'includes/ajax.php',
				data: {action: 'price', currency: currency},
				success: function(data) {
					$('.response').prepend(data);
				}
			});
		}, 1500);
	});
	
	$(document).on("click", "#stop", function () {
		clearInterval(timer);
	});
	
	$(document).on("click", "#sell", function () {
		var currency = $('#name').val();
		
		$('.response').empty();
		$.ajax({
			type: "POST",
			url: 'includes/ajax.php',
			data: {action: 'sell', currency: currency},
			success: function(data) {
				$('.response').html(data);
			}
		});
	});
});