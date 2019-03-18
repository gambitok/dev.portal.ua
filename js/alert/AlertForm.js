function showAlertForm() {
	document.getElementById("AlertForm").style.visibility="visible";
	document.getElementById("AlertForm").style.position="absolute";
	document.getElementById("AlertForm").style.width="800px";
	document.getElementById("AlertForm").style.left="50%";
	document.getElementById("AlertForm").style.marginLeft="-400px";
	document.getElementById("AlertForm").style.top=jQuery(document).scrollTop()+50;
}
function closeAlertForm() {
	document.getElementById("AlertForm").style.visibility="hidden";
	document.getElementById("AlertForm").style.position="absolute";
	document.getElementById("AlertForm").style.left="-150%";
	document.getElementById("AlertForm").style.top="0%";
	document.getElementById("AlertInfo").innerHTML="";
}
function showAlertFileForm() {
	document.getElementById("AlertFileForm").style.visibility="visible";
	document.getElementById("AlertFileForm").style.position="absolute";
	document.getElementById("AlertFileForm").style.width="800px";
	document.getElementById("AlertFileForm").style.left="50%";
	document.getElementById("AlertFileForm").style.marginLeft="-400px";
	document.getElementById("AlertFileForm").style.top=jQuery(document).scrollTop()+50;
}
function closeAlertFileForm() {
	document.getElementById("AlertFileForm").style.visibility="hidden";
	document.getElementById("AlertFileForm").style.position="absolute";
	document.getElementById("AlertFileForm").style.left="-150%";
	document.getElementById("AlertFileForm").style.top="0%";
	document.getElementById("AlertFileInfo").innerHTML="";
}
window.onkeyup = function (event) {
	if (event.keyCode == 27){
		if(document.getElementById("AlertForm").style.visibility=='visible') {closeAlertForm();}
		if(document.getElementById("AlertFileForm").style.visibility=='visible') {closeAlertFileForm();}
		if(document.getElementById("AnalogWindow").style.visibility=='visible') {closeAnalogWindow();}
		if (document.getElementById("BusketForm").style.visibility=='visible') {	closeBusketForm();}
	}
}
jQuery.fn.ForceNumericOnly =function(){
    return this.each(function(){
        $(this).keydown(function(e){
            var key = e.charCode || e.keyCode || 0;
            return (
                key == 8 || 
                key == 9 ||
                key == 13 ||
                key == 46 ||
                key == 110 ||
                key == 190 ||
                (key >= 35 && key <= 40) ||
                (key >= 48 && key <= 57) ||
                (key >= 96 && key <= 105));
        });
    });
};