<template>
    <div>
        <header class="section-header">
            <span style="color: #ffffff">
                Products
            </span>
            <button class="btn" style="color: #ffffff" @click="openStationProductCreateModal()">
                <i class="icon material-icons" style="font-size:24px; color:white;">add_circle_outline</i>
            </button>
        </header>
        <div>
            <table class="settings-table table small" v-if="showStationProductsTable">
                <thead>
                    <tr>
                        <th>Name</th>
                        <th>Cycle Time</th>
                        <th>Unit</th>
                        <th>Timeout</th>
                        <th>Units/Signal</th>
                        <th>Threshold</th>
                        <th class="text-center">Actions</th>
                    </tr>
                </thead>
                <tbody>
                <tr v-for="stationProduct in stationProducts" :key="stationProduct.id">
                    <td>{{stationProduct.product.name}}</td>
                    <td>{{stationProduct.cycle_time}}</td>
                    <td style="text-transform: lowercase">{{stationProduct.cycle_unit}}</td>
                    <td>{{stationProduct.cycle_timeout}}</td>
                    <td>{{stationProduct.units_per_signal}}</td>
                    <td>{{stationProduct.performance_threshold}}</td>
                    <td>
                        <div class="d-inline-flex">
                            <a class="btn btn-link">
                                <i class="material-icons" @click.prevent="openStationProductEditForm(stationProduct)">
                                    edit
                                </i>
                            </a>
                            <a class="btn btn-link">
                                <i class="material-icons" @click.prevent="openStationProductDeleteForm(stationProduct)">
                                    delete
                                </i>
                            </a>
                        </div>
                    </td>
                </tr>
                </tbody>
            </table>
        </div>
        <Modal v-if="showStationProductModal == true" @close="closeStationProductModal()">
            <template v-slot:header>
                <div class="text-center">
                    <h5>{{stationProduct.id == null ? 'Add Station Product' : 'Edit Station Product'}}</h5>
                </div>
            </template>
            <template v-slot:content>
                <form>
                    <div class="form-group">
                        <label>Product</label>
                        <select v-if="stationProduct.id == null" class="form-control"
                                v-model="stationProduct.product_id">
                            <option v-for="product in products" :value="product.id">
                                {{ product.name }}
                            </option>
                        </select>
                        <label>Cycle Time</label>
                        <input type="number" v-model="stationProduct.cycle_time" class="form-control"
                               placeholder="Enter Cycle Time">
                        <label>Cycle Unit</label>
                        <select class="form-control" v-model="stationProduct.cycle_unit">
                            <option value="SEC_PER_PCS">SEC_PER_PCS</option>
                            <option value="PCS_PER_SEC">PCS_PER_SEC</option>
                            <option value="PCS_PER_MINUTE">PCS_PER_MINUTE</option>
                            <option value="PCS_PER_HOUR">PCS_PER_HOUR</option>
                        </select>
                        <label>Cycle Timeout</label>
                        <input type="number" v-model="stationProduct.cycle_timeout" class="form-control"
                               placeholder="Enter Cycle Timeout">
                        <label>Units Per Signal</label>
                        <input type="number" v-model="stationProduct.units_per_signal" class="form-control"
                               placeholder="Enter Units Per Signal">
                        <label>Performance Threshold</label>
                        <input type="number" v-model="stationProduct.performance_threshold" class="form-control"
                               placeholder="Enter Performance Threshold">
                    </div>
                    <button class="btn btn-primary" @click.prevent="createStationProduct()">Submit</button>
                </form>
            </template>
            <template v-slot:footer>
            </template>
        </Modal>
        <Modal v-if="showStationProductDeleteModal" @close="closeStationProductModal">
            <template v-slot:header>
                <div class="container">
                    Delete Station Product
                </div>
            </template>
            <template v-slot:content>
                <form @submit.prevent="">
                    <p>Are you sure you want to delete ?</p>
                    <button class="btn btn-danger" @click="deleteStationProduct">Submit</button>
                </form>
            </template>
            <template v-slot:footer>
            </template>
        </Modal>
    </div>
</template>

<script>
    import stationProductService from '../../services/StationProductService';
    import productService from '../../services/Products';

    export default {
        name: "StationProduct",
        props: {
            stationId: {
                type: Number,
                required: true
            },
        },
        data: () => ({
            stationProducts: [],
            showStationProductDeleteModal: false,
            showStationProductModal: false,
            products: [],
            selectedProductId: null,
            stationProduct: {
                id: null,
                station_id: null,
                product_id: null,
                cycle_time: 0,
                cycle_unit: "",
                cycle_timeout: 0,
                units_per_signal: 0,
                performance_threshold: 0
            }
        }),
        watch: {
            stationId: function(newStationId, oldStationId) { // watch it
                this.stationProduct.station_id = newStationId;
                this.fetchStationProducts();
                // this.fetchAllProducts();
            }
        },
        methods: {
            fetchStationProducts() {
                stationProductService.fetchAll(this.stationId, r => {
                    this.stationProducts = r;
                    console.log(r);
                }, e => {
                    console.log(e);
                });
            },
            closeStationProductModal() {
                this.showStationProductModal = false;
                this.stationProduct.id = null;
                this.showStationProductDeleteModal = false;
                this.clearStationProductData();
            },
            clearStationProductData() {
                this.stationProduct = {
                    id: null,
                    station_id: this.stationId,
                    product_id: null,
                    cycle_time: 0,
                    cycle_unit: "",
                    cycle_timeout: 0,
                    units_per_signal: 0,
                    performance_threshold: 0
                };
            },
            createStationProduct() {
                stationProductService.createOrUpdateStationProduct(this.stationProduct, r => {
                    this.stationProducts = r;
                    this.closeStationProductModal();
                });
            },
            openStationProductCreateModal() {
                this.showStationProductModal = true;
            },
            openStationProductEditForm(stationProduct) {
                this.showStationProductModal = true;
                this.stationProduct = Object.assign({}, stationProduct);
            },
            openStationProductDeleteForm(stationProduct) {
                this.showStationProductDeleteModal = true;
                this.stationProduct = Object.assign({}, stationProduct);
            },
            deleteStationProduct() {
                stationProductService.deleteStationProduct(this.stationProduct.id, r => {
                    this.stationProducts = r;
                    this.closeStationProductModal();
                }, e => {
                    console.log(e);
                })
            },
            fetchAllProducts() {
                productService.fetchAll({}, r => {
                    this.products = r;
                }, e => {
                    console.log(e);
                });
            }
        },
        computed:{
          showStationProductsTable(){
              return this.stationProducts.length > 0;
          }
        },
        mounted() {
            this.fetchStationProducts();
            this.fetchAllProducts();
            this.stationProduct.station_id = this.stationId;
        }
    }
</script>

<style scoped lang="scss">
    .section-header {
        padding: 0.5rem 1rem;
        background: #232156;

        display: flex;
        flex-direction: row;
        justify-content: flex-start;
        align-items: center;

        > span {
            flex-grow: 1;
        }

        .action {
            background: #0c4da1;
            border: None;
            border-radius: 50%;

            padding: 0;
            width: 1.5rem;
            height: 1.5rem;

            font-size: 1.25rem;
            line-height: 1.5rem;

            box-shadow: 1px 1px 1px 1px #0c4da1;
        }
    }

    .settings-table {
        thead {
            background: #4e4d78;
            color: black;
            opacity: .7;
            th {
                padding: 0.25rem;
                font-weight: bold;
                border: 1px solid #0B312A;
            }
        }

        tbody {
            td {
                background: transparent;
                border: 1px solid #0B312A;
                color: black;
            }
        }
    }
</style>

