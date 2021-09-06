<template>
    <div class="container-fluid mt-4">
        <header class="row">
            <div class="col mx-2" style="border: 1px solid grey">
                <div class="gauge-container d-flex flex-direction-row flex-nowrap justify-content-start align-items-baseline">
                    <div class="gaugeL">
                        <div id="totalOee"></div>
                        <span>{{ `${gaugeTotalOee}%` }}</span>
                        <span>STATION OEE</span>
                    </div>
                    <div class="gaugeS">
                        <div id="totalAvailability"></div>
                        <span>{{ `${gaugeAvailability}%` }}</span>
                        <span>AVAILABILITY</span>
                    </div>
                    <div class="gaugeS">
                        <div id="totalPerformance"></div>
                        <span>{{ `${gaugePerformance}%` }}</span>
                        <span>PERFORMANCE</span>
                    </div>
                    <div class="gaugeS">
                        <div id="totalQuality"></div>
                        <span>{{ `${gaugeQuality}%` }}</span>
                        <span>QUALITY</span>
                    </div>
                </div>
            </div>


            <div class="col mx-2" style="border: 1px solid grey">
                <oee-summary-panel
                    :hourly-data="oeeSummary.hourly">
                </oee-summary-panel>
            </div>

            <div class="col mx-2" style="border: 1px solid grey">

                <div class="row mt-1">
                    <div class="col-md-2">
                        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-gear-fill" viewBox="0 0 16 16">
                            <path d="M9.405 1.05c-.413-1.4-2.397-1.4-2.81 0l-.1.34a1.464 1.464 0 0 1-2.105.872l-.31-.17c-1.283-.698-2.686.705-1.987 1.987l.169.311c.446.82.023 1.841-.872 2.105l-.34.1c-1.4.413-1.4 2.397 0 2.81l.34.1a1.464 1.464 0 0 1 .872 2.105l-.17.31c-.698 1.283.705 2.686 1.987 1.987l.311-.169a1.464 1.464 0 0 1 2.105.872l.1.34c.413 1.4 2.397 1.4 2.81 0l.1-.34a1.464 1.464 0 0 1 2.105-.872l.31.17c1.283.698 2.686-.705 1.987-1.987l-.169-.311a1.464 1.464 0 0 1 .872-2.105l.34-.1c1.4-.413 1.4-2.397 0-2.81l-.34-.1a1.464 1.464 0 0 1-.872-2.105l.17-.31c.698-1.283-.705-2.686-1.987-1.987l-.311.169a1.464 1.464 0 0 1-2.105-.872l-.1-.34zM8 10.93a2.929 2.929 0 1 1 0-5.86 2.929 2.929 0 0 1 0 5.858z"/>
                        </svg>
                    </div>
                    <div class="col-md-10">
                      <div class="dropdown">
                        <label for="station-picker" style="font-weight: 500; font-size: 1.2rem;">STATION:</label>
                        <button class="btn btn-light dropdown-toggle" type="button" id="station-picker" data-bs-toggle="dropdown" aria-expanded="false">
                          {{ filter.stationName }}
                        </button>
                        <ul class="dropdown-menu" aria-labelledby="dropdownMenuButton1">
                          <li v-for="station in stations"
                              :key="station.id"
                              @click.prevent="changeSelectedStation(station)">
                            <a class="dropdown-item" href="#">{{ station.name }}&nbsp;<i class="material-icons" v-if="station.id === filter.stationId">done</i>
                            </a>
                          </li>
                        </ul>
                      </div>
                    </div>
                </div>

              <div class="row my-1">
                <div class="col-md-2">
                </div>
                <div class="col-md-10">
                  Operator: {{ filter.stationOperatorName }}
                </div>
              </div>

                <div class="row">
                  <div class="col-md-6">
                    <production-summary-panel
                        :produced="oeeSummary.summary.produced"
                        :expected="oeeSummary.summary.expected"
                        :oee="oeeSummary.summary.oee"
                        :products="products">
                    </production-summary-panel>
                  </div>

                  <div class="col-md-6">
                    <div class="mt-4 card" style="width: 12rem;">
                      <div class="card-body p-1">
                        <v-date-picker :max-date='new Date()'
                                      mode='single' v-model='filter.selectedDate'
                                      :masks="{ input: 'WWW, DD MMMM' }"
                                      :input-props="{ class: 'date-picker-input border-0 w-full bg-transparent px-2 h5' }"
                                      @input="changeSelectedDate">
                        </v-date-picker>
                      </div>
                    </div>

                  </div>
                </div>
            </div>

        </header>



        <div class="row mt-4">
            <div class="col-md-2 mt-2">
                <div class="table-responsive">
                    <table class="table">
                        <thead class="top_downtime_table_header">
                            <tr>
                                <th colspan="2">
                                    Top Downtime Reasons (last 7 days)
                                </th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr v-for="topDowntimeReason in topDowntimeReasons">
                                <td>{{ topDowntimeReason.reason_name }}</td>
                                <td>{{ topDowntimeReason.duration }}</td>
                            </tr>
                        </tbody>
                    </table>
                </div>

                <div class="table-responsive">
                    <table class="table">
                        <thead class="top_downtime_table_header">
                        <tr>
                            <th colspan="2">Top Operator Downtime Reasons (last 7 days)</th>
                        </tr>
                        </thead>
                        <tbody>
                        <tr v-for="topOperatorDowntimeReason in topOperatorDowntimeReasons">
                            <td>{{ topOperatorDowntimeReason.operator_name }}</td>
                            <td>{{ topOperatorDowntimeReason.duration }}</td>
                        </tr>
                        </tbody>
                    </table>
                </div>
            </div>


            <div class="col-md-10 mt-2">
                <div class="row">
                    <line-view-graph
                        class="col"
                        :linedata="linedata.logs"
                        :stationShiftsData="stationShifts.data"
                        @stationshift-selected="updateStationShiftId"
                        @downtime-clicked="openDowntimeReasonsSelectionModal"
                        @reportDefects="submitReportDefects"
                    >
                    </line-view-graph>
                </div>
            </div>

        </div>


        <footer class="row">
            <div class="controls container-fluid">
                <button class="btn btn-secondary" @click="">
                    Assign Product
                </button>
                <button class="btn btn-secondary" @click="isOperatorSelectionModalShown = true">
                    Assign Operator
                </button>
                <button class="btn btn-secondary" @click="isDowntimeSummaryModalShown = true">
                    Assign Downtime Reason
                </button>
                <button class="btn btn-secondary" @click="isScrapInputModalShown = true">
                    Batch Defect Entry
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

        <product-selection-modal
            v-if="isProductSelectionModalShown"
            @close="productSelectionModalClosed()"
            :station-id="filter.stationId"
            :station-name="filter.stationName">
        </product-selection-modal>

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
            :date="filter.selectedDate">
        </downtime-summary-modal>

        <scrap-input-model
          v-if="isScrapInputModalShown"
          @close="scrapInputModalClosed()"
          :stationId="filter.stationId"
          :date="filter.selectedDate">
        </scrap-input-model>
    </div>
</template>

<script>
    import * as GaugeChart from "gauge-chart"
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
    import moment from "moment";
    import downtimeReasonsService from "../services/DowntimeReasons";
    import toastrService from "../services/ToastrService";

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
        },
        data: () => ({
            currentTime: '',
            isInitialized: false,
            isStationSelectionFormShown: false,
            isDowntimeReasonsModalShown: false,
            isDowntimeSummaryModalShown: false,
            isOperatorSelectionModalShown: false,
            isProductSelectionModalShown: false,
            isScrapInputModalShown: false,
            selectedDowntime: null,
            filter: {
                stationId: 1,
                stationShiftId: null,
                stationName: '',
                stationOperatorName: 'N/A',
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
            gaugeOptions: {
                hasNeedle: true,
                needleColor: 'black',
                arcColors: ['#EAEAEA'],
                arcDelimiters: [],
            },
            range: {
                start: moment().startOf('week').subtract(7, "days").toDate(),
                end: moment().endOf('day').subtract(7, "days").toDate()
            },
            topDowntimeReasons: null,
            topOperatorDowntimeReasons: null,
            stationShifts: {
                data: []
            }
        }),
        watch: {
            gaugeTotalOee(nv, ov){
                let element = document.querySelector('#totalOee');
                element.innerHTML = "";
                GaugeChart.gaugeChart(element, 150, {...this.gaugeOptions, ...{arcColors: ['#FF2D1C', '#FFA600', '#49B92A'],
                        arcDelimiters: [30, 70]}}).updateNeedle(nv);
            },
            gaugeAvailability(nv, ov){
                console.log("Availability");
                console.log(nv);
                let element = document.querySelector('#totalAvailability');
                element.innerHTML = "";

                GaugeChart.gaugeChart(element, 120, this.generateGaugeProperties(nv, '#1947A4')).updateNeedle(nv);
            },
            gaugePerformance(nv, ov){
                let element = document.querySelector('#totalPerformance');
                element.innerHTML = "";
                GaugeChart.gaugeChart(element, 120, this.generateGaugeProperties(nv, '#49B92A')).updateNeedle(nv);
            },
            gaugeQuality(nv, ov){
                let element = document.querySelector('#totalQuality');
                element.innerHTML = "";

                GaugeChart.gaugeChart(element, 120, this.generateGaugeProperties(nv, '#FFA600')).updateNeedle(nv);
            },
        },
        methods: {
            showStationSelectionForm(show = false) {
                this.isStationSelectionFormShown = show;
            },
            operatorSelectionModalClosed(){
                this.isOperatorSelectionModalShown = false;
            },
            productSelectionModalClosed(){
                this.isProductSelectionModalShown = false;
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
                // this.filter.stationOperatorName = station.station_operator_name;
                this.isStationSelectionFormShown = false;

                this.fetchData();
            },
            fetchOperatorName() {
                LineViewService.fetchOperatorName({
                        stationId: this.filter.stationId,
                        date: moment(this.filter.selectedDate).format('YYYY-MM-DD')
                    },
                    (data) => {
                        // console.log(data.operatorName)
                        this.filter.stationOperatorName = data.operatorName;
                    }
                );
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
            updateStationShiftId(selectedStationShiftId) {
                this.filter.stationShiftId = selectedStationShiftId;
                this.fetchData();
            },
            submitReportDefects(defectsData) {
                let hour = defectsData.defectTime;
                hour = hour.substring(0, hour.length - 3);
                // console.log('defect hour '+hour);

                // submit the defect
                LineViewService.storeDefects({
                    defectValue: defectsData.defectValue,
                    date: moment(this.filter.selectedDate).format('YYYY-MM-DD'),
                    defectTime: hour,
                    stationId: this.filter.stationId,
                    stationShiftId: this.filter.stationShiftId,
                    productId: this.products[0].product_id,
                }, data => {
                    console.log('success')
                }, error => {
                    console.log(error)
                });
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
                        stationShiftId: this.filter.stationShiftId,
                        date: moment(this.filter.selectedDate).format('YYYY-MM-DD')
                    },
                    (data) => {
                        const {products, logs, hourlyMetric, summaryMetric} = LineViewDataTransformer(data);

                        this.$set(this, 'products', products);
                        this.$set(this.linedata, 'logs', logs);
                        this.$set(this.oeeSummary, 'hourly', hourlyMetric);
                        this.$set(this.oeeSummary, 'summary', summaryMetric);
                        this.gaugeTotalOee = (isNaN((this.oeeSummary.summary.oee * 100).toFixed(2)) || this.oeeSummary.summary.oee === 0) ? 0 : (this.oeeSummary.summary.oee * 100).toFixed(2);
                        this.gaugeAvailability = (isNaN(this.oeeSummary.summary.availability.toFixed(2)) || this.oeeSummary.summary.availability === 0) ? 0 : this.oeeSummary.summary.availability.toFixed(2);
                        this.gaugePerformance = (isNaN(this.oeeSummary.summary.performance.toFixed(2)) || this.oeeSummary.summary.performance === 0) ? 0 : this.oeeSummary.summary.performance.toFixed(2);
                        this.gaugeQuality = (isNaN(this.oeeSummary.summary.quality.toFixed(2)) || this.oeeSummary.summary.quality === 0) ? 0 : this.oeeSummary.summary.quality.toFixed(2);

                        this.isInitialized = true;
                    }
                );

                this.fetchOperatorName();

            },
            fetchStationShift() {
                LineViewService.fetchStationShift({},
                (data) => {
                    this.$set(this.stationShifts, 'data', data);
                });
            },
            fetchTopDowntimeReasons() {
                LineViewService.fetchTopDowntimeReasons({
                        start: this.range.start,
                        end: this.range.end,
                    },
                    (data) => {
                        this.topDowntimeReasons = data;
                        console.log(this.topDowntimeReasons);
                    }
                );
            },
            fetchTopOperatorDowntimeReasons() {
                LineViewService.fetchTopOperatorDowntimeReasons({
                        start: this.range.start,
                        end: this.range.end,
                    },
                    (data) => {
                        this.topOperatorDowntimeReasons = data;
                    }
                );
            },
            renderGaugeChart(){
                let elements = ['#totalOee', '#totalAvailability', '#totalPerformance', '#totalQuality'];
                for (let i = 0; i < elements.length; i++) {
                    GaugeChart.gaugeChart(document.querySelector(elements[i]), elements[i] === '#totalOee' ? 150 : 120,
                      elements[i] === '#totalOee' ? {...this.gaugeOptions, ...{arcColors: ['#FF2D1C', '#FFA600', '#49B92A'],
                      arcDelimiters: [30, 70]}} : {...this.gaugeOptions, ...{arcColors: ['#EAEAEA']}} );

                }
            },
            generateGaugeProperties(nv, color){
                if ([100, 100.00,'100', '100.00', '100.0'].includes(nv)){
                    return  {...this.gaugeOptions, ...{arcColors: [color], arcDelimiters: []}};
                }else if([0,0.00,'0', '0.00', '0.0'].includes(nv)){
                    return  {...this.gaugeOptions, ...{arcColors: ['#EAEAEA'], arcDelimiters: []}};
                }else {
                    return  {...this.gaugeOptions, ...{arcColors: [color], arcDelimiters: [nv]}};
                };
            },
        },
        computed: {
            formattedSelectedDate() {
                return moment(this.filter.selectedDate).format('dddd, DD MMMM, YYYY');
            },
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
                // console.log('station service fetch all')
                // console.log(data)
                this.$set(this, 'stations', data);
                this.filter.stationId = this.stations[0].id;
                this.filter.stationName = this.stations[0].name;
            });

            DowntimeReasonsService.fetchAll({}, (data) => {
                this.$set(this, 'downtimeReasons', data);
            });

            this.renderGaugeChart();

            this.fetchStationShift();
            this.fetchTopDowntimeReasons();
            this.fetchTopOperatorDowntimeReasons();
        },
        destroyed() {
            clearInterval(this.$data._clock);
            clearInterval(this.$data._updateData);
        }
    }
</script>

<style>
.gaugeS > span:nth-child(2), .gaugeL > span:nth-child(2){
    font-weight: bolder;
    font-size: 1.3em;
    position: relative;
    bottom: 1rem;
}

.gaugeS > span:nth-child(3), .gaugeL > span:nth-child(3){
    font-size: .8em;
    position: relative;
    bottom: 1rem;
}

</style>
