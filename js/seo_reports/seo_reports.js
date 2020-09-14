var select_ids = [];

$(document).ready(function(e) {
    $("select#managers option").each(function(index, element) {
        select_ids.push($(this).val());
    });
	let elem2 = document.querySelector("#client_status");if (elem2){ let client_status = new Switchery(elem2, { color: '#1AB394' });}
});

function toggleManagerReport() {
	let manager_list=$("#manager_report_list");
	if (manager_list.css("display")==="none"){
		$("#toggle_btn").text("Сховати звіт"); } else { $("#toggle_btn").text("Показати звіт");
	}
    manager_list.slideToggle();
}

function getSummToday() {
    let today = moment().format('YYYY-MM-DD');
	$("#date_start").val(today);
	$("#date_end").val(today);
	showSeoReports();
}

function getSummMonth() {
    let first = moment().format('YYYY-MM-DD');
    let today = moment().format('YYYY-MM-01');
	$("#date_start").val(today);
	$("#date_end").val(first);
	showSeoReports();
}

function showSeoReports() {
	let date_start=$("#date_start").val();
    let date_end=$("#date_end").val();
    let managers = Array.prototype.filter.call( document.getElementById("managers").options, el => el.selected).map(el => el.value).join(",");
    let cash_id=$("#cash_select option:selected").val();
    let client_status=$("#client_status").prop("checked");
	JsHttpRequest.query($rcapi,{ 'w': 'showSeoReports', 'date_start':date_start, 'date_end':date_end, 'managers':managers, 'cash_id':cash_id, 'client_status':client_status},
		function (result, errors){ if (errors) {alert(errors);} if (result){
			let dt=$("#datatable");
			dt.DataTable().destroy();
			$("#seo_reports_range").html(result["content"]);
            dt.DataTable({keys: true,"aaSorting": [],"processing": true,"scrollX": true,fixedColumns: {leftColumns: 2},"searching": true,fixedHeader: true,"lengthMenu": [[20, 50, 100, -1], [20, 50, 100, "All"]], "language": {"url": "//cdn.datatables.net/plug-ins/1.10.12/i18n/Ukrainian.json"}});
			if($("#summ_user").length) {
				getSummUser(managers,date_start,date_end,cash_id);
			}
		}}, true);
}

function getSummUser(user_id,date_start,date_end,cash_id) {
	JsHttpRequest.query($rcapi,{ 'w': 'getSummUser', 'user_id':user_id, 'date_start':date_start, 'date_end':date_end, 'cash_id':cash_id}, 
	function (result, errors){ if (errors) {alert(errors);} if (result){ 
		$("#summ_user").html(result.content);
	}}, true);
}

function exportSeoReports() {
	let date_start=$("#date_start").val();
    let date_end=$("#date_end").val();
    let managers = Array.prototype.filter.call( document.getElementById("managers").options, el => el.selected).map(el => el.value).join(","); if (managers==="") managers=0;
    let cash_id=$("#cash_select option:selected").val();
    let client_status=$("#client_status").prop("checked"); if (client_status) client_status=1; else client_status=0;
    let url = "/export_managers.php?w=Export&date_start="+date_start+"&date_end="+date_end+"&managers="+managers+"&cash_id="+cash_id+"&client_status="+client_status;
	window.open(url, '_blank');
}