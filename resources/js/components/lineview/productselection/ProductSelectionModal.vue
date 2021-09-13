<template xmlns:v-slot="http://www.w3.org/1999/XSL/Transform">
    <modal @close="$emit('close')">
        <template v-slot:header>
            <div class="container" style="width: 960px; ">
                <div class="row" style="margin-left: 0!important; margin-bottom: 10px;">
                    <h5>Select Product</h5>
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
                            :product-id="productId">
                        </product-selection-list-item>
                    </ul>
                </div>
            </div>
        </template>

        <template v-slot:footer>
            <button class="btn btn-outline-danger" @click.prevent="$emit('close')">Close</button>
            <button class="btn btn-success ms-2" >Assign</button>
        </template>
    </modal>

</template>

<script>
import Modal from "../../Modal";
import ProductsService from "../../../services/Products"
import ProductSelectionListItem from "./ProductSelectionListItem";

export default {
    name: "ProductSelectionModal",
    data: function () {
        return {
            products: []
        }
    },

    components: {
        modal: Modal,
        ProductSelectionListItem
    },

    methods: {

    },
    mounted: function (){
        const vm = this;

        ProductsService.fetchAll([], (data) => {
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
