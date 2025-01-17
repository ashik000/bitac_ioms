<template>
    <span>
        <div class="card-wrapper row">
            <b-overlay :show="showUpdateInprogress" opacity="0.6">
                <div class="col-12 h-100">
                    <ShiftList :items="shifts" sectionHeader="Shifts" @action-clicked="openShiftAddModal">
                        <template v-slot:columnHeaders>
                            <tr>
                                <th class="text-left" width="41%">Name</th>
                                <th class="text-center" width="23%">Start</th>
                                <th class="text-center" width="23%">End</th>
                                <th class="text-center" width="15%">Actions</th>
                            </tr>
                        </template>

                        <template v-slot:row="{ row }">
                            <td class="text-left">
                                <div v-if="selectedShiftId === row.id" >
                                    <input class="form-control" v-model="row.name" />
                                </div>
                                <span v-else>{{ row.name }}</span>
                            </td>
                            <td class="text-center">
                                <div v-if="selectedShiftId === row.id" >
                                    <VueCtkDateTimePicker input-size="sm" v-model="row.start_time" format="HH:mm" formatted="HH:mm" :only-time="true" :no-label="true" label="Select Start Time" />
                                </div>
                                <span v-else>{{ row.start_time }}</span>
                            </td>
                            <td class="text-center">
                                <div v-if="selectedShiftId === row.id" >
                                    <VueCtkDateTimePicker input-size="sm" v-model="row.end_time" format="HH:mm" formatted="HH:mm" :only-time="true" :no-label="true" label="Select End Time" />
                                </div>
                                <span v-else>{{ row.end_time }}</span>
                            </td>
                            <td class="text-center">
                                <button v-if="selectedShiftId === row.id" type="button" class="btn btn-success btn-sm" @click.prevent="selectShiftId(row.id); updateShift(row);">
                                    <b-icon icon="cloud-arrow-up" class="pb-sm-1" font-scale="1.30"></b-icon> SAVE
                                </button>
                                <button v-else type="button" class="btn btn-primary btn-sm" @click.prevent="selectShiftId(row.id);">
                                    <b-icon icon="pencil-square" class="pb-sm-1" font-scale="1.30"></b-icon> EDIT
                                </button>
                                <button v-if="selectedShiftId === row.id" type="button" class="btn btn-danger btn-sm" @click.prevent="cancelUpdateShift(row.id)">
                                    <b-icon icon="x-circle-fill" class="pb-sm-1" font-scale="1.30"></b-icon> CANCEL
                                </button>
                                <button v-else type="button" class="btn btn-danger btn-sm" @click.prevent="showShiftDeleteModal(row)">
                                    <b-icon icon="trash" class="pb-sm-1" font-scale="1.30"></b-icon> DELETE
                                </button>
                            </td>
                        </template>
                    </ShiftList>
                </div>
            </b-overlay>
        </div>

        <Modal v-if="showShiftForm" @close="closeModal">
            <template v-slot:header>
                <h5>
                    Add Shift
                </h5>
            </template>
            <template v-slot:content>
                <form>
                    <div class="form-group">
                        <label>Name</label>
                        <input type="text" v-model="shiftName" class="form-control" placeholder="Enter Shift Name" />
                    </div>
                    <div class="form-group mt-3">
                        <label>Shift Start Time</label>
                        <VueCtkDateTimePicker v-model="shiftStartTime" format="HH:mm" formatted="HH:mm" :only-time="true" :no-label="true" label="Select Start Time" />
                    </div>
                    <div class="form-group mt-3">
                        <label>Shift End Time</label>
                        <VueCtkDateTimePicker v-model="shiftEndTime" format="HH:mm" formatted="HH:mm" :only-time="true" :no-label="true" label="Select End Time" />
                    </div>
                </form>
                <b-overlay :show="showAddInprogress" opacity="0.6" no-wrap></b-overlay>
            </template>
            <template v-slot:footer>
                <div class="float-end pb-3" style="padding-right: 15px;">
                    <button class="btn btn-outline-danger" @click.prevent="closeModal()">CLOSE</button>
                    <button class="btn btn-success ms-3" @click="shiftId == null ? createShift() : closeModal()">SUBMIT</button>
                </div>
            </template>
        </Modal>

        <Modal v-if="showShiftDeleteForm" @close="closeModal">
            <template v-slot:header>
                <h5>
                    Delete Shift
                </h5>
            </template>
            <template v-slot:content>
                <form @submit.prevent="deleteShift">
                    <p>Are you sure you want to delete the shift named <span style="color: darkred">{{shiftName}}</span>?</p>
                    <button class="btn btn-danger float-end">SUBMIT</button>
                </form>
            </template>
            <template v-slot:footer>
            </template>
        </Modal>
    </span>

</template>

<script>
    import ShiftList from "../../components/settings/ShiftList";
    import shiftsService from '../../services/ShiftsService';
    import groupMixin from '../../mixins/groupMixin';
    import toastrService from '../../services/ToastrService';

    export default {
        name: "Shifts",
        components: {ShiftList},
        mixins:[groupMixin],
        data: function() {
            return {
                showShiftForm: false,
                showShiftDeleteForm: false,
                shiftId: null,
                shiftName: null,
                shiftStartTime: null,
                shiftEndTime: null,
                shifts: [],
                hide: true,
                selectedShiftId: null,
                selectedId: null,
                showAddInprogress: false,
                showUpdateInprogress: false
            };
        },
        methods:{
            openShiftAddModal: function(){
                this.showShiftForm = true;
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
                this.showAddInprogress = true;
                shiftsService.createShift(formData, (data) => {
                    this.shifts = data;
                    this.closeModal();
                    this.showAddInprogress = false;
                    toastrService.showSuccessToast('Shift created.');
                } , (error) => {
                    this.showAddInprogress = false;
                    toastrService.showErrorToast(error);
                });
            },

            updateShift: function (shift) {
                const formData = {
                    name: shift.name,
                    start_time: shift.start_time,
                    end_time: shift.end_time
                };
                this.showUpdateInprogress = true;
                shiftsService.updateShift(shift.id, formData, (data) => {
                    this.shifts = data;
                    this.closeModal();
                    this.showUpdateInprogress = false;
                    toastrService.showSuccessToast('Shift updated.');
                }, (error) => {
                    this.showUpdateInprogress = false;
                    toastrService.showErrorToast(error);
                });
            },

            deleteShift: function () {
                shiftsService.deleteShift(this.shiftId, (data) => {
                    this.shifts = data;
                    this.closeModal();
                }, (error) => {
                    this.closeModal();
                    toastrService.showErrorToast(error);
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
            },

            selectShiftId: function(shiftId) {
                if(this.selectedShiftId === shiftId)
                    this.selectedShiftId = null;
                else this.selectedShiftId = shiftId;
            },
            cancelUpdateShift: function (){
                this.selectedShiftId = null;
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
