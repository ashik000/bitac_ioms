<template>
    <div>
        <h3 class="page-header">Manage Shifts</h3>
        <div class="row">
            <section class="section col-8">
                <SettingsTable :items="shifts" sectionHeader="Shifts" @action-clicked="openShiftAddModal">
                    <template v-slot:columnHeaders>
                        <tr>
                            <th style="width: 40%;">Name</th>
                            <th style="width: 25%;">Start</th>
                            <th style="width: 25%;">End</th>
                            <th style="width: 10%;"></th>
                        </tr>
                    </template>
                    <div></div>
                    <template v-slot:row="{ row }">
                        <td style="width: 40%;">
                            <div class="d-flex justify-content-between align-items-center">
                                {{ row.name }}
                            </div>
                        </td>
                        <td style="width: 25%;">
                            <div class="d-flex justify-content-between align-items-center">
                                {{ row.start_time }}
                            </div>
                        </td>
                        <td style="width: 25%;">
                            <div class="d-flex justify-content-between align-items-center">
                                {{ row.end_time }}
                            </div>
                        </td>
                        <td style="width: 10%;">
                            <div class="d-flex justify-content-between align-items-center">
                                <a class="btn btn-link" style="margin-left: auto">
                                    <i class="material-icons" style="color: #e6e6e6;" @click.prevent="showShiftEditModal(row)">
                                        edit
                                    </i>
                                </a>
                                <a class="btn btn-link">
                                    <i class="material-icons" style="color: #e6e6e6;" @click.prevent="showShiftDeleteModal(row)">
                                        delete
                                    </i>
                                </a>
                            </div>
                        </td>
                    </template>
                </SettingsTable>
            </section>
        </div>

        <Modal v-if="showShiftForm" @close="closeModal">
            <template v-slot:header>
                <div class="container">
                    Add Shift
                </div>
            </template>

            <template v-slot:content>
                <form @submit.prevent="shiftId == null? createShift():updateShift()">
                    <div class="form-group">
                        <label>Name</label>
                        <input type="text" v-model="shiftName" class="form-control" placeholder="Enter Shift Name" />
                    </div>

                    <div class="form-group">
                        <label>Shift Start Time</label>
                        <VueCtkDateTimePicker v-model="shiftStartTime" format="HH:mm" formatted="HH:mm" :only-time="true" :no-label="true" label="Select Start Time" />
                    </div>

                    <div class="form-group">
                        <label>Shift End Time</label>
                        <VueCtkDateTimePicker v-model="shiftEndTime" format="HH:mm" formatted="HH:mm" :only-time="true" :no-label="true" label="Select End Time" />
                    </div>

                    <button class="btn btn-primary" >Submit</button>
                </form>

            </template>

        </Modal>

        <Modal v-if="showShiftDeleteForm" @close="closeModal">
            <template v-slot:header>
                <div class="container">
                    Delete Shift
                </div>
            </template>
            <template v-slot:content>
                <form @submit.prevent="deleteShift">
                    <p>Are you sure you want to delete the shift named <span style="color: darkred">{{shiftName}}</span>?</p>
                    <button class="btn btn-danger">Submit</button>
                </form>
            </template>
            <template v-slot:footer>
            </template>
        </Modal>
    </div>

</template>

<script>
    import shiftsService from '../../services/ShiftsService';
    import groupMixin from '../../mixins/groupMixin';

    export default {
        name: "Shifts",
        mixins:[groupMixin],
        data: function() {
            return {
                showShiftForm: false,
                showShiftDeleteForm: false,
                shiftId: null,
                shiftName: null,
                shiftStartTime: null,
                shiftEndTime: null,
                shifts: []
            };
        },
        methods:{
            openShiftAddModal: function(){
                this.showShiftForm = true;
            },

            showShiftEditModal: function (item) {
                this.showShiftForm = true;
                this.shiftId = item.id;
                this.shiftName = item.name;
                this.shiftStartTime = item.start_time;
                this.shiftEndTime = item.end_time;
            },

            showShiftDeleteModal: function (item) {
                this.showShiftDeleteForm = true;
                this.shiftId = item.id;
                this.shiftName = item.name;
                this.shiftStartTime = item.start_time;
                this.shiftEndTime = item.end_time;
            },

            createShift: function () {
                const formData = {
                    name: this.shiftName,
                    start_time: this.shiftStartTime,
                    end_time: this.shiftEndTime
                };

                shiftsService.createShift(formData, (data) => {
                    this.shifts = data;
                    this.closeModal();
                } , (error) => {
                    console.log(error);
                });
            },

            updateShift: function () {
                const formData = {
                    name: this.shiftName,
                    start_time: this.shiftStartTime,
                    end_time: this.shiftEndTime
                };

                shiftsService.updateShift(this.shiftId, formData, (data) => {
                    this.shifts = data;
                    this.closeModal();
                }, (error) => {
                    console.log(error);
                });
            },

            deleteShift: function () {
                shiftsService.deleteShift(this.shiftId, (data) => {
                    this.shifts = data;
                    this.closeModal();
                }, (error) => {
                    console.log(error);
                });
            },

            resetForm: function () {
                this.shiftId = null;
                this.shiftName = null;
                this.shiftStartTime = null;
                this.shiftEndTime = null;
            },

            closeModal: function () {
                this.showShiftForm = false;
                this.showShiftDeleteForm = false;
                this.resetForm();
            }
        },

        mounted() {
            shiftsService.fetchAll((data) => {
                this.shifts = data;
            }, (error) => {
                console.log(error);
            });
        }
    }
</script>

<style src="vue-ctk-date-time-picker/dist/vue-ctk-date-time-picker.css"></style>

<style scoped lang="scss">

</style>
