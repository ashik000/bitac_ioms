<template>
    <div class="p-2">
        <div class="chart-wrap">
            <canvas width="100%" height="150px" ref="chartElement"/>
        </div>

        <div class="progress-bars-legend">
            <div class="legend-wrap">
                <div class="progress-bar-wrap">
                    <div class="progress" :style="{ width: `${summaryData.availability}%` }" style="background: #03A9F4"></div>
                    <span class="progress-label">{{ `${summaryData.availability.toFixed(2)}%` }}</span>
                </div>
                <p>
                    Availability
                </p>
            </div>
            <div class="legend-wrap">
                <div class="progress-bar-wrap">
                    <div class="progress" :style="{ width: `${summaryData.performance}%` }" style="background: #8BC34A"></div>
                    <span class="progress-label">{{ `${summaryData.performance.toFixed(2)}%` }}</span>
                </div>
                <p>
                    Performance
                </p>
            </div>
            <div class="legend-wrap">
                <div class="progress-bar-wrap">
                    <div class="progress" :style="{ width: `${summaryData.quality}%` }" style="background: #FF9800"></div>
                    <span class="progress-label">{{ `${summaryData.quality.toFixed(2)}%` }}</span>
                </div>
                <p>
                    Quality
                </p>
            </div>
        </div>
    </div>
</template>

<script>
    import Chart from 'chart.js';
    import {clone} from "../../utils";

    export default {
        name: "OEESummaryPanel",
        props: {
            hourlyData: {
                type: Object,
            },
            summaryData: {
                type: Object,
            }
        },
        watch: {
            hourlyData: {
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
                    labels: this.hourlyData.labels,
                    datasets: [
                        {
                            type: 'line',
                            label: 'Performance',
                            data: clone(this.hourlyData.performance),
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
                            data: clone(this.hourlyData.quality),
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
                            data: clone(this.hourlyData.availability),
                            fill: false,
                            borderWidth: 2,
                            lineTension: 0,
                            pointBackgroundColor: '#222629',
                            pointBorderColor: '#03A9F4',
                            pointBorderWidth: 1,
                            pointHoverBackgroundColor: '#03A9F4',
                            pointHoverBorderWidth	: 2,
                            pointHoverRadius: 6,
                            borderColor: '#03A9F4',
                            backgroundColor: '#03A9F4',
                        },
                        {
                            label: 'OEE',
                            data: clone(this.hourlyData.oee),
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
                                callback: function(value, index, values) {
                                    return value + '%';
                                }
                            },
                            gridLines: {
                                offsetGridLines: true,
                                color: '#000'
                            }
                        }],
                        xAxes: [{
                            barPercentage: 0.5,
                            ticks: {
                                fontColor: '#000',
                            },
                            gridLines: {
                                offsetGridLines: true,
                                color: '#000',
                            }
                        },],
                    },
                    legend: {
                        display: false,
                    },
                    tooltips: {
                        callbacks: {
                            title: (tooltipItem, data) => `Hour: ${data.labels[tooltipItem[0].index]}:00`,
                            label: (tooltipItem, data) => data.datasets[tooltipItem.datasetIndex].data[tooltipItem.index] + '%'
                        }
                    }
                }
            });

            this.$data._chart = chart;
        }
    }
</script>

<style scoped lang="scss">
    .chart-wrap {
        position: relative;

        canvas {
            cursor: pointer;
        }
    }

    .progress-bars-legend {
        position: relative;
        display: flex;
        flex-direction: row;
        flex-wrap: nowrap;

        justify-content: space-between;

        .legend-wrap {
            width: 28%;
            color: #000;
            text-align: center;
        }

        .progress-bar-wrap {
            position: relative;
            height: 1.25rem;
            background: #000;
            border-radius: 0.25rem;

            .progress {
                height: 1.25rem;
            }

            .progress-label {
                position: absolute;
                display: block;

                left: 0;
                top: 0;
                right: 0;
                border-bottom: 0;
            }
        }
    }
</style>
