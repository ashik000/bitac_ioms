<template>
    <nav class="navbar navbar-expand-lg partitionNav ps-3 pe-3 py-0">

        <div class="row justify-content-between" style="width: 1600px;">
            <div class="col-md-6 col-sm-12">
                <ReportFilters :reportType="reportType" :reportName="reportName" class="w-100">
                </ReportFilters>
            </div>
            <div class="col-md-3 col-sm-12">
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
            </div>

            <div class="col-md-3 col-sm-12">
                <div class="date-range-picker-wrap mt-3" style="float: right;">
                    <span class="range-picker-label">
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
                        @input="onDateRangeChanged">
                    </v-date-picker>


                    <div class="dropdown">
                        <button class="btn btn-light dropdown-toggle" type="button" id="dropdownMenuButton1" data-bs-toggle="dropdown" aria-expanded="false">
                            {{ selectedRange.title }}
                        </button>
                        <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="dropdownMenuButton1">
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
        </div>
    </nav>
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
            default: 'oee',
        },
        showPartition: {
            type: Boolean,
            default: true
        },
        reportType: {
            type: String,
            default: 'station'
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
        selectedRangeX: {
            start: moment().startOf('day').toDate(),
            end: moment().endOf('day').toDate()
        },
        selectedPartition: 'hourly',
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
        downtimeDataset: {
            labels: [],
            downtimes: []
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
            // this.fetchOEEData();
            return this.$emit('partitionSelected', p);
        },
        onPartitionSelect(eventData) {
            this.selectedPartition = eventData;
            this.fetchOEEData();
        },
        onRangeSelect(eventData) {
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
            this.selectedReportType = eventData;
            this.fetchOEEData();
        },
    },
}
</script>


