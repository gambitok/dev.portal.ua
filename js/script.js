$(document).ready(function() {
    var maxIndex = 100, cIndex = 0;
    setInterval(function() {
        if (cIndex == maxIndex)
            cIndex = 0;
        loadTopNavigation();
        cIndex++;
    }, 15000);

});

function loadTopNavigation() {
    JsHttpRequest.query($rcapi,{ 'w': 'loadTopNavigation'},
        function (result, errors){ if (errors) {alert(errors);} if (result){
            $("#menu-top-nav").html(result.content);
        }}, true);
}

