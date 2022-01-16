<template>
    <div class="container-fluid mt-4">
        <div class="row">
            <div class="col-md-12 d-flex justify-content-start flex-wrap">
                <chart-card></chart-card>
                <chart-card v-for="data in summaryData" :key="data.stationId"  :summaryData="data"></chart-card>
            </div>
        </div>
    </div>
</template>

<script>
import ChartCard from "../components/dashboard/ChartCard";
import dashboardService from "../services/DashboardService"
export default {
    name: "Dashboard",
    components: {
        ChartCard,
    },
    data: () => ({
        summaryData : null
    }),
    methods: {
        fetchAllSummaryData : function() {
            var vm = this;
            dashboardService.fetchAllSummaryData(function (data) {
                vm.summaryData = data;
            }, function (error) {
                console.error(error);
            })
        }
    },
    mounted() {
        this.fetchAllSummaryData();
    }
}
</script>
