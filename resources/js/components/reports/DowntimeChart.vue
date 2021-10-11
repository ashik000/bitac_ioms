<template>
    <div class="wrap">
        <h2 class="report-title">{{ title }}</h2>

        <div class="chart-container" >
            <canvas height="360" ref="downtimeChart"/>
        </div>
    </div>
</template>

<script>
    import Chart from 'chart.js';
    import {clone} from "../../utils";
    import { convertToHM } from "../../utils";
    export default {
        name: "DowntimeChart",
        props: {
            title: {
                type: String,
            },
            dataset: {
                type: Object,
            }
        },
        data: () => ({}),
        watch: {
            dataset: {
                handler(n, o) {
                    const chart = this.$data._chart;

                    if (chart === null) {
                        return;
                    }

                    chart.data.labels = n.labels;
                    chart.data.datasets[0].data = clone(n.planned);
                    chart.data.datasets[1].data = clone(n.unplanned);
                    chart.update();
                },
                deep: true
            }
        },
        mounted() {
            let context = this.$refs.downtimeChart;

            const chart = new Chart(context, {
                type: 'bar',
                data: {
                    labels: this.dataset.labels,
                    datasets: [
                        {
                            label: "Planned",
                            data: clone(this.dataset.planned),
                            backgroundColor: '#0077d8',
                        },
                        {
                            label: "Unplanned",
                            data: clone(this.dataset.unplanned),
                            backgroundColor: '#db0000',
                        }
                    ]
                },
                options: {
                    maintainAspectRatio: false,
                    scales: {
                        yAxes: [{
                          stacked: true,
                        }],
                        xAxes: [{
                          stacked: true,
                        }],
                    },
                    legend: {
                        position: 'bottom',
                        usePointStyle: true
                    },
                    tooltips: {
                        mode: 'index',
                        axis: 'y',
                        enabled: false,

                        custom: function(tooltipModel) {
                            // Tooltip Element
                            let tooltipEl = document.getElementById('chartjs-tooltip');

                            // Create element on first render
                            if (!tooltipEl) {
                                tooltipEl = document.createElement('div');
                                tooltipEl.id = 'chartjs-tooltip';
                                tooltipEl.innerHTML = '<table></table>';
                                document.body.appendChild(tooltipEl);
                            }

                            // Hide if no tooltip
                            if (tooltipModel.opacity === 0) {
                                tooltipEl.style.opacity = 0;
                                return;
                            }

                            // Set caret Position
                            // tooltipEl.classList.remove('above', 'below', 'no-transform');
                            // if (tooltipModel.yAlign) {
                            //     tooltipEl.classList.add(tooltipModel.xAlign);
                            // } else {
                            //     tooltipEl.classList.add('no-transform');
                            // }

                            function getBody(bodyItem) {
                                return bodyItem.lines;
                            }

                            function showPercentage(value, un, pl){
                                if(value === 'Unplanned'){
                                    return ' ('+un+'%)';
                                }else if(value === 'Planned'){
                                    return ' ('+pl+'%)';
                                }

                            }


                            // Set Text
                            if (tooltipModel.body) {
                                let titleLines = tooltipModel.title || [];
                                let bodyLines = tooltipModel.body.map(getBody).reverse();

                                let unplanned = parseInt(bodyLines[0][0].split(": ")[1]);
                                let planned = parseInt(bodyLines[1][0].split(": ")[1]);
                                let unplanned_percentage = ((unplanned * 100)/(unplanned+planned)).toFixed(2);
                                let planned_percentage =  ((planned * 100)/(unplanned+planned)).toFixed(2);

                                let innerHtml = '<thead>';

                                innerHtml += '</thead><tbody>';

                                bodyLines.forEach(function(body, i) {
                                    let div = '<div id="'+ body[0].split(":")[0] +'">';

                                    innerHtml += '<tr><td>' + div + body[0].split(":")[0] + "  "
                                        + convertToHM(body[0].split(": ")[1]) + showPercentage(body[0].split(":")[0], unplanned_percentage, planned_percentage) +'</div></td></tr>';
                                });
                                innerHtml += '</tbody>';

                                let tableRoot = tooltipEl.querySelector('table');
                                tableRoot.innerHTML = innerHtml;
                            }

                            // `this` will be the overall tooltip
                            let position = this._chart.canvas.getBoundingClientRect();

                            // Display, position, and set styles for font
                            tooltipEl.style.opacity = 1;
                            tooltipEl.style.position = 'absolute';
                            tooltipEl.style.left = position.left + window.pageXOffset + tooltipModel.caretX + 'px';
                            tooltipEl.style.top = position.top + window.pageYOffset + tooltipModel.caretY + 'px';
                            tooltipEl.style.fontFamily = tooltipModel._bodyFontFamily;
                            tooltipEl.style.fontSize = tooltipModel.bodyFontSize + 'px';
                            tooltipEl.style.fontStyle = tooltipModel._bodyFontStyle;
                            tooltipEl.style.padding = tooltipModel.yPadding + 'px ' + tooltipModel.xPadding + 'px';
                            tooltipEl.style.pointerEvents = 'none';
                        }
                    }
                }
            });

            this.$data._chart = chart;
        }
    }
</script>


<style>
#Planned{
    background-color: #0077d8 !important;
    color: #FFFFFF;
    padding: 3px;
}

#Unplanned{
    background-color: #db0000 !important;
    color: #FFFFFF;
    padding: 3px;
}
#chartjs-tooltip{
    padding: 16px;
    background-color: rgba(0,0,0,0.45);
}

#chartjs-tooltip >table{
    border-collapse: collapse !important;
}

#chartjs-tooltip > table > tbody > tr, #chartjs-tooltip > table > tbody > tr > td{
    padding: 0 !important;
    margin: 0 !important;
    border: 0px rgba(0,0,0,0) !important;
}

</style>
