<template xmlns:v-slot="http://www.w3.org/1999/XSL/Transform">
    <div>
        <div class="card-wrapper row">
            <div class="col-md-3 col-sm-12 h-100">
                <ProductGroup sectionHeader="Product Groups" :items="groups" @action-clicked="openGroupAddModal" buttonText="Add Product Group">
                    <template v-slot="{ item }">
                        <span class="hide_overflow_text" @click.prevent="loadGroupData(item.id)">
                            {{ item.name }}
                        </span>
                        <span style="float: right;">
                            <button type="button" class="btn btn-primary btn-sm" @click.prevent="showGroupEditModal(item)">
                                <b-icon icon="pencil-square" class="pb-sm-1" font-scale="1.30"></b-icon> EDIT
                            </button>
                            <button type="button" class="btn btn-danger btn-sm" @click.prevent="showGroupDeleteModal(item)">
                                <b-icon icon="trash" class="pb-sm-1" font-scale="1.30"></b-icon> DELETE
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
                                <button type="button" class="btn btn-primary btn-sm" @click.prevent="showProductEditModal(row)">
                                    <b-icon icon="pencil-square" class="pb-sm-1" font-scale="1.30"></b-icon> EDIT
                                </button>
                                <button type="button" class="btn btn-danger btn-sm" @click.prevent="showProductDeleteModal(row)">
                                    <b-icon icon="trash" class="pb-sm-1" font-scale="1.30"></b-icon> DELETE
                                </button>
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
    import productService from '../../services/Products';
    import toastrService from '../../services/ToastrService';
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
                    toastrService.showSuccessToast('Product deleted.');
                    this.closeProductModal();
                }, error => {
                    toastrService.showErrorToast(error);
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
                    toastrService.showSuccessToast('Product group deleted.');
                    this.closeGroupForm();
                }, error =>{
                    toastrService.showErrorToast(error);
                    // console.log(error);
                })
            },
            updateGroup(){
                productService.updateGroup(this.groupId,{name:this.groupName}, r=>{
                    this.groups = r;
                    toastrService.showSuccessToast('Product group updated.');
                    this.closeGroupForm();
                }, error => {
                    console.log(error);
                    toastrService.showErrorToast(error);
                });
            },
            createGroup() {
                productService.addGroup({name: this.groupName}, data => {
                    this.groups = data;
                    toastrService.showSuccessToast('Product group added.');
                    this.clearProductGroup();
                    this.closeGroupForm();
                }, error => {
                    toastrService.showErrorToast(error);
                });
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
                    toastrService.showSuccessToast('Product updated.');
                    this.closeProductModal();
                }, error => {
                    toastrService.showErrorToast(error);
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
                    toastrService.showSuccessToast('Product added.');
                }, error => {
                    toastrService.showErrorToast(error);
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
