<template>
    <div>
        <header class="section-header">
            <span style="color: #ffffff">
                Operators
            </span>
            <button class="btn" style="color: #ffffff" @click="openStationOperatorCreateModal()">
                <i class="icon material-icons" style="font-size:24px; color:white;">add_circle_outline</i>
            </button>
        </header>
        <div>
            <table class="settings-table table small" v-if="showStationOperatorsTable">
                <thead>
                <th>Name</th>
                <th>Start Time</th>
                <th class="text-center">Actions</th>
                </thead>
                <tbody>
                <tr v-for="stationOperator in stationOperators" :key="stationOperator.id">
                    <td>{{stationOperator.operator_name + " " + stationOperator.operator_code}}</td>
                    <td>{{stationOperator.start_time}}</td>
                    <td>
                        <a class="btn btn-link">
                            <i class="material-icons" @click.prevent="openStationOperatorDeleteModal(stationOperator)">
                                delete
                            </i>
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
            color: white;
            opacity: .7;
            th {
                padding: 0.25rem;
                font-weight: bold;
                border: 1px solid #0B312A;
            }
        }

        tbody {
            /*tr {*/
            /*    &:nth-child(even) {*/
            /*        background: #fafafa;*/
            /*    }*/
            /*}*/
            td {
                background: transparent;
                border: 1px solid #0B312A;
                color: #ffffff;
            }
        }
    }
</style>
