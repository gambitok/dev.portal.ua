function exportStocks() {
    let url = "/ExportStockPrice/download-stocks/";
    window.open(url, '_blank');
}

function exportPrices() {
    let price = $("#price_list option:selected").val();
    if (price === "0") {
        alert("Виберіть прайс");
    } else {
        let url = "/ExportStockPrice/download-prices/" + price;
        window.open(url, '_blank');
    }
}

function exportClients() {
    let url = "/ExportStockPrice/download-clients/";
    window.open(url, '_blank');
}

function exportSupplClients() {
    let url = "/ExportStockPrice/download-suppl-clients/";
    window.open(url, '_blank');
}

function exportClientsAll() {
    let url = "/ExportStockPrice/download-clients-all/";
    window.open(url, '_blank');
}

function exportArticlesPriceRating() {
    let brand_id = $("#brand_list option:selected").val();
    let url = "/ExportStockPrice/download-price-rating/" + brand_id;
    window.open(url, '_blank');
}

