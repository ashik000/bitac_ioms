<template>
    <div class="justify-content-center align-items-center" style="display: flex; flex-direction: row; ">
        <div style="width: 100%!important">
            <nav class="navbar navbar-expand-lg navbar-light bg-theme nav-fill" style="padding: 15px;">
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
                        <label>Select Shift (With station)</label>
                        <select class="form-control" v-model="selectedStationShiftId" @change="stationShiftSelected">
                            <option value="0">All</option>
                            <option v-for="ss in filteredStationShifts" :value="ss.id" :key="ss.id">{{ ss.name }}</option>
                        </select>
                    </div>
                </div>
            </nav>
        </div>
    </div>
</template>

<script>
    import StationService from '../../../services/StationsService';
    import StationShiftService from '../../../services/StationShiftService';

    export default {
        name: "ReportFiltersForShiftWiseReport",
        data: () => ({
            selectedStationGroupId: 0,
            selectedStationId: 0,
            selectedStationShiftId: 0,
            StationGroup:[],
            allStations:[],
            allStationShifts: []
        }),
        computed: {
            filteredStations() {
                let vm = this;
                if(this.selectedStationGroupId == 0) {
                    return vm.allStations;
                }
                return vm.allStations.filter(function (station) {
                    return station.station_group.id === vm.selectedStationGroupId;
                });
            },
            filteredStationShifts() {
                let stationShifts = this.allStationShifts.map(function(ss){
                    ss.name = `${ss.shift_name} (Station: ${ss.station_name} )`;
                    return ss;
                });

                let vm = this;
                if(this.selectedStationGroupId != 0) {
                    return stationShifts.filter(function(ss){
                        return ss.station_group_id === vm.selectedStationGroupId;
                    });
                }
                if(this.selectedStationId != 0) {
                    return stationShifts.filter(function(ss){
                        return ss.station_id === vm.selectedStationId;
                    });
                }
                return stationShifts;
            },
            selectedStation(){
                const vm = this;
                return vm.allStations.find(station => station.id === vm.selectedStationId);
            },
            selectedStationShift(){
                const vm = this;
                return vm.allStationShifts.find(so => so.id === vm.selectedStationShiftId);
            }
        },
        methods: {
            stationChanged() {
                if(this.selectedStation && this.selectedStation.station_group) this.selectedStationGroupId = this.selectedStation.station_group.id;
            },
            stationShiftSelected() {
                if(this.selectedStationShift){
                    this.selectedStationId = this.selectedStationShift.station_id;
                    this.selectedStationGroupId = this.selectedStationShift.station_group_id;
                }
                this.$emit('stationShiftSelected', {
                    'stationShiftId': this.selectedStationShiftId,
                    'stationShift': this.selectedStationShift
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
            StationShiftService.allStationShiftsForReportDropdown(response => {
                this.allStationShifts = response;
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

