<template>
    <div class="mt-2 mb-2">
        <ul class="list-unstyled">
            <li class="mb-2" v-for="product in products">
                <span class="mb-1" style="font-weight: 500;">PRODUCING: <span style="font-weight: 500; color: green;">{{ product.product_name }}</span></span>
                <br>
                <div class="row">
                    <div class="col md-6">
                        <div class="card mb-1" style="width: 6.5rem;">
                            <div class="card-body p-1">
                                <p style="font-weight: 500;" class="card-text">PRODUCED: <br> <span style="color: green; font-size: 2.5rem;">{{ product.produced }}</span></p>
                            </div>
                        </div>
                    </div>

                    <div class="col-md-6">
                        <div class="card mb-1" style="width: 6.5rem;">
                            <div class="card-body p-1">
                                <p style="font-weight: 500;" class="card-text">DEFECT: <br> <span style="color: red; font-size: 2.5rem;">{{ product.scrapped }}</span></p>
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
            }
        }
    }
</script>
