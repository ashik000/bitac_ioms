<template>
    <div>
        <header class="section-header">
            <button class="btn btn btn-primary btn-sm btn-assign-item" @click="openStationOperatorCreateModal()">
                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-plus-lg" viewBox="0 0 16 16">
                    <path d="M8 0a1 1 0 0 1 1 1v6h6a1 1 0 1 1 0 2H9v6a1 1 0 1 1-2 0V9H1a1 1 0 0 1 0-2h6V1a1 1 0 0 1 1-1z"></path>
                </svg>
                Assign Operator
            </button>
        </header>
        <div>
            <table class="table table-bordered settings-station-table" v-if="showStationOperatorsTable">
                <thead>
                    <tr>
                        <th>Name</th>
                        <th>Start Time</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                <tr v-for="stationOperator in stationOperators" :key="stationOperator.id">
                    <td>{{stationOperator.operator_name + " " + stationOperator.operator_code}}</td>
                    <td>{{stationOperator.start_time| formatDateTime}}</td>
                    <td>
                        <a class="btn btn-danger btn-sm" @click.prevent="openStationOperatorDeleteModal(stationOperator)">
                            <b-icon icon="trash" class="pb-sm-1" font-scale="1.30"></b-icon>
                                DELETE
                        </a>
                    </td>
                </tr>
                </tbody>
            </table>
        </div>


        <Modal v-if="showStationOperatorCreateModal === true" @close="closeStationOperatorModal()" >
            <template v-slot:header>
                <h3>Assign Station Operator</h3>
            </template>
            <template v-slot:content>
                <form>
                    <div class="form-group">
                        <label for="operatorSelect">Operator</label>
                        <select required id="operatorSelect" class="form-control"
                                v-model="selectedOperatorId">
                            <option disabled value="">--Select--</option>
                            <option v-for="operator in operators" :value="operator.id">
                                {{ operator.first_name + operator.last_name + " " + operator.code }}
                            </option>
                        </select>
                        <label for="startTimeSelect">Select Start Date and Time</label>
                        <VueCtkDateTimePicker id="startTimeSelect" v-model="stationOperator.start_time" :inline="true" format="YYYY-MM-DD HH:mm:ss" formatted="YYYY-MM-DD HH:mm:ss" />
                    </div>
                    <button style="float: right;" class="btn btn-primary" @click.prevent="createStationOperator()">Submit</button>
                </form>
            </template>
            <template v-slot:footer>
            </template>
        </Modal>

        <Modal v-if="showStationOperatorDeleteModal" @close="closeStationOperatorModal()">
            <template v-slot:header>
                <div class="container">
                    Delete Station Operator
                </div>
            </template>
            <template v-slot:content>
                <form @submit.prevent="">
                    <p>Are you sure you want to delete the operator named <span style="color: darkred">{{selectedStationOperatorNameToDelete}}</span>?</p>
                    <br>
                    <button class="btn btn-danger" @click="deleteStationOperator">Delete</button>
                </form>
            </template>
            <template v-slot:footer>
            </template>
        </Modal>
    </div>
</template>

<script>

    import stationOperatorService from '../../services/StationOperatorService';
    import operatorService from '../../services/OperatorsService';
    import toasterService from '../../services/ToastrService'

    export default {
        name: "StationOperator",
        props: {
            stationId: {
                type: Number,
                required: true
            },
        },
        data: () => ({
            stationOperators: [],
            showStationOperatorDeleteModal: false,
            showStationOperatorCreateModal: false,
            operators: [],
            selectedOperatorId: null,
            selectedStationOperatorIdToDelete: null,
            selectedStationOperatorNameToDelete: null,
            stationOperator: {
                id: null,
                station_id: null,
                operator_id: null,
                start_time : null,
            }
        }),
        watch: {
            stationId: function(newStationId, oldStationId) {
                this.fetchStationOperators();
                this.fetchAllOperators();
            }
        },
        methods: {
            fetchStationOperators() {
                stationOperatorService.fetchAll(this.stationId, r => {
                    this.stationOperators = r;
                    console.log(r);
                }, e => {
                    console.log(e);
                });
            },
            fetchAllOperators() {
                operatorService.fetchAll( r => {
                    this.operators = r;
                    console.log(r);
                }, e => {
                    console.log(e);
                });
            },
            openStationOperatorCreateModal() {
                this.showStationOperatorCreateModal = true;
            },
            closeStationOperatorModal() {
                this.showStationOperatorCreateModal = false;
                this.showStationOperatorDeleteModal = false;
                this.stationOperator.start_time = "";
            },
            createStationOperator() {
                let data = {
                    'station_id': this.stationId,
                    'operator_id': this.selectedOperatorId,
                    'start_time' : this.stationOperator.start_time
                };
                stationOperatorService.createOrUpdateStationOperator(data, r => {
                    this.stationOperators = r;
                    this.closeStationOperatorModal();
                    toasterService.showSuccessToast('Added station operator.');
                }, error => {
                    toasterService.showErrorToast("Validation failed");
                });
            },
            openStationOperatorDeleteModal(stationOperator) {
                this.selectedStationOperatorIdToDelete = stationOperator.id;
                this.selectedStationOperatorNameToDelete = stationOperator.operator_name;
                this.showStationOperatorDeleteModal = true;
            },
            deleteStationOperator() {
                stationOperatorService.deleteStationOperator(this.selectedStationOperatorIdToDelete, r => {
                    this.stationOperators = r;
                    this.closeStationOperatorModal();
                    toasterService.showSuccessToast('Deleted station operator.')
                }, e => {
                    console.log(e);
                });
                this.fetchStationOperators();
            }
        },
        computed:{
            showStationOperatorsTable() {
                return this.stationOperators.length > 0;
            }
        },
        mounted() {
            this.fetchStationOperators();
            this.fetchAllOperators();
        }
    }
</script>

<style src="vue-ctk-date-time-picker/dist/vue-ctk-date-time-picker.css"></style>
