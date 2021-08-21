<template>
    <span>
        <div class="row">
            <div class="col-md-3">
                <ReportSideBar v-on:reportTypeChanged="changeReportType" :reportType="reportType">
<!--                    <template v-slot:reportContainer>-->

<!--                    </template>-->
                </ReportSideBar>
            </div>
            <div class="col-md-9">
                <nav class="navbar navbar-expand-lg navbar-light bg-light partitionNav">
                    <div class="container-fluid">

                        <ReportFilters :reportType="reportType"></ReportFilters>

                        <ul class="partition-picker" v-if="showPartition">
                            <li class="nav-item" :class="{ active: partition === 'hourly' }"
                                @click.prevent="partitionChanged('hourly')">
                                <a href="#" class="nav-link">
                                    Hourly
                                </a>
                            </li>
                            <li class="nav-item" :class="{ active: partition === 'daily' }"
                                @click.prevent="partitionChanged('daily')">
                                <a href="#" class="nav-link">
                                    Daily
                                </a>
                            </li>
                            <li class="nav-item" :class="{ active: partition === 'weekly' }"
                                @click.prevent="partitionChanged('weekly')">
                                <a href="#" class="nav-link">
                                    Weekly
                                </a>
                            </li>
                            <li class="nav-item" :class="{ active: partition === 'monthly' }"
                                @click.prevent="partitionChanged('monthly')">
                                <a href="#" class="nav-link">
                                    Monthly
                                </a>
                            </li>
                        </ul>

                        <div class="date-range-picker-wrap">
                            <span class="range-picker-label" style="color: #0a1520">
                                Date Range
                            </span>
                            <v-date-picker class="date-range-picker" v-show="selectedRange.tag === 'custom'"
                                           :input-props="{ style: `
                                                background-color: #ffffff;
                                                color: #3D3B30;
                                                padding: 0.5rem;
                                                border-radius: 0.25rem;
                                                margin: 0.1rem 0;
                                                width: 200px;
                                                text-align: center;
                                                cursor: pointer;
                                            `
                                       }"
                                           mode='range'
                                           :value='range'
                                           @input="onDateRangeChanged"/>


                            <div class="dropdown">
                                <button class="btn btn-secondary dropdown-toggle" type="button" id="dropdownMenuButton1" data-bs-toggle="dropdown" aria-expanded="false">
                                    {{ selectedRange.title }}
                                </button>
                                <ul class="dropdown-menu" aria-labelledby="dropdownMenuButton1">
                                    <li>
                                        <a href="#" class="dropdown-item"
                                           v-for="item in rangeSelections" @click="changeSelectedRange(item)">
                                            {{ item.title }}
                                        </a>

                                        <a href="#" class="dropdown-item"
                                           @click="changeSelectedRange({ tag: 'custom', title: 'Custom' })">
                                            Custom
                                        </a>
                                    </li>
                                </ul>
                            </div>

                        </div>
                    </div>
                </nav>

                <report-container @reportTypeChanged="changeReportType">
                    <template v-slot:reportContainer>
                        <div>
                            <div style="background-color: #343345; width: 100%; padding: 30px;">
                                <div class="report-page">
                                    <div class="chart-wrapper">
                                        <oee-chart :title="title" :dataset="dataset"/>
                                    </div>
                                </div>
                                <div style="margin-top:30px;">
                                    <div style="margin-bottom: 10px;">
                                        <span style="font-size: 18px; color:#dddddd">{{ reportTableTitle }}</span>
                                    </div>
                                    <report-table-by-station :stationId="selectedStationId" :start="selectedRange.start" :end="selectedRange.end" :type="selectedPartition" v-if="selectedReportType==='station'"></report-table-by-station>
                                    <report-table-by-product :stationProductId="selectedStationProductId" :start="selectedRange.start" :end="selectedRange.end" :type="selectedPartition" v-if="selectedReportType==='product'"></report-table-by-product>
                                    <report-table-by-shift :stationShiftId="selectedStationShiftId" :start="selectedRange.start" :end="selectedRange.end" :type="selectedPartition" v-if="selectedReportType==='shift'"></report-table-by-shift>
                                    <report-table-by-operator :stationOperatorId="selectedStationOperatorId" :start="selectedRange.start" :end="selectedRange.end" :type="selectedPartition" v-if="selectedReportType==='operator'"></report-table-by-operator>
                                </div>
                            </div>
                        </div>
                    </template>
                </report-container>

            </div>
        </div>


    </span>
</template>

<script>
import moment from 'moment';
import ReportContainer from './ReportContainer';
import ReportSideBar from "../../components/reports/ReportSideBar";
import ReportFilters from "../../pages/Reports/filters/ReportFilters";
import OEEChart from '../../components/reports/OEEChart';
import ReportTableOEEByStation from './reporttable/oee/ReportTableOEEByStation';
import ReportTableOEEByProduct from './reporttable/oee/ReportTableOEEByProduct';
import ReportTableOEEByShift from './reporttable/oee/ReportTableOEEByShift';
import ReportTableOEEByOperator from './reporttable/oee/ReportTableOEEByOperator';
import reportService from "../../services/Reports";

export default {
    name: "ReportsCommonHeader",
    components: {
        ReportSideBar,
        ReportContainer,
        ReportFilters,
        'oee-chart': OEEChart,
        'report-table-by-station': ReportTableOEEByStation,
        'report-table-by-product': ReportTableOEEByProduct,
        'report-table-by-shift': ReportTableOEEByShift,
        'report-table-by-operator': ReportTableOEEByOperator,
    },
    props: {
        reportName: {
            type: String,
            default: 'OEE Report',
        },
        showPartition: {
            type: Boolean,
            default: true
        }
    },
    data: () => ({
        selectedRange: {
            tag: 'today',
            title: 'Today'
        },
        partition: 'hourly',
        range: {
            start: moment().startOf('day').toDate(),
            end: moment().endOf('day').toDate()
        },
        reportType: 'station',
        selectedPartition: 'hourly',
        // selectedRange: {
        //     start: moment().startOf('day').toDate(),
        //     end: moment().endOf('day').toDate()
        // },
        selectedReportType: 'station',
        selectedStationId: 0,
        selectedStation: null,
        selectedStationProductId: 0,
        selectedStationProduct: null,
        selectedStationShiftId: 0,
        selectedStationShift: null,
        selectedStationOperatorId: 0,
        selectedStationOperator: null,
        title: '',
        dataset: {
            labels: [],
            performance: [],
            availability: [],
            quality: [],
            oee: [],
        },
        tableData: []
    }),
    computed: {
        rangeSelections() {
            switch (this.partition) {
                case 'hourly':
                    return [
                        {
                            tag: 'today',
                            title: 'Today'
                        },
                        {
                            tag: 'yesterday',
                            title: 'Yesterday'
                        },
                        {
                            tag: 'this_week',
                            title: 'This Week'
                        },
                        {
                            tag: 'last_week',
                            title: 'Last Week'
                        }
                    ];

                case 'daily':
                    return [
                        {
                            tag: 'this_week',
                            title: 'This Week'
                        },
                        {
                            tag: 'last_week',
                            title: 'Last Week'
                        }
                    ]
            }
        },
        reportTableTitle(){
            if(this.selectedReportType === 'station'){
                if(this.selectedStationId){
                    if(this.selectedStation) return `Station: ${this.selectedStation.name}`;
                    return '';
                } else {
                    return 'All Stations';
                }
            } else if(this.selectedReportType === 'product'){
                if(this.selectedStationProductId){
                    if(this.selectedStationProduct) return `Station: ${this.selectedStationProduct.station_name} > Product: ${this.selectedStationProduct.product_name}`;
                    return '';
                } else {
                    return 'All Products';
                }
            } else if(this.selectedReportType === 'shift'){
                if(this.selectedStationShiftId){
                    if(this.selectedStationShift) return `Station: ${this.selectedStationShift.station_name} > Shift: ${this.selectedStationShift.shift_name}`;
                    return '';
                } else {
                    return 'All Shifts';
                }
            } else if(this.selectedReportType === 'operator'){
                if(this.selectedStationOperatorId){
                    if(this.selectedStationOperator) return `Station: ${this.selectedStationOperator.station_name} > Operator: ${this.selectedStationOperator.operator_name}`;
                    return '';
                } else {
                    return 'All Operators';
                }
            }
        }
    },
    methods: {
        reportTitle(report) {
            switch (report) {
                case 'oee':
                    return 'OEE';
                case 'quality':
                    return 'Quality';
                case 'availability':
                    return 'Availability';
                case 'downtime':
                    return 'Downtime';
            }
        },
        changeSelectedRange(item) {
            this.selectedRange = item;

            switch (item.tag) {
                case 'today':
                    this.range.start = moment().startOf('day').toDate();
                    this.range.end = moment().endOf('day').toDate();
                    break;

                case 'yesterday':
                    this.range.start = moment().subtract(1, "days").startOf('day').toDate();
                    this.range.end = moment().subtract(1, "days").endOf('day').toDate();
                    break;

                case 'this_week':
                    this.range.start = moment().startOf('week').toDate();
                    this.range.end = moment().endOf('week').toDate();
                    break;

                case 'last_week':
                    this.range.start = moment().startOf('week').subtract(7, "days").toDate();
                    this.range.end = moment().endOf('week').subtract(7, "days").toDate();
                    break;
            }

            this.onDateRangeChanged(this.range)
        },
        onDateRangeChanged(range) {
            this.$emit('rangeSelected', range);
        },
        partitionChanged(p){
            this.partition = p;
            this.selectedPartition = p;
            this.fetchOEEData();
            // return this.$emit('partitionSelected', p);
        },
        onPartitionSelect(eventData) {
            console.log("parent received partitionSelected event: " + eventData);
            this.selectedPartition = eventData;
            this.fetchOEEData();
        },
        onRangeSelect(eventData) {
            console.log(`parent received rangeselected event: start: ${eventData.start} , end: ${eventData.end}`);
            this.selectedRange.start = eventData.start;
            this.selectedRange.end = eventData.end;
            this.fetchOEEData();
        },
        changeReportType(type) {
            this.reportType = type;
            this.selectedReportType = type;
            this.fetchOEEData();
        },
        onReportTypeChange(eventData) {
            console.log("parent received report type changed event: " + eventData);
            this.selectedReportType = eventData;
            this.fetchOEEData();
        },
        onStationChange(eventData) {
            console.log("parent received station changed event: " + JSON.stringify(eventData));
            this.selectedStationId = Number.parseInt(eventData.stationId);
            this.selectedStation = eventData.station;
            this.selectedStationProductId = null;
            this.selectedStationShiftId = null;
            this.selectedStationOperatorId = null;
            this.fetchOEEData();
        },
        onStationProductSelect(eventData) {
            console.log("parent received station-product change event: " + JSON.stringify(eventData));
            this.selectedStationProductId = Number.parseInt(eventData.stationProductId);
            this.selectedStationProduct = eventData.stationProduct;
            this.selectedStationId = null;
            this.selectedStationShiftId = null;
            this.selectedStationOperatorId = null;
            this.fetchOEEData();
        },
        onStationShiftSelect(eventData) {
            console.log("parent received station-shift change event: " + eventData);
            this.selectedStationShiftId = Number.parseInt(eventData.stationShiftId);
            this.selectedStationShift = eventData.stationShift;
            this.selectedStationId = null;
            this.selectedStationProductId = null;
            this.selectedStationOperatorId = null;
            this.fetchOEEData();
        },
        onStationOperatorSelect(eventData) {
            console.log("parent received station-operator change event: " + eventData);
            this.selectedStationOperatorId = Number.parseInt(eventData.stationOperatorId);
            this.selectedStationOperator = eventData.stationOperator;
            this.selectedStationId = null;
            this.selectedStationProductId = null;
            this.selectedStationShiftId = null;
            this.fetchOEEData();
        },
        fetchOEEData() {
            let data = {
                stationId: this.selectedStationId,
                stationProductId: this.selectedStationProductId,
                stationShiftId: this.selectedStationShiftId,
                stationOperatorId: this.selectedStationOperatorId,
                start: moment(this.selectedRange.start).format('YYYY-MM-DD'),
                endTime: moment(this.selectedRange.end).format('YYYY-MM-DD'),
                type: this.selectedPartition
            };
            console.log(`Fetching OEE report data with ${JSON.stringify(data)} params`);
            reportService.fetchReports(data, response => {
                this.title = response.title;
                this.dataset.labels = response.dataset.labels;
                this.dataset.performance = response.dataset.performance;
                this.dataset.availability = response.dataset.availability;
                this.dataset.quality = response.dataset.quality;
                this.dataset.oee = response.dataset.oee;
                console.log(`Received OEE report data: ${JSON.stringify(response)}`);
            });
        }
    },
    mounted(){
        this.fetchOEEData();
    }
}
</script>

<style scoped lang="scss">
@import "#/app.scss";

.navbar {
    background: #222056;

    .nav-item {
        padding-top: 0.5rem;
        padding-bottom: 0.5rem;

        .nav-link {
            color: #337ab7;
        }

        &.active {
            padding-bottom: 0.25rem;
            border-bottom: 0.10rem solid #dddddd;

            .nav-link {
                color: #dddddd;
            }
        }

        &:hover {
            .nav-link {
                color: #999999;
            }
        }
    }
}

.report-type-picker {
    color: #337ab7;

    &:hover {
        color: #999999;
    }

    &.show {
        color: #dddddd;
    }
}

.partition-picker {
    @extend .navbar-nav, .flex-grow-1, .justify-content-center;
}

.date-range-picker-wrap {
    @extend .d-flex, .flex-row, .justify-content-end, .align-items-center, .w-25;

    .range-picker-label {
        color: #dddddd;
        padding: 0.5rem 1rem;
        margin: 0 0.5rem;
        white-space: nowrap;
    }

    .date-range-picker.vc-reset {
        flex: 1;

        .__date-picker-input {
        }
    }

    .date-shortcut {
        .dropdown-menu {
            left: auto;
            right: 0;
        }
    }
}

.partitionNav .nav-item.active a {
    color: #035FA3!important;
    font-weight: bold;
}
.partitionNav .nav-item.active {
    border-bottom: 0.2rem solid #007EDA!important;
}
</style>

