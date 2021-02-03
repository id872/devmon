function sendAJAX(url, params)
{
    document.title = "Monitoring - Charts loading";

    $.ajax({
        type: "post",
        url: url,
        dataType: "json",
        data: params,
        success: function (jsonResp) {
            if (jsonResp && Object.keys(jsonResp).length > 0) {
                document.title = "Monitoring - Charts loaded";
                drawChartJS(jsonResp);
            } else {
                document.title = "Monitoring - NO DATA";
                document.getElementById('chartsContainer').innerHTML = '<br><div style="background-color:#ff9933;color:black;">NO DATA</div>';
            }
        },
        error: function(){
            document.title = "Monitoring - reception error";
            alert("Invalid response");
        }
    });
}

function checkDateInterval(obj)
{
    if (obj === undefined)
        return;

    const interval31days =  1000 * 60 * 60 * 24 * 31;
    const dateFrom = new Date(document.getElementById('dateFrom').value);
    const dateTo = new Date(document.getElementById('dateTo').value);
    const dateDiff = dateTo.getTime() - dateFrom.getTime();

    if (obj.id === "dateFrom" && (Math.abs(dateDiff) > interval31days || dateTo < dateFrom))
        document.getElementById('dateTo').value = document.getElementById('dateFrom').value;

    if (obj.id === "dateTo" && (Math.abs(dateDiff) > interval31days || dateTo < dateFrom))
        document.getElementById('dateFrom').value = document.getElementById('dateTo').value;
}

function getChartsByDate(obj = undefined)
{
    checkDateInterval(obj);

    const type = document.getElementById('dataTypeSelector').value;
    const dateFrom = document.getElementById('dateFrom').value;
    let dateTo = document.getElementById('dateTo').value;
    const hash = document.getElementById('userSelector').value;

    if (!dateFrom || !dateTo)
        return;

    if (type === 'santerno_readouts') {
        dateTo = dateFrom;
        document.getElementById('dateTo').value = dateFrom;
        document.getElementById('dateTo').readOnly = true;
    }
    else
        document.getElementById('dateTo').readOnly = false;

    sendAJAX('Chart', { "type": type, "dateFrom": dateFrom, "dateTo": dateTo, "hash": hash });
}

function getSanternoStatsCharts()
{
    const hash = document.getElementById('userSelector').value
    sendAJAX('Chart', { "stats": 0, "hash": hash });
}

function setContainer(id)
{
    const container = document.getElementById('chartsContainer');
    const node = document.createElement('canvas');

    node.setAttribute('id', id);
    container.appendChild(node);
}

function drawChartJS(jsonResp)
{
    document.getElementById('chartsContainer').innerHTML = '';

    for(let jsonKey in jsonResp)
    {
        const chartConfig = jsonResp[jsonKey];

        if (chartConfig.hasOwnProperty('data')){
            setContainer(jsonKey);

            const ctx = document.getElementById(jsonKey).getContext("2d");
            new Chart(ctx, chartConfig);
        }
    }
}
