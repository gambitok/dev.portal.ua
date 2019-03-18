var select_ids = [];
$(document).ready(function(e) {
    $('select#payboxes option').each(function(index, element) {
        select_ids.push($(this).val());
    })
});

function showCashReports() {
	var e = document.getElementById("payboxes");	
	var payboxes = Array.prototype.filter.call( document.getElementById("payboxes").options, el => el.selected).map(el => el.value).join(",");
	var date_start=$("#date_start").val();
	var date_end=$("#date_end").val();
	var cash_id=1;
	if (payboxes==="" || payboxes===undefined) toastr["error"]("������� ����!"); else {
		cash_id=1;
		JsHttpRequest.query($rcapi,{ 'w': 'showCashReportsList', 'date_start':date_start, 'date_end':date_end, 'payboxes':payboxes, 'cash_id':cash_id}, 
		function (result, errors){ if (errors) {alert(errors);} if (result){  
			$("#cash_reports_uah").html(result.content);
		}}, true);
		cash_id=2;
		JsHttpRequest.query($rcapi,{ 'w': 'showCashReportsList', 'date_start':date_start, 'date_end':date_end, 'payboxes':payboxes, 'cash_id':cash_id}, 
		function (result, errors){ if (errors) {alert(errors);} if (result){  
			$("#cash_reports_usd").html(result.content);
		}}, true);
		cash_id=3;
		JsHttpRequest.query($rcapi,{ 'w': 'showCashReportsList', 'date_start':date_start, 'date_end':date_end, 'payboxes':payboxes, 'cash_id':cash_id}, 
		function (result, errors){ if (errors) {alert(errors);} if (result){  
			$("#cash_reports_eur").html(result.content);
		}}, true);
	}
}