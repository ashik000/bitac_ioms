<template>
    <div>
        <reports-common-header
            reportName="OEE Report"
            :showPartition="true"
            @partitionSelected="onPartitionSelect"
            @rangeSelected="onRangeSelect"
            :reportType="reportType"
            :reportName="reportName"
        >
        </reports-common-header>

        <report-container>
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
                            <report-table-by-station :stationId="reportPageFilters.selectedStationId" :start="selectedRange.start" :end="selectedRange.end" :type="selectedPartition" v-if="selectedReportType==='station'"></report-table-by-station>
                            <report-table-by-product :stationProductId="selectedStationProductId" :start="selectedRange.start" :end="selectedRange.end" :type="selectedPartition" v-if="selectedReportType==='product'"></report-table-by-product>
                            <report-table-by-shift :stationShiftId="selectedStationShiftId" :start="selectedRange.start" :end="selectedRange.end" :type="selectedPartition" v-if="selectedReportType==='shift'"></report-table-by-shift>
                            <report-table-by-operator :stationOperatorId="selectedStationOperatorId" :start="selectedRange.start" :end="selectedRange.end" :type="selectedPartition" v-if="selectedReportType==='operator'"></report-table-by-operator>
                        </div>
                    </div>
                </div>
            </template>
        </report-container>

    </div>
</template>

<script>
    import ReportsCommonHeader from './ReportsCommonHeader';
    import ReportContainer from './ReportContainer';
    import ReportFilters from './filters/ReportFilters';
    import OEEChart from '../../components/reports/OEEChart';
    import reportService from '../../services/Reports';
    import ReportTableOEEByStation from './reporttable/oee/ReportTableOEEByStation';
    import ReportTableOEEByProduct from './reporttable/oee/ReportTableOEEByProduct';
    import ReportTableOEEByShift from './reporttable/oee/ReportTableOEEByShift';
    import ReportTableOEEByOperator from './reporttable/oee/ReportTableOEEByOperator';
    import { mapState } from 'vuex';

    export default {
        name: "OeeReport",
        props: {
            reportType: {
                type: String,
                default: 'station'
            },
            reportName: {
                type: String,
                default: 'oee'
            }
        },
        components: {
            ReportFilters,
            ReportContainer,
            'oee-chart': OEEChart,
            'report-table-by-station': ReportTableOEEByStation,
            'report-table-by-product': ReportTableOEEByProduct,
            'report-table-by-shift': ReportTableOEEByShift,
            'report-table-by-operator': ReportTableOEEByOperator,
            'reports-common-header': ReportsCommonHeader
        },
        data: () => ({
            selectedPartition: 'hourly',
            selectedRange: {
                start: moment().startOf('day').toDate(),
                end: moment().endOf('day').toDate()
            },
            selectedReportType: 'station',
            // selectedStationId: 0,
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
        computed:{
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
            },
            ...mapState({
                reportPageFilters: 'reportPageFilters',
            })
        },
        methods: {
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
            onReportTypeChange(eventData) {
                console.log("parent received report type changed event: " + eventData);
                this.selectedReportType = eventData;
                this.fetchOEEData();
            },
            // onStationChange(eventData) {
            //     console.log("parent received station changed event: " + JSON.stringify(eventData));
            //     this.selectedStationId = Number.parseInt(eventData.stationId);
            //     this.selectedStation = eventData.station;
            //     this.selectedStationProductId = null;
            //     this.selectedStationShiftId = null;
            //     this.selectedStationOperatorId = null;
            //     this.fetchOEEData();
            // },
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
                    stationId: this.reportPageFilters.selectedStationId,
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
            },
        },
        watch: {
            reportType: function (newReportType, oldReportType) {
                this.selectedReportType = newReportType;
                this.fetchOEEData();
                // console.log('new report type '+newReportType);
            },
            reportPageFilters: {
                handler(newFilters, oldFilters) {
                    this.fetchOEEData();
                },
                deep: true
            }
        },
        mounted(){
            this.fetchOEEData();
        }
    }
</script>
