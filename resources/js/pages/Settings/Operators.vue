<template>
    <span>
        <div class="card-wrapper row">
            <div class="col-12">
                <OperatorList :items="operators" sectionHeader="Operators" @action-clicked="openOperatorAddModal">
                    <template v-slot:columnHeaders>
                        <tr>
                            <th>Operator Name</th>
                            <th>Operator ID</th>
<!--                            <th>Status</th>-->
                            <th>Actions</th>
                        </tr>
                    </template>
                    <div></div>
                    <template v-slot:row="{ row }">
                        <td>
                            <div>
                                <input v-model="row.first_name" v-bind:type="type" />
                                <input v-model="row.last_name" v-bind:type="type" />
                                <span v-if="hide">{{ row.first_name + " " + row.last_name }}</span>
                            </div>
                        </td>
                        <td>
                            <div>
                                <input v-model="row.code" v-bind:type="type" />
                                <span v-if="hide">{{ row.code }}</span>
                            </div>
                        </td>
                        <td>
                            <div>
                                <a class="btn btn-primary">
                                    <i class="material-icons" style="color: #e6e6e6;" v-bind:data-id="row.id" v-on:click="hide = !hide; selectedOperatorId(row.id);" @click.prevent="enableEdit(row.id, row.first_name, row.last_name, row.code);" >
                                        {{ editBtnText }}
                                    </i>
                                </a>
                                <a class="btn btn-danger">
                                    <i class="material-icons" @click.prevent="showOperatorDeleteModal(row)">
                                        delete
                                    </i>
                                </a>
                            </div>
                        </td>
                    </template>
                </OperatorList>
            </div>
        </div>

        <Modal v-if="showOperatorForm" @close="closeModal">
            <template v-slot:header>
                <div class="container">
                    Add Operator
                </div>
            </template>

            <template v-slot:content>
                <form @submit.prevent="operatorId == null? createOperator():updateOperator()">
                    <div class="form-group">
                        <label>First Name</label>
                        <input type="text" v-model="firstName" class="form-control" placeholder="Enter First Name" />
                    </div>

                    <div class="form-group">
                        <label>Last Name</label>
                        <input type="text" v-model="lastName" class="form-control" placeholder="Enter Last Name" />
                    </div>

                    <div class="form-group">
                        <label>Operator code</label>
                        <input type="text" v-model="operatorCode" class="form-control" placeholder="Enter Code" />
                    </div>

                    <button class="btn btn-primary" >Submit</button>
                </form>

            </template>

        </Modal>

        <Modal v-if="showOperatorDeleteForm" @close="closeModal">
            <template v-slot:header>
                <div class="container">
                    Delete Operator
                </div>
            </template>
            <template v-slot:content>
                <form @submit.prevent="deleteOperator">
                    <p>Are you sure you want to delete the operator named <span style="color: darkred">{{firstName + " " + lastName}}</span>?</p>
                    <button class="btn btn-danger">Submit</button>
                </form>
            </template>
            <template v-slot:footer>
            </template>
        </Modal>
    </span>
</template>

<script>
    import OperatorList from "../../components/settings/OperatorList";
    import operatorsService from '../../services/OperatorsService';
    import groupMixin from '../../mixins/groupMixin';

    export default {
        name: "Operators",
        components: {OperatorList},
        mixins:[groupMixin],
        data: function () {
            return {
                operators: [],
                firstName: null,
                lastName: null,
                operatorId: null,
                operatorCode: null,
                showOperatorForm: false,
                showOperatorDeleteForm: false,
                type: 'hidden',
                editBtnText: "Edit",
                hide: true,
                editState: false,
                selectedOperatorIdCheck: null,
                selectedId: null
            };
        },
        methods: {
            openOperatorAddModal: function () {
                this.showOperatorForm = true;
            },

            showOperatorEditModal: function (item) {
                this.showOperatorForm = true;
                this.operatorId = item.id;
                this.firstName = item.first_name;
                this.lastName = item.last_name;
                this.operatorCode = item.code;
            },

            showOperatorDeleteModal: function (item) {
                this.showOperatorDeleteForm = true;
                this.operatorId = item.id;
                this.firstName = item.first_name;
                this.lastName = item.last_name;
                this.operatorCode = item.code;
            },

            createOperator: function(){
                const formData = {
                    first_name: this.firstName,
                    last_name: this.lastName,
                    code: this.operatorCode
                };

                operatorsService.createOperator(formData, (data) => {
                    this.operators = data;
                    this.closeModal();
                } , (error) => {
                    console.log(error);
                });
            },

            updateOperator: function(){
                const formData = {
                    first_name: this.firstName,
                    last_name: this.lastName,
                    code: this.operatorCode
                };

                operatorsService.updateOperator(this.operatorId, formData, (data) => {
                    this.operators = data;
                    this.closeModal();
                } , (error) => {
                    console.log(error);
                });
            },

            deleteOperator: function(){
                operatorsService.deleteOperator(this.operatorId, (data) => {
                    this.operators = data;
                    this.closeModal();
                }, (error) => {
                    console.log(error);
                });
            },

            resetForm: function () {
                this.operatorId = null;
                this.firstName = null;
                this.lastName = null;
                this.operatorCode = null;
            },

            closeModal: function () {
                this.showOperatorForm = false;
                this.showOperatorDeleteForm = false;
                this.resetForm();
            },

            selectedOperatorId(operatorId) {
                this.selectedOperatorIdCheck = operatorId;
                // console.log(this.selectedOperatorIdCheck);
            },

            // getDataId(e) {
            //     var data_id = e.target.dataset.id;
            //     console.log('data id '+data_id);
            //     // this.row_data_id = data_id;
            // },

            enableEdit(operatorId, firstName, lastName, operatorCode) {
                // console.log(firstName);
                // console.log(lastName);
                // console.log(operatorCode);

                if (this.selectedOperatorIdCheck === operatorId) {

                    this.editBtnText = 'Save';
                    this.type = 'text';

                    const formData = {
                        first_name: firstName,
                        last_name: lastName,
                        code: operatorCode
                    };

                    operatorsService.updateOperator(operatorId, formData, (data) => {
                        this.operators = data;
                        this.editState = true;
                    } , (error) => {
                        console.log(error);
                    });
                }

                // console.log(formData);

                if (this.editState === true) {
                    this.hideInputFields();
                    // this.editState = false;
                }

            },

            hideInputFields() {
                this.editBtnText = 'Edit';
                this.type = 'hidden';
            }
        },

        mounted() {
            operatorsService.fetchAll((data) => {
                this.operators = data;
            }, (error) => {
                console.log(error);
            });
        }
    }
</script>

