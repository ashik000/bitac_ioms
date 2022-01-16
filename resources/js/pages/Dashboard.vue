<template>
    <div class="container-fluid mt-4">
        <div class="row">
            <div class="col-md-12 d-flex justify-content-start flex-wrap">
                <chart-card :stationC="stationCount" :activeStationCount="activeStationCount" :inactiveStationCount="inactiveStationCount"></chart-card>
                <chart-card v-for="data in summaryData" :key="data.stationId" :summaryData="data"></chart-card>
            </div>
        </div>
    </div>
</template>

<script>
import ChartCard from "../components/dashboard/ChartCard";
import dashboardService from "../services/DashboardService";
import moment from "moment";
export default {
    name: "Dashboard",
    components: {
        ChartCard,
    },
    data: () => ({
        summaryData: null,
        stationCount: 0,
        activeStationCount: 0,
        inactiveStationCount: 0,
    }),
    methods: {
        fetchAllSummaryData: function() {
            var vm = this;
            dashboardService.fetchAllSummaryData(function (data) {
                vm.summaryData = data;
                vm.stationCount = Object.keys(data).length;
                vm.stationCount = parseInt(vm.stationCount);

                // loop through the data
                for (var key in data) {
                    if (data[key].color === "black") {
                        vm.inactiveStationCount++;
                    } else {
                        vm.activeStationCount++;
                    }
                }

                console.log(vm.inactiveStationCount);
                console.log(vm.activeStationCount);
            }, function (error) {
                console.error(error);
            })
        },
        updateData: function() {
            this.activeStationCount = 0;
            this.inactiveStationCount = 0;
            this.fetchAllSummaryData();
        }
    },
    mounted() {
        this.fetchAllSummaryData();
        this.dataUpdateTimer = setInterval(this.updateData, 1000*5);
    },
    destroyed() {
        clearInterval(this.dataUpdateTimer);
    }
}
</script>
