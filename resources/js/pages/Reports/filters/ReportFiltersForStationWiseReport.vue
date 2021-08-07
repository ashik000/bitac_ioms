<template>
    <div class="justify-content-center align-items-center" style="display: flex; flex-direction: row; ">
        <div style="width: 100%!important">
            <nav class="navbar navbar-expand-lg navbar-light bg-theme nav-fill" style="padding: 15px;">
                <div class="row" style="width: 100%!important;margin-left:0px!important">
                    <div class="col-sm-6">
                        <label>Select Station Group</label>
                        <select class="form-control" v-model="selectedStationGroupId">
                            <option value="0">All</option>
                            <option v-for="stationGroup in StationGroup" :value="stationGroup.id" :key="stationGroup.id">{{ stationGroup.name }}</option>
                        </select>
                    </div>
                    <div class="col-sm-6">
                        <label>Select Station</label>
                        <select class="form-control" v-model="selectedStationId" @change="stationChanged">
                            <option value="0">All</option>
                            <option v-for="station in filteredStations" :value="station.id" :key="station.id">{{ station.name }}</option>
                        </select>
                    </div>
                </div>
            </nav>
        </div>
    </div>
</template>

<script>
    import StationService from '../../../services/StationsService';

    export default {
        name: "ReportFiltersForStationWiseReport",
        data: () => ({
            selectedStationGroupId: 0,
            selectedStationId: 0,
            StationGroup:[],
            allStations:[],
        }),
        computed: {
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
            stationChanged() {
                if(this.selectedStation){
                    this.selectedStationGroupId = this.selectedStation.station_group.id;
                }
                this.$emit('stationChanged', {
                    'stationId': this.selectedStationId,
                    'station': this.selectedStation
                });
            },
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

