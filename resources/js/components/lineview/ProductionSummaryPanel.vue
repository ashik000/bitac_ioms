<template>
    <div>
        <div class="d-flex justify-content-between">
            <p class="display-4 text-white">{{ produced }} pcs</p>
            <p class="display-4 text-success">{{ totalOee }} %</p>
        </div>
        <ul class="line-product-list">
            <li class="line-item" v-for="product in products">
                <span class="product-name">{{ product.product_name }}</span>
                <span class="quality">
                    <span class="produced">{{ product.produced }}</span>
                    <span class="scrapped">{{ product.scrapped }}</span>
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

<style scoped lang="scss">
    @import "#/app.scss";

    .line-product-list {
        padding: 0;
        list-style: none;

        color: #ffffff;
        font-size: 1.25rem;

        .line-item {
            display: flex;
            flex-direction: row;
            flex-wrap: nowrap;
            justify-content: space-between;

            .scrapped {
                &:before {
                    font-family: "Material Icons";
                    content: "\e5cd";
                    color: #ff1111;
                    display: inline-block;
                    padding-left: 0.5rem;
                    padding-right: 0.5rem;
                }
                color: #ff1111;
            }

            &:first-of-type {
                font-size: 1.5rem;
                font-weight: 600;

                padding-bottom: 1rem;
                margin-bottom: 1rem;
                //border-bottom: 0.25rem solid $primary-color-accent;

                .produced {
                    &:before {
                        font-family: "Material Icons";
                        content: "\e5ca";
                        color: #14a76c;
                        display: inline-block;
                        padding-left: 0.5rem;
                        padding-right: 0.5rem;
                    }
                    color: #14a76c;
                }
            }
        }
    }
</style>
