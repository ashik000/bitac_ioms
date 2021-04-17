<template xmlns:v-slot="http://www.w3.org/1999/XSL/Transform">
    <div>
        <h3 class="page-header">Manage Products</h3>
        <div class="row">
            <aside class="section col-4">
                <SettingsList sectionHeader="Product Groups"  :items="groups" @action-clicked="openGroupAddModal">
                    <template v-slot="{ item }">
                        <div class="d-flex justify-content-between align-items-center">
                            {{ item.name }}
                            <a class="btn btn-link" style="margin-left: auto">
                                <i class="material-icons" style="color: #e6e6e6;" @click.prevent="showGroupEditModal(item)">
                                    edit
                                </i>
                            </a>
                            <a class="btn btn-link">
                                <i class="material-icons" style="color: #e6e6e6;" @click.prevent="showGroupDeleteModal(item)">
                                    delete
                                </i>
                            </a>
                        </div>
                    </template>
                </SettingsList>
            </aside>
            <section class="section col-8">
                <SettingsTable :items="products" sectionHeader="Products" @action-clicked="openProductAddModal">
                    <template v-slot:columnHeaders>
                    </template>
                    <div></div>
                    <template v-slot:row="{ row }">
                        <td>
                            <div class="d-flex justify-content-between align-items-center">
                                {{ row.name }}
                                <a class="btn btn-link" style="margin-left: auto">
                                    <i class="material-icons" style="color: #e6e6e6;" @click.prevent="showProductEditModal(row)">
                                        edit
                                    </i>
                                </a>
                                <a class="btn btn-link">
                                    <i class="material-icons" style="color: #e6e6e6;" @click.prevent="showProductDeleteModal(row)">
                                        delete
                                    </i>
                                </a>
                            </div>
                        </td>
                    </template>
                </SettingsTable>
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
                    <button class="btn btn-danger" >Submit</button>
                </form>
            </template>
            <template v-slot:footer>
            </template>
        </Modal>

        <Modal v-if="showGroupForm" @close="closeGroupForm">
            <template v-slot:header>
                <div class="container">
                    Add Product Group
                </div>
            </template>
            <template v-slot:content>
                <form @submit.prevent="groupId == null? createGroup() : updateGroup()">
                    <div class="form-group">
                        <label for="name">Name</label>
                        <input type="text" v-model="groupName" class="form-control" id="name" placeholder="Enter Name">
                    </div>
                    <button class="btn btn-primary" >Submit</button>
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
                        <label>Group</label>
                        <select class="form-control" v-model="selectedGroupId">
                            <option v-for="group in groups" :value="group.id">
                                {{ group.name }}
                            </option>
                        </select>
                        <label>Code</label>
                        <input type="text" v-model="productCode" class="form-control" placeholder="Enter Code">
                        <label>Unit</label>
                        <input type="text" v-model="productUnit" class="form-control" placeholder="Enter Unit">
                    </div>
                    <button class="btn btn-primary" >Submit</button>
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
    import productService from '../../services/Products'
    import stationService from '../../services/StationsService';
    import groupMixin from '../../mixins/groupMixin';
    export default {
        name: "Products",
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
            }

        },
        computed:{
          showStationProductList(){
              return
            }
        },
        mounted() {
            productService.fetchAllGroups(groups => {
                this.groups = groups;

            });
            productService.fetchAll([], products => {
                this.products = products;
            });
            stationService.fetchAll(stations=>{
                this.stations = stations;
            });
        }
    }
</script>

<style scoped lang="scss">

</style>
