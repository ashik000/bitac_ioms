<template>
    <nav class="navbar w-100">
        <div class="row w-100">
            <div class="col-sm-6">
                <div class="">
                    <label>Select Station Group</label>
                    <select class="form-control station_wise_select" v-model="selectedStationGroupId">
                        <option value="0">All</option>
                        <option v-for="stationGroup in StationGroup" :value="stationGroup.id" :key="stationGroup.id">{{ stationGroup.name }}</option>
                    </select>
                </div>
            </div>
            <div class="col-sm-6">
                <div class="">
                    <label>Select Station</label>
                    <select class="form-control station_wise_select" v-model="selectedStationId">
                        <option value="0">All</option>
                        <option v-for="station in filteredStations" :value="station.id" :key="station.id">{{ station.name }}</option>
                    </select>
                </div>
            </div>
        </div>
    </nav>
</template>

<script>
    import StationService from '../../../services/StationsService';

    export default {
        name: "ReportFiltersForStationWiseReport",
        data: () => ({
            // selectedStationId: 0,
            selectedStationGroupId: 0,
            StationGroup:[],
            allStations:[],
        }),
        computed: {

            selectedStationId: {
                get() {
                    let selectedStationIdCheck = this.$store.state.reportPageFilters.selectedStationId;
                    return  selectedStationIdCheck === null ? 0 : selectedStationIdCheck;
                },
                set(stationId) {
                    this.$store.dispatch('selectedStationId', stationId);
                }
            },

            filteredStations() {
                const vm = this;
                if(vm.selectedStationGroupId == 0){
                    return vm.allStations;
                }
                return vm.allStations.filter(function (station) {
                    return station.station_group.id === vm.selectedStationGroupId;
                });
            },
            selectedStation(){
                const vm = this;
                return vm.allStations.find(station => station.id === vm.selectedStationId);
            },
        },
        methods: {
            // stationChanged() {
            //     if(this.selectedStation){
            //         this.selectedStationGroupId = this.selectedStation.station_group.id;
            //     }
            //     this.$emit('stationChanged', {
            //         'stationId': this.selectedStationId,
            //         'station': this.selectedStation
            //     });
            // },
        },
        mounted() {
            StationService.fetchAllGroups( response => {
                this.StationGroup = response;
            });
            StationService.fetchAll([], response => {
                this.allStations = response;
            });


        }
    }
</script>

<style scoped>
.station_wise_select, option {
  width: 120px!important;
}
</style>
