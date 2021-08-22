<template>
    <div>
        <div class="row">
            <div class="col-md-3">
                <ReportSideBar v-on:reportTypeChanged="changeReportType" :reportType="reportType">
                </ReportSideBar>
            </div>
            <div class="col-md-9">
                <router-view :reportType="reportType"></router-view>
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
            reportType: 'station'
        }),
        methods : {
            changeReportType(type) {
                this.reportType = type;
                this.selectedReportType = type;
            },
        }
    }
</script>

<style scoped>

</style>
