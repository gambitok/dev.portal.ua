$(document).ready(function() {

    $("#parts_category").select2({placeholder: "Виберіть категорію"});
    $("#parts_brand").select2({placeholder: "Виберіть бренд"});
    $("#parts_name_select").select2({placeholder: "Виберіть назву індекса"});
    $("#parts_name_exist_select").select2({placeholder: "Виберіть назву індекса"});
    $("#list_check").change(function() {
        if($(this).is(':checked')) {
            let count = 0;
            $(".list-check").each(function() {
                $(this).prop("checked", true);
                count++;
            });
            $("#catalog_range_count_checked").html(count);
        } else {
            $(".list-check").each(function() {
                $(this).prop("checked", false);
            });
            $("#catalog_range_count_checked").html(0);
        }
    });
});

function showCatalogTree() {
    $("#CatalogTreeCard").modal("show");
    JsHttpRequest.query($rcapi,{ 'w': 'showCatalogTree'},
        function (result, errors){ if (errors) {alert(errors);} if (result){
            $("#CatalogTreeCardBody").html(result.content);

            var tree = new treefilter($("#my-tree"), {
                searcher : $("input#my-search")
            });
            $(".tree-head").on('click', function(event){
                $(this).next().toggleClass("dnone");
                $(this).toggleClass("check-head");
            });
        }}, true);
}

function chooseSelect2Str(str_id) {
    $("#parts_category").val(str_id); // Select the option with a value of '1'
    $("#parts_category").trigger('change');
    $("#CatalogTreeCard").modal("hide");
}

function showCountChecked() {
    let count = 0;
    $(".list-check").each(function() {
        if($(this).is(':checked')) {
            count++;
        }
    });
    $("#catalog_range_count_checked").html(count);
}

function initCheckBoxes() {
    toastr["success"]("Вибір за допомогою Shift включено!");
    var $chkboxes = $(".list-check");
    var lastChecked = null;
    $chkboxes.click(function(e) {
        if (!lastChecked) {
            lastChecked = this;
            return;
        }
        if (e.shiftKey) {
            var start = $chkboxes.index(this);
            var end = $chkboxes.index(lastChecked);
            $chkboxes.slice(Math.min(start,end), Math.max(start,end) + 1).prop('checked', lastChecked.checked);
        }
        lastChecked = this;
    });
}

function showCatalogList() {
    $("#catalog_range").html("");
    $("#catalog_range_count_checked").html(0);
    let str_id = $("#parts_category option:selected").val();
    let brand_id = $("#parts_brand ").select2("val");
    let type_id = $("#parts_type option:selected").val();
    let text = $("#parts_text").val();
    let name = $("#parts_name").val();
    let name_exist = $("#parts_name_exist").val();
    let check_auto = $("#check_auto").is(':checked');
    if (check_auto===true) check_auto=1; else check_auto=0;
    let name_select = $("#parts_name_select").select2("val");
    let name_exist_select = $("#parts_name_exist_select").select2("val");
    if (str_id==0) {
        toastr["error"]("Виберіть категорію спершу!");
    } else {
        JsHttpRequest.query($rcapi,{ 'w': 'showCatalogList', 'str_id':str_id, 'brand_id':brand_id, 'type_id':type_id, 'text':text, 'name':name, 'name_exist':name_exist, 'name_select':name_select, 'name_exist_select':name_exist_select, 'check_auto':check_auto},
            function (result, errors){ if (errors) {alert(errors);} if (result){
                $("#catalog_range").html(result.content[0]);
                $("#catalog_range_count").html(result.content[1]);
            }}, true);
    }
}

function showCatalogPartsCard() {
    let art_ids = []; let art_ids_str="";
    $(".list-check").each(function() {
        if($(this).is(':checked')) {
            art_ids.push($(this).attr("data-id"));
            art_ids_str = art_ids_str + "," + $(this).attr("data-id");
        }
    });
    if (art_ids_str!=="") {
        $("#CatalogCard").modal("show");
        JsHttpRequest.query($rcapi,{ 'w': 'showCatalogPartsCard', 'art_ids_str':art_ids_str},
            function (result, errors){ if (errors) {alert(errors);} if (result){
                $("#CatalogCardBody").html(result.content);
            }}, true);
    } else {
        toastr["error"]("Виберіть хоч один артикул спершу!");
    }
}

function showCatalogPartsCard2() {
    $("#CatalogCard").modal("show");
    JsHttpRequest.query($rcapi,{ 'w': 'showCatalogPartsCard2'},
        function (result, errors){ if (errors) {alert(errors);} if (result){
            $("#CatalogCardBody").html(result.content);
        }}, true);
}

function saveCatalogParts() {
    let arts = $("#selected_art_ids").val();
    let group_id = $("#selected_category_id").val();
    JsHttpRequest.query($rcapi,{ 'w': 'saveCatalogParts', 'group_id':group_id, 'arts':arts},
        function (result, errors){ if (errors) {alert(errors);} if (result){
            $("#CatalogCard").modal("hide");
            toastr["success"](result.content);
        }}, true);
}

function saveCatalogParts2() {
    let group_id = $("#selected_category_id").val();
    let str_id = $("#parts_category option:selected").val();
    let brand_id = $("#parts_brand ").select2("val");
    let type_id = $("#parts_type option:selected").val();
    let text = $("#parts_text").val();
    let name = $("#parts_name").val();
    let name_exist = $("#parts_name_exist").val();
    let check_auto = $("#check_auto").is(':checked');
    if (check_auto===true) check_auto=1; else check_auto=0;
    let name_select = $("#parts_name_select ").select2("val");
    let name_exist_select = $("#parts_name_exist_select").select2("val");
    JsHttpRequest.query($rcapi,{ 'w': 'saveCatalogParts2', 'group_id':group_id, 'str_id':str_id, 'brand_id':brand_id, 'type_id':type_id, 'text':text, 'name':name, 'name_exist':name_exist, 'name_select':name_select, 'name_exist_select':name_exist_select, 'check_auto':check_auto},
        function (result, errors){ if (errors) {alert(errors);} if (result){
            $("#CatalogCard").modal("hide");
            toastr["success"](result.content);
        }}, true);
}

function setCatalogPartsBrands() {
    $("#parts_brand").html("");
    $("#parts_name_select").html("");
    $("#parts_name_exist_select").html("");
    let str_id = $("#parts_category option:selected").val();
    let type_id = $("#parts_type option:selected").val();
    if (str_id==="0") {toastr["error"]("Виберіть категорію!");}
    if (str_id!=="0") {
        JsHttpRequest.query($rcapi,{ 'w': 'setCatalogPartsBrands', 'str_id':str_id, 'type_id':type_id},
            function (result, errors){ if (errors) {alert(errors);} if (result){
                $("#parts_brand").html(result.list_brand);
                $("#parts_name_select").html(result.list_name);
                $("#parts_name_exist_select").html(result.list_name_exist);
                $("#parts_brand").select2({placeholder: "Виберіть бренд"});
                $("#parts_name_select").select2({placeholder: "Виберіть назву індекса"});
                $("#parts_name_exist_select").select2({placeholder: "Виберіть назву EXIST індекса"});
            }}, true);
    }
}

function setCatalogPartsBrandsName() {
    $("#parts_name_select").html("");
    $("#parts_name_exist_select").html("");
    let str_id = $("#parts_category option:selected").val();
    let brand_id = $("#parts_brand option:selected").val();
    let type_id = $("#parts_type option:selected").val();
    if (str_id==="0") {toastr["error"]("Виберіть категорію!");}
    if (brand_id==="0") {toastr["error"]("Виберіть бренд!");}
    if (str_id!=="0" && brand_id!=="0") {
        JsHttpRequest.query($rcapi,{ 'w': 'setCatalogPartsBrandsName', 'str_id':str_id, 'brand_id':brand_id, 'type_id':type_id},
            function (result, errors){ if (errors) {alert(errors);} if (result){
                $("#parts_name_select").html(result.content[0]);
                $("#parts_name_exist_select").html(result.content[1]);
                $("#parts_name_select").select2({placeholder: "Виберіть назву індекса"});
                $("#parts_name_exist_select").select2({placeholder: "Виберіть назву EXIST індекса"});
            }}, true);
    }
}

function checkCatalogPartsGroup(group_id) {
    $(".tree-head").each(function() {
        $(this).removeClass("check-head");
    });
    $(".tree-list").each(function() {
        $(this).addClass("dnone");
    });
    JsHttpRequest.query($rcapi,{ 'w': 'getGroupName', 'group_id':group_id},
        function (result, errors){ if (errors) {alert(errors);} if (result){
            $("#selected_category").val(result.content);
            $("#selected_category_id").val(group_id);
        }}, true);
    $("#save_btn").prop("disabled", false);
}

function getArticleNameCount() {
    let name_select = $("#parts_name_select").select2("val");
    JsHttpRequest.query($rcapi,{ 'w': 'getArticleNameCount', 'name_select':name_select},
        function (result, errors){ if (errors) {alert(errors);} if (result){
            $("#count_arts").html(result.content);
        }}, true);
}

function searchCatInput() {
    var input, filter, li, a, i, txtValue;
    input = document.getElementById("searchInput");
    filter = input.value.toUpperCase();

    if (filter!=="") {
        $(".tree-list").each(function () {
            $(this).removeClass("dnone");
        });
    } else {
        $(".tree-list").each(function () {
            $(this).addClass("dnone");
        });
    }

    li = $(".group-tree li");
    for (i = 0; i < li.length; i++) {
        a = li[i].getElementsByTagName("a")[0];
        txtValue = a.textContent || a.innerText;
        if (txtValue.toUpperCase().indexOf(filter) > -1) {
            li[i].style.display = "";
        } else {
            li[i].style.display = "none";
        }
    }
}

/*==== EDIT FORM ====*/
function showCatalogPartsEditCard() {
    $("#CatalogEditCard").modal("show");
    JsHttpRequest.query($rcapi,{ 'w': 'showCatalogPartsEditCard'},
        function (result, errors){ if (errors) {alert(errors);} if (result){
            $("#CatalogEditCardBody").html(result.content);
        }}, true);
}

function showCatalogLogs(group_id) {
    $("#CatalogHistoryCard").modal("show");
    JsHttpRequest.query($rcapi,{ 'w': 'showCatalogLogs', 'group_id':group_id},
        function (result, errors){ if (errors) {alert(errors);} if (result){
            $("#CatalogHistoryCardBody").html(result.content);
        }}, true);
}

function untieCatalogGroup(head_id = "", cat_id = "", group_id = "") {
    if (head_id==="") head_id = $("#select_tree_head").select2("val");
    if (cat_id==="") cat_id = $("#select_tree_cat").select2("val");
    if (group_id==="") group_id = $("#select_tree_group").select2("val");
    if (head_id==="0" || cat_id==="0" || group_id==="0") {
        toastr["error"]("Спочатку виберіть всі поля!");
    } else {
        JsHttpRequest.query($rcapi,{ 'w': 'untieCatalogGroup', 'head_id':head_id, 'cat_id':cat_id, 'group_id':group_id},
            function (result, errors){ if (errors) {alert(errors);} if (result){
                showCatalogPartsEditCard();
                toastr["success"]("Успішно видалено!");
            }}, true);
    }
}

function showCatalogLogsCard(group_id, date, user_id) {
    $("#CatalogHistoryCard2").modal("show");
    JsHttpRequest.query($rcapi,{ 'w': 'showCatalogLogsCard', 'group_id':group_id, 'date':date, 'user_id':user_id},
        function (result, errors){ if (errors) {alert(errors);} if (result){
            $("#CatalogHistoryCard2Body").html(result.content);
        }}, true);
}

function dropCatalogPartsArtsGroup(group_id, date, user_id) {
    JsHttpRequest.query($rcapi,{ 'w': 'dropCatalogPartsArtsGroup', 'group_id':group_id, 'date':date, 'user_id':user_id},
        function (result, errors){ if (errors) {alert(errors);} if (result){
            toastr["success"]("Видалено - " + result.content + " позицій!");
        }}, true);
}

function dropCatalogPartsArts(group_id, art_id) {
    JsHttpRequest.query($rcapi,{ 'w': 'dropCatalogPartsArts', 'group_id':group_id, 'art_id':art_id},
        function (result, errors){ if (errors) {alert(errors);} if (result){
            toastr["success"]("Успішно видалено!");
        }}, true);
}

function showCatalogLogsArt(group_id) {
    let art_id = $("#search_art_id").val();
    JsHttpRequest.query($rcapi,{ 'w': 'showCatalogLogsArt', 'group_id':group_id, 'art_id':art_id},
        function (result, errors){ if (errors) {alert(errors);} if (result){
            let status = result.content["status"];
            if (status>0) {
                showCatalogLogsCard(group_id, result.content["date"], result.content["user_id"])
            } else {
                toastr["error"]("Позиції в групі не знайдено!");
            }
        }}, true);
}

/*==== /EDIT FORM ====*/

/*==== ADD FORM ====*/
function showCatalogPartsAddCard(head_id = "", cat_id = "", group_id = "") {
    $("#CatalogAddCard").modal("show");
    JsHttpRequest.query($rcapi,{ 'w': 'showCatalogPartsAddCard', 'head_id':head_id, 'cat_id':cat_id, 'group_id':group_id},
        function (result, errors){ if (errors) {alert(errors);} if (result){
            $("#CatalogAddCardBody").html(result.content);
            if (head_id!=="") showCatalogItem("head");
            if (cat_id!=="") showCatalogItem("cat");
            if (group_id!=="") showCatalogItem("group");
        }}, true);

}

function getCatalogHeadList() {
    $("#select_tree_head").html();
    $("#select_tree_cat").html();
    $("#select_tree_group").html();
    JsHttpRequest.query($rcapi,{ 'w': 'getCatalogHeadList'},
        function (result, errors){ if (errors) {alert(errors);} if (result){
            $("#select_tree_head").html(result.content);
            $("#select_tree_head").select2();
        }}, true);
}

function getCatalogCatList() {
    $("#select_tree_cat").html();
    $("#select_tree_group").html();
    JsHttpRequest.query($rcapi,{ 'w': 'getCatalogCatList'},
        function (result, errors){ if (errors) {alert(errors);} if (result){
            $("#select_tree_cat").html(result.content);
            $("#select_tree_cat").select2();
        }}, true);
}

function getCatalogGroupList() {
    $("#select_tree_group").html();
    JsHttpRequest.query($rcapi,{ 'w': 'getCatalogGroupList'},
        function (result, errors){ if (errors) {alert(errors);} if (result){
            $("#select_tree_group").html(result.content);
            $("#select_tree_group").select2();
        }}, true);
}

function saveCatalogPartsAddCard() {
    let head_id = $("#select_tree_head").select2("val");
    let cat_id = $("#select_tree_cat").select2("val");
    let group_id = $("#select_tree_group").select2("val");
    if (head_id==="0" || cat_id==="0" || group_id==="0") {
        toastr["error"]("Спочатку виберіть всі поля!");
    } else {
        JsHttpRequest.query($rcapi,{ 'w': 'saveCatalogPartsAddCard', 'head_id':head_id, 'cat_id':cat_id, 'group_id':group_id},
            function (result, errors){ if (errors) {alert(errors);} if (result){
                getCatalogHeadList();
                getCatalogCatList();
                getCatalogGroupList();
                toastr["success"]("Успішно привязано!");
            }}, true);
    }
}

function showCatalogItem(type) {
    let item_id = $("#select_tree_" + type).select2("val");
    if (item_id==="undefined" || item_id==="0") {
        $("#add-btn-" + type).prop("disabled", false);
        $("#edit-btn-" + type).prop("disabled", true);
        $("#drop-btn-" + type).prop("disabled", true);
        $(".t2_" + type + "[name='text_ru']").val("");
        $(".t2_" + type + "[name='text_ua']").val("");
        $(".t2_" + type + "[name='text_en']").val("");
        $(".t2_" + type + "[name='text_link']").val("");
        $(".t2_" + type + "[name='status']").val("");
        if (type==="group") {
            $(".t2_" + type + "[name='status_auto']").val("");
            $(".t2_" + type + "[name='reviewed']").val("");
            $(".t2_" + type + "[name='description_ru']").val("");
            $(".t2_" + type + "[name='description_ua']").val("");
            $(".t2_" + type + "[name='description_en']").val("");
        }
    } else {
        $("#add-btn-" + type).prop("disabled", true);
        $("#edit-btn-" + type).prop("disabled", false);
        $("#drop-btn-" + type).prop("disabled", false);
        JsHttpRequest.query($rcapi,{ 'w': 'showCatalogItem', 'type':type, 'item_id':item_id},
            function (result, errors){ if (errors) {alert(errors);} if (result){
                $(".t2_" + type + "[name='text_ru']").val(result.content["text_ru"]);
                $(".t2_" + type + "[name='text_ua']").val(result.content["text_ua"]);
                $(".t2_" + type + "[name='text_en']").val(result.content["text_en"]);
                $(".t2_" + type + "[name='text_link']").val(result.content["text_link"]);
                $(".t2_" + type + "[name='status']").val(result.content["status"]);
                if (type==="group") {
                    $(".t2_" + type + "[name='status_auto']").val(result.content["status_auto"]);
                    $(".t2_" + type + "[name='reviewed']").val(result.content["reviewed"]);
                    $(".t2_" + type + "[name='description_ru']").val(result.content["description_ru"]);
                    $(".t2_" + type + "[name='description_ua']").val(result.content["description_ua"]);
                    $(".t2_" + type + "[name='description_en']").val(result.content["description_en"]);
                }
            }}, true);
    }
}

function addCatalogItem(type) {
    if (type!=="") {
        let text_ru      = $(".t2_" + type + "[name='text_ru']").val();
        let text_ua      = $(".t2_" + type + "[name='text_ua']").val();
        let text_en      = $(".t2_" + type + "[name='text_en']").val();
        let text_link    = $(".t2_" + type + "[name='text_link']").val();
        let status       = $(".t2_" + type + "[name='status']").val();
        let status_auto  = $(".t2_" + type + "[name='status_auto']").val();
        let reviewed  = $(".t2_" + type + "[name='reviewed']").val();
        let description_ru  = $(".t2_" + type + "[name='description_ru']").val();
        let description_ua  = $(".t2_" + type + "[name='description_ua']").val();
        let description_en  = $(".t2_" + type + "[name='description_en']").val();
        if (text_ru==="" && text_ua==="" && text_en==="") {
            toastr["error"]("Введіть текстові поля!");
        } else {
            JsHttpRequest.query($rcapi,{ 'w': 'addCatalogItem', 'type':type, 'text_ru':text_ru, 'text_ua':text_ua, 'text_en':text_en, 'text_link':text_link, 'status':status, 'status_auto':status_auto, 'reviewed':reviewed, 'description_ru':description_ru, 'description_ua':description_ua, 'description_en':description_en},
                function (result, errors){ if (errors) {alert(errors);} if (result){
                    getCatalogHeadList();
                    getCatalogCatList();
                    getCatalogGroupList();
                    toastr["success"]("Додано: `" + text_ru + "`!");
                }}, true);
        }
    }
}

function editCatalogItem(type) {
    if (type!=="") {
        let item_id      = $("#select_tree_" + type).select2("val");
        let text_ru      = $(".t2_" + type + "[name='text_ru']").val();
        let text_ua      = $(".t2_" + type + "[name='text_ua']").val();
        let text_en      = $(".t2_" + type + "[name='text_en']").val();
        let text_link    = $(".t2_" + type + "[name='text_link']").val();
        let status       = $(".t2_" + type + "[name='status']").val();
        let status_auto  = $(".t2_" + type + "[name='status_auto']").val();
        let reviewed  = $(".t2_" + type + "[name='reviewed']").val();
        let description_ru  = $(".t2_" + type + "[name='description_ru']").val();
        let description_ua  = $(".t2_" + type + "[name='description_ua']").val();
        let description_en  = $(".t2_" + type + "[name='description_en']").val();
        if (item_id!==undefined && item_id!=="0") {
            JsHttpRequest.query($rcapi,{ 'w': 'editCatalogItem', 'type':type, 'item_id':item_id, 'text_ru':text_ru, 'text_ua':text_ua, 'text_en':text_en, 'text_link':text_link, 'status':status, 'status_auto':status_auto, 'reviewed':reviewed, 'description_ru':description_ru, 'description_ua':description_ua, 'description_en':description_en},
                function (result, errors){ if (errors) {alert(errors);} if (result){
                    getCatalogHeadList();
                    getCatalogCatList();
                    getCatalogGroupList();
                    toastr["success"]("Збережено: `" + text_ru + "`!");
                }}, true);
        } else {
            toastr["error"]("Спершу виберіть щось!");
        }
    }
}

function dropCatalogItem(type) {
    let item_id = $("#select_tree_" + type).select2("val");
    if (item_id!==undefined && item_id!=="0") {
        JsHttpRequest.query($rcapi,{ 'w': 'dropCatalogItem', 'type':type, 'item_id':item_id},
            function (result, errors){ if (errors) {alert(errors);} if (result){
                getCatalogHeadList();
                getCatalogCatList();
                getCatalogGroupList();
                toastr["success"]("Видалено: `" + result.content + "`!");
            }}, true);
    } else {
        toastr["error"]("Спершу виберіть щось!");
    }

}

/*==== /ADD FORM ====*/


