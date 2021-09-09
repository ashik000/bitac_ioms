<template>
    <li class="list-group-item listitemm">
        <div class="row">
            <div class="col-sm-5">
                <span>{{ operator.first_name + " " + operator.last_name }}</span>
                <pre>{{ operator.code }}</pre>
            </div>

            <div class="col-sm-3" v-if="operator.stations && operator.stations.length > 0">
                <span v-for="(operatorStation, index) in operator.stations" :key="operatorStation.id">
                    <span v-if="index < operator.stations.length -1">{{ operatorStation.name + ", " }}</span>
                    <span v-else>{{ operatorStation.name }}</span>
                </span>
            </div>
            <div class="col-sm-3" v-else>
                <span>None</span>
            </div>

            <div class="col-sm-4">
                <div class="form-check">
                    <input class="form-check-input" type="radio" name="flexRadioDefault" id="flexRadioDefault1" :value="operator.id" @change="onOperatorChange($event)" :checked="operator.id == checkedVal">
                </div>
            </div>

            <!-- <div class="col-sm-4">
                <template v-if="alreadyAssigned">
                    <div class="d-flex flex-direction-row justify-content-end">
                        <button class="btn" style="background-color: white;border-color: white;" @click="removeOperator(operator.stations)">
                            Delete
                        </button>
                    </div>
                </template>
                <template v-else>
                    <div class="d-flex flex-row flex-nowrap justify-content-between">
                        <VueCtkDateTimePicker id="startTimeSelect" :no-label="true" :right="true" v-model="operator.startTime" :inline="false" format="YYYY-MM-DD hh:mm:ss" formatted="YYYY-MM-DD hh:mm:ss" />
                        <button class="btn py-0 px-1 border-white bg-white" @click="assignOperator(operator.id)">
                            Add
                        </button>
                    </div>
                </template>
            </div> -->
        </div>
    </li>

</template>

<script>
    import stationOperatorService from "../../../services/StationOperatorService"

    export default {
        name: "OperatorSelectionListItem",

        data: function () {
            return {
                assignedStationIds: [],
                checkedVal: null,
                selectedOperatorId: null
            }
        },

        methods: {
            onOperatorChange: function (event) {
                var data = event.target.value;
                console.log('on operator change triggered')

                this.selectedOperatorId = data;

                console.log(this.selectedOperatorId);
                
                this.$emit('update-operator', this.selectedOperatorId);

                // this.assignOperator(this.selectedOperatorId);
            },

            // assignOperator: function (operatorId) {
            //     stationOperatorService.assignOperator
            // },

            // stationOperatorService.assignOperator({
            //     operator_id: this.selectedOperatorId,
            //     station_id: stationId,
            // }, data => {
            //     console.log('success')
            //     ToastrService.showSuccessToast('Operator assigned to station successfully');
            // }, error => {
            //     console.log(error)
            // });

            getAssignedStationIdsFromObject: function () {
                this.operator.stations.forEach((value, index) => {
                    this.assignedStationIds.push(value.id);
                });
            },

            removeOperator: function (operatorStations) {
                operatorStations.forEach((value, index) => {
                    if(value.id === this.stationId){
                        stationOperatorService.deleteStationOperator(value.meta.id, (data) => {
                            this.operator.stations.splice(index, 1);
                        }, (error) => {
                            console.log(error);
                        });
                        return;
                    }
                });
            },

            assignOperator: function (operatorId) {
                const vm = this;
                const request = {
                    station_id: this.stationId,
                    operator_id: operatorId,
                    start_time : this.operator.startTime
                };

                stationOperatorService.createOrUpdateStationOperator(request, function(data){
                    let stationOperator = data.filter(function(d){
                        return d.operator_id === vm.operator.id && d.station_id === vm.stationId;
                    });

                    let stationOperatorMeta = {
                        'id': stationOperator[0].id,
                        'operator_id': stationOperator[0].operator_id,
                        'station_id': stationOperator[0].station_id,
                        'start_time': stationOperator[0].start_time,
                        'end_time': stationOperator[0].end_time
                    }

                    let station = {
                        'id': vm.stationId,
                        'name': vm.stationName,
                        'meta': stationOperatorMeta
                    };
                    if(vm.operator.stations && vm.operator.stations instanceof Array){
                        vm.operator.stations.push(station);
                    } else {
                        vm.operator.stations = [];
                        vm.operator.stations.push(station);
                    }
                }, (error) => {
                    console.log(error);
                });
            }
        },

        props: {
            operator: Object,
            stationId: Number,
            stationName: String,
            operatorId: Number,
        },
        mounted() {
            console.log('test operator mount')
            console.log(this.operator);

            let vm = this;
            vm.checkedVal = vm.operatorId;     // to set the default value of the radio button means set the operatorId

            // console.log('check operatorId')
            // console.log(vm.operatorId);

            // let filteredStations = this.operator.stations.filter(function(opst){
            //     return opst.meta.station_id == vm.stationId;
            // });
            // if(filteredStations && filteredStations.length > 0){
            //     this.$set(this.operator, 'startTime' , filteredStations[0].meta.start_time);
            // }

        },
        computed:{
            alreadyAssigned: function (){
                return this.assignedStationIds.indexOf(this.stationId) != -1;
            },
            assignedStationIds(){
                let arr = [];
                this.operator.stations.forEach((value, index) => {
                    arr.push(value.id);
                });
                return arr;
            }
        },
        // updated() {
        // }
    }
</script>

<style src="vue-ctk-date-time-picker/dist/vue-ctk-date-time-picker.css"></style>

<style scoped lang="scss">
    .listitemm {
        margin-bottom: 15px;
        /*-webkit-box-shadow: 1px 2px 5px -1px rgba(0, 0, 0, 0.5);
        -moz-box-shadow: 1px 2px 5px -1px rgba(0, 0, 0, 0.5);
        box-shadow: 1px 2px 5px -1px rgba(0, 0, 0, 0.5);*/
    }

    li:hover {
        /*margin-right:40px;*/
        background: #f5f5f5;
        border-left: 3px solid #337ab7;
    }

    /*li {*/
    /*    transition: all 300ms ease-in-out;*/
    /*    &:hover {*/
    /*margin-right:40px;*/
    /*}*/
    /*}*/
</style>
