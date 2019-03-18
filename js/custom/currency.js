function setNumeric(elm){
	elm.inputmask("numeric", {
		radixPoint: ".",
		groupSeparator: ",",
		digits: 2,
		autoGroup: true,
		prefix: '', //No Space, this will truncate the first character
		rightAlign: false,
		oncleared: function () { self.Value(''); }
	});
}