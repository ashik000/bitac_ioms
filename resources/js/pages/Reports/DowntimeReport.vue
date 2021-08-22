<template>
    <div>
        <reports-common-header
            reportName="Downtime Report"
            :showPartition="false"
            @rangeSelected="onRangeSelect"
            :reportType="reportType"
            :reportName="reportName"
        ></reports-common-header>

        <report-container>
            <template v-slot:reportContainer>
                <div>
                    <div style="background-color: #343345; width: 100%; padding: 30px;">
                        <div class="report-page">
                            <div class="chart-wrapper">
                                <downtime-chart :title="title" :dataset="downtimeDataset"/>
                            </div>
                        </div>
                        <div style="margin-top:30px;">
                            <div style="margin-bottom: 10px;">
                                <span style="font-size: 18px; color:#dddddd">{{ reportTableTitle }}</span>
                            </div>
                            <report-table-by-station :stationId="selectedStationId" :start="selectedRange.start" :end="selectedRange.end" v-if="selectedReportType==='station'"></report-table-by-station>
                            <report-table-by-product :stationProductId="selectedStationProductId" :start="selectedRange.start" :end="selectedRange.end" v-if="selectedReportType==='product'"></report-table-by-product>
                            <report-table-by-shift :stationShiftId="selectedStationShiftId" :start="selectedRange.start" :end="selectedRange.end" v-if="selectedReportType==='shift'"></report-table-by-shift>
                            <report-table-by-operator :stationOperatorId="selectedStationOperatorId" :start="selectedRange.start" :end="selectedRange.end" v-if="selectedReportType==='operator'"></report-table-by-operator>
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
    import DowntimeChart from '../../components/reports/DowntimeChart';
    import reportService from '../../services/Reports';
    import ReportTableDowntimeByStation from './reporttable/downtime/ReportTableDowntimeByStation';
    import ReportTableDowntimeByProduct from './reporttable/downtime/ReportTableDowntimeByProduct';
    import ReportTableDowntimeByShift from './reporttable/downtime/ReportTableDowntimeByShift';
    import ReportTableDowntimeByOperator from './reporttable/downtime/ReportTableDowntimeByOperator';

    export default {
        name: "DowntimeReport",
        components: {
            ReportFilters,
            ReportsCommonHeader,
            ReportContainer,
            'downtime-chart': DowntimeChart,
            'report-table-by-station': ReportTableDowntimeByStation,
            'report-table-by-product': ReportTableDowntimeByProduct,
            'report-table-by-shift': ReportTableDowntimeByShift,
            'report-table-by-operator': ReportTableDowntimeByOperator,
        },
        props: {
            reportType: {
                type: String,
                default: 'station'
            },
            reportName: {
                type: String,
                default: 'downtime'
            }
        },
        data: () => ({
            selectedRange: {
                start: moment().startOf('day').toDate(),
                end: moment().endOf('day').toDate()
            },
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
            downtimeDataset: {
                labels: [],
                downtimes: []
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
            }
        },
        methods: {
            clearQueryParams(){
                this.selectedStationProductId = 0;
                this.selectedStationShiftId = 0;
                this.selectedStationOperatorId = 0;
            },
            onRangeSelect(eventData) {
                console.log('range selected');
                console.log(eventData);
                this.selectedRange.start = eventData.start;
                this.selectedRange.end = eventData.end;
                this.fetchDowntimeData();
            },
            onReportTypeChange(eventData) {
                this.selectedReportType = eventData;
                this.fetchDowntimeData();
            },
            onStationChange(eventData) {
                this.clearQueryParams();
                this.selectedStationId = Number.parseInt(eventData.stationId);
                this.selectedStation = eventData.station;
                this.fetchDowntimeData();
            },
            onStationProductSelect(eventData) {
                this.clearQueryParams();
                this.selectedStationProductId = Number.parseInt(eventData.stationProductId);
                this.selectedStationProduct = eventData.stationProduct;
                this.fetchDowntimeData();
            },
            onStationShiftSelect(eventData) {
                this.clearQueryParams();
                this.selectedStationShiftId = Number.parseInt(eventData.stationShiftId);
                this.selectedStationShift = eventData.stationShift;
                this.fetchDowntimeData();
            },
            onStationOperatorSelect(eventData) {
                this.clearQueryParams();
                this.selectedStationOperatorId = Number.parseInt(eventData.stationOperatorId);
                this.selectedStationOperator = eventData.stationOperator;
                this.fetchDowntimeData();
            },
            fetchDowntimeData() {
                let data = {
                    stationId: this.selectedStationId == 0 ? null : this.selectedStationId,
                    stationProductId: this.selectedStationProductId == 0 ? null : this.selectedStationProductId,
                    stationShiftId : this.selectedStationShiftId == 0 ? null : this.selectedStationShiftId,
                    stationOperatorId : this.selectedStationOperatorId == 0 ? null : this.selectedStationOperatorId,
                    start: moment(this.selectedRange.start).format('YYYY-MM-DD'),
                    endTime: moment(this.selectedRange.end).format('YYYY-MM-DD'),
                };

                reportService.fetchDowntimeData(data, response => {
                    this.title = response.title;
                    this.$set(this.downtimeDataset, 'labels', response.dataset.labels);
                    this.$set(this.downtimeDataset, 'downtimes', response.dataset.duration);
                });
            }
        },
        watch: {
            reportType: function (newReportType, oldReportType) {
                this.selectedReportType = newReportType;
                this.fetchDowntimeData();
                // console.log(newReportType);
            }
        },
        mounted (){
            this.fetchDowntimeData();
        }
    }
</script>

<style scoped>

</style>
