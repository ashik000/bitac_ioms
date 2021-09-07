<template>
    <li class="list-group-item listitemm">
        <div class="row">

            <div class="form-check">
                <input class="form-check-input" type="radio" name="flexRadioDefault" id="flexRadioDefault1" :checked="product.id == checkedVal">
                <label class="form-check-label" for="flexRadioDefault1">
                    {{ product.name }}
                </label>
            </div>

        </div>
    </li>
</template>

<script>
    import stationProductService from "../../../services/StationProductService"

    export default {
        name: "ProductSelectionListItem",

        data: function () {
            return {
                assignedStationIds: [],
                checkedVal: 1
            }
        },

        methods: {
            // getAssignedStationIdsFromObject: function () {
            //     this.product.stations.forEach((value, index) => {
            //         this.assignedStationIds.push(value.id);
            //     });
            // },

            // removeProduct: function (productStations) {
            //     productStations.forEach((value, index) => {
            //         if(value.id === this.stationId){
            //             stationProductService.deleteStationProduct(value.meta.id, (data) => {
            //                 this.product.stations.splice(index, 1);
            //             }, (error) => {
            //                 console.log(error);
            //             });
            //             return;
            //         }
            //     });
            // },

            // assignProduct: function (productId) {
            //     const vm = this;
            //     const request = {
            //         station_id: this.stationId,
            //         product_id: productId,
            //         // start_time : this.product.startTime
            //     };

            //     stationProductService.createOrUpdateStationProduct(request, function(data){
            //         let stationProduct = data.filter(function(d){
            //             return d.product_id === vm.product.id && d.station_id === vm.stationId;
            //         });

            //         let stationProductMeta = {
            //             'id': stationProduct[0].id,
            //             'product_id': stationProduct[0].product_id,
            //             'station_id': stationProduct[0].station_id,
            //         }

            //         let station = {
            //             'id': vm.stationId,
            //             'name': vm.stationName,
            //             'meta': stationProductMeta
            //         };
            //         if(vm.product.stations && vm.product.stations instanceof Array){
            //             vm.product.stations.push(station);
            //         } else {
            //             vm.product.stations = [];
            //             vm.product.stations.push(station);
            //         }
            //     }, (error) => {
            //         console.log(error);
            //     });
            // }
        },

        props: {
            product: Object,
            stationId: Number,
            stationName: String
        },
        mounted() {
            this.ch
            // console.log('test item')
            let vm = this;

            // let filteredStations = this.product.stations.filter(function(opst){
            //     return opst.meta.station_id == vm.stationId;
            // });
            // if(filteredStations && filteredStations.length > 0){
            //     this.$set(this.product, 'startTime' , filteredStations[0].meta.start_time);
            // }
        },
        computed:{
            // alreadyAssigned: function (){
            //     return this.assignedStationIds.indexOf(this.stationId) != -1;
            // },
            // assignedStationIds(){
            //     let arr = [];
            //     this.product.stations.forEach((value, index) => {
            //         arr.push(value.id);
            //     });
            //     return arr;
            // }
        },
        updated() {
            // this.checkedVal = 2
            // console.log('pre');
            // console.log(stationId);
            // if (stationId !== null) {
            //   stationProductService.fetchAll(stationId, (data) => {
            //       console.log('stationProductService');
            //       console.log(data)
            //   }, (error) => {
            //       console.log(error);
            //   });
            // }
        }
    }
</script>

<style src="vue-ctk-date-time-picker/dist/vue-ctk-date-time-picker.css"></style>

<style scoped lang="scss">
    .listitemm {
        margin-bottom: 15px;
        /*-webkit-box-shadow: 1px 2px 5px -1px rgba(0, 0, 0, 0.5);
        -moz-box-shadow: 1px 2px 5px -1px rgba(0, 0, 0, 0.5);
        box-shadow: 1px 2px 5px -1px rgba(0, 0, 0, 0.5);*/
    }

    li:hover {
        /*margin-right:40px;*/
        background: #f5f5f5;
        border-left: 3px solid #337ab7;
    }

    /*li {*/
    /*    transition: all 300ms ease-in-out;*/
    /*    &:hover {*/
    /*margin-right:40px;*/
    /*}*/
    /*}*/
</style>
