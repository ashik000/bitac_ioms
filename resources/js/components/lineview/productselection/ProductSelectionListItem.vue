<template>
    <li class="list-group-item listitemm">
        <div class="row">

            <div class="form-check">
                <input class="form-check-input" type="radio" name="flexRadioDefault" id="flexRadioDefault1" :value="product.id" @change="onProductChange($event)" :checked="product.id == checkedVal">
                <label class="form-check-label" for="flexRadioDefault1">
                    {{ product.name }}
                </label>
            </div>

        </div>
    </li>
</template>

<script>
    import stationProductService from "../../../services/StationProductService"
import ToastrService from '../../../services/ToastrService';

    export default {
        name: "ProductSelectionListItem",

        data: function () {
            return {
                assignedStationIds: [],
                checkedVal: 0,
                triggeredProductId: 0,
            }
        },

        methods: {
            onProductChange(event) {
                var data = event.target.value;
                console.log('on product change triggered')

                console.log(data);
                this.triggeredProductId = data;
                // store the new product id & remove the old ones
                this.assignProductToStation(this.triggeredProductId, this.stationId);
            },
            assignProductToStation(productId, stationId) {
            let payload = {
                productId: productId,
                stationId: stationId
            }
            console.log('payload b4 assigning product')
            console.log(payload);

            // on hold

            // stationProductService.assignProductToStation({
            //     product_id: productId,
            //     station_id: stationId,
            // }, data => {
            //     console.log('success')
            //     ToastrService.showSuccessToast('Product assigned to station successfully');
            // }, error => {
            //     console.log(error)
            // });
            },
        },

        props: {
            product: Object,
            stationId: Number,
            stationName: String,
            productId: Number,
        },
        mounted() {
            console.log('test product mount')
            console.log(this.product);
            let vm = this;

            vm.checkedVal = vm.productId;
            console.log('check productId')
            console.log(vm.productId);

            // if (vm.stationId !== null) {
            //     stationProductService.fetchAll(vm.stationId, (data) => {
            //         console.log('station product loaded');
            //         // console.log(data[0].product_id);
            //         vm.checkedVal = data[0].product_id;
            //     }, (error) => {
            //         console.log(error);
            //     });
            // }
        },
        computed:{

        },
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
