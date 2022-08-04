function showWriteOffCard(write_off_id) {
    if (write_off_id <= 0 || write_off_id === "") {
        toastr["error"](errs[0]);
    }
    if (write_off_id > 0) {
        JsHttpRequest.query($rcapi,{ 'w': 'showWriteOffCard', 'write_off_id':write_off_id},
            function (result, errors){ if (errors) {alert(errors);} if (result){
                $("#WriteOffCard").modal("show");
                $("#WriteOffCardBody").html(result.content[0]);
                $("#WriteOffCardLabel").html(result.content[1]);
                $("#write_off_tabs").tab();
                $("#data_pay").datepicker({format: "yyyy-mm-dd",autoclose:true});
                $(".i-checks").iCheck({checkboxClass: 'icheckbox_square-green',radioClass: 'iradio_square-green',});
            }}, true);
    }
}

function filterWriteOffList() {
    let date_start = $("#date_start").val();
    let date_end = $("#date_end").val();
    JsHttpRequest.query($rcapi,{ 'w': 'filterWriteOffList', 'date_start':date_start, 'date_end':date_end},
        function (result, errors){ if (errors) {alert(errors);} if (result){
            let dt = $("#datatable");
            dt.DataTable().destroy();
            $("#write_off_range").html(result.content[0]);
            $("#write_off_summ").html(result.content[1]);
            dt.DataTable({keys: true,"aaSorting": [],"processing": true,"scrollX": true,fixedColumns: {leftColumns: 2},"searching": true,fixedHeader: true,"lengthMenu": [[20, 50, 100, -1], [20, 50, 100, "All"]], "language": {"url": "//cdn.datatables.net/plug-ins/1.10.12/i18n/Ukrainian.json"}});
        }}, true);
}

function loadWriteOffPartitions(write_off_id) {
    JsHttpRequest.query($rcapi,{ 'w': 'loadWriteOffPartitions', 'write_off_id':write_off_id},
        function (result, errors){ if (errors) {alert(errors);} if (result){
            $("#partiotion_place").html(result.content);
            $("#write_off_tabs").tab();
        }}, true);
}

function printWriteOffFromDp(write_off_id) {
    if (write_off_id==="" || write_off_id===0 || write_off_id==="0"){toastr["error"](errs[0]);}
    if (write_off_id>0){
        window.open("/WriteOff/printWriteOff/"+write_off_id,"_blank","printWindow");
    }
}
