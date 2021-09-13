<template>
    <div class="container-fluid wrapper wrapper-top-4">
        <div class="row card-wrapper gx-0">
            <div class="section col-md-2 h-100">
                <ReportSideBar v-on:reportTypeChanged="changeReportType" :reportName="reportName" :reportType="reportType">
                </ReportSideBar>
            </div>
            <div class="section col-md-10 h-100">
                <router-view :reportType="reportType" @reportNameX="updateReportName"></router-view>
            </div>
        </div>
    </div>

</template>

<script>

    import ReportSideBar from "../../components/reports/ReportSideBar";
    import ReportFilters from "../../pages/Reports/filters/ReportFilters";
    import ReportContainer from "./ReportContainer";
    import OEEChart from "../../components/reports/OEEChart";
    import ReportTableOEEByStation from "./reporttable/oee/ReportTableOEEByStation";
    import ReportTableOEEByProduct from "./reporttable/oee/ReportTableOEEByProduct";
    import ReportTableOEEByShift from "./reporttable/oee/ReportTableOEEByShift";
    import ReportTableOEEByOperator from "./reporttable/oee/ReportTableOEEByOperator";

    export default {
        name: "ReportBase",
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
        data: () => ({
            reportType: 'station',
            reportName: 'report name loading'
        }),
        methods : {
            changeReportType(type) {
                this.reportType = type;
                this.selectedReportType = type;
            },
            updateReportName(reportNameX) {
                this.reportName = reportNameX;
            },
        },
    }
</script>
