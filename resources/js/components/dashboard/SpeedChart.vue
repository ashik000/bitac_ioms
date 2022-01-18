<template>
    <div class="p-1">
        <div class="chart-wrap">
            <canvas height="200px" width="400px" ref="chartElement"/>
        </div>
    </div>
</template>

<script>
import Chart from 'chart.js';
import {clone} from "../../utils";
export default {
  name: "SpeedChart",
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
        chart.data.datasets.data = clone(n.speed);
        chart.update();
      },
      deep: true
    }
  },
  mounted() {
    let context = this.$refs.chartElement;
    var maxSpeed = this.dataset.speed.reduce(function (prevVal, currentVal) {
        return prevVal > currentVal? prevVal : currentVal;
    }, 0);

    const chart = new Chart(context, {
      type: 'line',
      data: {
        labels: clone(this.dataset.labels),
        datasets: [
          {
            label: "Speed Chart",
            data: clone(this.dataset.speed),
            borderColor: "#36495d",
            borderWidth: 1,
          },
        ],
      },
      options: {
        responsive: true,
        maintainAspectRatio: false,
        scales: {
          yAxes: [{
            display: true,
            gridLines: {
              display: false
            },
            ticks: {
              fontColor: '#fff',
              max: maxSpeed,
              min: 0,
              beginAtZero: true,
              stepSize: 1,
              grace: '10%',
              callback: function(value, index, values) {
                return value;
              }
            }
          }],
          xAxes: [{
            barPercentage: 0.5,
            ticks: {
              fontColor: '#fff',
            },
            gridLines: {
              display: false,
            }
          }],
        },
        legend: {
          display: false,
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

<style scoped>

</style>
