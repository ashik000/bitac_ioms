<template>
    <div class="h-100">
        <reports-common-header
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
                    <div>
                        <div class="report-page">
                            <div class="chart-wrapper" v-if="dataset.labels.length > 0">
                                <oee-chart :title="title" :dataset="dataset"/>
                            </div>
                            <div v-else>
                                <p style="text-align: center; color: red">No Data Found</p>
                            </div>
                        </div>
                        <div class="table-container y-scroll report_table_container">
                            <report-table-by-station :stationId="reportPageFilters.selectedStationId" :start="selectedRange.start" :end="selectedRange.end" :type="selectedPartition" v-if="selectedReportType==='station'"></report-table-by-station>
                            <report-table-by-product :stationProductId="reportPageFilters.selectedStationProductId" :start="selectedRange.start" :end="selectedRange.end" :type="selectedPartition" v-if="selectedReportType==='product'"></report-table-by-product>
                            <report-table-by-shift :stationShiftId="reportPageFilters.selectedStationShiftId" :start="selectedRange.start" :end="selectedRange.end" :type="selectedPartition" v-if="selectedReportType==='shift'"></report-table-by-shift>
                            <report-table-by-operator :stationOperatorId="reportPageFilters.selectedStationOperatorId" :start="selectedRange.start" :end="selectedRange.end" :type="selectedPartition" v-if="selectedReportType==='operator'"></report-table-by-operator>
                            <report-table-by-team :stationTeamId="reportPageFilters.selectedStationTeamId" :start="selectedRange.start" :end="selectedRange.end" :type="selectedPartition" v-if="selectedReportType==='team'"></report-table-by-team>
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
    import ReportTableOEEByTeam from './reporttable/oee/ReportTableOEEByTeam';
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
            'report-table-by-team': ReportTableOEEByTeam,
            'reports-common-header': ReportsCommonHeader
        },
        data: () => ({
            selectedPartition: 'hourly',
            selectedRange: {
                start: moment().startOf('day').toDate(),
                end: moment().endOf('day').toDate()
            },
            selectedReportType: 'station',
            selectedStation: null,
            selectedStationProduct: null,
            selectedStationShift: null,
            selectedStationOperator: null,
            selectedStationTeam: null,
            title: '',
            dataset: {
                labels: [],
                performance: [],
                availability: [],
                quality: [],
                oee: [],
            },
            tableData: [],
            reportNameX: 'OEE Report',
            reportType: 'oee'
        }),
        computed:{
            reportTableTitle(){
                if(this.selectedReportType === 'station'){
                    if(this.reportPageFilters.selectedStationId){
                        if(this.selectedStation) return `Station: ${this.selectedStation.name}`;
                        return '';
                    } else {
                        return 'All Stations';
                    }
                } else if(this.selectedReportType === 'product'){
                    if(this.reportPageFilters.selectedStationProductId){
                        if(this.selectedStationProduct) return `Station: ${this.selectedStationProduct.station_name} > Product: ${this.selectedStationProduct.product_name}`;
                        return '';
                    } else {
                        return 'All Products';
                    }
                } else if(this.selectedReportType === 'shift'){
                    if(this.reportPageFilters.selectedStationShiftId){
                        if(this.selectedStationShift) return `Station: ${this.selectedStationShift.station_name} > Shift: ${this.selectedStationShift.shift_name}`;
                        return '';
                    } else {
                        return 'All Shifts';
                    }
                } else if(this.selectedReportType === 'operator'){
                    if(this.reportPageFilters.selectedStationOperatorId){
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
                this.selectedPartition = eventData;
                this.fetchOEEData();
            },
            onRangeSelect(eventData) {
                this.selectedRange.start = eventData.start;
                this.selectedRange.end = eventData.end;
                this.fetchOEEData();
            },
            onReportTypeChange(eventData) {
                this.selectedReportType = eventData;
                this.fetchOEEData();
            },
            fetchOEEData() {
                let data = {
                    stationId: this.reportPageFilters.selectedStationId,
                    stationProductId: this.reportPageFilters.selectedStationProductId,
                    stationShiftId: this.reportPageFilters.selectedStationShiftId,
                    stationOperatorId: this.reportPageFilters.selectedStationOperatorId,
                    stationTeamId: this.reportPageFilters.selectedStationTeamId,
                    start: moment(this.selectedRange.start).format('YYYY-MM-DD'),
                    endTime: moment(this.selectedRange.end).format('YYYY-MM-DD'),
                    type: this.selectedPartition,
                    reportType: this.reportType
                };
                reportService.fetchReports(data, response => {
                    this.title = response.title;
                    this.dataset.labels = response.dataset.labels;
                    this.dataset.performance = response.dataset.performance;
                    this.dataset.availability = response.dataset.availability;
                    this.dataset.quality = response.dataset.quality;
                    this.dataset.oee = response.dataset.oee;
                });
            },
        },
        watch: {
            reportType: function (newReportType, oldReportType) {
                this.selectedReportType = newReportType;
                this.fetchOEEData();
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
            this.$emit('reportNameX', this.reportNameX);
        }
    }
</script>
