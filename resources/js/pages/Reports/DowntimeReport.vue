<template>
    <div>
        <reports-common-header
            :showPartition="false"
            @rangeSelected="onRangeSelect"
            :reportType="reportType"
            :reportName="reportName">
        </reports-common-header>

        <report-container>
            <template v-slot:reportContainer>
                <div>
                    <div>
                        <div class="report-page" v-if="downtimeDataset.labels.length > 0">
                            <div class="chart-wrapper">
                                <downtime-chart :title="title" :dataset="downtimeDataset"/>
                            </div>
                        </div>
                        <div v-else>
                            <p style="text-align: center; color: red">No Data Found</p>
                        </div>
                        <div class="table-container y-scroll report_table_container">
                            <div>
                                <span>{{ reportTableTitle }}</span>
                            </div>
                            <report-table-by-station :stationId="reportPageFilters.selectedStationId" :start="selectedRange.start" :end="selectedRange.end" v-if="selectedReportType==='station'"></report-table-by-station>
                            <report-table-by-product :stationProductId="reportPageFilters.selectedStationProductId" :start="selectedRange.start" :end="selectedRange.end" v-if="selectedReportType==='product'"></report-table-by-product>
                            <report-table-by-shift :stationShiftId="reportPageFilters.selectedStationShiftId" :start="selectedRange.start" :end="selectedRange.end" v-if="selectedReportType==='shift'"></report-table-by-shift>
                            <report-table-by-operator :stationOperatorId="reportPageFilters.selectedStationOperatorId" :start="selectedRange.start" :end="selectedRange.end" v-if="selectedReportType==='operator'"></report-table-by-operator>
                            <report-table-by-team :stationTeamId="reportPageFilters.selectedStationTeamId" :start="selectedRange.start" :end="selectedRange.end" v-if="selectedReportType==='team'"></report-table-by-team>
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
    import ReportTableDowntimeByTeam from './reporttable/downtime/ReportTableDowntimeByTeam';
    import {mapState} from "vuex";

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
            'report-table-by-team': ReportTableDowntimeByTeam,
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
            selectedStation: null,
            selectedStationProduct: null,
            selectedStationShift: null,
            selectedStationOperator: null,
            selectedStationTeam: null,
            title: '',
            downtimeDataset: {
                labels: [],
                planned: [],
                unplanned: []
            },
            tableData: [],
            reportNameX: 'Downtime Report'
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
            clearQueryParams(){
                this.selectedStationId = 0;
                this.selectedStationProductId = 0;
                this.selectedStationShiftId = 0;
                this.selectedStationOperatorId = 0;
                this.selectedStationTeamId = 0;
            },
            onRangeSelect(eventData) {
                this.selectedRange.start = eventData.start;
                this.selectedRange.end = eventData.end;
                this.fetchDowntimeData();
            },
            onReportTypeChange(eventData) {
                this.selectedReportType = eventData;
                this.fetchDowntimeData();
            },
            fetchDowntimeData() {
                let data = {
                    stationId: this.reportPageFilters.selectedStationId === 0 ? null : this.reportPageFilters.selectedStationId,
                    stationProductId: this.reportPageFilters.selectedStationProductId === 0 ? null : this.reportPageFilters.selectedStationProductId,
                    stationShiftId : this.reportPageFilters.selectedStationShiftId === 0 ? null : this.reportPageFilters.selectedStationShiftId,
                    stationOperatorId : this.reportPageFilters.selectedStationOperatorId === 0 ? null : this.reportPageFilters.selectedStationOperatorId,
                    stationTeamId : this.reportPageFilters.selectedStationTeamId === 0 ? null : this.reportPageFilters.selectedStationTeamId,
                    start: moment(this.selectedRange.start).format('YYYY-MM-DD'),
                    endTime: moment(this.selectedRange.end).format('YYYY-MM-DD'),
                };

                reportService.fetchDowntimeData(data, response => {
                    this.title = response.title;

                    let x_labels = [];
                    for (const [key, value] of Object.entries(response.dataset)) {
                        x_labels.push(`${key}`);
                    }

                    let planned = [];
                    let unplanned = [];
                    let reasons = [];
                    for (var key in response.dataset) {
                        // skip loop if the property is from prototype
                        if (!response.dataset.hasOwnProperty(key)) continue;

                        var obj = response.dataset[key];
                        for (var prop in obj) {
                            // skip loop if the property is from prototype
                            if (!obj.hasOwnProperty(prop)) continue;


                            if (prop === 'planned_duration') {
                                planned.push(obj[prop]);
                            }
                            if (prop === 'unplanned_duration') {
                                unplanned.push(obj[prop]);
                            }
                            if (prop === 'reasons') {
                                reasons.push(obj[prop]);
                            }
                        }
                    }

                    this.$set(this.downtimeDataset, 'labels', x_labels);
                    this.$set(this.downtimeDataset, 'planned', planned);
                    this.$set(this.downtimeDataset, 'unplanned', unplanned);
                });
            }
        },
        watch: {
            reportType: function (newReportType, oldReportType) {
                this.selectedReportType = newReportType;
                this.fetchDowntimeData();
            },
            reportPageFilters: {
                handler(newFilters, oldFilters) {
                    this.fetchDowntimeData();
                },
                deep: true
            }
        },
        mounted (){
            this.fetchDowntimeData();
            this.$emit('reportNameX', this.reportNameX);
        }
    }
</script>
