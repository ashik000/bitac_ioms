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
                    console.log("Data changed");
                    const chart = this.$data._chart;

                    if (chart === null) {
                        return;
                    }

                    chart.data.labels = n.labels;
                    chart.data.datasets[0].data = clone(n.downtimes);
                    chart.options.scales.yAxes[0].ticks.suggestedMax = this.dataset.downtimes.reduce((max, element) => max > element? max : element, 0);
                    chart.update();
                },
                deep: true
            }
        },
        mounted() {
            console.log('Mounted');
            let context = this.$refs.downtimeChart;

            const chart = new Chart(context, {
                type: 'bar',
                data: {
                    labels: this.dataset.labels,
                    datasets: [
                        {
                            label: "Downtime",
                            data: clone(this.dataset.downtimes),
                            // pointHoverBackgroundColor: '#9E9E9E',
                            // pointHoverRadius: 6,
                            // borderColor: '#9E9E9E',
                            backgroundColor: 'rgb(225,0,0)',
                        }
                    ]
                },
                options: {
                    maintainAspectRatio: false,
                    scales: {
                        yAxes: [{
                            display: true,
                            ticks: {
                                fontColor: '#ffffff',
                                suggestedMax: this.dataset.downtimes.reduce((max, element) => max > element? max : element, 0),
                                min: 0,
                                beginAtZero: true,
                                stepSize: this.dataset.downtimes.reduce((max, element) => max > element? max : element, 0)/10,
                                callback: function(value, index, values) {
                                    return moment.duration(value, "seconds").format("hh[h]:mm[m]:ss[s]");
                                }
                            },
                            gridLines: {
                                offsetGridLines: true,
                                color: 'rgba(255, 255, 255, 0.15)'
                            }
                        }],
                        xAxes: [{
                            barPercentage: 0.5,
                            ticks: {
                                fontColor: '#ffffff',
                            },
                            gridLines: {
                                offsetGridLines: true,
                                color: 'rgba(255, 255, 255, 0.15)'
                            }
                        }],
                    },
                    // legend: {
                    //     position: 'bottom',
                    //     usePointStyle: true
                    // },
                    tooltips: {
                        callbacks: {
                            label: (tooltipItem, data) => {
                                console.log(tooltipItem);
                                return tooltipItem.value + " seconds";
                            }
                        }
                    }
                }
            });

            this.$data._chart = chart;
        }
    }
</script>
