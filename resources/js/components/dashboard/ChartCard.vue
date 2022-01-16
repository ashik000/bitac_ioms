<template>
    <div class="col-md-3 mb-2">
        <div class="card shadow rounded-2 mx-2">
            <div v-if="summaryData" :class="getBackGroundColor" class="card-body">
                <div class="flex-container">
                    <h6 class="" style="color: white">{{this.summaryData.stationName}}</h6>
                    <h6 class="" style="color: white">{{this.summaryData.oee}}%</h6>
                </div>
                <div class="flex-container">
                    <div class="progress-bar-wrapper">
                        <div class="progress-bar">
                            <span class="progress-bar-fill" style="width: 70%;"></span>
                        </div>
                    </div>
                    <h6 class="" style="color: white">{{this.summaryData.performance}}%</h6>
                </div>
                <div class="flex-container">
                    <speed-chart></speed-chart>
                </div>
            </div>
            <div style="background-color: black" v-else>
                <speed-chart></speed-chart>
            </div>
        </div>
    </div>
</template>

<script>

import speedChart from "./SpeedChart";

export default {
    name: "ChartCard",
    props: {
        summaryData: {
            type: Object,
            required: false
        },
    },
    components: {
        'speed-chart' : speedChart
    },
    data: () => ({
    }),
    methods: {

    },
    computed : {
        getBackGroundColor() {
            switch (this.summaryData.color ) {
                case 'red':
                    return 'bg-danger';
                case 'green':
                    return 'bg-success';
            }
            return 'bg-dark';
        }
    },
    mounted() {
    },
}
</script>
<style scoped>
    .flex-container {
        display: flex;
        justify-content: space-between;
    }

    .progress-bar-wrapper {
        padding: 4px;
        width: 500px;
    }

    .progress-bar {
        width: 95%;
        background-color: #696363;
        padding: 1px;
        border-radius: 3px;
        box-shadow: inset 0 1px 3px rgba(0, 0, 0, .2);
    }

    .progress-bar-fill {
        display: block;
        height: 5px;
        background-color: #f7f7f8;
        border-radius: 3px;
        transition: width 500ms ease-in-out;
    }
</style>
