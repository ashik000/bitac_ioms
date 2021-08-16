<template>
    <div>
        <header class="section-header">

            <button class="btn btn btn-primary btn-sm btn-assign-item" @click="showStationShiftForm = true">
                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-plus-lg" viewBox="0 0 16 16">
                    <path d="M8 0a1 1 0 0 1 1 1v6h6a1 1 0 1 1 0 2H9v6a1 1 0 1 1-2 0V9H1a1 1 0 0 1 0-2h6V1a1 1 0 0 1 1-1z"></path>
                </svg>
                Assign Shift
            </button>
        </header>
        <div>
            <table class="table table-bordered settings-station-table" v-if="showStationShiftList">
                <thead>
                <tr>
                    <th>Name</th>
                    <th>Schedule</th>
                    <th>Shift Time</th>
                    <th>Actions</th>
                </tr>
                </thead>
                <tbody>
                <tr v-for="stationShift in stationShifts" :key="stationShift.id">
                    <td>{{stationShift.shift.name}}</td>
                    <td class="center-flex">
                        <div :class="day === '1'? 'day-circle-active' : 'day-circle-inactive'" v-for="(day, index) in stationShift.schedule.split('')">{{dayToNameMap[index].name}}</div>
                    </td>
                    <td>{{stationShift.shift.start_time | formatTime}}-{{stationShift.shift.end_time | formatTime}}</td>
                    <td>
                        <div class="d-flex station-actions-btn-group">
                            <a class="btn btn-primary btn-sm"  @click.prevent="openEditStationShiftModal(stationShift)">
                                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-pencil-square" viewBox="0 0 16 16">
                                    <path d="M15.502 1.94a.5.5 0 0 1 0 .706L14.459 3.69l-2-2L13.502.646a.5.5 0 0 1 .707 0l1.293 1.293zm-1.75 2.456-2-2L4.939 9.21a.5.5 0 0 0-.121.196l-.805 2.414a.25.25 0 0 0 .316.316l2.414-.805a.5.5 0 0 0 .196-.12l6.813-6.814z"></path>
                                    <path fill-rule="evenodd" d="M1 13.5A1.5 1.5 0 0 0 2.5 15h11a1.5 1.5 0 0 0 1.5-1.5v-6a.5.5 0 0 0-1 0v6a.5.5 0 0 1-.5.5h-11a.5.5 0 0 1-.5-.5v-11a.5.5 0 0 1 .5-.5H9a.5.5 0 0 0 0-1H2.5A1.5 1.5 0 0 0 1 2.5v11z"></path>
                                </svg>
                                    EDIT
                            </a>
                            <a class="btn btn-danger btn-sm" @click.prevent="openDeleteStationShift(stationShift)">
                                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-trash" viewBox="0 0 16 16">
                                    <path d="M5.5 5.5A.5.5 0 0 1 6 6v6a.5.5 0 0 1-1 0V6a.5.5 0 0 1 .5-.5zm2.5 0a.5.5 0 0 1 .5.5v6a.5.5 0 0 1-1 0V6a.5.5 0 0 1 .5-.5zm3 .5a.5.5 0 0 0-1 0v6a.5.5 0 0 0 1 0V6z"></path>
                                    <path fill-rule="evenodd" d="M14.5 3a1 1 0 0 1-1 1H13v9a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V4h-.5a1 1 0 0 1-1-1V2a1 1 0 0 1 1-1H6a1 1 0 0 1 1-1h2a1 1 0 0 1 1 1h3.5a1 1 0 0 1 1 1v1zM4.118 4 4 4.059V13a1 1 0 0 0 1 1h6a1 1 0 0 0 1-1V4.059L11.882 4H4.118zM2.5 3V2h11v1h-11z"></path>
                                </svg>
                                    REMOVE
                            </a>
                        </div>
                    </td>
                </tr>
                </tbody>
            </table>
        </div>
        <Modal v-if="showStationShiftDeleteModal" @close="closeStationShiftModals">
            <template v-slot:header>
                Delete
            </template>
            <template v-slot:content>
                <form @submit.prevent="">
                    <p>Are you sure you want to delete ?</p>
                    <button class="btn btn-danger" @click="deleteStationShift">Submit</button>
                </form>
            </template>
            <template v-slot:footer>
            </template>
        </Modal>
        <Modal v-if="showStationShiftForm" @close="closeStationShiftModals">
            <template v-slot:header>
                <div class="text-center">
                    <h5>{{!stationShiftEditMode? 'Add Station Shift' : 'Edit Station Shift'}}</h5>
                </div>
            </template>
            <template v-slot:content>
                <form @submit.prevent="">
                    <div class="form-group">
                        <label>Shift</label>
                        <select v-bind:disabled="stationShiftEditMode" class="form-control" v-model="stationShift.shift_id">
                            <option v-for="shift in shifts" :value="shift.id">
                                {{ shift.name }}
                            </option>
                        </select>
                        <label>Days</label>
                        <div class="center-flex">
                            <div @click="toggleSelectedShiftDays(index)" style="cursor: pointer" :class="day === '1'? 'day-circle-active' : 'day-circle-inactive'" v-for="(day, index) in selectedShiftDays">{{dayToNameMap[index].name}}</div>
                        </div>
                    </div>
                </form>
            </template>
            <template v-slot:footer>
                <button class="btn btn-primary" @click="createStationShift">Submit</button>
            </template>
        </Modal>

    </div>
</template>

<script>
    import stationShiftService from '../../services/StationShiftService';
    import shiftService from '../../services/ShiftsService';
    import toastrService from '../../services/ToastrService';
    import Vue from 'vue';

    export default {
        name: "StationShift",
        props: {
            stationId: {
                type: Number,
                required: true
            },
        },
        data : () =>({
            showStationShiftDeleteModal:false,
            showStationShiftModal:false,
            selectedDays:[],
            schedule:[],
            showStationShiftForm:false,
            stationShifts:[],
            shifts:[],
            stationShiftEditMode: false,
            selectedShiftId:null,
            selectedShiftDays: [],
            stationShift:{
              id:null,
              shift_id:null,
              scheduleString:"",
              scheduleDisplayString :"",
            },
            dayToNameMap :[
                {name: "SAT", value: 0},
                {name: "SUN", value: 1},
                {name: "MON", value: 2},
                {name: "TUE", value: 3},
                {name: "WED", value: 4},
                {name: "THU", value: 5},
                {name: "FRI", value: 6},
            ]
        }),
        methods:{

            toggleSelectedShiftDays(dayIndex) {
                let val = this.selectedShiftDays[dayIndex] === '1' ? '0' : '1';
                Vue.set(this.selectedShiftDays, dayIndex, val);
            },

            openEditStationShiftModal(stationShift){
                this.stationShiftEditMode = true;
                this.stationShift = stationShift;
                this.selectedShiftDays = stationShift.schedule.split('');
                this.showStationShiftForm = true;
            },
            createStationShift() {
                let stationShift = {
                    id:this.stationShift.id,
                    station_id:this.stationId,
                    shift_id:this.stationShift.shift_id,
                    schedule:this.getScheduleStringForPost(),
                };
                stationShiftService.createOrUpdateStationShift(stationShift, r=> {
                    this.stationShifts = r;
                    this.stationShiftEditMode? toastrService.showSuccessToast('Shift edit successful') : toastrService.showSuccessToast('Shift creation successful');
                    this.showStationShiftForm = false;
                    this.stationShiftEditMode = false;
                }, e => {
                    toastrService.showErrorToast(e);
                    this.showStationShiftForm = false;
                    this.stationShiftEditMode = false;
                })
            },
            getScheduleStringForPost() {
                return this.selectedShiftDays.join('');
            },
            openDeleteStationShift(stationShift){
                this.showStationShiftDeleteModal = true;
                this.selectedStationShiftId = stationShift.id;
            },
            deleteStationShift(){
                stationShiftService.deleteStationShift(this.selectedStationShiftId, r =>{
                    this.stationShifts = r;
                    this.closeStationShiftModals();
                }, e => {
                    console.log(e);
                });
            },
            closeStationShiftModals(){
                this.showStationShiftForm = false;
                this.stationShiftEditMode = false;
                this.showStationShiftDeleteModal = false;
                this.selectedStationShiftId = null;
                this.selectedShiftDays.fill('0', 0, this.selectedShiftDays.length);
            },
            getAllStationShifts(){
                stationShiftService.fetchAll(this.stationId,r => {
                    this.stationShifts = r;
                    console.log(r);
                },e=>{
                    console.log(e);
                })
            },
            getAllShifts(){
                shiftService.fetchAll(r=>{
                    this.shifts = r;
                });
            },
        },
        watch:{
            stationId: function(newStationId, oldStationId) { // watch it
                this.getAllStationShifts();
                this.getAllShifts();
            }
        },
        computed:{
          showStationShiftList(){
              return this.stationShifts.length > 0;
          }
        },
        mounted() {
            this.getAllStationShifts();
            this.getAllShifts();
        }
    }
</script>

<style src="vue-multiselect/dist/vue-multiselect.min.css"></style>
