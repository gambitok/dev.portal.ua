var select_ids = [];
var states_ids = [];
var regions_ids = [];
var citys_ids = [];
var citys_selected = [];
var brands_ids = [];
var goods_group_ids = [];
var client_ids = [];

$(document).ready(function() {
    $("select#clients option").each(function() { select_ids.push($(this).val()); });
    $("select#states option").each(function() { states_ids.push($(this).val()); });
    $("select#regions option").each(function() { regions_ids.push($(this).val()); });
    $("select#citys option").each(function() { citys_ids.push($(this).val()); });

    $("select#brands option").each(function() { brands_ids.push($(this).val()); });
    $("select#goods_group option").each(function() { goods_group_ids.push($(this).val()); });
    $("select#client_ids option").each(function() { client_ids.push($(this).val()); });

    $(".js-switch").each(function() {
        new Switchery(this, { color: '#1AB394' });
    });

    $( "#city_form .chosen-search-input" ).keyup(function() {
        let input_text = $( "#city_form .chosen-search-input" ).val();
        updateCitysRange(input_text);
    });
});

function showReportClients() {
    let date_start = $("#date_start").val();
    let date_end = $("#date_end").val();
    let clients = Array.prototype.filter.call( document.getElementById("clients").options, el => el.selected).map(el => el.value).join(",");
    let cash_id = $("#cash_select option:selected").val();
    let tpoint_id = $("#tpoint_select option:selected").val();
    let client_category = $("#category_select option:selected").val();
	JsHttpRequest.query($rcapi,{ 'w': 'showReportClients', 'date_start':date_start, 'date_end':date_end, 'clients':clients, 'cash_id':cash_id, 'tpoint_id_report':tpoint_id, 'client_category':client_category},
		function (result, errors){ if (errors) {alert(errors);} if (result){
			let dt = $("#datatable");
			dt.DataTable().destroy();
			$("#report_clients_range").html(result["content"]);
            dt.DataTable({keys: true,"aaSorting": [],"processing": true,"scrollX": true,fixedColumns: {leftColumns: 2},"searching": true,fixedHeader: true,"lengthMenu": [[20, 50, 100, -1], [20, 50, 100, "All"]], "language": {"url": "//cdn.datatables.net/plug-ins/1.10.12/i18n/Ukrainian.json"}});
		}}, true);
}

function showAnalyticsClients() {
    $("#report_clients_range").html("<div class=\"dtable\"><div class=\"loader\"></div></div>");
    let date_start = $("#date_start").val();
    let date_end = $("#date_end").val();
    let cash_id = $("#cash_select option:selected").val();
    let tpoint_id = $("#tpoint_select option:selected").val();
    let price_id = $("#price_select option:selected").val();
    let margin_status = $("#margin_status").prop("checked"); if (margin_status) margin_status=1; else margin_status=0;
    let clients = Array.prototype.filter.call( document.getElementById("clients").options, el => el.selected).map(el => el.value).join(",");
    let states = Array.prototype.filter.call( document.getElementById("states").options, el => el.selected).map(el => el.value).join(",");
    let regions = Array.prototype.filter.call( document.getElementById("regions").options, el => el.selected).map(el => el.value).join(",");
    let citys = Array.prototype.filter.call( document.getElementById("citys").options, el => el.selected).map(el => el.value).join(",");
    JsHttpRequest.query($rcapi,{ 'w': 'showAnalyticsClients', 'date_start':date_start, 'date_end':date_end, 'clients':clients, 'cash_id':cash_id, 'tpoint_id_report':tpoint_id, 'price_id':price_id, 'margin_status':margin_status, 'states':states, 'regions':regions, 'citys':citys},
        function (result, errors){ if (errors) {alert(errors);} if (result){
            let dt = $("#datatable");
            dt.DataTable().destroy();
            $("#report_clients_range").html(result["content"]);
            dt.DataTable({keys: true,"aaSorting": [],"processing": true,"scrollX": true,fixedColumns: {leftColumns: 2},"searching": true,fixedHeader: true,"lengthMenu": [[20, 50, 100, -1], [20, 50, 100, "All"]], "language": {"url": "//cdn.datatables.net/plug-ins/1.10.12/i18n/Ukrainian.json"}});
        }}, true);
}

function updateCitysRange(text) {
    $("#citys > option").each(function() {
        citys_selected.push(this.value);
    });
    if (text.length === 3) {
        JsHttpRequest.query($rcapi,{ 'w': 'updateCitysRange', 'text':text, 'citys_selected':citys_selected},
            function (result, errors){ if (errors) {alert(errors);} if (result){
                let citys_range = result.content;
                Object.keys(citys_range).forEach(function(key) {
                    $("#citys").append("<option value='"+key+"'>"+citys_range[key]+"</option>");
                });
                let citys = $("#citys");
                let citys_input = $("#city_form .chosen-search-input");
                let input_text = citys_input.val();
                let chosenSelectedItems = citys.val();
                citys.trigger("chosen:updated");
                citys.val(chosenSelectedItems);
                citys_input.val(input_text);
            }}, true);
    }
}

function showReportSalesArticles() {
    $("#report_sales_articles_range").html("<div class=\"dtable\"><div class=\"loader\"></div></div>");
    let date_start = $("#date_start").val();
    let date_end = $("#date_end").val();
    let brands = Array.prototype.filter.call( document.getElementById("brands").options, el => el.selected).map(el => el.value).join(",");
    let goods_group = Array.prototype.filter.call( document.getElementById("goods_group").options, el => el.selected).map(el => el.value).join(",");
    let client_ids = Array.prototype.filter.call( document.getElementById("client_ids").options, el => el.selected).map(el => el.value).join(",");
    let availability_status = $("#availability_status").prop("checked"); if (availability_status) availability_status=1; else availability_status=0;
    let real_cost_status = $("#real_cost_status").prop("checked"); if (real_cost_status) real_cost_status=1; else real_cost_status=0;
    let real_sale_status = $("#real_sale_status").prop("checked"); if (real_sale_status) real_sale_status=1; else real_sale_status=0;
    let last_income = $("#last_income").prop("checked"); if (last_income) last_income=1; else last_income=0;
    let storage_rate = $("#storage_rate").prop("checked"); if (storage_rate) storage_rate=1; else storage_rate=0;
    let create_order = $("#create_order").prop("checked"); if (create_order) create_order=1; else create_order=0;
    let params = [];
    params['availability'] = availability_status;
    params['real_cost'] = real_cost_status;
    params['real_sale'] = real_sale_status;
    params['last_income'] = last_income;
    params['storage_rate'] = storage_rate;
    params['create_order'] = create_order;
    console.log(brands);
    console.log(goods_group);
    JsHttpRequest.query($rcapi,{ 'w': 'showReportSalesArticles', 'date_start':date_start, 'date_end':date_end, 'brands':brands, 'goods_group':goods_group, 'client_ids':client_ids, 'params':params},
        function (result, errors){ if (errors) {alert(errors);} if (result){
            let dt = $("#datatable");
            dt.DataTable().destroy();
            $("#report_sales_articles_range").html(result["content"]);
            dt.DataTable({keys: true,"aaSorting": [],"processing": true,"scrollX": true,fixedColumns: {leftColumns: 2},"searching": true,fixedHeader: true,"lengthMenu": [[20, 50, 100, -1], [20, 50, 100, "All"]], "language": {"url": "//cdn.datatables.net/plug-ins/1.10.12/i18n/Ukrainian.json"}});
        }}, true);
}

function exportReportSalesArticles() {
    let date_start = $("#date_start").val();
    let date_end = $("#date_end").val();
    let client_ids = Array.prototype.filter.call( document.getElementById("client_ids").options, el => el.selected).map(el => el.value).join(",");
    let brands = Array.prototype.filter.call( document.getElementById("brands").options, el => el.selected).map(el => el.value).join(","); if (brands==="" || brands===undefined) brands="";
    let goods_group = Array.prototype.filter.call( document.getElementById("goods_group").options, el => el.selected).map(el => el.value).join(","); if (goods_group==="" || goods_group===undefined) goods_group="";
    let availability_status = $("#availability_status").prop("checked"); if (availability_status) availability_status=1; else availability_status=0;
    let real_cost_status = $("#real_cost_status").prop("checked"); if (real_cost_status) real_cost_status=1; else real_cost_status=0;
    let real_sale_status = $("#real_sale_status").prop("checked"); if (real_sale_status) real_sale_status=1; else real_sale_status=0;
    let last_income_status = $("#last_income").prop("checked"); if (last_income_status) last_income_status=1; else last_income_status=0;
    let storage_rate_status = $("#storage_rate").prop("checked"); if (storage_rate_status) storage_rate_status=1; else storage_rate_status=0;
    let create_order_status = $("#create_order").prop("checked"); if (create_order_status) create_order_status=1; else create_order_status=0;
    let url = "/ReportSalesArticles/export/?date_start="+date_start+"&date_end="+date_end+"&clients="+client_ids+"&brands="+brands+"&goods_group="+goods_group+"&availability_status="+availability_status+"&real_cost_status="+real_cost_status+"&real_sale_status="+real_sale_status+"&last_income_status="+last_income_status+"&storage_rate_status="+storage_rate_status+"&create_order_status="+create_order_status;
    window.open(url, '_blank');
}

