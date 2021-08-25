<template>
    <div class="justify-content-center align-items-center" style="display: flex; flex-direction: row; ">
        <div style="width: 100%!important">
            <nav class="navbar navbar-expand-lg navbar-light bg-light" style="padding: 15px;">
                <div class="row" style="width: 100%!important;margin-left:0px!important">
                    <div class="col-sm-4">
                        <label>Select Station Group</label>
                        <select class="form-control" v-model="selectedStationGroupId" >
                            <option value="0">All</option>
                            <option v-for="stationGroup in StationGroup" :value="stationGroup.id" :key="stationGroup.id">{{ stationGroup.name }}</option>
                        </select>
                    </div>
                    <div class="col-sm-4">
                        <label>Select Station</label>
                        <select class="form-control" v-model="selectedStationId" @change="stationChanged">
                            <option value="0">All</option>
                            <option v-for="station in filteredStations" :value="station.id" :key="station.id">{{ station.name }}</option>
                        </select>
                    </div>
                    <div class="col-sm-4">
                        <label>Select Operator (With station)</label>
                        <select class="form-control" v-model="selectedStationOperatorId">
                            <option value="0">All</option>
                            <option v-for="so in filteredStationOperators" :value="so.id" :key="so.id">{{ so.name }}</option>
                        </select>
                    </div>
                </div>
            </nav>
        </div>
    </div>
</template>

<script>
    import StationService from '../../../services/StationsService';
    import StationOperatorService from '../../../services/StationOperatorService';

    export default {
        name: "ReportFiltersForOperatorWiseReport",
        data: () => ({
            selectedStationGroupId: 0,
            selectedStationId: 0,
            // selectedStationOperatorId: 0,
            StationGroup:[],
            allStations:[],
            allStationOperators: []
        }),
        computed: {

            selectedStationOperatorId: {
                get() {
                    return this.$store.state.reportPageFilters.selectedStationOperatorId;
                },
                set(stationOperatorId) {
                    this.$store.dispatch('selectedStationOperatorId', stationOperatorId);
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
            filteredStationOperators() {
                let stationOperators = this.allStationOperators.map(function(sp){
                    sp.name = `${sp.operator_name} (Station: ${sp.station_name} )`;
                    return sp;
                });

                let vm = this;
                if(this.selectedStationGroupId != 0) {
                    return stationOperators.filter(function(so){
                        return so.station_group_id === vm.selectedStationGroupId;
                    });
                }
                if(this.selectedStationId != 0) {
                    return stationOperators.filter(function(so){
                        return so.station_id === vm.selectedStationId;
                    });
                }
                return stationOperators;
            },
            selectedStation(){
                const vm = this;
                return vm.allStations.find(station => station.id === vm.selectedStationId);
            },
            selectedStationOperator(){
                const vm = this;
                return vm.allStationOperators.find(so => so.id === vm.selectedStationOperatorId);
            }
        },
        methods: {
            stationChanged() {
                if(this.selectedStation && this.selectedStation.station_group) this.selectedStationGroupId = this.selectedStation.station_group.id;
            },
            // stationOperatorSelected() {
            //     if(this.selectedStationOperator){
            //         this.selectedStationId = this.selectedStationOperator.station_id;
            //         this.selectedStationGroupId = this.selectedStationOperator.station_group_id;
            //     }
            //     this.$emit('stationOperatorSelected', {
            //         'stationOperatorId' : this.selectedStationOperatorId,
            //         'stationOperator': this.selectedStationOperator
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
            StationOperatorService.allStationOperatorsForReportDropdown(response => {
                this.allStationOperators = response;
            });

        }
    }
</script>

<style scoped lang="scss">
    @import "#/app.scss";

    .navbar {
        background-color: #b3b3b3;
        .nav-item {
            padding-top: 0.5rem;
            padding-bottom: 0.5rem;
            .nav-link {
                color: #666666;
            }
        }
    }

    .partition-picker {
        @extend .navbar-nav, .flex-grow-1, .justify-content-center;
    }

    .date-range-picker-wrap {
        @extend .d-flex, .flex-row, .justify-content-end, .align-items-center, .w-25;

        .range-picker-label {
            color: #dddddd;
            padding: 0.5rem 1rem;
            margin: 0 0.5rem;
            white-space: nowrap;
        }

        .date-range-picker.vc-reset {
            flex: 1;

            .__date-picker-input {
            }
        }

        .date-shortcut {
            .dropdown-menu {
                left: auto;
                right: 0;
            }
        }
    }
</style>

