function exportStocks() {
    var url = "/ExportStockPrice/download-stocks/";
    window.open(url, '_blank');
}

function exportPrices() {
    var price=$("#price_list option:selected").val();
    if (price=="0") alert("Виберіть прайс"); else {
        var url = "/ExportStockPrice/download-prices/"+price;
        window.open(url, '_blank');
    }
}