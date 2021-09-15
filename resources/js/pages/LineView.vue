<template>
    <div class="container-fluid mt-4">
        <header class="row">
            <div class="col mx-2 line-view-header-card">
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


            <div class="col mx-2 line-view-header-card">
                <oee-summary-panel
                    :hourly-data="oeeSummary.hourly">
                </oee-summary-panel>
            </div>

            <div class="col mx-2 line-view-header-card">

                <div class="row mt-1">
                    <div class="col-md-2">
                        <svg xmlns="http://www.w3.org/2000/svg" width="32" height="32" fill="currentColor" class="bi bi-gear-fill" viewBox="0 0 16 16" style="height: 4em; width: 4em;">
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
                                    <a class="dropdown-item" href="#">{{ station.name }}&nbsp;<i class="material-icons"
                                        v-if="station.id === filter.stationId">done</i></a>
                                </li>
                            </ul>
                        </div>
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

                    <div class="col-md-6" style="justify-content: center;display: flex;align-items: baseline;">

                        <b-icon class="mx-1" icon="caret-left-fill" aria-hidden="true" @click="changeSelectedDate('PREV')" style="cursor: pointer;"></b-icon>

                        <div class="mt-4 card" style="width: 12rem;">
                            <div class="card-body p-1">
                                <v-date-picker :max-date='new Date()'
                                    mode='single' v-model='filter.selectedDate'
                                    :masks="{ input: 'WWW, DD MMMM' }"
                                    :input-props="{ class: 'date-picker-input border-0 w-full bg-transparent' }"
                                    @input="changeSelectedDate">
                                </v-date-picker>
                            </div>
                        </div>

                        <b-icon class="mx-1" icon="caret-right-fill" aria-hidden="true" @click="changeSelectedDate('NEXT')"
                                :disabled="filter.selectedDate >= new Date().setHours(0,0,0,0)" style="cursor: pointer;"></b-icon>

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
                    <table class="table">
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
                <h4 class="text-uppercase">Select Downtime Reason</h4>
            </template>
            <template v-slot:content>
                <div class="list-group">
                    <a href="#" class="list-group-item list-group-item-action"
                        v-for="reason in downtimeReasons" :key="reason.id"
                        @click.prevent="assignDowntimeReason(reason)">{{ reason.name }}</a>
                </div>
                <b-overlay :show="showInprogress" opacity="0.6" no-wrap></b-overlay>
            </template>
        </modal>

        <product-selection-modal
            v-if="isProductSelectionModalShown"
            @close="productSelectionModalClosed()"
            :station-id="filter.stationId"
            :station-name="filter.stationName"
            :product-id="this.products[0].product_id">
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
        }),
        watch: {
            gaugeTotalOee(nv, ov){
                let element = document.querySelector('#totalOee');
                element.innerHTML = "";
                GaugeChart.gaugeChart(element, window.innerWidth >= 1920 ? 200 : 130, {...this.gaugeOptions, ...{arcColors: ['#FF2D1C', '#FFA600', '#49B92A'],
                        arcDelimiters: [30, 70]}}).updateNeedle(nv);
            },
            gaugeAvailability(nv, ov){
                // console.log("Availability");
                // console.log(nv);
                let element = document.querySelector('#totalAvailability');
                element.innerHTML = "";

                GaugeChart.gaugeChart(element, window.innerWidth >= 1920 ? 120 : 100, this.generateGaugeProperties(nv, '#1947A4')).updateNeedle(nv);
            },
            gaugePerformance(nv, ov){
                let element = document.querySelector('#totalPerformance');
                element.innerHTML = "";
                GaugeChart.gaugeChart(element, window.innerWidth >= 1920 ? 120 : 100, this.generateGaugeProperties(nv, '#49B92A')).updateNeedle(nv);
            },
            gaugeQuality(nv, ov){
                let element = document.querySelector('#totalQuality');
                element.innerHTML = "";

                GaugeChart.gaugeChart(element, window.innerWidth >= 1920 ? 120 : 100, this.generateGaugeProperties(nv, '#FFA600')).updateNeedle(nv);
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
                        // console.log(data.operatorId)
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
                        // console.log('downtimeReasonSelectOnBar');
                        // console.log(reasons);
                        this.downtimeReasons = reasons['downtime_reason_list'];
                    })
                }
            },
            updateStationShiftId(selectedStationShiftId) {
                // console.log('when update shiftId triggers')
                // console.log(selectedStationShiftId);
                this.filter.stationShiftId = selectedStationShiftId;
                this.fetchData();
            },
            submitReportDefects(defectsData) {
                let hour = defectsData.defectTime;
                hour = hour.substring(0, hour.length - 3);
                // console.log('defect hour '+hour);

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
                    console.log('success')
                    ToastrService.showSuccessToast('Defect added successfully.');
                }, error => {
                    console.log(error);
                    this.showInprogress = false;
                    ToastrService.showErrorToast('Error! Try again.');
                });
            },
            assignDowntimeReason(reason) {
                this.showInprogress = true;
                DowntimeReasonsService.assignDowntime({
                    downtimeId: this.selectedDowntime.id,
                    downtimeReasonId: reason.id,
                }, data => {
                    this.isDowntimeReasonsModalShown = false;
                    this.showInprogress = false;
                    ToastrService.showSuccessToast('Downtime reason assigned successfully.');
                    this.fetchData();
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
                console.log('topDR mounted');
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

                    GaugeChart.gaugeChart(document.querySelector(elements[i]), elements[i] === '#totalOee' ? (window.innerWidth >= 1920 ? 200 : 130) : (window.innerWidth >= 1920 ? 120 : 100),
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
            showTableTopDowntimeReasons(){
                return this.topDowntimeReasons && this.topDowntimeReasons.length > 0;
            },
            showTableTopOperartorDowntimeReasons(){
                return this.topOperatorDowntimes && this.topOperatorDowntimes.length > 0;
            },
        },
        mounted() {
            const vm = this;
            this.$data._clock = () => {
                vm.currentTime = moment().format('LTS');
            };
            setInterval(this.$data._clock, 20000);

            this.fetchData();
            this.$data._updateData = () => {
                if (moment().diff(vm.filter.selectedDate, 'day') === 0) {
                    vm.fetchData();
                }
            };
            setInterval(this.$data._updateData, 20000);

            StationsService.fetchAll({}, (data) => {
                // console.log('station service fetch all')
                // console.log(data)
                this.$set(this, 'stations', data);
                this.filter.stationId = this.stations[0].id;
                this.filter.stationName = this.stations[0].name;
            });

            DowntimeReasonsService.fetchAll({}, (data) => {
                // console.log('downtime reasons service fetch all')
                // console.log(data)
                this.$set(this, 'downtimeReasons', data);
            });

            this.renderGaugeChart();

            this.fetchStationShift();

            this.fetchTopDowntimeReasons();
            this.fetchTopOperatorDowntimes();
        },
        destroyed() {
            clearInterval(this.$data._clock);
            clearInterval(this.$data._updateData);
        }
    }
</script>
