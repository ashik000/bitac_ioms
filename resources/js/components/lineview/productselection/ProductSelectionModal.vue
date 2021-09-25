<template xmlns:v-slot="http://www.w3.org/1999/XSL/Transform">
    <modal @close="$emit('close')">
        <template v-slot:header>
            <div class="container" style="width: 960px; ">
                <div class="row" style="margin-left: 0!important;">
                    <h5>Assign Product</h5>
                </div>
            </div>
        </template>
        <template v-slot:content>
            <div class="container" style="width: 960px; ">
                <div class="row" style="margin-left: 0!important; margin-bottom: 10px;">
                    <span>Current Station: {{ stationName }}</span>
                </div>
                <div style="overflow-y: scroll; height: 400px;">
                    <ul class="list-group">
                        <product-selection-list-item
                            v-for="product in products"
                            :key="product.id"
                            :product="product"
                            :station-id="stationId"
                            :station-name="stationName"
                            :product-id="productId"
                            @update-product="updateProduct"
                            :isChecked="selectedProductId === product.id">
                        </product-selection-list-item>
                    </ul>
                </div>
            </div>
        </template>

        <template v-slot:footer>
            <button class="btn btn-outline-danger" @click.prevent="$emit('close')">Close</button>
            <button class="btn btn-success ms-3" @click="assignProductToStation();" >Assign</button>
        </template>
    </modal>

</template>

<script>
import Modal from "../../Modal";
import ProductsService from "../../../services/Products"
import ProductSelectionListItem from "./ProductSelectionListItem";
import ToastrService from "../../../services/ToastrService";
import stationProductService from "../../../services/StationProductService";

export default {
    name: "ProductSelectionModal",
    data: function () {
        return {
            products: [],
            selectedProductId: this.productId
        }
    },

    components: {
        modal: Modal,
        ProductSelectionListItem
    },

    methods: {
        updateProduct(selectedProductId) {
            console.log('when updateProduct emit triggers on modal')
            console.log(selectedProductId);
            this.selectedProductId = selectedProductId;
        },
        assignProductToStation() {
            if (this.selectedProductId === this.productId) {
                ToastrService.showInfoToast('Product already assigned');
            } else {
                console.log('prev productId')
                console.log(this.productId)
                console.log('prev stationId')
                console.log(this.stationId)
                stationProductService.assignProductToStation({
                    product_id: this.selectedProductId,
                    station_id: this.stationId,
                }, data => {
                    console.log('success')
                    ToastrService.showSuccessToast('Product assigned to the station successfully.');
                    this.$emit('close');
                }, error => {
                    // console.log(error)
                    ToastrService.showErrorToast('Error! Try again.')
                    this.$emit('close');
                });
            }
        }
    },
    mounted: function (){
        const vm = this;
        console.log('stationID check')
        console.log(this.stationId)
        ProductsService.fetchAllByStationId({
            stationId: this.stationId
        }, (data) => {
            console.log('fetchallbystationid')
            console.log(data)
            vm.products = data;
        }, (error) => {
            console.log(error);
        });
    },

    props: {
        stationId: Number,
        stationName: String,
        productId: Number
    }

}
</script>

<style scoped>

</style>
