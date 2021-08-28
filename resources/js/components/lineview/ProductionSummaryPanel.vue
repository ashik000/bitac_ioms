<template>
    <div>
<!--        <div class="d-flex justify-content-between">-->
<!--            <p class="display-4 text-white">{{ produced }} pcs</p>-->
<!--            <p class="display-4 text-success">{{ totalOee }} %</p>-->
<!--        </div>-->
        <ul class="line-product-list">
            <li class="line-item" v-for="product in products">
                <span class="product-name">PRODUCING: {{ product.product_name }}</span>
                <br>
                <span class="quality">
                    <span class="produced">PRODUCED: {{ product.produced }}</span>
                    <span class="scrapped">DEFECT: {{ product.scrapped }}</span>
                </span>
            </li>
        </ul>
    </div>
</template>

<script>
    export default {
        name: "ProductionSummaryPanel",
        props: {
            produced: Number,
            expected: Number,
            products: {
                type: Array,
            },
            oee: Number
        },
        computed: {
            totalOee(){
                return isNaN(this.oee * 100) ? 0 : (this.oee * 100).toFixed(2);
            }
        }
    }
</script>
