<template>
    <div>
        <header class="section-header">

            <button class="btn btn btn-primary btn-sm btn-assign-item" @click="openCreateStationShiftModal()">
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
                        <label class="mt-2">Days</label>
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
                {name: "SUN", value: 0},
                {name: "MON", value: 1},
                {name: "TUE", value: 2},
                {name: "WED", value: 3},
                {name: "THU", value: 4},
                {name: "FRI", value: 5},
                {name: "SAT", value: 6},
            ]
        }),
        methods:{

            toggleSelectedShiftDays(dayIndex) {
                let val = this.selectedShiftDays[dayIndex] === '1' ? '0' : '1';
                Vue.set(this.selectedShiftDays, dayIndex, val);
            },

            openCreateStationShiftModal() {
                this.stationShiftEditMode = false;
                for(var i = 0; i < 7; i++) Vue.set(this.selectedShiftDays, i, '0');
                this.showStationShiftForm = true;
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

<style scoped>
.toast-title{font-weight:700}.toast-message{-ms-word-wrap:break-word;word-wrap:break-word}.toast-message a,.toast-message label{color:#FFF}.toast-message a:hover{color:#CCC;text-decoration:none}.toast-close-button{position:relative;right:-.3em;top:-.3em;float:right;font-size:20px;font-weight:700;color:#FFF;-webkit-text-shadow:0 1px 0 #fff;text-shadow:0 1px 0 #fff;opacity:.8;-ms-filter:progid:DXImageTransform.Microsoft.Alpha(Opacity=80);filter:alpha(opacity=80);line-height:1}.toast-close-button:focus,.toast-close-button:hover{color:#000;text-decoration:none;cursor:pointer;opacity:.4;-ms-filter:progid:DXImageTransform.Microsoft.Alpha(Opacity=40);filter:alpha(opacity=40)}.rtl .toast-close-button{left:-.3em;float:left;right:.3em}button.toast-close-button{padding:0;cursor:pointer;background:0 0;border:0;-webkit-appearance:none}.toast-top-center{top:0;right:0;width:100%}.toast-bottom-center{bottom:0;right:0;width:100%}.toast-top-full-width{top:0;right:0;width:100%}.toast-bottom-full-width{bottom:0;right:0;width:100%}.toast-top-left{top:12px;left:12px}.toast-top-right{top:12px;right:12px}.toast-bottom-right{right:12px;bottom:12px}.toast-bottom-left{bottom:12px;left:12px}#toast-container{position:fixed;z-index:999999;pointer-events:none}#toast-container *{-moz-box-sizing:border-box;-webkit-box-sizing:border-box;box-sizing:border-box}#toast-container>div{position:relative;pointer-events:auto;overflow:hidden;margin:0 0 6px;padding:15px 15px 15px 50px;width:300px;-moz-border-radius:3px;-webkit-border-radius:3px;border-radius:3px;background-position:15px center;background-repeat:no-repeat;-moz-box-shadow:0 0 12px #999;-webkit-box-shadow:0 0 12px #999;box-shadow:0 0 12px #999;color:#FFF;opacity:.8;-ms-filter:progid:DXImageTransform.Microsoft.Alpha(Opacity=80);filter:alpha(opacity=80)}#toast-container>div.rtl{direction:rtl;padding:15px 50px 15px 15px;background-position:right 15px center}#toast-container>div:hover{-moz-box-shadow:0 0 12px #000;-webkit-box-shadow:0 0 12px #000;box-shadow:0 0 12px #000;opacity:1;-ms-filter:progid:DXImageTransform.Microsoft.Alpha(Opacity=100);filter:alpha(opacity=100);cursor:pointer}#toast-container>.toast-info{background-image:url(data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAABgAAAAYCAYAAADgdz34AAAAAXNSR0IArs4c6QAAAARnQU1BAACxjwv8YQUAAAAJcEhZcwAADsMAAA7DAcdvqGQAAAGwSURBVEhLtZa9SgNBEMc9sUxxRcoUKSzSWIhXpFMhhYWFhaBg4yPYiWCXZxBLERsLRS3EQkEfwCKdjWJAwSKCgoKCcudv4O5YLrt7EzgXhiU3/4+b2ckmwVjJSpKkQ6wAi4gwhT+z3wRBcEz0yjSseUTrcRyfsHsXmD0AmbHOC9Ii8VImnuXBPglHpQ5wwSVM7sNnTG7Za4JwDdCjxyAiH3nyA2mtaTJufiDZ5dCaqlItILh1NHatfN5skvjx9Z38m69CgzuXmZgVrPIGE763Jx9qKsRozWYw6xOHdER+nn2KkO+Bb+UV5CBN6WC6QtBgbRVozrahAbmm6HtUsgtPC19tFdxXZYBOfkbmFJ1VaHA1VAHjd0pp70oTZzvR+EVrx2Ygfdsq6eu55BHYR8hlcki+n+kERUFG8BrA0BwjeAv2M8WLQBtcy+SD6fNsmnB3AlBLrgTtVW1c2QN4bVWLATaIS60J2Du5y1TiJgjSBvFVZgTmwCU+dAZFoPxGEEs8nyHC9Bwe2GvEJv2WXZb0vjdyFT4Cxk3e/kIqlOGoVLwwPevpYHT+00T+hWwXDf4AJAOUqWcDhbwAAAAASUVORK5CYII=)!important}#toast-container>.toast-error{background-image:url(data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAABgAAAAYCAYAAADgdz34AAAAAXNSR0IArs4c6QAAAARnQU1BAACxjwv8YQUAAAAJcEhZcwAADsMAAA7DAcdvqGQAAAHOSURBVEhLrZa/SgNBEMZzh0WKCClSCKaIYOED+AAKeQQLG8HWztLCImBrYadgIdY+gIKNYkBFSwu7CAoqCgkkoGBI/E28PdbLZmeDLgzZzcx83/zZ2SSXC1j9fr+I1Hq93g2yxH4iwM1vkoBWAdxCmpzTxfkN2RcyZNaHFIkSo10+8kgxkXIURV5HGxTmFuc75B2RfQkpxHG8aAgaAFa0tAHqYFfQ7Iwe2yhODk8+J4C7yAoRTWI3w/4klGRgR4lO7Rpn9+gvMyWp+uxFh8+H+ARlgN1nJuJuQAYvNkEnwGFck18Er4q3egEc/oO+mhLdKgRyhdNFiacC0rlOCbhNVz4H9FnAYgDBvU3QIioZlJFLJtsoHYRDfiZoUyIxqCtRpVlANq0EU4dApjrtgezPFad5S19Wgjkc0hNVnuF4HjVA6C7QrSIbylB+oZe3aHgBsqlNqKYH48jXyJKMuAbiyVJ8KzaB3eRc0pg9VwQ4niFryI68qiOi3AbjwdsfnAtk0bCjTLJKr6mrD9g8iq/S/B81hguOMlQTnVyG40wAcjnmgsCNESDrjme7wfftP4P7SP4N3CJZdvzoNyGq2c/HWOXJGsvVg+RA/k2MC/wN6I2YA2Pt8GkAAAAASUVORK5CYII=)!important}#toast-container>.toast-success{background-image:url(data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAABgAAAAYCAYAAADgdz34AAAAAXNSR0IArs4c6QAAAARnQU1BAACxjwv8YQUAAAAJcEhZcwAADsMAAA7DAcdvqGQAAADsSURBVEhLY2AYBfQMgf///3P8+/evAIgvA/FsIF+BavYDDWMBGroaSMMBiE8VC7AZDrIFaMFnii3AZTjUgsUUWUDA8OdAH6iQbQEhw4HyGsPEcKBXBIC4ARhex4G4BsjmweU1soIFaGg/WtoFZRIZdEvIMhxkCCjXIVsATV6gFGACs4Rsw0EGgIIH3QJYJgHSARQZDrWAB+jawzgs+Q2UO49D7jnRSRGoEFRILcdmEMWGI0cm0JJ2QpYA1RDvcmzJEWhABhD/pqrL0S0CWuABKgnRki9lLseS7g2AlqwHWQSKH4oKLrILpRGhEQCw2LiRUIa4lwAAAABJRU5ErkJggg==)!important}#toast-container>.toast-warning{background-image:url(data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAABgAAAAYCAYAAADgdz34AAAAAXNSR0IArs4c6QAAAARnQU1BAACxjwv8YQUAAAAJcEhZcwAADsMAAA7DAcdvqGQAAAGYSURBVEhL5ZSvTsNQFMbXZGICMYGYmJhAQIJAICYQPAACiSDB8AiICQQJT4CqQEwgJvYASAQCiZiYmJhAIBATCARJy+9rTsldd8sKu1M0+dLb057v6/lbq/2rK0mS/TRNj9cWNAKPYIJII7gIxCcQ51cvqID+GIEX8ASG4B1bK5gIZFeQfoJdEXOfgX4QAQg7kH2A65yQ87lyxb27sggkAzAuFhbbg1K2kgCkB1bVwyIR9m2L7PRPIhDUIXgGtyKw575yz3lTNs6X4JXnjV+LKM/m3MydnTbtOKIjtz6VhCBq4vSm3ncdrD2lk0VgUXSVKjVDJXJzijW1RQdsU7F77He8u68koNZTz8Oz5yGa6J3H3lZ0xYgXBK2QymlWWA+RWnYhskLBv2vmE+hBMCtbA7KX5drWyRT/2JsqZ2IvfB9Y4bWDNMFbJRFmC9E74SoS0CqulwjkC0+5bpcV1CZ8NMej4pjy0U+doDQsGyo1hzVJttIjhQ7GnBtRFN1UarUlH8F3xict+HY07rEzoUGPlWcjRFRr4/gChZgc3ZL2d8oAAAAASUVORK5CYII=)!important}#toast-container.toast-bottom-center>div,#toast-container.toast-top-center>div{width:300px;margin-left:auto;margin-right:auto}#toast-container.toast-bottom-full-width>div,#toast-container.toast-top-full-width>div{width:96%;margin-left:auto;margin-right:auto}.toast{background-color:#030303}.toast-success{background-color:#51A351}.toast-error{background-color:#BD362F}.toast-info{background-color:#2F96B4}.toast-warning{background-color:#F89406}.toast-progress{position:absolute;left:0;bottom:0;height:4px;background-color:#000;opacity:.4;-ms-filter:progid:DXImageTransform.Microsoft.Alpha(Opacity=40);filter:alpha(opacity=40)}@media all and (max-width:240px){#toast-container>div{padding:8px 8px 8px 50px;width:11em}#toast-container>div.rtl{padding:8px 50px 8px 8px}#toast-container .toast-close-button{right:-.2em;top:-.2em}#toast-container .rtl .toast-close-button{left:-.2em;right:.2em}}@media all and (min-width:241px) and (max-width:480px){#toast-container>div{padding:8px 8px 8px 50px;width:18em}#toast-container>div.rtl{padding:8px 50px 8px 8px}#toast-container .toast-close-button{right:-.2em;top:-.2em}#toast-container .rtl .toast-close-button{left:-.2em;right:.2em}}@media all and (min-width:481px) and (max-width:768px){#toast-container>div{padding:15px 15px 15px 50px;width:25em}#toast-container>div.rtl{padding:15px 50px 15px 15px}}
</style>
