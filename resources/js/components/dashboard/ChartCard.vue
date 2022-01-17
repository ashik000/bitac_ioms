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
                            <span class="progress-bar-fill" :style="getProgressBarWidth"></span>
                        </div>
                    </div>
                    <h6 class="" style="color: white">{{this.summaryData.performance}}%</h6>
                </div>
                <div class="flex-container">
                    <speed-chart :title="title" :dataset="summaryData"></speed-chart>
                </div>
            </div>
            <div class="d-flex align-items-center justify-content-center card-body" style="background-color: #34403A; color: #FFFFFF; height: 18rem" v-else>
                <div class="row">
                    <div class="col-md-6">
                        <div class="row" style="font-size: 1.2rem">
                            <span>
                                Stations: <br> <span style="font-size: 1.8rem; font-weight: 600;">{{this.stationC ? this.stationC : '0'}}</span>
                            </span>
                            <span>
                                Operating: {{ this.activeStationCount ? this.activeStationCount : '0' }}
                            </span>
                            <span>
                                Stopped: {{ this.inactiveStationCount ? this.inactiveStationCount : '0' }}
                            </span>
                        </div>
                    </div>
                    <div class="col-md-6 text-center">
                        <img src="storage/images/walton-logo-dashboard.jpg" alt="walton-logo" style="height: 125px; width: 125px; border-radius: 50%">
                    </div>
                </div>
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
        stationC: {
            type: Number,
            required: false
        },
        activeStationCount: {
            type: Number,
            required: false
        },
        inactiveStationCount: {
            type: Number,
            required: false
        },
    },
    components: {
        'speed-chart' : speedChart
    },
    data: () => ({
      title: "Speed Chart",
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
            return 'inactive-card';
        },
        getProgressBarWidth() {
            return "width:" +  this.summaryData.performance + "%";
        }
    },
    mounted() {
      console.log(this.stationC);
    }
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

    .inactive-card {
        background-color: #34403A;
    }
</style>
