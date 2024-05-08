<template>
    <div class="col-md-3 mb-2">
        <div class="card shadow rounded-2 mx-2">

            <div v-if="summaryData" :class="getBackGroundColor" class="card-body rounded-2 main_card" style="min-height: 20rem;">
                <div class="flex-container">
                    <h6 class="text-white">{{this.summaryData.stationName}}</h6>
                    <h6 class="text-white">OEE: {{this.summaryData.oee}}%</h6>
                </div>
                <div class="flex-container">
                    <h6 class="text-white">{{this.summaryData.productName}}</h6>
                </div>
                <div class="flex-container" :class="{ bar_update: checkProgressBar }">
                    <div class="progress-bar-wrapper">
                        <div class="progress-bar">
                            <span class="progress-bar-fill" :style="getProgressBarWidth"></span>
                        </div>
                    </div>
                    <h6 class="text-white">{{this.summaryData.performance}}%</h6>
                </div>

                <div v-if="summaryData.stationId" @click="setCookie(summaryData.stationId)" style="cursor: pointer;">
                    <div class="flex-container" v-if="!makeInactive" >
                        <speed-chart :title="title" :dataset="summaryData"></speed-chart>
                    </div>

                    <div class="flex-container" v-else>
                        <div class="row d-flex align-items-center">
                            <div class="col-md-1 col-sm-12">
                                <div class="col-md-12">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="orange" class="bi bi-exclamation-circle-fill" viewBox="0 0 16 16">
                                        <path d="M16 8A8 8 0 1 1 0 8a8 8 0 0 1 16 0zM8 4a.905.905 0 0 0-.9.995l.35 3.507a.552.552 0 0 0 1.1 0l.35-3.507A.905.905 0 0 0 8 4zm.002 6a1 1 0 1 0 0 2 1 1 0 0 0 0-2z"></path>
                                    </svg>
                                </div>
                            </div>
                            <div class="col-11 col-sm-12">
                                <div class="row">
                                    <div class="col-12">
                                        <span class="text-white">Last Log Received {{ summaryData.timeDifference }}</span>
                                    </div>
                                    <div class="col-12">
                                        <span v-if="summaryData.stationType == 'regular'" class="text-white">Please check Inovace gateway</span>
                                        <span v-else class="text-white">Please check Walton QC API</span>
                                    </div>
                                    <div class="col-12">
                                        <span class="text-white">Device ID: {{ summaryData.deviceId }}</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

            </div>


            <div class="d-flex align-items-center justify-content-center card-body rounded-2" style="background-color: #262626; color: #FFFFFF; height: 20rem" v-else>
                <div class="row">
                    <div class="col-md-6 col-6">
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
                    <div class="col-md-6 col-6 text-center">
                        <img class="walton_logo" src="storage/images/bitac-logo.png" alt="bitac-logo" style="">
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
        makeInactive: false,
        checkProgressBar: false,
        checkSummaryData: false,
        selectedStationId: null,
    }),
    methods: {
        generateStationId(stationId) {
            return "/lineview?stn_id=" + stationId;
        },
        setCookie(stnId) {
            this.selectedStationId = stnId;
            this.$cookies.set("selectedStationIdFromDashboard",stnId,"15d");
            window.location.href = "/lineview?stn_id=" + stnId;
        },
        deleteCookie(name) {
            this.$cookies.remove(name);
        },
    },
    computed : {
        getBackGroundColor() {
            switch (this.summaryData.color ) {
                case 'red':
                    return 'bg-danger';
                case 'green':
                    return 'bg-success';
            }
            this.makeInactive = true;
            return 'inactive-card';
        },
        getProgressBarWidth() {
            return "width:" +  this.summaryData.performance + "%";
        }
    },
    mounted() {
        // if (this.summaryData) {
        //   this.checkProgressBar = true;
        //   this.checkSummaryData = true;
        // }
        // if (this.summaryData.deviceId === 'N/A') {
        //   console.log('Device ID not found');
        // } else {
        //   console.log('Device ID found');
        // }
    },
    updated() {
        // if (this.summaryData) {
        //   this.checkProgressBar = true;
        //   this.checkSummaryData = true;
        // }
        // if (this.summaryData.deviceId === 'N/A') {
        //   console.log('Device ID not found');
        // } else {
        //   console.log('Device ID found');
        // }
    },
}
</script>
<style scoped>

.flex-container {
    display: flex;
    justify-content: space-between;
}

.progress-bar-wrapper {
    margin-top: 6px;
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
    background-color: #030303;
    max-width: 50rem!important;
}
.walton_logo {
    height: 125px; width: 125px; border-radius: 50%;
}

@media (max-width: 1200px) {
    .walton_logo {
        height: 100px; width: 100px; border-radius: 50%;
    }
}
@media (max-width: 768px) {
    .main_card {
        max-width: 30rem;
    }
    .inactive-card {
        max-width: 30rem!important;
    }
    .bar_update {
        width: 450px;
    }
}
@media (max-width: 520px) {
    .main_card {
        max-width: 28.5rem;
    }
    .inactive-card {
        max-width: 28.5rem!important;
    }
    .bar_update {
        width: 425px;
    }
}
@media (max-width: 495px) {
    .main_card {
        max-width: 27rem;
    }
    .inactive-card {
        max-width: 27rem!important;
    }
    .bar_update {
        width: 410px;
    }
}
@media (max-width: 450px) {
    .main_card {
        max-width: 25rem;
    }
    .inactive-card {
        max-width: 25rem!important;
    }
    .bar_update {
        width: 350px;
    }
}
@media (max-width: 400px) {
    .main_card {
        max-width: 23rem;
    }
    .inactive-card {
        max-width: 22rem!important;
    }
    .bar_update {
        width: 330px;
    }
}
@media (max-width: 390px) {
    .main_card {
        max-width: 21.5rem;
    }
    .chart_card_update {
        max-width: 10rem!important;
    }
}
@media (max-width: 375px) {
    .main_card {
        max-width: 21rem;
    }
    .inactive-card {
        max-width: 21rem!important;
    }
    .bar_update {
        width: 300px;
    }
}
@media (max-width: 350px) {
    .main_card {
        max-width: 18rem;
    }
    .inactive-card {
        max-width: 18rem!important;
    }
    .bar_update {
        width: 270px;
    }
}
@media (max-width: 320px) {
    .main_card {
        max-width: 16.5rem;
    }
    .inactive-card {
        max-width: 16.5rem!important;
    }
    .bar_update {
        width: 230px;
    }
}
</style>
