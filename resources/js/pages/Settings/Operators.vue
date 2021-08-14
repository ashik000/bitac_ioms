<template>
    <span>
        <div class="card-wrapper row">
            <div class="col-12">
                <OperatorList :items="operators" sectionHeader="Operators" @action-clicked="openOperatorAddModal">
                    <template v-slot:columnHeaders>
                        <tr>
                            <th style="width: 40%;">Operator Name</th>
                            <th style="width: 40%;">Operator ID</th>
<!--                            <th>Status</th>-->
                            <th style="width: 20%;">Actions</th>
                        </tr>
                    </template>
                    <div></div>
                    <template v-slot:row="{ row }">
                        <td>
                            <div>
                                <div v-if="selectedOperatorId === row.id" >
                                    <input v-model="row.first_name" />
                                    <input v-model="row.last_name" />
                                </div>

                                <span v-else>{{ row.first_name + " " + row.last_name }}</span>
                            </div>
                        </td>
                        <td>
                            <div>
                                <input v-if="selectedOperatorId === row.id" v-model="row.code" />
                                <span v-else>{{ row.code }}</span>
                            </div>
                        </td>
                        <td>
                            <div>
                                <a class="btn btn-primary">
                                    <i v-if="selectedOperatorId === row.id" class="material-icons" style="color: #e6e6e6;" @click="selectOperatorId(row.id); updateOperator(row)">
                                        Save
                                    </i>
                                    <i v-else class="material-icons" style="color: #e6e6e6;" @click="selectOperatorId(row.id)"  >
                                        Edit
                                    </i>
                                </a>
                                <a class="btn btn-danger">
                                    <i v-if="selectedOperatorId === row.id" class="material-icons" @click.prevent="cancelEdit()">
                                        Cancel
                                    </i>
                                    <i v-else class="material-icons" @click.prevent="showOperatorDeleteModal(row)">
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

                    <div class="form-group mt-2">
                        <label>Last Name</label>
                        <input type="text" v-model="lastName" class="form-control" placeholder="Enter Last Name" />
                    </div>

                    <div class="form-group mt-2">
                        <label>Operator code</label>
                        <input type="text" v-model="operatorCode" class="form-control" placeholder="Enter Code" />
                    </div>

                    <button class="btn btn-primary mt-2">Submit</button>
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
                hide: true,
                selectedOperatorId: null,
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

            updateOperator: function(operator) {
                const formData = {
                    first_name: operator.first_name,
                    last_name: operator.last_name,
                    code: operator.code
                };

                operatorsService.updateOperator(operator.id, formData, (data) => {
                    this.operators = data;
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

            selectOperatorId: function(operatorId) {
                if(this.selectedOperatorId === operatorId)
                    this.selectedOperatorId = null;
                else this.selectedOperatorId = operatorId;
            },
            cancelEdit: function () {
                this.selectedOperatorId = null;
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

