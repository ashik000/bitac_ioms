<template>
    <div>
        <header class="section-header">
            <span style="color: #ffffff">
                Shifts
            </span>
            <button class="btn" style="color: #ffffff" @click="showStationShiftForm = true">
                <i class="icon material-icons" style="font-size:24px; color:white;">add_circle_outline</i>
            </button>
        </header>
        <div>
            <table class="settings-table table small" v-if="showStationShiftList">
                <thead>
                <tr>
                    <th>Name</th>
                    <th>Schedule</th>
                    <th class="text-center">Actions</th>
                </tr>
                </thead>
                <tbody>
                <tr v-for="stationShift in stationShifts" :key="stationShift.id">
                    <td>{{stationShift.shift.name}}</td>
                    <td style="text-overflow: ellipsis;">{{getScheduleString(stationShift.schedule)}}</td>
                    <td>
                        <div class="d-inline-flex">
                            <a class="btn btn-link">
                                <i class="material-icons" @click.prevent="openEditStationShiftModal(stationShift)">
                                    edit
                                </i>
                            </a>
                            <a class="btn btn-link">
                                <i class="material-icons" @click.prevent="openDeleteStationShift(stationShift)">
                                    delete
                                </i>
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
                    <h5>{{stationShift.id == null? 'Add Station Shift' : 'Edit Station Shift'}}</h5>
                </div>
            </template>
            <template v-slot:content>
                <form @submit.prevent="">
                    <div class="form-group">
                        <label>Shift</label>
                        <select class="form-control" v-model="stationShift.shift_id">
                            <option v-for="shift in shifts" :value="shift.id">
                                {{ shift.name }}
                            </option>
                        </select>
                        <label>Days</label>
                        <div style="height: 300px">
                        <multiselect v-model="selectedDays"
                                     :options="dayToNameMap"
                                     :multiple="true"
                                     :close-on-select="false"
                                     :clear-on-select="false"
                                     :preserve-search="true"
                                     open-direction="bottom"
                                     placeholder="Pick some"
                                     label="name"
                                     track-by="value"
                                     :preselect-first="true">

                        </multiselect>
                        </div>
                    </div>
                </form>
            </template>
            <template v-slot:footer>
                <button class="btn btn-primary" @click="createStationShift">Submit</button>
            </template>
        </Modal>

<!--        <pre class="language-json"><code>{{selectedDays}}</code></pre>-->
    </div>
</template>

<script>
    import stationShiftService from '../../services/StationShiftService';
    import shiftService from '../../services/ShiftsService';

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
            selectedShiftId:null,
            stationShift:{
              id:null,
              shift_id:null,
              scheduleString:"",
              scheduleDisplayString :"",
            },
            dayToNameMap :[
                {name: "Saturday", value: 0},
                {name: "Sunday", value: 1},
                {name: "Monday", value: 2},
                {name: "Tuesday", value: 3},
                {name: "Wednesday", value: 4},
                {name: "Thursday", value: 5},
                {name: "Friday", value: 6},
            ]
        }),
        methods:{
            openEditStationShiftModal(stationShift){
                this.stationShift = stationShift;
                this.stationShift.scheduleDisplayString = this.getScheduleString(stationShift.schedule);
                this.fillSelectedDays(stationShift.schedule);
                this.showStationShiftForm = true;
            },
            createStationShift(){
              let stationShift = {
                  id:this.stationShift.id,
                  station_id:this.stationId,
                  shift_id:this.stationShift.shift_id,
                  schedule:this.getScheduleStringForPost(),
              };
            stationShiftService.createOrUpdateStationShift(stationShift, r=> {
                this.stationShifts = r;
            }, e => {
                console.log(e);
            })
          },
            getScheduleStringForPost(){
                let scheduleString="";
                let selectedDays = this.selectedDays.map((object)=> object.value);
                for(let i =0; i < 7;i++ ){
                    if(selectedDays.includes(i)){
                        scheduleString+="1";
                    }else {
                        scheduleString+="0";
                    }
                }
                return scheduleString;
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
                this.showStationShiftDeleteModal = false;
                this.selectedStationShiftId = null;
                this.selectedDays = [];
            },
            getAllStationShifts(){
                stationShiftService.fetchAll(this.stationId,r => {
                    this.stationShifts = r;
                },e=>{
                    console.log(e);
                })
            },
            getAllShifts(){
                shiftService.fetchAll(r=>{
                    this.shifts = r;
                });
            },
            fillSelectedDays(scheduleString) {
                let selectedDays = [];
                for(let i = 0; i< scheduleString.length ; i++){
                    let character = scheduleString.charAt(i);
                    if(character ==1){
                        for(let j=0;j<this.dayToNameMap.length; j++){
                            if(this.dayToNameMap[j].value == i){
                                selectedDays.push(this.dayToNameMap[j]);
                            }
                        }
                    }
                }
                this.selectedDays = selectedDays;
            },
            getScheduleString(scheduleString){
                let formattedScheduleString = "";
                for(let i = 0 ; i < scheduleString.length;i++){
                   let character = scheduleString.charAt(i);
                   if(character == 1){
                       for(let j = 0 ; j < this.dayToNameMap.length ; j++){
                           if(this.dayToNameMap[j].value == i){
                               formattedScheduleString+= " "+this.dayToNameMap[j].name.substr(0,3);
                           }
                       }
                   }
                }
                console.log(formattedScheduleString);
                return formattedScheduleString;
            }
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
                font-weight: normal;
                border: 1px solid #0B312A;
            }
        }

        tbody {
            td {
                background: transparent;
                border: 1px solid #0B312A;
                color: #ffffff;
            }
        }
    }
</style>
