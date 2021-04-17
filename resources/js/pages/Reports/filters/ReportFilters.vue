<template>
    <div>
        <report-filters-for-station-wise-report v-if="reportType==='station'" v-on="$listeners"></report-filters-for-station-wise-report>
        <report-filters-for-product-wise-report v-if="reportType==='product'" v-on="$listeners"></report-filters-for-product-wise-report>
        <report-filters-for-shift-wise-report v-if="reportType==='shift'" v-on="$listeners"></report-filters-for-shift-wise-report>
        <report-filters-for-operator-wise-report v-if="reportType==='operator'" v-on="$listeners"></report-filters-for-operator-wise-report>
    </div>
</template>

<script>
    import ReportFiltersForStationWiseReport from './ReportFiltersForStationWiseReport';
    import ReportFiltersForProductWiseReport from './ReportFiltersForProductWiseReport';
    import ReportFiltersForOperatorWiseReport from './ReportFiltersForOperatorWiseReport';
    import ReportFiltersForShiftWiseReport from './ReportFiltersForShiftWiseReport';

    export default {
        name: "ReportFilters",
        props:{
            reportType:{
                type: String,
                default: 'station'
            }
        },
        components:{
            ReportFiltersForStationWiseReport,
            ReportFiltersForProductWiseReport,
            ReportFiltersForOperatorWiseReport,
            ReportFiltersForShiftWiseReport
        }
    }
</script>

<style scoped>

</style>
