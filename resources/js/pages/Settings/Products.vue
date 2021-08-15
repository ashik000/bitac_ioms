<template xmlns:v-slot="http://www.w3.org/1999/XSL/Transform">
    <div>
        <div class="card-wrapper row">
            <div class="col-md-3 col-sm-12 h-100">
                <ProductGroup sectionHeader="Product Groups" :items="groups" @action-clicked="openGroupAddModal" buttonText="Add Product Group">
                    <template v-slot="{ item }">
                        <span class="hide_overflow_text anchor_btn" @click.prevent="loadGroupData(item.id)">
                            {{ item.name }}
                        </span>
                        <span style="float: right;">
                              <button type="button" class="btn btn-primary btn-sm" @click.prevent="showGroupEditModal(item)">
                                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-pencil-square" viewBox="0 0 16 16">
                                  <path d="M15.502 1.94a.5.5 0 0 1 0 .706L14.459 3.69l-2-2L13.502.646a.5.5 0 0 1 .707 0l1.293 1.293zm-1.75 2.456-2-2L4.939 9.21a.5.5 0 0 0-.121.196l-.805 2.414a.25.25 0 0 0 .316.316l2.414-.805a.5.5 0 0 0 .196-.12l6.813-6.814z"></path>
                                  <path fill-rule="evenodd" d="M1 13.5A1.5 1.5 0 0 0 2.5 15h11a1.5 1.5 0 0 0 1.5-1.5v-6a.5.5 0 0 0-1 0v6a.5.5 0 0 1-.5.5h-11a.5.5 0 0 1-.5-.5v-11a.5.5 0 0 1 .5-.5H9a.5.5 0 0 0 0-1H2.5A1.5 1.5 0 0 0 1 2.5v11z"></path>
                                </svg>
                                Edit
                              </button>

                              <button type="button" class="btn btn-danger btn-sm" @click.prevent="showGroupDeleteModal(item)">
                                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-trash" viewBox="0 0 16 16">
                                  <path d="M5.5 5.5A.5.5 0 0 1 6 6v6a.5.5 0 0 1-1 0V6a.5.5 0 0 1 .5-.5zm2.5 0a.5.5 0 0 1 .5.5v6a.5.5 0 0 1-1 0V6a.5.5 0 0 1 .5-.5zm3 .5a.5.5 0 0 0-1 0v6a.5.5 0 0 0 1 0V6z"></path>
                                  <path fill-rule="evenodd" d="M14.5 3a1 1 0 0 1-1 1H13v9a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V4h-.5a1 1 0 0 1-1-1V2a1 1 0 0 1 1-1H6a1 1 0 0 1 1-1h2a1 1 0 0 1 1 1h3.5a1 1 0 0 1 1 1v1zM4.118 4 4 4.059V13a1 1 0 0 0 1 1h6a1 1 0 0 0 1-1V4.059L11.882 4H4.118zM2.5 3V2h11v1h-11z"></path>
                                </svg>
                                Delete
                              </button>
                        </span>
                    </template>
                </ProductGroup>
            </div>
            <section class="section col-md-9 col-sm-12">
                <ProductList :items="products" sectionHeader="Products" @action-clicked="openProductAddModal">
<!--                    <template v-slot:columnHeaders>-->
<!--                    </template>-->
                    <template v-slot:row="{ row }">

                            <div class="d-flex justify-content-between align-items-center">
                                {{ row.name }}
                                <span style="float: right;">
                                    <a class="btn btn-primary anchor_btn">
                                        <i class="material-icons" @click.prevent="showProductEditModal(row)">
                                            edit
                                        </i>
                                    </a>
                                    <a class="btn btn-danger anchor_btn">
                                        <i class="material-icons" @click.prevent="showProductDeleteModal(row)">
                                            delete
                                        </i>
                                    </a>
                                </span>
                            </div>

                    </template>
                </ProductList>
            </section>
        </div>
        <Modal v-if="showGroupDeleteForm" @close="closeGroupForm">
            <template v-slot:header>
                <div class="container">
                    Delete Group
                </div>
            </template>
            <template v-slot:content>
                <form @submit.prevent="deleteGroup">
                    <p>Are you sure you want to delete the group named <span style="color: darkred">{{groupName}}</span>?</p>
                    <button class="btn btn-danger">Submit</button>
                </form>
            </template>
            <template v-slot:footer>
            </template>
        </Modal>

        <Modal v-if="showGroupForm" @close="closeGroupForm">
            <template v-slot:header>
                <div class="container">
                    {{ modalTitleText }} Product Group
                </div>
            </template>
            <template v-slot:content>
                <form @submit.prevent="groupId == null? createGroup() : updateGroup()">
                    <div class="form-group">
                        <label for="name">Name</label>
                        <input type="text" v-model="groupName" class="form-control" id="name" placeholder="Enter Name">
                    </div>
                    <button class="btn btn-primary mt-2" >Submit</button>
                </form>
            </template>
        </Modal>

        <Modal v-if="showProductForm" @close="closeProductModal()">
            <template v-slot:header>
                <div class="container">
                    {{productId == null ? "Add Product" : "Edit Product"}}
                </div>
            </template>
            <template v-slot:content>
                <form @submit.prevent="productId == null ? createProduct():updateProduct()">
                    <div class="form-group">
                        <label>Name</label>
                        <input type="text" v-model="productName" class="form-control" placeholder="Enter Name">
                        <label class="mt-2">Group</label>
                        <select class="form-control" v-model="selectedGroupId">
                            <option v-for="group in groups" :value="group.id">
                                {{ group.name }}
                            </option>
                        </select>
                        <label class="mt-2">Code</label>
                        <input type="text" v-model="productCode" class="form-control" placeholder="Enter Code">
                        <label class="mt-2">Unit</label>
                        <input type="text" v-model="productUnit" class="form-control" placeholder="Enter Unit">
                    </div>
                    <button class="btn btn-primary mt-2" >Submit</button>
                </form>
            </template>
            <template v-slot:footer>
            </template>
        </Modal>

        <Modal v-if="showProductDeleteForm" @close="closeProductModal">
            <template v-slot:header>
                <div class="container">
                    Delete Product
                </div>
            </template>
            <template v-slot:content>
                <form @submit.prevent="deleteProduct">
                    <p>Are you sure you want to delete the product named <span style="color: darkred">{{productName}}</span>?</p>
                    <button class="btn btn-danger">Submit</button>
                </form>
            </template>
            <template v-slot:footer>
            </template>
        </Modal>
    </div>
</template>

<script>
    import ProductGroup from "../../components/settings/ProductGroup";
    import ProductList from "../../components/settings/ProductList";
    import productService from '../../services/Products'
    // import stationService from '../../services/StationsService';
    import groupMixin from '../../mixins/groupMixin';
    export default {
        name: "Products",
        components: {ProductGroup, ProductList},
        mixins:[groupMixin],
        data: () => ({
            showProductForm: false,
            showProductDeleteForm : false,
            productName:"",
            productUnit : "",
            selectedGroupId :null,
            productCode : "",
            groups: [],
            products: [],
            stations:[],
            productId: null,
            modalTitleText: 'Add'
        }),
        methods: {
            deleteProduct(){
                productService.deleteProduct(this.productId, r=>{
                    this.products = r;
                    this.closeProductModal();
                });
            },
            showProductDeleteModal(item){
                this.productId = item.id;
                this.productName = item.name;
                this.showProductDeleteForm = true;
                this.selectedGroupId = item.product_group_id;
                this.productUnit = item.unit;
                this.productCode = item.code;
            },
            showProductEditModal(item){
                this.modalTitleText = 'Edit';
                this.productId = item.id;
                this.productName = item.name;
                this.showProductForm = true;
                this.selectedGroupId = item.product_group_id;
                this.productUnit = item.unit;
                this.productCode = item.code;
            },
            closeProductModal(){
                this.productId = null;
                this.showProductForm = false;
                this.showProductDeleteForm = false;
                this.selectedGroupId = null;
                this.productUnit = "";
                this.productName = "";
                this.productCode = "";
                this.modalTitleText = 'Add';
            },
            deleteGroup(){
                productService.deleteGroup(this.groupId, r =>{
                    this.groups = r;
                    this.closeGroupForm();
                }, e =>{
                    console.log(e);
                })
            },
            updateGroup(){
                productService.updateGroup(this.groupId,{name:this.groupName}, r=>{
                    this.groups = r;
                    this.closeGroupForm();
                }, e => {
                    console.log(e);
                });
            },
            createGroup() {
                productService.addGroup({name: this.groupName}, data => {
                    console.log(data);
                    this.groups = data
                });
                this.clearProductGroup();
                this.closeGroupForm();
            },
            openProductAddModal() {
                this.showProductForm = true;
            },
            updateProduct(){
                productService.updateProduct(this.productId,{
                    name:this.productName,
                    product_group_id: this.selectedGroupId,
                    code : this.productCode,
                    unit : this.productUnit
                }, data => {
                    this.products = data;
                    this.closeProductModal();
                });
            },
            createProduct(){
                productService.addProduct({
                    name:this.productName,
                    product_group_id: this.selectedGroupId,
                    code : this.productCode,
                    unit : this.productUnit
                }, data => {
                    this.products = data;
                    this.showProductForm = false;
                });
                productService.fetchAllProductsByGroupId(this.selectedGroupId, products => {
                    this.products = products;
                });
                this.clearProducts();
            },
            clearProducts(){
                this.productName = "";
                this.productUnit = "";
                this.selectedGroupId = null;
                this.productCode = "";
            },
            clearProductGroup(){
                this.groupName = "";
            },
            loadGroupData(groupId){
                // console.log(groupId);
                productService.fetchAllProductsByGroupId(groupId, products => {
                    this.products = products;
                });
            },

        },
        computed:{
          // showStationProductList(){
          //     return
          //   }
        },
        mounted() {
            productService.fetchAllGroups(groups => {
                this.groups = groups;
                productService.fetchAllProductsByGroupId(this.groups[0].id, products => {
                    // console.log(products);
                    this.products = products;
                });
            });
            // productService.fetchAll([], products => {
            //     this.products = products;
            // });
            // stationService.fetchAll(stations=>{
            //     this.stations = stations;
            // });
        }
    }
</script>
