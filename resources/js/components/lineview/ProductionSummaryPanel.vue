<template>
    <div class="mt-1">
        <ul class="list-unstyled">
            <li v-for="product in filteredProducts">
                <div class="text-truncate mb-3" style="font-size: 17px;">PRODUCING: <span class="text-truncate" style="font-weight: 500; color: green;">{{ product.product_name }}</span></div>

                <div class="row">
                    <div class="col-md-12 d-flex justify-content-start">
                        <div class="card col-md-6 rounded-0" style="height: 4.5rem; background-color: #F8FCFF;">
                            <div class="card-body p-1 text-center">
                                <div style="font-weight: 500;" class="card-text text-center">PRODUCED</div>
                                <div style="color: green; font-size: 2.5rem; position: relative; bottom: 1rem">{{ product.produced }}</div>
                            </div>
                        </div>

                        <div class="card col-md-6 rounded-0" style="height: 4.5rem; background-color: #F8FCFF;">
                            <div class="card-body p-1 text-center">
                                <div style="font-weight: 500;" class="card-text text-center">DEFECT</div>
                                <div style="color: red; font-size: 2.5rem; position: relative; bottom: 1rem">{{ product.scrapped }}</div>
                            </div>
                        </div>
                    </div>
                </div>


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
            },
            filteredProducts() {
                return this.products.filter(prod => prod.start_time != null);
            }
        }
    }
</script>
