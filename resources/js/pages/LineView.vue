<template>
    <div class="container-fluid mt-4">
        <header class="row">
            <div class="col mx-2 line-view-header-card">
                <div class="gauge-container d-flex flex-direction-row flex-nowrap justify-content-center align-items-baseline">
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


            <div class="col mx-2 line-view-header-card">
                <oee-summary-panel
                    :hourly-data="oeeSummary.hourly">
                </oee-summary-panel>
            </div>

            <div class="col mx-2 line-view-header-card">

                <div class="row" style="margin-top: .66rem;">
                    <div class="col-md-2 station-logo" style="width: 4.7em;">
                        <svg xmlns="http://www.w3.org/2000/svg" width="32" height="32" fill="currentColor" class="bi bi-gear-fill" viewBox="0 0 16 16" style="height: 4em; width: 4em;">
                            <path d="M9.405 1.05c-.413-1.4-2.397-1.4-2.81 0l-.1.34a1.464 1.464 0 0 1-2.105.872l-.31-.17c-1.283-.698-2.686.705-1.987 1.987l.169.311c.446.82.023 1.841-.872 2.105l-.34.1c-1.4.413-1.4 2.397 0 2.81l.34.1a1.464 1.464 0 0 1 .872 2.105l-.17.31c-.698 1.283.705 2.686 1.987 1.987l.311-.169a1.464 1.464 0 0 1 2.105.872l.1.34c.413 1.4 2.397 1.4 2.81 0l.1-.34a1.464 1.464 0 0 1 2.105-.872l.31.17c1.283.698 2.686-.705 1.987-1.987l-.169-.311a1.464 1.464 0 0 1 .872-2.105l.34-.1c1.4-.413 1.4-2.397 0-2.81l-.34-.1a1.464 1.464 0 0 1-.872-2.105l.17-.31c.698-1.283-.705-2.686-1.987-1.987l-.311.169a1.464 1.464 0 0 1-2.105-.872l-.1-.34zM8 10.93a2.929 2.929 0 1 1 0-5.86 2.929 2.929 0 0 1 0 5.858z"/>
                        </svg>
                    </div>
                    <div class="col-md-10">
                        <div class="dropdown">
                            <label for="station-picker" style="font-size: 1.35rem; padding-top: 1px">STATION:</label>
                            <button class="btn btn-light dropdown-toggle text-uppercase" type="button" id="station-picker" data-bs-toggle="dropdown" aria-expanded="false" style="padding: 0px;font-size: 20px;font-weight: 500;margin-top: -12px;">
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
                        Operator: {{ filter.stationOperatorName }}
                    </div>
                </div>


                <div class="row mt-3">
                    <div class="col-md-6">
                        <production-summary-panel
                            :produced="oeeSummary.summary.produced"
                            :expected="oeeSummary.summary.expected"
                            :oee="oeeSummary.summary.oee"
                            :products="products">
                        </production-summary-panel>
                    </div>

                    <div class="col-md-6 d-flex justify-content-center align-items-baseline" style="margin-top: 2.8rem;">

                        <div class="fs-2">
                            <svg xmlns="http://www.w3.org/2000/svg" @click="changeSelectedDate('PREV')" width="32" height="32" fill="currentColor" class="bi bi-chevron-compact-left" viewBox="0 0 16 16" style="cursor: pointer;">
                                <path fill-rule="evenodd" d="M9.224 1.553a.5.5 0 0 1 .223.67L6.56 8l2.888 5.776a.5.5 0 1 1-.894.448l-3-6a.5.5 0 0 1 0-.448l3-6a.5.5 0 0 1 .67-.223z"></path>
                            </svg>
                        </div>

                        <div class="card col-md-10">
                            <div class="card-body p-0"
                                 style="background: linear-gradient(90deg, rgba(5,65,119,1) 0%, rgba(244,244,244,1) 0%, rgba(5,65,119,1) 0%, rgba(219,218,218,0) 0%, rgba(233,233,233,0.27494747899159666) 59%, rgba(245,245,245,1) 100%);"
                            >
                                <v-date-picker :max-date='new Date()'
                                               mode='single' v-model='filter.selectedDate'
                                               :masks="{ input: 'WWW, DD MMMM' }"
                                               @input="changeSelectedDate">
                                    <template v-slot="{ inputValue, inputEvents }">
                                        <input
                                            style="height: 4.5rem; font-size: 1.3em;"
                                            class="date-picker-input text-center border-0 w-100 bg-transparent"
                                            :value="inputValue"
                                            v-on="inputEvents"
                                        />
                                    </template>
                                </v-date-picker>
                            </div>
                        </div>

                        <div class="fs-2">
                            <svg xmlns="http://www.w3.org/2000/svg" @click="changeSelectedDate('NEXT')" width="32" height="32" fill="currentColor" class="bi bi-chevron-compact-right" viewBox="0 0 16 16" style="cursor: pointer;">
                                <path fill-rule="evenodd" d="M6.776 1.553a.5.5 0 0 1 .671.223l3 6a.5.5 0 0 1 0 .448l-3 6a.5.5 0 1 1-.894-.448L9.44 8 6.553 2.224a.5.5 0 0 1 .223-.671z"></path>
                            </svg>
                        </div>

                    </div>
                </div>
            </div>

        </header>

        <div class="row mt-4">
            <div class="col-md-2 mt-2">
                <div class="table-responsive">
                    <table class="table" style="border: 2px solid #C5E9FF;">
                        <thead class="top_downtime_table_header">
                            <tr>
                                <th colspan="2">
                                    Top Downtime Reasons (last 7 days)
                                </th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr v-if="!showTableTopDowntimeReasons">
                                <td class="text-center">No data found</td>
                            </tr>
                            <tr v-else v-for="topDowntimeReason in topDowntimeReasons">
                                <td>{{ topDowntimeReason.reason_name }}</td>
                                <td>{{ topDowntimeReason.duration }}</td>
                            </tr>
                        </tbody>
                    </table>
                </div>

                <div class="table-responsive">
                    <table class="table" style="border: 2px solid #C5E9FF;">
                        <thead class="top_downtime_table_header">
                        <tr>
                            <th colspan="2">Top Operator Downtime (last 7 days)</th>
                        </tr>
                        </thead>
                        <tbody>
                            <tr v-if="!showTableTopOperartorDowntimeReasons">
                                <td class="text-center">No data found</td>
                            </tr>
                            <tr v-else v-for="topOperatorDowntime in topOperatorDowntimes">
                                <td>{{ topOperatorDowntime.operator_name }}</td>
                                <td>{{ topOperatorDowntime.duration }}</td>
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
                        :station-shifts-data="stationShifts.data"
                        @stationshift-selected="updateStationShiftId"
                        @downtime-clicked="openDowntimeReasonsSelectionModal"
                        @reportDefects="submitReportDefects"
                    >
                    </line-view-graph>
                    <b-overlay :show="showInprogress" opacity="0.6" no-wrap></b-overlay>
                </div>
            </div>

        </div>


        <footer class="row fixed-bottom">
            <div class="controls container-fluid d-flex justify-content-around">

                <div class="card rounded-0" style="width: auto; flex-grow: 1; cursor: pointer;" @click="isProductSelectionModalShown = true">
                    <div class="card-body" style="text-align: center;">
                        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-box-seam" viewBox="0 0 16 16">
                            <path d="M8.186 1.113a.5.5 0 0 0-.372 0L1.846 3.5l2.404.961L10.404 2l-2.218-.887zm3.564 1.426L5.596 5 8 5.961 14.154 3.5l-2.404-.961zm3.25 1.7-6.5 2.6v7.922l6.5-2.6V4.24zM7.5 14.762V6.838L1 4.239v7.923l6.5 2.6zM7.443.184a1.5 1.5 0 0 1 1.114 0l7.129 2.852A.5.5 0 0 1 16 3.5v8.662a1 1 0 0 1-.629.928l-7.185 2.874a.5.5 0 0 1-.372 0L.63 13.09a1 1 0 0 1-.63-.928V3.5a.5.5 0 0 1 .314-.464L7.443.184z"/>
                        </svg>&nbsp;ASSIGN PRODUCT
                    </div>
                </div>

                <div class="card rounded-0" style="width: auto; flex-grow: 1; cursor: pointer;" @click="isOperatorSelectionModalShown = true">
                    <div class="card-body" style="text-align: center;">
                        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-person-plus-fill" viewBox="0 0 16 16">
                            <path d="M1 14s-1 0-1-1 1-4 6-4 6 3 6 4-1 1-1 1H1zm5-6a3 3 0 1 0 0-6 3 3 0 0 0 0 6z"/>
                        <path fill-rule="evenodd" d="M13.5 5a.5.5 0 0 1 .5.5V7h1.5a.5.5 0 0 1 0 1H14v1.5a.5.5 0 0 1-1 0V8h-1.5a.5.5 0 0 1 0-1H13V5.5a.5.5 0 0 1 .5-.5z"/>
                        </svg>&nbsp;ASSIGN OPERATOR
                    </div>
                </div>

                <div class="card rounded-0" style="width: auto; flex-grow: 1; cursor: pointer;" @click="isDowntimeSummaryModalShown = true">
                    <div class="card-body" style="text-align: center;">
                        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-clock" viewBox="0 0 16 16">
                            <path d="M8 3.5a.5.5 0 0 0-1 0V9a.5.5 0 0 0 .252.434l3.5 2a.5.5 0 0 0 .496-.868L8 8.71V3.5z"/>
                            <path d="M8 16A8 8 0 1 0 8 0a8 8 0 0 0 0 16zm7-8A7 7 0 1 1 1 8a7 7 0 0 1 14 0z"/>
                        </svg>&nbsp;ASSIGN DOWNTIME REASON
                    </div>
                </div>

                <div class="card rounded-0" style="width: auto; flex-grow: 1; cursor: pointer;" @click="isScrapInputModalShown = true">
                    <div class="card-body" style="text-align: center;">
                        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-trash-fill" viewBox="0 0 16 16">
                            <path d="M2.5 1a1 1 0 0 0-1 1v1a1 1 0 0 0 1 1H3v9a2 2 0 0 0 2 2h6a2 2 0 0 0 2-2V4h.5a1 1 0 0 0 1-1V2a1 1 0 0 0-1-1H10a1 1 0 0 0-1-1H7a1 1 0 0 0-1 1H2.5zm3 4a.5.5 0 0 1 .5.5v7a.5.5 0 0 1-1 0v-7a.5.5 0 0 1 .5-.5zM8 5a.5.5 0 0 1 .5.5v7a.5.5 0 0 1-1 0v-7A.5.5 0 0 1 8 5zm3 .5v7a.5.5 0 0 1-1 0v-7a.5.5 0 0 1 1 0z"/>
                        </svg>&nbsp;BATCH DEFECT ENTRY
                    </div>
                </div>

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
                <h5 class="text-uppercase px-5">Select Downtime Reason</h5>
            </template>
            <template v-slot:content>
                <div class="container" >
                    <ul class="list-group">
                        <li class="list-group-item listitemm border mb-2" v-for="reason in downtimeReasons" :key="reason.id" @click.prevent="selectDowntimeReason(reason)">
                            <div class="row align-items-center cursor-pointer">
                                <div class="col-sm-8">
                                    {{ reason.name }}
                                </div>
                                <div class="col-sm-4">
                                    <div class="form-check">
                                        <input class="form-check-input float-end" type="radio" name="flexRadioDefault" id="flexRadioDefault1"
                                            :value="reason.id" @change="onReasonChange($event)" :checked="selectedDowntimeReasonId === reason.id"/>
                                    </div>
                                </div>
                            </div>
                        </li>
                    </ul>
                </div>
                <b-overlay :show="showInprogress" opacity="0.6" no-wrap></b-overlay>
            </template>
            <template v-slot:footer>
                <button class="btn btn-success ms-3 me-4 mb-2" @click="assignDowntimeReason();" >ASSIGN</button>
            </template>
        </modal>

        <product-selection-modal
            v-if="isProductSelectionModalShown"
            @close="productSelectionModalClosed()"
            :station-id="filter.stationId"
            :station-name="filter.stationName"
            :product-id="currentProduct != null? currentProduct.product_id : 0">
        </product-selection-modal>

        <operator-selection-modal
            v-if="isOperatorSelectionModalShown"
            @close="operatorSelectionModalClosed()"
            :station-id="filter.stationId"
            :station-name="filter.stationName"
            :operator-id="filter.stationOperatorId">
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
    import ProductSelectionModal from "../components/lineview/productselection/ProductSelectionModal";
    import moment from "moment";
    import ToastrService from '../services/ToastrService';

    export default {
        name: "LineView",
        components: {
            OperatorSelectionModal,
            ProductSelectionModal,
            Modal,
            DowntimeSummaryModal,
            LineViewGraph,
            ProductionSummaryPanel,
            'oee-summary-panel': OEESummaryPanel,
            ScrapInputModel,
        },
        data: () => ({
            isInitialized: false,
            isStationSelectionFormShown: false,
            isDowntimeReasonsModalShown: false,
            isDowntimeSummaryModalShown: false,
            isOperatorSelectionModalShown: false,
            isProductSelectionModalShown: false,
            isScrapInputModalShown: false,
            selectedDowntime: null,
            currentProduct: null,
            filter: {
                stationId: 1,
                stationShiftId: null,
                stationName: '',
                stationOperatorId: null,
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
                start: moment().startOf('week').subtract(6, "days").toDate(),
                end: moment().endOf('day').toDate()
            },
            topDowntimeReasons: null,
            topOperatorDowntimes: null,
            stationShifts: {
                data: []
            },
            showInprogress: false,
            dataUpdateTimer: null,
            clockUpdateTimer: null,
            isChecked: Boolean,
            selectedDowntimeReasonId: null,
        }),
        watch: {
            gaugeTotalOee(nv, ov){
                let element = document.querySelector('#totalOee');
                element.innerHTML = "";
                GaugeChart.gaugeChart(element, window.innerWidth >= 1920 ? 260 : 185, {...this.gaugeOptions, ...{arcColors: ['#FF2D1C', '#FFA600', '#49B92A'],
                        arcDelimiters: [30, 70]}}).updateNeedle(nv);
            },
            gaugeAvailability(nv, ov){
                let element = document.querySelector('#totalAvailability');
                element.innerHTML = "";

                GaugeChart.gaugeChart(element, window.innerWidth >= 1920 ? 170 : 135, this.generateGaugeProperties(nv, '#1947A4')).updateNeedle(nv);
            },
            gaugePerformance(nv, ov){
                let element = document.querySelector('#totalPerformance');
                element.innerHTML = "";
                GaugeChart.gaugeChart(element, window.innerWidth >= 1920 ? 170 : 135, this.generateGaugeProperties(nv, '#49B92A')).updateNeedle(nv);
            },
            gaugeQuality(nv, ov){
                let element = document.querySelector('#totalQuality');
                element.innerHTML = "";

                GaugeChart.gaugeChart(element, window.innerWidth >= 1920 ? 170 : 135, this.generateGaugeProperties(nv, '#FFA600')).updateNeedle(nv);
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
                this.isStationSelectionFormShown = false;

                this.fetchData();
            },
            fetchOperatorName() {
                LineViewService.fetchOperatorName({
                        stationId: this.filter.stationId,
                        date: moment(this.filter.selectedDate).format('YYYY-MM-DD')
                    },
                    (data) => {
                        this.filter.stationOperatorName = data.operatorName;
                        this.filter.stationOperatorId = data.operatorId;
                    }
                );
            },
            openDowntimeReasonsSelectionModal(bar) {
                if (bar.type === 'downtime') {
                    this.isDowntimeReasonsModalShown = true;
                    this.selectedDowntime = bar;
                    DowntimeReasonsService.fetchAll([], reasons => {
                        this.downtimeReasons = reasons['downtime_reason_list'];
                    })
                    this.selectedDowntimeReasonId = bar.reason? bar.reason.id : null;
                }
            },
            updateStationShiftId(selectedStationShiftId) {
                this.filter.stationShiftId = selectedStationShiftId;
                this.fetchData();
            },
            submitReportDefects(defectsData) {
                let hour = defectsData.defectTime;
                hour = hour.substring(0, hour.length - 3);

                // submit the defect
                this.showInprogress = true;
                LineViewService.storeDefects({
                    defectValue: defectsData.defectValue,
                    date: moment(this.filter.selectedDate).format('YYYY-MM-DD'),
                    defectTime: hour,
                    stationId: this.filter.stationId,
                    stationShiftId: this.filter.stationShiftId,
                    productId: this.products[0].product_id,
                }, data => {
                    this.showInprogress = false;
                    ToastrService.showSuccessToast('Defect added successfully.');
                }, error => {
                    this.showInprogress = false;
                    ToastrService.showErrorToast('Error! Try again.');
                });
            },
            assignDowntimeReason() {
                this.showInprogress = true;
                DowntimeReasonsService.assignDowntime({
                    downtimeId: this.selectedDowntime.id,
                    downtimeReasonId: this.selectedDowntimeReasonId,
                }, data => {
                    this.isDowntimeReasonsModalShown = false;
                    this.showInprogress = false;
                    ToastrService.showSuccessToast('Downtime reason assigned successfully.');
                    this.fetchData();
                });
            },
            selectDowntimeReason(reason) {
                this.selectedDowntimeReasonId = reason.id;
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
                        this.gaugeTotalOee = (isNaN((this.oeeSummary.summary.oee * 100).toFixed(2)) || this.oeeSummary.summary.oee === 0) ? 0 : Math.ceil((this.oeeSummary.summary.oee * 100).toFixed(2));
                        this.gaugeAvailability = (isNaN(this.oeeSummary.summary.availability.toFixed(2)) || this.oeeSummary.summary.availability === 0) ? 0 : Math.ceil(this.oeeSummary.summary.availability.toFixed(2));
                        this.gaugePerformance = (isNaN(this.oeeSummary.summary.performance.toFixed(2)) || this.oeeSummary.summary.performance === 0) ? 0 : Math.ceil(this.oeeSummary.summary.performance.toFixed(2));
                        this.gaugeQuality = (isNaN(this.oeeSummary.summary.quality.toFixed(2)) || this.oeeSummary.summary.quality === 0) ? 0 : Math.ceil(this.oeeSummary.summary.quality.toFixed(2));

                        var assignedProducts = this.products.filter(prod => prod.start_time !== null);
                        this.currentProduct = assignedProducts.length > 0? assignedProducts[0] : null;
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
                    }
                );
            },
            fetchTopOperatorDowntimes() {
                LineViewService.fetchTopOperatorDowntimes({
                        start: this.range.start,
                        end: this.range.end,
                    },
                    (data) => {
                        this.topOperatorDowntimes = data;
                    }
                );
            },
            renderGaugeChart(){
                let elements = ['#totalOee', '#totalAvailability', '#totalPerformance', '#totalQuality'];
                for (let i = 0; i < elements.length; i++) {

                    let element = document.querySelector(elements[i]);
                    element.innerHTML = "";

                    GaugeChart.gaugeChart(document.querySelector(elements[i]), elements[i] === '#totalOee' ? (window.innerWidth >= 1920 ? 260 : 185) : (window.innerWidth >= 1920 ? 170 : 135),
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
                }
            },
            onReasonChange: function (event) {
                var data = event.target.value;
                this.selectedDowntimeReasonId = data;
            },
        },
        computed: {
            formattedSelectedDate() {
                return moment(this.filter.selectedDate).format('dddd, DD MMMM, YYYY');
            },
            showTableTopDowntimeReasons(){
                return this.topDowntimeReasons && this.topDowntimeReasons.length > 0;
            },
            showTableTopOperartorDowntimeReasons(){
                return this.topOperatorDowntimes && this.topOperatorDowntimes.length > 0;
            },
        },
        mounted() {
            const vm = this;
            this.fetchData();
            this.$data._updateData = () => {
                if (moment().diff(vm.filter.selectedDate, 'day') === 0) {
                    vm.fetchData();
                }
            };
            this.dataUpdateTimer = setInterval(this.$data._updateData, 2000);

            StationsService.fetchAll({}, (data) => {
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
            this.fetchTopOperatorDowntimes();
        },
        destroyed() {
            clearInterval(this.dataUpdateTimer);
        }
    }
</script>

<style scoped>
.station-logo > svg{border: 1px solid #E6E6E6; padding: 4px; color: #6D6D6D;}
.toast-title{font-weight:700}.toast-message{-ms-word-wrap:break-word;word-wrap:break-word}.toast-message a,.toast-message label{color:#FFF}.toast-message a:hover{color:#CCC;text-decoration:none}.toast-close-button{position:relative;right:-.3em;top:-.3em;float:right;font-size:20px;font-weight:700;color:#FFF;-webkit-text-shadow:0 1px 0 #fff;text-shadow:0 1px 0 #fff;opacity:.8;-ms-filter:progid:DXImageTransform.Microsoft.Alpha(Opacity=80);filter:alpha(opacity=80);line-height:1}.toast-close-button:focus,.toast-close-button:hover{color:#000;text-decoration:none;cursor:pointer;opacity:.4;-ms-filter:progid:DXImageTransform.Microsoft.Alpha(Opacity=40);filter:alpha(opacity=40)}.rtl .toast-close-button{left:-.3em;float:left;right:.3em}button.toast-close-button{padding:0;cursor:pointer;background:0 0;border:0;-webkit-appearance:none}.toast-top-center{top:0;right:0;width:100%}.toast-bottom-center{bottom:0;right:0;width:100%}.toast-top-full-width{top:0;right:0;width:100%}.toast-bottom-full-width{bottom:0;right:0;width:100%}.toast-top-left{top:12px;left:12px}.toast-top-right{top:12px;right:12px}.toast-bottom-right{right:12px;bottom:12px}.toast-bottom-left{bottom:12px;left:12px}#toast-container{position:fixed;z-index:999999;pointer-events:none}#toast-container *{-moz-box-sizing:border-box;-webkit-box-sizing:border-box;box-sizing:border-box}#toast-container>div{position:relative;pointer-events:auto;overflow:hidden;margin:0 0 6px;padding:15px 15px 15px 50px;width:300px;-moz-border-radius:3px;-webkit-border-radius:3px;border-radius:3px;background-position:15px center;background-repeat:no-repeat;-moz-box-shadow:0 0 12px #999;-webkit-box-shadow:0 0 12px #999;box-shadow:0 0 12px #999;color:#FFF;opacity:.8;-ms-filter:progid:DXImageTransform.Microsoft.Alpha(Opacity=80);filter:alpha(opacity=80)}#toast-container>div.rtl{direction:rtl;padding:15px 50px 15px 15px;background-position:right 15px center}#toast-container>div:hover{-moz-box-shadow:0 0 12px #000;-webkit-box-shadow:0 0 12px #000;box-shadow:0 0 12px #000;opacity:1;-ms-filter:progid:DXImageTransform.Microsoft.Alpha(Opacity=100);filter:alpha(opacity=100);cursor:pointer}#toast-container>.toast-info{background-image:url(data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAABgAAAAYCAYAAADgdz34AAAAAXNSR0IArs4c6QAAAARnQU1BAACxjwv8YQUAAAAJcEhZcwAADsMAAA7DAcdvqGQAAAGwSURBVEhLtZa9SgNBEMc9sUxxRcoUKSzSWIhXpFMhhYWFhaBg4yPYiWCXZxBLERsLRS3EQkEfwCKdjWJAwSKCgoKCcudv4O5YLrt7EzgXhiU3/4+b2ckmwVjJSpKkQ6wAi4gwhT+z3wRBcEz0yjSseUTrcRyfsHsXmD0AmbHOC9Ii8VImnuXBPglHpQ5wwSVM7sNnTG7Za4JwDdCjxyAiH3nyA2mtaTJufiDZ5dCaqlItILh1NHatfN5skvjx9Z38m69CgzuXmZgVrPIGE763Jx9qKsRozWYw6xOHdER+nn2KkO+Bb+UV5CBN6WC6QtBgbRVozrahAbmm6HtUsgtPC19tFdxXZYBOfkbmFJ1VaHA1VAHjd0pp70oTZzvR+EVrx2Ygfdsq6eu55BHYR8hlcki+n+kERUFG8BrA0BwjeAv2M8WLQBtcy+SD6fNsmnB3AlBLrgTtVW1c2QN4bVWLATaIS60J2Du5y1TiJgjSBvFVZgTmwCU+dAZFoPxGEEs8nyHC9Bwe2GvEJv2WXZb0vjdyFT4Cxk3e/kIqlOGoVLwwPevpYHT+00T+hWwXDf4AJAOUqWcDhbwAAAAASUVORK5CYII=)!important}#toast-container>.toast-error{background-image:url(data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAABgAAAAYCAYAAADgdz34AAAAAXNSR0IArs4c6QAAAARnQU1BAACxjwv8YQUAAAAJcEhZcwAADsMAAA7DAcdvqGQAAAHOSURBVEhLrZa/SgNBEMZzh0WKCClSCKaIYOED+AAKeQQLG8HWztLCImBrYadgIdY+gIKNYkBFSwu7CAoqCgkkoGBI/E28PdbLZmeDLgzZzcx83/zZ2SSXC1j9fr+I1Hq93g2yxH4iwM1vkoBWAdxCmpzTxfkN2RcyZNaHFIkSo10+8kgxkXIURV5HGxTmFuc75B2RfQkpxHG8aAgaAFa0tAHqYFfQ7Iwe2yhODk8+J4C7yAoRTWI3w/4klGRgR4lO7Rpn9+gvMyWp+uxFh8+H+ARlgN1nJuJuQAYvNkEnwGFck18Er4q3egEc/oO+mhLdKgRyhdNFiacC0rlOCbhNVz4H9FnAYgDBvU3QIioZlJFLJtsoHYRDfiZoUyIxqCtRpVlANq0EU4dApjrtgezPFad5S19Wgjkc0hNVnuF4HjVA6C7QrSIbylB+oZe3aHgBsqlNqKYH48jXyJKMuAbiyVJ8KzaB3eRc0pg9VwQ4niFryI68qiOi3AbjwdsfnAtk0bCjTLJKr6mrD9g8iq/S/B81hguOMlQTnVyG40wAcjnmgsCNESDrjme7wfftP4P7SP4N3CJZdvzoNyGq2c/HWOXJGsvVg+RA/k2MC/wN6I2YA2Pt8GkAAAAASUVORK5CYII=)!important}#toast-container>.toast-success{background-image:url(data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAABgAAAAYCAYAAADgdz34AAAAAXNSR0IArs4c6QAAAARnQU1BAACxjwv8YQUAAAAJcEhZcwAADsMAAA7DAcdvqGQAAADsSURBVEhLY2AYBfQMgf///3P8+/evAIgvA/FsIF+BavYDDWMBGroaSMMBiE8VC7AZDrIFaMFnii3AZTjUgsUUWUDA8OdAH6iQbQEhw4HyGsPEcKBXBIC4ARhex4G4BsjmweU1soIFaGg/WtoFZRIZdEvIMhxkCCjXIVsATV6gFGACs4Rsw0EGgIIH3QJYJgHSARQZDrWAB+jawzgs+Q2UO49D7jnRSRGoEFRILcdmEMWGI0cm0JJ2QpYA1RDvcmzJEWhABhD/pqrL0S0CWuABKgnRki9lLseS7g2AlqwHWQSKH4oKLrILpRGhEQCw2LiRUIa4lwAAAABJRU5ErkJggg==)!important}#toast-container>.toast-warning{background-image:url(data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAABgAAAAYCAYAAADgdz34AAAAAXNSR0IArs4c6QAAAARnQU1BAACxjwv8YQUAAAAJcEhZcwAADsMAAA7DAcdvqGQAAAGYSURBVEhL5ZSvTsNQFMbXZGICMYGYmJhAQIJAICYQPAACiSDB8AiICQQJT4CqQEwgJvYASAQCiZiYmJhAIBATCARJy+9rTsldd8sKu1M0+dLb057v6/lbq/2rK0mS/TRNj9cWNAKPYIJII7gIxCcQ51cvqID+GIEX8ASG4B1bK5gIZFeQfoJdEXOfgX4QAQg7kH2A65yQ87lyxb27sggkAzAuFhbbg1K2kgCkB1bVwyIR9m2L7PRPIhDUIXgGtyKw575yz3lTNs6X4JXnjV+LKM/m3MydnTbtOKIjtz6VhCBq4vSm3ncdrD2lk0VgUXSVKjVDJXJzijW1RQdsU7F77He8u68koNZTz8Oz5yGa6J3H3lZ0xYgXBK2QymlWWA+RWnYhskLBv2vmE+hBMCtbA7KX5drWyRT/2JsqZ2IvfB9Y4bWDNMFbJRFmC9E74SoS0CqulwjkC0+5bpcV1CZ8NMej4pjy0U+doDQsGyo1hzVJttIjhQ7GnBtRFN1UarUlH8F3xict+HY07rEzoUGPlWcjRFRr4/gChZgc3ZL2d8oAAAAASUVORK5CYII=)!important}#toast-container.toast-bottom-center>div,#toast-container.toast-top-center>div{width:300px;margin-left:auto;margin-right:auto}#toast-container.toast-bottom-full-width>div,#toast-container.toast-top-full-width>div{width:96%;margin-left:auto;margin-right:auto}.toast{background-color:#030303}.toast-success{background-color:#51A351}.toast-error{background-color:#BD362F}.toast-info{background-color:#2F96B4}.toast-warning{background-color:#F89406}.toast-progress{position:absolute;left:0;bottom:0;height:4px;background-color:#000;opacity:.4;-ms-filter:progid:DXImageTransform.Microsoft.Alpha(Opacity=40);filter:alpha(opacity=40)}@media all and (max-width:240px){#toast-container>div{padding:8px 8px 8px 50px;width:11em}#toast-container>div.rtl{padding:8px 50px 8px 8px}#toast-container .toast-close-button{right:-.2em;top:-.2em}#toast-container .rtl .toast-close-button{left:-.2em;right:.2em}}@media all and (min-width:241px) and (max-width:480px){#toast-container>div{padding:8px 8px 8px 50px;width:18em}#toast-container>div.rtl{padding:8px 50px 8px 8px}#toast-container .toast-close-button{right:-.2em;top:-.2em}#toast-container .rtl .toast-close-button{left:-.2em;right:.2em}}@media all and (min-width:481px) and (max-width:768px){#toast-container>div{padding:15px 15px 15px 50px;width:25em}#toast-container>div.rtl{padding:15px 50px 15px 15px}}
</style>
