<template>
    <div class="justify-content-center align-items-center" style="display: flex; flex-direction: row; ">
        <div style="width: 100%!important">
            <nav class="navbar navbar-expand-lg navbar-light bg-light" style="padding: 15px;">
                <div class="row" style="width: 100%!important;margin-left:0px!important">
                    <div class="col-sm-3">
                        <label>Select Station Group</label>
                        <select class="form-control" v-model="selectedStationGroupId" >
                            <option value="0">All</option>
                            <option v-for="stationGroup in StationGroup" :value="stationGroup.id" :key="stationGroup.id">{{ stationGroup.name }}</option>
                        </select>
                    </div>
                    <div class="col-sm-3">
                        <label>Select Station</label>
                        <select class="form-control" v-model="selectedStationId" @change="stationChanged">
                            <option value="0">All</option>
                            <option v-for="station in filteredStations" :value="station.id" :key="station.id">{{ station.name }}</option>
                        </select>
                    </div>
                    <div class="col-sm-3">
                        <label>Select Product Group</label>
                        <select class="form-control" v-model="selectedProductGroupId">
                            <option value="0">All</option>
                            <option v-for="productGroup in productGroups" :value="productGroup.id" :key="productGroup.id">{{ productGroup.name }}</option>
                        </select>
                    </div>
                    <div class="col-sm-3">
                        <label>Select Product (With station)</label>
                        <select class="form-control" v-model="selectedStationProductId" @change="stationProductSelected">
                            <option value="0">All</option>
                            <option v-for="sp in filteredStationProducts" :value="sp.id" :key="sp.id">{{ sp.name }}</option>
                        </select>
                    </div>
                </div>
            </nav>
        </div>
    </div>
</template>

<script>
    import StationService from '../../../services/StationsService';
    import ProductService from '../../../services/Products';
    import StationProductService from '../../../services/StationProductService';

    export default {
        name: "ReportFiltersForProductWiseReport",
        data: () => ({
            selectedStationGroupId: 0,
            selectedStationId: 0,
            selectedProductGroupId: 0,
            selectedStationProductId: 0,
            StationGroup:[],
            allStations:[],
            productGroups:[],
            allStationProducts: []
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
            filteredStationProducts() {
                let stationProducts = this.allStationProducts.map(function(sp){
                    sp.name = `${sp.product_name} (Station: ${sp.station_name} )`;
                    return sp;
                });

                let vm = this;
                if(this.selectedStationGroupId != 0) {
                    return stationProducts.filter(function(sp){
                        return sp.station_group_id === vm.selectedStationGroupId;
                    });
                }
                if(this.selectedProductGroupId != 0) {
                    return stationProducts.filter(function(sp){
                        return sp.product_group_id === vm.selectedProductGroupId;
                    });
                }
                if(this.selectedStationId != 0) {
                    return stationProducts.filter(function(sp){
                        return sp.station_id === vm.selectedStationId;
                    });
                }
                return stationProducts;
            },
            selectedStation(){
                const vm = this;
                return vm.allStations.find(station => station.id === vm.selectedStationId);
            },
            selectedStationProduct(){
                const vm = this;
                return vm.allStationProducts.find(sp => sp.id === vm.selectedStationProductId);
            }
        },
        methods: {
            stationChanged() {
                if(this.selectedStation && this.selectedStation.station_group) this.selectedStationGroupId = this.selectedStation.station_group.id;
            },
            stationProductSelected() {
                if(this.selectedStationProduct){
                    this.selectedProductGroupId = this.selectedStationProduct.product_group_id;
                    this.selectedStationId = this.selectedStationProduct.station_id;
                    this.selectedStationGroupId = this.selectedStationProduct.station_group_id;
                }
                this.$emit('stationProductSelected', {
                    'stationProductId': this.selectedStationProductId,
                    'stationProduct': this.selectedStationProduct
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
            ProductService.fetchAllGroups( response => {
                this.productGroups = response;
            });
            StationProductService.allStationProductsForReportDropdown(response => {
                this.allStationProducts = response;
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

