var select_ids = [];

$(document).ready(function(e) {
    $("select#storages option").each(function(index, element) {
        select_ids.push($(this).val());
    })
});

function exportStorageReports() {
    let storages = Array.prototype.filter.call( document.getElementById("storages").options, el => el.selected).map(el => el.value).join(",");
	if (storages=="" || storages==undefined) storages=0;
    let url = "/export_storages.php?w=Export&storages="+storages;
	window.open(url, '_blank');
}
