<template>
    <div class="container-fluid mt-4">
        <div class="row">
            <div class="col-md-12 d-flex justify-content-start flex-wrap">
                <chart-card :stationC="stationCount"></chart-card>
                <chart-card v-for="data in summaryData" :key="data.stationId" :summaryData="data"></chart-card>
            </div>
        </div>
    </div>
</template>

<script>
import ChartCard from "../components/dashboard/ChartCard";
import dashboardService from "../services/DashboardService";
export default {
    name: "Dashboard",
    components: {
        ChartCard,
    },
    data: () => ({
        summaryData: null,
        stationCount: 0
    }),
    methods: {
        fetchAllSummaryData: function() {
            var vm = this;
            dashboardService.fetchAllSummaryData(function (data) {
                vm.summaryData = data;
                vm.stationCount = Object.keys(data).length;
                vm.stationCount = parseInt(vm.stationCount);
                console.log(vm.stationCount);
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
