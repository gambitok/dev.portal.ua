function numberOnly(){
	$(".numberOnly").each(function() {
	  $(this).inputmask("decimal", {
			placeholder: "0", radixPoint: ".", groupSeparator: "", digits: 2, autoGroup: true,allowMinus: true, clearMaskOnLostFocus: true, removeMaskOnSubmit: true,prefix: '', rightAlign: true,
			onUnMask: function(maskedValue, unmaskedValue) {
				var x = unmaskedValue.split(',');
				if (x.length != 2)return "0.00";
				return x[0].replace(/\./g, '') + '.' + x[1];
			}
		});
	});
	$(".currencyOnly").each(function() {
	  $(this).inputmask("decimal", {
			placeholder: "0",radixPoint: ".",groupSeparator: "",digits: 6,autoGroup: true,allowMinus: true,clearMaskOnLostFocus: true,removeMaskOnSubmit: true,	prefix: '', rightAlign: true,
			onUnMask: function(maskedValue, unmaskedValue) {
				var x = unmaskedValue.split(',');
				if (x.length != 2)return "0.00";
				return x[0].replace(/\./g, '') + '.' + x[1];
			}
		});
	});
}
function numberOnlyPlace(place){
	$("."+place).each(function() {
	  $(this).inputmask("decimal", {
			placeholder: "0", radixPoint: ".", groupSeparator: "", digits: 2, autoGroup: true,allowMinus: true, clearMaskOnLostFocus: true, removeMaskOnSubmit: true,prefix: '', rightAlign: true,
			onUnMask: function(maskedValue, unmaskedValue) {
				var x = unmaskedValue.split(',');
				if (x.length != 2)return "0.00";
				return x[0].replace(/\./g, '') + '.' + x[1];
			}
		});
	});
}
function numberOnlyLong(){
	$(".numberOnlyLong").each(function() {
	  $(this).inputmask("decimal", {
			placeholder: "0", radixPoint: ".", groupSeparator: "", digits: 6, autoGroup: true,allowMinus: true, clearMaskOnLostFocus: true, removeMaskOnSubmit: true,prefix: '', rightAlign: true,
			onUnMask: function(maskedValue, unmaskedValue) {
				var x = unmaskedValue.split(',');
				if (x.length != 6)return "0.000000";
				return x[0].replace(/\./g, '') + '.' + x[1];
			}
		});
	});
	$(".currencyOnlyLong").each(function() {
	  $(this).inputmask("decimal", {
			placeholder: "0",radixPoint: ".",groupSeparator: "",digits: 6,autoGroup: true,allowMinus: true,clearMaskOnLostFocus: true,removeMaskOnSubmit: true,	prefix: '', rightAlign: true,
			onUnMask: function(maskedValue, unmaskedValue) {
				var x = unmaskedValue.split(',');
				if (x.length != 6)return "0.000000";
				return x[0].replace(/\./g, '') + '.' + x[1];
			}
		});
	});
}