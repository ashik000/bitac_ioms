<template>
    <div class="wrap">
        <h4 class="text-center text-uppercase">{{ title }}</h4>

        <div class="chart-container">
            <canvas height="360" ref="chartElement"/>
        </div>
    </div>
</template>

<script>
    import Chart from 'chart.js';
    import {clone} from "../../utils";

    export default {
        name: "OEEChart",
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
                    chart.data.datasets.forEach((dataset) => {
                        switch (dataset.label) {
                            case 'Performance':
                                dataset.data = clone(n.performance);
                                break;
                            case 'Quality':
                                dataset.data = clone(n.quality);
                                break;
                            case 'Availability':
                                dataset.data = clone(n.availability);
                                break;
                            case 'OEE':
                                dataset.data = clone(n.oee);
                                break;
                        }
                    });

                    chart.update();
                },
                deep: true
            }
        },
        mounted() {
            let context = this.$refs.chartElement;

            const chart = new Chart(context, {
                type: 'bar',
                data: {
                    labels: this.dataset.labels,
                    datasets: [
                        {
                            type: 'line',
                            label: 'Performance',
                            data: clone(this.dataset.performance),
                            fill: false,
                            borderWidth: 2,
                            lineTension: 0,
                            pointBackgroundColor: '#222629',
                            pointBorderColor: '#8BC34A',
                            pointBorderWidth: 1,
                            pointHoverBackgroundColor: '#8BC34A',
                            pointHoverRadius: 6,
                            borderColor: '#8BC34A',
                            backgroundColor: '#8BC34A',
                        }, {
                            type: 'line',
                            label: 'Quality',
                            data: clone(this.dataset.quality),
                            fill: false,
                            borderWidth: 2,
                            lineTension: 0,
                            pointBackgroundColor: '#222629',
                            pointBorderColor: '#FF9800',
                            pointBorderWidth: 1,
                            pointHoverBackgroundColor: '#FF9800',
                            pointHoverRadius: 6,
                            borderColor: '#FF9800',
                            backgroundColor: '#FF9800',
                        }, {
                            type: 'line',
                            label: 'Availability',
                            data: clone(this.dataset.availability),
                            fill: false,
                            borderWidth: 2,
                            lineTension: 0,
                            pointBackgroundColor: '#222629',
                            pointBorderColor: '#1947a4',
                            pointBorderWidth: 1,
                            pointHoverBackgroundColor: '#1947a4',
                            pointHoverBorderWidth	: 2,
                            pointHoverRadius: 6,
                            borderColor: '#1947a4',
                            backgroundColor: '#1947a4',
                            grace: '10%',
                        },
                        {
                            label: 'OEE',
                            data: clone(this.dataset.oee),
                            pointHoverBackgroundColor: '#9E9E9E',
                            pointHoverRadius: 6,
                            borderColor: '#9E9E9E',
                            backgroundColor: '#9E9E9E',
                        },
                    ],
                },
                options: {
                    maintainAspectRatio: false,
                    scales: {
                        yAxes: [{
                            display: true,
                            ticks: {
                                fontColor: '#000',
                                max: 100,
                                min: 0,
                                beginAtZero: true,
                                stepSize: 25,
                                grace: '10%',
                                callback: function(value, index, values) {
                                    return value + '%';
                                }
                            },
                            gridLines: {
                                offsetGridLines: true,
                                color: '#e8e8e8',
                                lineWidth: 2,
                                drawBorder: false
                            }
                        }],
                        xAxes: [{
                            barPercentage: 0.5,
                            ticks: {
                                fontColor: '#000',
                            },
                            gridLines: {
                                display: false,
                            }
                        }],
                    },
                    legend: {
                        position: 'bottom',
                        usePointStyle: true
                    },
                    tooltips: {
                        callbacks: {
                            label: (tooltipItem, data) => data.datasets[tooltipItem.datasetIndex].data[tooltipItem.index].toFixed(2) + '%'
                        }
                    }
                }
            });

            this.$data._chart = chart;
        }
    }
</script>

