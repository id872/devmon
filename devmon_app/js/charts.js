function sendAJAX(url, params){
    $.ajax({
        type: "post",
        url: url,
        dataType: "json",
        data: params,
        success: function (jsonResp) {
            if (jsonResp && Object.keys(jsonResp).length > 0) {
                document.title = "Monitoring - Charts loaded";
                drawChartJS(jsonResp);
            }
        },
        error: function(){
            document.title = "Monitoring - reception error";
            alert("Invalid response or no data");
        }
    });
}

function getChartsByDate(){

    var type = parseInt(document.getElementById('dataTypeSelector').value);
    var dateFrom = document.getElementById('dateFrom').value;
    var dateTo = document.getElementById('dateTo').value;
    var hash = document.getElementById('userSelector').value
    
    if (!dateFrom || !dateTo)
        return;

    if (type === 0) {
        dateTo = dateFrom;
        document.getElementById('dateTo').value = dateFrom;
        document.getElementById('dateTo').readOnly = true;
    }
    else {
        document.getElementById('dateTo').readOnly = false;
    }
    
    sendAJAX('Chart', { "type": type, "dateFrom": dateFrom, "dateTo": dateTo, "hash": hash });
}

function getSanternoStatsCharts(){
    var hash = document.getElementById('userSelector').value
    sendAJAX('Chart', { "stats": 0, "hash": hash });
}

function setContainer(id){
    var container = document.getElementById('chartsContainer');
    
    var node = document.createElement('canvas');
    node.setAttribute('id', id);
    
    if (container.children[id])
    {
        container.children[id] = node;
    }
    else
    {
        container.appendChild(node);
    }
}

var charts = [];

function drawChartJS(jsonResp){
    document.getElementById('chartsContainer').innerHTML = '';
    
    for(var jsonKey in jsonResp)
    {
        var item = jsonResp[jsonKey];
        
        if (item){
            setContainer(jsonKey);

            var ctx = document.getElementById(jsonKey).getContext("2d");

            if (charts[jsonKey]){
                charts[jsonKey].destroy();
            }
            
            charts[jsonKey] = new Chart(ctx, item);
        }
    }
}
