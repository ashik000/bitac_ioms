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
                    chart.data.datasets[0].data = clone(n.planned);
                    chart.data.datasets[1].data = clone(n.unplanned);
                    // chart.options.scales.yAxes[0].ticks.suggestedMax = this.dataset.downtimes.reduce((max, element) => max > element? max : element, 0);
                    chart.update();
                },
                deep: true
            }
        },
        mounted() {
            console.log('Mounted');
            let context = this.$refs.downtimeChart;

            // console.log(this.dataset.x_labels);

            const chart = new Chart(context, {
                type: 'bar',
                data: {
                    labels: this.dataset.labels,
                    datasets: [
                        {
                            label: "Planned",
                            data: clone(this.dataset.planned),
                            // pointHoverBackgroundColor: '#9E9E9E',
                            // pointHoverRadius: 6,
                            // borderColor: '#9E9E9E',
                            backgroundColor: '#0000FF',
                        },
                        {
                          label: "Unplanned",
                          data: clone(this.dataset.unplanned),
                          // pointHoverBackgroundColor: '#9E9E9E',
                          // pointHoverRadius: 6,
                          // borderColor: '#9E9E9E',
                          backgroundColor: '#ff0000',
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
                          gridLines: { display: false },
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
