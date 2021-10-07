<template>
    <div>
        <header class="section-header">
            <button class="btn btn-primary btn-sm btn-assign-item" @click="openStationProductCreateModal()">
                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-plus-lg" viewBox="0 0 16 16">
                    <path d="M8 0a1 1 0 0 1 1 1v6h6a1 1 0 1 1 0 2H9v6a1 1 0 1 1-2 0V9H1a1 1 0 0 1 0-2h6V1a1 1 0 0 1 1-1z"></path>
                </svg>
                Assign Product
            </button>
        </header>
        <div>
            <b-overlay :show="showInprogress" opacity="0.6" no-wrap></b-overlay>
            <table class="table table-bordered settings-station-table" v-if="showStationProductsTable">
                <thead>
                    <tr>
                        <th>Name</th>
                        <th>Cycle Time</th>
                        <th>Unit</th>
                        <th>Timeout</th>
                        <th>Units/Signal</th>
                        <th>Threshold</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                <tr v-for="stationProduct in stationProducts" :key="stationProduct.id">
                    <td>{{stationProduct.product.name}}</td>
                    <td>{{stationProduct.cycle_time}}</td>
                    <td>{{stationProduct.cycle_unit}}</td>
                    <td>{{stationProduct.cycle_timeout}}</td>
                    <td>{{stationProduct.units_per_signal}}</td>
                    <td>{{stationProduct.performance_threshold}}</td>
                    <td>
                        <div class="d-flex station-actions-btn-group">
                            <a class="btn btn-primary btn-sm" @click.prevent="openStationProductEditForm(stationProduct)">
                                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-pencil-square" viewBox="0 0 16 16">
                                    <path d="M15.502 1.94a.5.5 0 0 1 0 .706L14.459 3.69l-2-2L13.502.646a.5.5 0 0 1 .707 0l1.293 1.293zm-1.75 2.456-2-2L4.939 9.21a.5.5 0 0 0-.121.196l-.805 2.414a.25.25 0 0 0 .316.316l2.414-.805a.5.5 0 0 0 .196-.12l6.813-6.814z"></path>
                                    <path fill-rule="evenodd" d="M1 13.5A1.5 1.5 0 0 0 2.5 15h11a1.5 1.5 0 0 0 1.5-1.5v-6a.5.5 0 0 0-1 0v6a.5.5 0 0 1-.5.5h-11a.5.5 0 0 1-.5-.5v-11a.5.5 0 0 1 .5-.5H9a.5.5 0 0 0 0-1H2.5A1.5 1.5 0 0 0 1 2.5v11z"></path>
                                </svg>
                                    EDIT
                            </a>
                            <a class="btn btn-danger btn-sm" @click.prevent="openStationProductDeleteForm(stationProduct)">
                                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-trash" viewBox="0 0 16 16">
                                    <path d="M5.5 5.5A.5.5 0 0 1 6 6v6a.5.5 0 0 1-1 0V6a.5.5 0 0 1 .5-.5zm2.5 0a.5.5 0 0 1 .5.5v6a.5.5 0 0 1-1 0V6a.5.5 0 0 1 .5-.5zm3 .5a.5.5 0 0 0-1 0v6a.5.5 0 0 0 1 0V6z"></path>
                                    <path fill-rule="evenodd" d="M14.5 3a1 1 0 0 1-1 1H13v9a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V4h-.5a1 1 0 0 1-1-1V2a1 1 0 0 1 1-1H6a1 1 0 0 1 1-1h2a1 1 0 0 1 1 1h3.5a1 1 0 0 1 1 1v1zM4.118 4 4 4.059V13a1 1 0 0 0 1 1h6a1 1 0 0 0 1-1V4.059L11.882 4H4.118zM2.5 3V2h11v1h-11z"></path>
                                </svg>
                                    REMOVE
                            </a>
                        </div>
                    </td>
                </tr>
                </tbody>
            </table>
        </div>
        <Modal v-if="showStationProductModal == true" @close="closeStationProductModal()">
            <template v-slot:header>
                <div>
                    <h5>{{stationProduct.id == null ? 'ASSIGN STATION PRODUCT' : 'EDIT STATION PRODUCT'}}</h5>
                </div>
            </template>
            <template v-slot:content>
                <form>
                    <div class="form-group">
                        <label>Product</label>
                        <select v-if="stationProduct.id == null" class="form-select"
                            v-model="stationProduct.product_id">
                            <option disabled value=""> --Select-- </option>
                            <option v-for="product in products" :value="product.id" :key="product.id">
                                {{ product.name }}
                            </option>
                        </select>
                        <label class="mt-2">Cycle Time</label>
                        <input type="number" v-model="stationProduct.cycle_time" class="form-control"
                            placeholder="Enter Cycle Time">
                        <label class="mt-2">Cycle Unit</label>
                        <select class="form-select" v-model="stationProduct.cycle_unit">
                            <option disabled value=""> --Select-- </option>
                            <option value="SEC_PER_PCS">SEC_PER_PCS</option>
                            <option value="PCS_PER_SEC">PCS_PER_SEC</option>
                            <option value="PCS_PER_MINUTE">PCS_PER_MINUTE</option>
                            <option value="PCS_PER_HOUR">PCS_PER_HOUR</option>
                        </select>
                        <label class="mt-2">Cycle Timeout</label>
                        <input type="number" v-model="stationProduct.cycle_timeout" class="form-control"
                            placeholder="Enter Cycle Timeout">
                        <label class="mt-2">Units Per Signal</label>
                        <input type="number" v-model="stationProduct.units_per_signal" class="form-control"
                            placeholder="Enter Units Per Signal">
                        <label class="mt-2">Performance Threshold</label>
                        <input type="number" v-model="stationProduct.performance_threshold" class="form-control"
                            placeholder="Enter Performance Threshold">
                    </div>
                </form>
            </template>
            <template v-slot:footer>
                <div class="float-end pb-4" style="padding-right: 15px;">
                    <button class="btn btn-outline-danger" @click.prevent="closeStationProductModal()">CLOSE</button>
                    <button class="btn btn-success ms-3" @click.prevent="createStationProduct()">SUBMIT</button>
                </div>
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
                </form>
            </template>
            <template v-slot:footer>
                <div class="float-end pb-4">
                    <button class="btn btn-outline-danger" @click.prevent="closeStationProductModal()">Close</button>
                    <button class="btn btn-danger ms-3" @click="deleteStationProduct">DELETE</button>
                </div>
            </template>
        </Modal>
    </div>
</template>

<script>
    import stationProductService from '../../services/StationProductService';
    import productService from '../../services/Products';
    import toastrService from '../../services/ToastrService';

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
                product_id: "",
                cycle_time: 0,
                cycle_unit: "",
                cycle_timeout: 0,
                units_per_signal: 0,
                performance_threshold: 0
            },
            showInprogress: false,
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
                // preloader
                this.showInprogress = true;
                stationProductService.fetchAll(this.stationId, r => {
                    this.stationProducts = r;
                    console.log(r);
                    this.showInprogress = false;
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
                    toastrService.success('Success');
                }, e => {
                    console.log(e);
                    toastrService.showErrorToast('Product cannot be assigned to station');
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
