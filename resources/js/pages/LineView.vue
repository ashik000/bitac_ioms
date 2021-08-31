<template>
    <div class="container-fluid">
            <header class="row" style="background-color: red;">
                <div class="col" style="background-color: purple;">
                    <div class="gauge-container d-flex flex-direction-row">
                        <div class="gauge">
                            <template>
                                <vue-gauge :refid="'oee-gauge'" :options="{'needleValue': this.gaugeTotalOee, 'arcDelimiters':[33, 66], 'arcOverEffect': false, 'arcColors': [ 'red', 'yellow', 'green'] ,'needleColor': 'black'}"></vue-gauge>
                            </template>

                            <span>{{ `${gaugeTotalOee}%` }}</span>
                        </div>

                        <div class="gauge">
                            <template>
                                <vue-gauge :refid="'availability-gauge'" :options="{'needleValue': this.oeeSummary.summary.availability.toFixed(2), 'arcDelimiters': [], 'arcOverEffect': false, 'arcColors': [ 'blue'] ,'needleColor': 'black'}"></vue-gauge>
                            </template>

                            <span>{{ `${oeeSummary.summary.availability.toFixed(2)}%` }}</span>
                        </div>

                        <div class="gauge">
                            <template>
                                <vue-gauge :refid="'performance-gauge'" :options="{'needleValue': this.gaugePerformance, 'arcDelimiters':[], 'arcOverEffect': false, 'arcColors': [ 'green'] ,'needleColor': 'black'}"></vue-gauge>
                            </template>

                            <span>{{ `${gaugePerformance}%` }}</span>
                        </div>

                        <div class="gauge">
                            <template>
                                <vue-gauge :refid="'oee-gauge'" :options="{'needleValue': this.gaugeQuality, 'arcDelimiters':[], 'arcOverEffect': false, 'arcColors': ['yellow'] ,'needleColor': 'black'}"></vue-gauge>
                            </template>

                            <span>{{ `${gaugeQuality}%` }}</span>
                        </div>

                    </div>
                </div>


                <div class="col" style="background-color: blue;">

                    <oee-summary-panel
                        :hourly-data="oeeSummary.hourly"
                        :summary-data="oeeSummary.summary">
                    </oee-summary-panel>
                </div>

                <div class="col" style="background-color: green;">
                    <label for="station-picker">STATION:</label>
                    <button class="station-picker" @click.prevent="showStationSelectionForm">
                        {{ filter.stationName }}
                    </button>

                    <production-summary-panel
                        :produced="oeeSummary.summary.produced"
                        :expected="oeeSummary.summary.expected"
                        :oee="oeeSummary.summary.oee"
                        :products="products">
                    </production-summary-panel>

                    <v-date-picker :max-date='new Date()'
                                   mode='single' v-model='filter.selectedDate'
                                   :masks="{ input: 'WWW, DD MMMM' }"
                                   :input-props="{ class: 'date-picker-input border-0 w-full bg-transparent px-2 text-white h5' }"
                                   @input="changeSelectedDate">
                    </v-date-picker>
                </div>
            </header>

        <div class="row">
            <line-view-graph
                class="col"
                :linedata="linedata.logs"
                @downtime-clicked="openDowntimeReasonsSelectionModal">
            </line-view-graph>
        </div>

        <footer class="row">
            <div class="controls container-fluid">
                <button @click="isOperatorSelectionModalShown = true">
                    <i class="icon material-icons">account_circle</i>
                    Operator
                </button>
                <button @click="isDowntimeSummaryModalShown = true">
                    <i class="icon material-icons">timer</i>
                    Downtime
                </button>
                <button @click="isScrapInputModalShown = true">
                    <i class="icon material-icons">delete</i>
                    Scrap
                    <title>Delete Downtime Reason</title>
                </button>
            </div>
        </footer>

        <modal v-if="isStationSelectionFormShown"
               class="station-selector-modal"
               @close="isStationSelectionFormShown = false">
            <template v-slot:header>
                <h4 class="text-uppercase">Select Station</h4>
            </template>
            <template v-slot:content>
                <ul class="stations-list">
                    <li v-for="station in stations"
                        :key="station.id"
                        @click.prevent="changeSelectedStation(station)">
                        {{ station.name }}
                        <i class="material-icons" v-if="station.id === filter.stationId">done</i>
                    </li>
                </ul>
            </template>
            <template v-slot:footer>
                <div class="d-flex justify-content-end">
                    <button class="btn btn-outline-danger" @click.prevent="isStationSelectionFormShown = false">Close
                    </button>
                </div>
            </template>
        </modal>

        <modal v-if="isDowntimeReasonsModalShown"
               class="reason-selector-modal"
               @close="isDowntimeReasonsModalShown = false"
        >
            <template v-slot:header>
                <h4 class="text-uppercase">Select Downtime Reason</h4>
            </template>
            <template v-slot:content>
                <ul class="reasons-list">
                    <li v-for="reason in downtimeReasons"
                        :key="reason.id"
                        @click.prevent="assignDowntimeReason(reason)">
                        {{ reason.name }}
                    </li>
                </ul>
            </template>
        </modal>

        <operator-selection-modal
            v-if="isOperatorSelectionModalShown"
            @close="operatorSelectionModalClosed()"
            :station-id="filter.stationId"
            :station-name="filter.stationName">
        </operator-selection-modal>

        <downtime-summary-modal
            v-if="isDowntimeSummaryModalShown"
            @close="downtimeSummaryModalClosed()"
            :stationId="filter.stationId"
            :date="filter.selectedDate"></downtime-summary-modal>

        <scrap-input-model
            v-if="isScrapInputModalShown"
           @close="scrapInputModalClosed()"
           :stationId="filter.stationId"
           :date="filter.selectedDate"></scrap-input-model>
    </div>
</template>

<script>
    import LineViewService from "../services/LineViewService";
    import StationsService from "../services/StationsService";
    import DowntimeReasonsService from '../services/DowntimeReasons'
    import {LineViewDataTransformer} from "../transformers/LineViewDataTransformer";

    import OEESummaryPanel from "../components/lineview/OEESummaryPanel";
    import ProductionSummaryPanel from "../components/lineview/ProductionSummaryPanel";
    import LineViewGraph from "../components/lineview/LineViewGraph";
    import Modal from "../components/Modal";
    import DowntimeSummaryModal from "../components/lineview/downtimesummary/DowntimeSummaryModal";
    import ScrapInputModel from "../components/lineview/scrapinput/ScrapInputModal";
    import OperatorSelectionModal from "../components/lineview/operatorselection/OperatorSelectionModal";
    import VueGauge from 'vue-gauge';

    export default {
        name: "LineView",
        components: {
            OperatorSelectionModal,
            Modal,
            DowntimeSummaryModal,
            LineViewGraph,
            ProductionSummaryPanel,
            'oee-summary-panel': OEESummaryPanel,
            ScrapInputModel,
            VueGauge,
        },
        data: () => ({
            currentTime: '',
            isInitialized: false,
            isStationSelectionFormShown: false,
            isDowntimeReasonsModalShown: false,
            isDowntimeSummaryModalShown: false,
            isOperatorSelectionModalShown: false,
            isScrapInputModalShown: false,
            selectedDowntime: null,
            filter: {
                stationId: 1,
                stationName: '',
                selectedDate: new Date(),
            },
            downtimeReasons: [],
            stations: [],
            products: [],
            oeeSummary: {
                hourly: {
                    labels: [],
                    performance: [],
                    availability: [],
                    quality: [],
                    oee: [],
                },
                summary: {
                    produced: 0,
                    expected: 0,
                    performance: 0,
                    availability: 0,
                    quality: 0,
                }
            },
            linedata: {
                logs: [],
            },
            gaugeTotalOee: 0,
            gaugeAvailability: 0,
            gaugePerformance: 0,
            gaugeQuality: 0,
        }),
        methods: {
            showStationSelectionForm(show = false) {
                this.isStationSelectionFormShown = show;
            },
            operatorSelectionModalClosed(){
                this.isOperatorSelectionModalShown = false;
            },
            downtimeSummaryModalClosed() {
                this.isDowntimeSummaryModalShown = false;
                this.fetchData();
            },
            scrapInputModalClosed() {
                this.isScrapInputModalShown = false;
            },
            changeSelectedDate(type) {
                switch (type) {
                    case 'PREV':
                        this.filter.selectedDate = moment(this.filter.selectedDate).subtract({days: 1}).toDate();
                        break;
                    case 'NEXT':
                        this.filter.selectedDate = moment(this.filter.selectedDate).add({days: 1}).toDate();
                        break;
                    default:
                        this.filter.selectedDate = moment(type).toDate();
                        break;
                }

                this.fetchData();

                this.isStationSelectionFormShown = false;
            },
            changeSelectedStation(station) {
                this.filter.stationId = station.id;
                this.filter.stationName = station.name;
                this.isStationSelectionFormShown = false;

                this.fetchData();
            },
            openDowntimeReasonsSelectionModal(bar) {
                if (bar.type === 'downtime') {
                    this.isDowntimeReasonsModalShown = true;
                    this.selectedDowntime = bar;
                    DowntimeReasonsService.fetchAll([], reasons => {
                        this.reasons = reasons;
                    })
                }
            },
            assignDowntimeReason(reason) {
                DowntimeReasonsService.assignDowntime({
                    downtimeId: this.selectedDowntime.id,
                    downtimeReasonId: reason.id,
                }, data => {
                    this.isDowntimeReasonsModalShown = false;
                });
            },
            fetchData() {
                LineViewService.fetchLineViewData({
                        stationId: this.filter.stationId,
                        date: moment(this.filter.selectedDate).format('YYYY-MM-DD')
                    },
                    (data) => {
                        const {products, logs, hourlyMetric, summaryMetric} = LineViewDataTransformer(data);

                        this.$set(this, 'products', products);
                        this.$set(this.linedata, 'logs', logs);
                        this.$set(this.oeeSummary, 'hourly', hourlyMetric);
                        this.$set(this.oeeSummary, 'summary', summaryMetric);

                        this.isInitialized = true;
                    }
                );
            }
        },
        computed: {
            formattedSelectedDate() {
                return moment(this.filter.selectedDate).format('dddd, DD MMMM, YYYY');
            },
            totalOee(){
                let oee = isNaN(this.oeeSummary.summary.oee * 100) ? 0 : (this.oeeSummary.summary.oee * 100).toFixed(2);
                this.gaugeTotalOee = oee;
                return oee;
            }
        },
        mounted() {
            const vm = this;
            this.$data._clock = () => {
                vm.currentTime = moment().format('LTS');
            };
            setInterval(this.$data._clock, 1000);

            this.fetchData();
            this.$data._updateData = () => {
                if (moment().diff(vm.filter.selectedDate, 'day') === 0) {
                    vm.fetchData();
                }
            };
            setInterval(this.$data._updateData, 10000);

            StationsService.fetchAll({}, (data) => {
                this.$set(this, 'stations', data);
                this.filter.stationId = this.stations[0].id;
                this.filter.stationName = this.stations[0].name;
            });

            DowntimeReasonsService.fetchAll({}, (data) => {
                this.$set(this, 'downtimeReasons', data);
            });
        },
        destroyed() {
            clearInterval(this.$data._clock);
            clearInterval(this.$data._updateData);
        }
    }
</script>
