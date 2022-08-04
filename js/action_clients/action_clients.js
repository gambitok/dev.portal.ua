
function loadActionClientsList() {
    JsHttpRequest.query($rcapi,{ 'w': 'loadActionClientsList'},
        function (result, errors){ if (errors) {alert(errors);} if (result){
            let dt=$("#datatable");
            if (dt.length) dt.DataTable().destroy();
            $("#action_clients_range").html(result.content);
            dt.DataTable({keys: true,"aaSorting": [],"processing": true,"scrollX": true,fixedColumns: {leftColumns: 2},"searching": true,fixedHeader: true,"lengthMenu": [[20, 50, 100, -1], [20, 50, 100, "All"]], "language": {"url": "//cdn.datatables.net/plug-ins/1.10.12/i18n/Russian.json"}});
        }}, true);
}

function showActionClientsCard(action_id,sel_art_id=0) {
    JsHttpRequest.query($rcapi,{ 'w': 'showActionClientsCard', 'action_id':action_id, 'sel_art_id':sel_art_id},
        function (result, errors){ if (errors) {alert(errors);} if (result){
            $("#ActionClientsCard").modal("show");
            $("#ActionClientsCardBody").html(result.content);
            $("#ActionClientsCardLabel").html(result.action_id);
            $("#action_clients_tabs").tab();

            let selected_clients=result.clients;
            let selected_categories=result.categories;

            let select_clients=$("select#action_clients");
            let select_category=$("select#action_category");

            select_clients.chosen({no_results_text: "Oops, nothing found!",width: "100%"});
            select_clients.val(selected_clients).trigger("chosen:updated");

            select_category.chosen({no_results_text: "Oops, nothing found!",width: "100%"});
            select_category.val(selected_categories).trigger("chosen:updated");

            setTimeout(function (){
                $("#action_clients_str").DataTable({keys: true,"aaSorting": [],"processing": true,"scrollX": true,fixedColumns: {leftColumns: 2},"searching": true,fixedHeader: true,"lengthMenu": [[10, 20, 100, -1], [20, 50, 100, "All"]], "language": {"url": "//cdn.datatables.net/plug-ins/1.10.12/i18n/Russian.json"}, "dom": '<"top">frt<"bottom"lpi><"clear">'});
            },500);
        }}, true);
}

function saveActionClients(action_id) {
    let client_list=[];
    $("select#action_clients option:selected").each(function() {
        client_list.push($(this).val());
    });
    let category_list=[];
    $("select#action_category option:selected").each(function() {
        category_list.push($(this).val());
    });
    let art_id=$("#action_art_id").val();
    let amount=$("#action_amount").val();
    let max_amount=$("#action_max_amount").val();
    let price=$("#action_price").val();
    let action_data=$("#action_data").val();
    let return_delay=$("#action_return_delay").val();
    JsHttpRequest.query($rcapi,{ 'w': 'saveActionClients', 'action_id':action_id, 'art_id':art_id, 'client_list':client_list, 'amount':amount, 'max_amount':max_amount, 'price':price, 'action_data':action_data, 'category_list':category_list, 'return_delay':return_delay },
        function (result, errors){ if (errors) {alert(errors);} if (result){
            if (result["answer"]==1){
                swal("Збережено!", "Внесені Вами зміни успішно збережені.", "success");
                showActionClientsCard(action_id);
                loadActionClientsList();
            } else { swal("Помилка!", result["error"], "error");}
        }}, true);
}

function dropActionClients(action_id) {
    JsHttpRequest.query($rcapi,{ 'w': 'dropActionClients', 'action_id':action_id },
        function (result, errors){ if (errors) {alert(errors);} if (result){
            if (result["answer"]==1){
                swal("Видалено!", "Внесені Вами зміни успішно збережені.", "success");
                $("#ActionClientsCard").modal("hide");
                loadActionClientsList();
            } else { swal("Помилка!", result["error"], "error");}
        }}, true);
}

function disableActionClients(action_id,status) {
    JsHttpRequest.query($rcapi,{ 'w': 'disableActionClients', 'action_id':action_id, 'status':status },
        function (result, errors){ if (errors) {alert(errors);} if (result){
            if (result["answer"]==1){
                swal("Виконано!", "Внесені Вами зміни успішно збережені.", "success");
                $("#ActionClientsCard").modal("hide");
                loadActionClientsList();
            } else { swal("Помилка!", result["error"], "error");}
        }}, true);
}

function setActionArtID(art_id,article_nr_displ) {
    $("#SearchFormModalWindow").modal("hide");
    $("#action_art_id").val(art_id);
    $("#article_nr_displ").val(article_nr_displ);
}

function showSearchIndexForm() {
    $("#SearchFormModalWindow").modal("show");
    JsHttpRequest.query($rcapi,{ 'w': 'showSearchIndexForm'},
        function (result, errors){ if (errors) {alert(errors);} if (result){
            $("#SearchFormModalBody").html(result.content);
            $("#SearchFormModalLabel").html("Вибрати артикул");
        }}, true);
}

function searchArticleDispl(brand_id) {
    $("#BrandFormModalWindow").modal("hide");
    let article_nr_displ=$("#search_article_nr_displ").val();
    JsHttpRequest.query($rcapi,{ 'w': 'searchArticleDispl', 'article_nr_displ':article_nr_displ, 'brand_id':brand_id },
        function (result, errors){ if (errors) {alert(errors);} if (result){
            if (result.type_search==0 || result.type_search==1) { //search
                $("#article_displ_range").html(result.content);
            }
            if (result.type_search==2) { //brand search
                $("#BrandFormModalWindow").modal("show");
                $("#BrandFormModalBody").html(result.brands);
                $("#BrandFormModalLabel").html("Вибрати бренд");
            }
        }}, true);
}
