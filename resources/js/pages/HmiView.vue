<template>
    <div class="dashboard-bg">
        <!--Dashboard body starts-->
        <div class="row main-body">
            <div class="col-md-12">
                <div class="row">
                    <!--Left column starts-->
                    <div class="col-md-4">
                        <div class="card card-style mt-3">
                            <div class="card-header card-header-bg text-center fw-bold">
                                Machine Information
                            </div>
                            <div class="card-body text-left">
                                <div class="row px-md-3 px-0 py-md-2 py-0">
                                    <div class="dropdown p-2 section-border">
                                        <label for="station-picker" style="font-size: 1.35rem; padding-top: 1px"><img src="../assets/Artboard7.png" alt=""/></label>
                                        <button class="btn btn-light dropdown-toggle text-uppercase" type="button" id="station-picker"
                                            data-bs-toggle="dropdown" aria-expanded="false"
                                            style="padding: 0px;font-size: 20px;font-weight: bold;margin-top: -5px; width: 80%;">
                                            {{ filter.stationName }}
                                        </button>
                                        <ul class="dropdown-menu text-uppercase" aria-labelledby="dropdownMenuButton1">
                                            <li class="text-uppercase" v-for="station in stations"
                                                :key="station.id"
                                                @click.prevent="changeSelectedStation(station)">
                                                <a class="dropdown-item" href="#">{{ station.name }}&nbsp;
                                                    <b-icon-check-circle v-if="station.id === filter.stationId"></b-icon-check-circle>
                                                </a>
                                            </li>
                                        </ul>
                                    </div>
                                </div>
                                <div class="row px-md-3 px-0 py-md-2 py-0">
                                    <div class="m-0 p-2 section-border d-flex justify-content-start">
                                        <div>
                                            <img src="../assets/Artboard9.png" alt=""/>
                                        </div>
                                        <div>
                                            <small class="text-black-50">Operator:</small>
                                            <h6 class="text-uppercase py-1 mb-0">
                                                <span class="fw-bolder">{{ operatorName }}</span>
                                            </h6>
                                        </div>
                                    </div>
                                </div>
                                <div class="row px-md-3 px-0 py-md-2 py-0">
                                    <div class="col-6 p-0">
                                        <div class="m-0 p-2 section-border d-flex justify-content-start">
                                            <div>
                                                <img src="../assets/Artboard8.png" alt=""/>
                                            </div>
                                            <div>
                                                <small class="text-black-50">Total Production:</small>
                                                <h6 class="text-uppercase py-1 mb-0">
                                                    <span><span class="fw-bolder">{{ machineStatus.production_counter2 }}</span> pcs</span>
                                                </h6>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-6 p-0">
                                        <div class="m-0 p-2 section-border d-flex justify-content-start">
                                            <div>
                                                <img src="../assets/Artboard7.png" alt=""/>
                                            </div>
                                            <div>
                                                <small class="text-black-50">Machine Status:</small>
                                                <h6 class="text-uppercase py-1 mb-0">
                                                    <div class="round" v-bind:class="{roundGreen, roundRed}"></div>
                                                    <span class="fw-bolder">{{ machineStatus.power_status }}</span>
                                                </h6>
                                            </div>

                                            <div v-if="isAlarm" class="col-md-12" style="margin-top: 10px">
                                                <span class="fw-bolder text-danger">Alarm:</span>
                                                <span class="fw-bolder text-danger">{{ machineStatus.alarm_info}}</span>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="card card-style mt-3">
                            <div class="card-header card-header-bg text-center fw-bold">
                                Production
                            </div>
                            <div class="card-body">
                                <div class="row px-md-3 px-0 py-md-2 py-0">
                                    <div class="row m-0 p-0 section-border">
                                        <div class="col-6">
                                            <small class="text-black-50">Cycle</small>
                                            <h6 class="py-1 mb-0">
                                                This Cycle
                                            </h6>
                                        </div>
                                        <div class="col-6">
                                            <span class="text-black-50">.</span>
                                            <h6 class="text-uppercase py-1 mb-0" style="text-align: right">
                                                <span class="fw-bolder">{{ machineStatus.cycle_time }}</span>
                                            </h6>
                                        </div>
                                    </div>
                                </div>
                                <div class="row px-md-3 px-0 py-md-2 py-0">
                                    <div class="row m-0 p-0 section-border">
                                        <div class="col-6">
                                            <small class="text-black-50">Counter #1</small>
                                            <h6 class="py-1 mb-0">
                                                M30
                                            </h6>
                                        </div>
                                        <div class="col-6">
                                            <span class="text-black-50">.</span>
                                            <h6 class="text-uppercase py-1 mb-0" style="text-align: right">
                                                <span class="fw-bolder">{{ machineStatus.production_counter1 }}</span>
                                            </h6>
                                        </div>
                                    </div>
                                </div>
                                <div class="row px-md-3 px-0 py-md-2 py-0">
                                    <div class="row m-0 p-0 section-border">
                                        <div class="col-6">
                                            <small class="text-black-50">Counter #2</small>
                                            <h6 class="py-1 mb-0">
                                                M30
                                            </h6>
                                        </div>
                                        <div class="col-6">
                                            <span class="text-black-50">.</span>
                                            <h6 class="text-uppercase py-1 mb-0" style="text-align: right">
                                                <span class="fw-bolder">{{ machineStatus.production_counter2 }}</span>
                                            </h6>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <!--Left column ends-->

                    <!--Middle column starts-->
                    <div class="col-md-4 middle-column">
                        <div class="card card-style mt-3">
                            <div class="card-header card-header-bg text-center fw-bold">
                                Programme
                            </div>
                            <div class="card-body text-left">
                                <div class="row px-md-3 px-0 py-md-2 py-0">
                                    <div class="row m-0 p-0 section-border bg-light">
                                        <div class="col-12">
                                            <h5 class="py-1 mb-0">
                                                <span class="fw-bolder">{{ machineStatus.program_name }}</span>
                                            </h5>
                                        </div>
                                    </div>
                                </div>
                                <div class="row px-md-3 px-0 py-md-2 py-0">
                                    <div class="row m-0 p-0 section-border">
                                        <div class="col-6">
                                            <small class="text-black-50">Spindle</small>
                                            <h6 class="py-1 mb-0">
                                                Spindle Speed
                                            </h6>
                                        </div>
                                        <div class="col-6">
                                            <span class="text-black-50">.</span>
                                            <h6 class="text-uppercase py-1 mb-0" style="text-align: right">
                                                <span class="fw-bolder">{{ parseFloat(machineStatus.spindle_speed).toFixed(2) }} RPM</span>
                                            </h6>
                                        </div>
                                    </div>
                                </div>
                                <div class="row px-md-3 px-0 py-md-2 py-0">
                                    <div class="row m-0 p-0 section-border">
                                        <div class="col-6">
                                            <small class="text-black-50">Feed</small>
                                            <h6 class="py-1 mb-0">
                                                Feed Rate
                                            </h6>
                                        </div>
                                        <div class="col-6">
                                            <span class="text-black-50">.</span>
                                            <h6 class="text-uppercase py-1 mb-0" style="text-align: right">
                                                <span class="fw-bolder">{{ machineStatus.feed_rate }} MMPH</span>
                                            </h6>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="card card-style mt-3">
                            <div class="card-header card-header-bg text-center fw-bold">
                                Machining
                            </div>
                            <div class="card-body">
                                <div class="row px-md-3 px-0 py-md-2 py-0">
                                    <div class="row m-0 p-0 section-border">
                                        <div class="col-6">
                                            <h6 class="py-1 mb-0">
                                                Tool Life
                                            </h6>
                                        </div>
                                        <div class="col-6">
                                            <h6 class="text-uppercase py-1 mb-0" style="text-align: right">
                                                <span class="fw-bolder">{{ machineStatus.cycle_time }}</span>
                                            </h6>
                                        </div>
                                    </div>
                                </div>
                                <div class="row px-md-3 px-0 py-md-2 py-0">
                                    <div class="row m-0 p-0 section-border">
                                        <div class="col-6">
                                            <h6 class="py-1 mb-0">
                                                Mood
                                            </h6>
                                        </div>
                                        <div class="col-6">
                                            <h6 class="text-uppercase py-1 mb-0" style="text-align: right">
                                                <span class="fw-bolder">{{ machineStatus.production_counter1 }}</span>
                                            </h6>
                                        </div>
                                    </div>
                                </div>
                                <div class="row px-md-3 px-0 py-md-2 py-0">
                                    <div class="row m-0 p-0 section-border">
                                        <div class="col-6">
                                            <h6 class="py-1 mb-0">
                                                Load On
                                            </h6>
                                        </div>
                                        <div class="col-6">
                                            <h6 class="text-uppercase py-1 mb-0" style="text-align: right">
                                                <span class="fw-bolder">{{ machineStatus.production_counter2 }}</span>
                                            </h6>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <!--Middle column ends-->

                    <!--Right column starts-->
                    <div class="col-md-4 right-column">
                        <div class="card card-style mt-3">
                            <div class="card-header card-header-bg text-center fw-bold">
                                Maintenance Feed
                            </div>
                            <div class="card-body">
                                <div class="row px-md-3 px-0 py-md-2 py-0">
                                    <div class="row m-0 p-1 section-border">
                                        <div class="col-12">
                                            <h6 class="p-0 mb-0">
                                                <strong>Maintenance Header</strong>
                                            </h6>
                                            <small>
                                                Maintenance details with short description.
                                            </small>
                                        </div>
                                    </div>
                                </div>
                                <div class="row px-md-3 px-0 py-md-2 py-0">
                                    <div class="row m-0 p-1 section-border">
                                        <div class="col-12">
                                            <h6 class="p-0 mb-0">
                                                <strong>Maintenance Header</strong>
                                            </h6>
                                            <small>
                                                Maintenance details with short description.
                                            </small>
                                        </div>
                                    </div>
                                </div>
                                <div class="row px-md-3 px-0 py-md-2 py-0">
                                    <div class="row m-0 p-1 section-border">
                                        <div class="col-12">
                                            <h6 class="p-0 mb-0">
                                                <strong>Maintenance Header</strong>
                                            </h6>
                                            <small>
                                                Maintenance details with short description.
                                            </small>
                                        </div>
                                    </div>
                                </div>
                                <div class="row px-md-3 px-0 py-md-2 py-0">
                                    <div class="row m-0 p-1 section-border">
                                        <div class="col-12">
                                            <h6 class="p-0 mb-0">
                                                <strong>Maintenance Header</strong>
                                            </h6>
                                            <small>
                                                Maintenance details with short description.
                                            </small>
                                        </div>
                                    </div>
                                </div>
                                <div class="row px-md-3 px-0 py-md-2 py-0">
                                    <div class="row m-0 p-1 section-border">
                                        <div class="col-12">
                                            <h6 class="p-0 mb-0">
                                                <strong>Maintenance Header</strong>
                                            </h6>
                                            <small>
                                                Maintenance details with short description.
                                            </small>
                                        </div>
                                    </div>
                                </div>
                                <div class="row px-md-3 px-0 py-md-2 py-0">
                                    <div class="row m-0 p-1 section-border">
                                        <div class="col-12">
                                            <h6 class="p-0 mb-0">
                                                <strong>Maintenance Header</strong>
                                            </h6>
                                            <small>
                                                Maintenance details with short description.
                                            </small>
                                        </div>
                                    </div>
                                </div>
                                <div class="row px-md-3 px-0 py-md-2 py-0">
                                    <div class="row m-0 p-1 section-border">
                                        <div class="col-12">
                                            <h6 class="p-0 mb-0">
                                                <strong>Maintenance Header</strong>
                                            </h6>
                                            <small>
                                                Maintenance details with short description.
                                            </small>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <!--Right column ends-->
                </div>
            </div>
        </div>
        <!--Dashboard body ends-->
    </div>

</template>

<script>

import dashboardService from "../services/DashboardService";
import moment from "moment";
import StationsService from "../services/StationsService";
import DowntimeReasonsService from "../services/DowntimeReasons";

export default {
    name: "HmiView",

    data() {
        return {
            machineStatus: [],
            stations: [],
            operatorName: '',
            roundGreen: false,
            roundRed: true,
            isAlarm: false,
            programStatus: false,
            filter: {
                stationId: 7,
                stationName: '',
            },
            dataUpdateTimer: null,
        }
    },

    methods: {
        getMachineData() {
            let params = {
                stationId: this.filter.stationId
            };
            dashboardService.getMachineStatus(params,response => {
                this.programStatus = response.programStatus
                this.machineStatus = response.machineStatus
                // console.log(this.programStatus)
                if (this.machineStatus.power_status === 'STOPPED') {
                    this.roundGreen = false
                    this.roundRed = true
                } else {
                    this.roundRed = false
                    this.roundGreen = true
                }

                if (this.machineStatus.alarm_info === 'NULL' || this.machineStatus.alarm_info === 'NO ACTIVE ALARMS') {
                    this.isAlarm = false
                } else {
                    this.isAlarm = true
                }
            });
        },
        changeSelectedStation(station) {
            this.filter.stationId = station.id;
            this.filter.stationName = station.name;

            this.getOperator()
            this.getMachineData()
        },
        getOperator() {
            let currentDate = new Date().toJSON().slice(0, 10).replace(/-/g, '/');
            axios.get('getOperatorName', {
                params: {
                    stationId: this.filter.stationId,
                    date: currentDate
                }
            })

                .then((response) => {
                    this.operatorName = response.data.operatorName
                })
        }
    },
    mounted() {

        this.filter.stationId = (this.$route.query.stn_id) ?  this.$route.query.stn_id : this.filter.stationId;
        const vm = this;
        StationsService.fetchAll({}, (data) => {
            //If a user comes to this page by clicking on a station from the SCADA page, the station will be selected by default
            //So we are taking that station to the front of the array
            var selectedStation = null;
            data = data.filter(function (item) {
                if((item['id'] + '') === (vm.filter.stationId + '')) selectedStation = item;
                return (item['id'] + '') !== (vm.filter.stationId + '');
            });
            data.unshift(selectedStation);
            this.$set(this, 'stations', data);
            this.filter.stationId = this.stations[0].id;
            this.filter.stationName = this.stations[0].name;
        });
        this.getOperator();
        this.getMachineData();

        this.dataUpdateTimer = setInterval(() => {
            this.getOperator()
            this.getMachineData()
        }, 1000*10);
    },
    destroyed() {
        clearInterval(this.dataUpdateTimer);
    }
}
</script>

<style>
.bitac-logo img {
    height: 34px;
    width: 105px;
}

.nav-bar {
    height: 55px;
    width: 100%;
    background-image: linear-gradient(to right, #00c595 5%, #0090fe 80%);
    padding: 12px 30px;
    color: white;
}

.main-nav {
    margin-left: 150px;
}

.main-nav a {
    text-decoration: none;
    font-size: 12px;
    color: white;
}

.time {
    font-size: 22px;
}

.main-body {
    margin: 25px;
}

.card-margin-top {
    margin-top: 25px;
}

.card-style {
    border: 1px solid #bbe7ff;
    border-radius: 0px;
    min-height: 181px;
    box-shadow: 0 4px 8px 0 rgba(0, 0, 0, 0.2), 0 2px 4px 0 rgba(0, 0, 0, 0.19);
}
.section-border {
    border: 1px solid #eeeeee;
}
.shadow-cus {
    box-shadow: 0 4px 8px 0 rgba(0, 0, 0, 0.2), 0 2px 4px 0 rgba(0, 0, 0, 0.19);
}

.main-body .card-body img {
    height: 50px;
    width: 48px;
    margin-top: 0px;
    margin-bottom: 0px;
    margin-right: 5px;
}

.round {
    display: inline-block;
    height: 16px;
    width: 16px;
    border-radius: 50%;
}

.roundGreen {
    background: #00dd20;
}

.roundRed {
    background: #FF0D0D;
}

.programStatus {
    font-weight: bolder;
    color: #FF0D0D;
}

.middle-column .card {
    border: none;
}

.right-column .card-3,
.right-column .card-4 {
    border: none;
}

.card-1 .card-header {
    background: white;
}

.card-1 .card-body {
    padding: 10px 25px 10px 25px;
    font-size: 14px;
}

.card-3.card-body {
    padding: 10px 25px 25px 25px;
}

.heading-color {
    color: #00c594;
}

.secondary-header {
    height: 40px;
    width: 100%;
    background: #2e25a5;
    color: white;
    font-size: 15px;
    padding: 0px 30px;
}

.card-header-bg {
    background: #10306c;
    color: white;
}

.font-12 {
    font-size: 12px;
}

.font-14 {
    font-size: 14px;
}

.borderless-table tr td {
    border-style: hidden !important;
}

.ml-120 {
    margin-left: 120px;
}

.mm {
    margin-left: 85px;
}

.load {
    margin-right: 68px;
}

.height-85 {
    height: 85px;
}

.height-420 {
    height: 420px;
}

.height-300 {
    height: 350px;
}

.dashboard-bg {
    background: #f4faff;
    /*height: 100vh;*/
    margin: 12px;
    margin-top: 0px;
}

.width-100 {
    width: 100%;
}

.width-90 {
    width: 90% !important;
}

.width-65 {
    width: 65% !important;
}

/* slider style starts */
.slide-range input[type='range'] {
    -webkit-appearance: none;
    margin: 10px 0;
    /* width: 300px; */
}

.slide-range input[type='range']:focus {
    outline: none;
}

.slide-range input[type='range']::-webkit-slider-runnable-track {
    width: 250px;
    height: 20px;
    cursor: pointer;
    box-shadow: 0px 0px 0px #000000, 0px 0px 0px #0d0d0d;
    background-image: linear-gradient(
        to right,
        #00c594 20%,
        #ffa400,
        #ff0000 85%
    );
}

.slide-range input[type='range']::-webkit-slider-thumb {
    box-shadow: 0px 0px 0px #000000, 0px 0px 0px #0d0d0d;
    height: 30px;
    width: 8px;
    border-radius: 5px;
    background: #008dff;
    cursor: pointer;
    -webkit-appearance: none;
    margin-top: -5px;
}

table {
    font-size: 13px;
    font-family: "Cascadia Code";
}

/* slider style ends */
</style>
