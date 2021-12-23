<template>
    <nav class="navbar w-100">
        <div class="row w-100">
            <div class="col-sm-4">
                <label>Select Station Group</label>
                <select class="form-select" v-model="selectedStationGroupId" >
                    <option value="0">All</option>
                    <option v-for="stationGroup in StationGroup" :value="stationGroup.id" :key="stationGroup.id">{{ stationGroup.name }}</option>
                </select>
            </div>
            <div class="col-sm-4">
                <label>Select Station</label>
                <select class="form-select" v-model="selectedStationId" @change="stationChanged">
                    <option value="0">All</option>
                    <option v-for="station in filteredStations" :value="station.id" :key="station.id">{{ station.name }}</option>
                </select>
            </div>
            <div class="col-sm-4">
                <label>Select Team (With station)</label>
                <select class="form-select" v-model="selectedStationTeamId">
                    <option value="0">All</option>
                    <option v-for="st in filteredStationTeams" :value="st.id" :key="st.id">{{ st.name }}</option>
                </select>
            </div>
        </div>
    </nav>
</template>

<script>
    import StationService from '../../../services/StationsService';
    import StationTeamService from '../../../services/StationTeamService';

    export default {
        name: "ReportFiltersForTeamWiseReport",
        data: () => ({
            selectedStationGroupId: 0,
            selectedStationId: 0,
            // selectedStationTeamId: 0,
            StationGroup:[],
            allStations:[],
            allStationTeams: []
        }),
        computed: {

            selectedStationTeamId: {
                get() {
                    let selectedStationTeamIdCheck = this.$store.state.reportPageFilters.selectedStationTeamId;
                    return  selectedStationTeamIdCheck === null ? 0 : selectedStationTeamIdCheck;
                },
                set(stationTeamId) {
                    this.$store.dispatch('selectedStationTeamId', stationTeamId);
                }
            },

            filteredStations() {
                let vm = this;
                if(this.selectedStationGroupId == 0) {
                    return vm.allStations;
                }
                return vm.allStations.filter(function (station) {
                    return station.station_group.id === vm.selectedStationGroupId;
                });
            },
            filteredStationTeams() {
                let stationTeams = this.allStationTeams.map(function(st){
                    st.name = `${st.name} (Station: ${st.station_name} )`;
                    return st;
                });

                let vm = this;
                if(this.selectedStationGroupId != 0) {
                    return stationTeams.filter(function(st){
                        return st.station_group_id === vm.selectedStationGroupId;
                    });
                }
                if(this.selectedStationId != 0) {
                    return stationTeams.filter(function(st){
                        return st.station_id === vm.selectedStationId;
                    });
                }
                return stationTeams;
            },
            selectedStation(){
                const vm = this;
                return vm.allStations.find(station => station.id === vm.selectedStationId);
            },
            selectedStationTeam(){
                const vm = this;
                return vm.allStationTeams.find(so => so.id === vm.selectedStationTeamId);
            }
        },
        methods: {
            stationChanged() {
                if(this.selectedStation && this.selectedStation.station_group) this.selectedStationGroupId = this.selectedStation.station_group.id;
            },
            // stationTeamSelected() {
            //     if(this.selectedStationTeam){
            //         this.selectedStationId = this.selectedStationTeam.station_id;
            //         this.selectedStationGroupId = this.selectedStationTeam.station_group_id;
            //     }
            //     this.$emit('stationTeamSelected', {
            //         'stationTeamId' : this.selectedStationTeamId,
            //         'stationTeam': this.selectedStationTeam
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
            StationTeamService.allStationTeamsForReportDropdown(response => {
                this.allStationTeams = response;
                console.log('all station teams');
                console.log(this.allStationTeams);
            });

        }
    }
</script>
