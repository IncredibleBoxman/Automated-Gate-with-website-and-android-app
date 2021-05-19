/*To automatically get colors for chart, the website https://medium.com/code-nebula/automatically-generate-chart-colors-with-chart-js-d3s-color-scales-f62e282b2b41
  was used. From said website, the functions calculatePoint and interpolateColors was obtained as well as some of the code in 
  the document ready function. */

function calculatePoint(i, intervalSize, colorRangeInfo) {
    var { colorStart, colorEnd, useEndAsStart } = colorRangeInfo;
    return (useEndAsStart ? (colorEnd - (i * intervalSize)) : (colorStart + (i * intervalSize)));
}

/* Must use an interpolated color scale, which has a range of [0, 1] */
function interpolateColors(dataLength, colorScale, colorRangeInfo) {
    var { colorStart, colorEnd } = colorRangeInfo;
    var colorRange = colorEnd - colorStart;
    var intervalSize = colorRange / dataLength;
    var i, colorPoint;
    var colorArray = [];
  
    for (i = 0; i < dataLength; i++) {
        colorPoint = calculatePoint(i, intervalSize, colorRangeInfo);
        colorArray.push(colorScale(colorPoint));
    }
  
    return colorArray;
}  

$(document).ready(function(){
    $.ajax({
        url: "/database/DBchart2.php",
        method: "GET",
        success: function(data) {
            var obj = JSON.parse(data);
            console.log(obj);
            hours = obj.labels;
            count = obj.data;

            /* Create color array */
            dataLength = count.length;
            var colorScale = d3.interpolateRgb("dodgerblue", "midnightblue");
            const colorRangeInfo = {
                colorStart: 0,
                colorEnd: 1,
                useEndAsStart: false,
            }; 
            var COLORS = interpolateColors(dataLength, colorScale, colorRangeInfo);
        
            var ctx = $("#canvasHour");
            var graph = new Chart(ctx, {
                type: 'bar',
                data: {
                    labels: hours,
                    datasets : [
                        {
                            label: 'Transactions per Hour',
                            backgroundColor: COLORS,
                            hoverBackgroundColor: 'dimgray',
                            data: count
                        }
                    ]
                },
                options: {
                    title: {
                        display: true,
                        text: 'Transactions per Hour',
                        fontSize: 20
                    },
                    scales: {
                        yAxes: [{
                            ticks: {
                                min: 0
                            }
                        }]
                    },
                    legend: {
                        display: false
                    },
                    plugins: {
                        labels: false
                    }
                }
            });
        },
        error: function(data) {
            console.log(data);
        }
    });
  });