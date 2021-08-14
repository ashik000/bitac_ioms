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
                    <td>{{stationOperator.start_time}}</td>
                    <td>
                        <a class="btn btn-danger btn-sm" @click.prevent="openStationOperatorDeleteModal(stationOperator)">
                            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-trash" viewBox="0 0 16 16">
                                <path d="M5.5 5.5A.5.5 0 0 1 6 6v6a.5.5 0 0 1-1 0V6a.5.5 0 0 1 .5-.5zm2.5 0a.5.5 0 0 1 .5.5v6a.5.5 0 0 1-1 0V6a.5.5 0 0 1 .5-.5zm3 .5a.5.5 0 0 0-1 0v6a.5.5 0 0 0 1 0V6z"></path>
                                <path fill-rule="evenodd" d="M14.5 3a1 1 0 0 1-1 1H13v9a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V4h-.5a1 1 0 0 1-1-1V2a1 1 0 0 1 1-1H6a1 1 0 0 1 1-1h2a1 1 0 0 1 1 1h3.5a1 1 0 0 1 1 1v1zM4.118 4 4 4.059V13a1 1 0 0 0 1 1h6a1 1 0 0 0 1-1V4.059L11.882 4H4.118zM2.5 3V2h11v1h-11z"></path>
                            </svg>
                                REMOVE
                        </a>
                    </td>
                </tr>
                </tbody>
            </table>
        </div>


        <Modal v-if="showStationOperatorCreateModal === true" @close="closeStationOperatorModal()" >
            <template v-slot:header>
                <h3>Add Station Operator</h3>
            </template>
            <template v-slot:content>
                <form>
                    <div class="form-group">
                        <label for="operatorSelect">Operator</label>
                        <select id="operatorSelect" class="form-control"
                                v-model="selectedOperatorId">
                            <option v-for="operator in operators" :value="operator.id">
                                {{ operator.first_name + operator.last_name + " " + operator.code }}
                            </option>
                        </select>
                        <label for="startTimeSelect">Select Start Time</label>
                        <VueCtkDateTimePicker id="startTimeSelect" v-model="stationOperator.start_time" :inline="true" format="YYYY-MM-DD hh:mm:ss" formatted="YYYY-MM-DD hh:mm:ss" />
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
                    <p>Are you sure you want to delete ?</p>
                    <button class="btn btn-danger" @click="deleteStationOperator">Submit</button>
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
                });
            },
            openStationOperatorDeleteModal(stationOperator) {
                this.selectedStationOperatorIdToDelete = stationOperator.id;
                this.showStationOperatorDeleteModal = true;
            },
            deleteStationOperator() {
                stationOperatorService.deleteStationOperator(this.selectedStationOperatorIdToDelete, r => {
                    this.stationOperators = r;
                    this.closeStationOperatorModal();
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
