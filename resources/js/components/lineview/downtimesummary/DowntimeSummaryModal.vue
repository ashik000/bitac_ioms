<template xmlns:v-slot="http://www.w3.org/1999/XSL/Transform">
    <modal @close="$emit('close')">
        <template v-slot:header>
            <div class="container" style="width: 960px; ">
                <div class="row" style="margin-left: 0!important; margin-bottom: 10px;">
                    <h5>Downtime Summary</h5>
                </div>
            </div>
        </template>
        <template v-slot:content>
            <div class="container" style="width: 960px; ">
                <div class="row" style="margin-left: 0!important; margin-bottom: 10px; cursor: pointer">
                    <downtime-summary-filter class="col-sm-3" type="uncommented" :count="uncommentedDowntimeCount" :totalHour="uncommentedDowntimeDurationInSeconds" @downtimeFilterSelected="filterChanged"></downtime-summary-filter>
                    <downtime-summary-filter class="col-sm-3" type="planned" :count="plannedDowntimeCount" :totalHour="plannedDowntimeDurationInSeconds" @downtimeFilterSelected="filterChanged"></downtime-summary-filter>
                    <downtime-summary-filter class="col-sm-3" type="unplanned" :count="unplannedDowntimeCount" :totalHour="unplannedDowntimeDurationInSeconds" @downtimeFilterSelected="filterChanged"></downtime-summary-filter>
                    <downtime-summary-filter class="col-sm-3" type="all" :count="allDowntimeCount" :totalHour="allDowntimeDurationInSeconds" @downtimeFilterSelected="filterChanged"></downtime-summary-filter>
                </div>
                <div style="overflow-y: scroll; height: 400px;">
                    <ul class="list-group">
                        <downtime-summary-list-item
                            v-for="downtime in downtimeSummary"
                            :key="downtime.id"
                            :downtime="downtime"
                            :reasonGroups="reasonGroups"
                            :allReasons="allReasons"
                            @downtimeReasonAssigned="reasonAssigned"
                            v-if="!downtime.filteredOut">
                        </downtime-summary-list-item>
                    </ul>
                </div>
            </div>
        </template>
        <template v-slot:footer>
            <button class="btn btn-outline-danger" @click.prevent="$emit('close')">Close</button>
            <button class="btn btn-success ms-2" @click="assignDowntimeReason();" >Assign</button>
        </template>

    </modal>
</template>

<script>
    import Modal from "../../Modal";
    import DowntimeSummaryService from "../../../services/DowntimeSummaryService";
    import DowntimeReasonService from "../../../services/DowntimeReasons";
    import ToastrService from "../../../services/ToastrService";
    import DowntimeSummaryFilter from "./DowntimeSummaryFilter";
    import DowntimeSummaryListItem from "./DowntimeSummaryListItem";

    export default {
        name: "DowntimeSummaryModal",
        data: () => {
            return {
                downtimeSummary: [],
                reasonGroups: [],
                allReasons: [],
                isAllSelected: true,
                isUncommentedSelected: false,
                isPlannedSelected: false,
                isUnplannedSelected: false,
                allDowntimeCount: 0,
                uncommentedDowntimeCount: 0,
                plannedDowntimeCount: 0,
                unplannedDowntimeCount: 0,
                allDowntimeDurationInSeconds: 0,
                uncommentedDowntimeDurationInSeconds: 0,
                plannedDowntimeDurationInSeconds: 0,
                unplannedDowntimeDurationInSeconds: 0,
                downtimeId: null,
                downtimeReasonId: null,
                eventData: null,
            }
        },
        components: {
            modal: Modal,
            DowntimeSummaryFilter,
            DowntimeSummaryListItem
        },
        methods:{
            reasonAssigned(eventData){
                this.eventData = eventData;
                // console.log(eventData);
                this.downtimeId = eventData.downtime.id;
                this.downtimeReasonId = eventData.downtimeReason ? eventData.downtimeReason.id : null;
            },
            assignDowntimeReason(){
                DowntimeReasonService.assignDowntime({
                    downtimeId: this.downtimeId,
                    downtimeReasonId: this.downtimeReasonId,
                },()=>{
                    ToastrService.showSuccessToast('Downtime reason successfully assigned.');
                    // console.log("downtime reason successfully assigned.");
                    for(let i =0; i< this.downtimeSummary.length;i++){
                        if(this.downtimeSummary[i].id === this.eventData.downtime.id){
                            this.downtimeSummary[i].reason = this.eventData.downtimeReason;
                            break;
                        }
                    }
                    this.processDowntimeSummaryData();
                });
            },
            processDowntimeSummaryData(){
                this.allDowntimeCount = 0;
                this.uncommentedDowntimeCount=0;
                this.plannedDowntimeCount=0;
                this.unplannedDowntimeCount=0;
                this.allDowntimeDurationInSeconds=0;
                this.uncommentedDowntimeDurationInSeconds=0;
                this.plannedDowntimeDurationInSeconds=0;
                this.unplannedDowntimeDurationInSeconds=0;

                for(let i =0; i < this.downtimeSummary.length; i++){
                    if(!this.downtimeSummary[i].reason) {
                        this.$set(this.downtimeSummary[i], 'selectedReasonGroupName', "Please Select");
                        this.$set(this.downtimeSummary[i], 'selectedReasonName', "Please Select");
                        this.uncommentedDowntimeCount++;
                        this.uncommentedDowntimeDurationInSeconds += this.downtimeSummary[i].duration;
                    } else {
                        this.$set(this.downtimeSummary[i], 'selectedReasonGroupName', this.downtimeSummary[i].reason.reason_group.name);
                        this.$set(this.downtimeSummary[i], 'selectedReasonName', this.downtimeSummary[i].reason.name);
                        if(this.downtimeSummary[i].reason.type === 'planned'){
                            this.plannedDowntimeCount++;
                            this.plannedDowntimeDurationInSeconds += this.downtimeSummary[i].duration;
                        } else if(this.downtimeSummary[i].reason.type === 'unplanned'){
                            this.unplannedDowntimeCount++;
                            this.unplannedDowntimeDurationInSeconds += this.downtimeSummary[i].duration;
                        } else if(this.downtimeSummary[i].reason.type === 'uncommented'){  
                            this.uncommentedDowntimeCount++;
                            this.uncommentedDowntimeDurationInSeconds += this.downtimeSummary[i].duration;
                        }
                    }
                }
                this.allDowntimeCount = parseInt(this.uncommentedDowntimeCount)+parseInt(this.plannedDowntimeCount)+parseInt(this.unplannedDowntimeCount);
                this.allDowntimeDurationInSeconds = parseInt(this.uncommentedDowntimeDurationInSeconds)+parseInt(this.plannedDowntimeDurationInSeconds)+parseInt(this.unplannedDowntimeDurationInSeconds);
                console.log('all downtime')
                console.log(this.allDowntimeCount);
                console.log(this.allDowntimeDurationInSeconds);
            },
            filterChanged(event){
                if(event.type==='all') this.isAllSelected = event.isSelected;
                if(event.type==='uncommented') this.isUncommentedSelected = event.isSelected;
                if(event.type==='planned') this.isPlannedSelected = event.isSelected;
                if(event.type==='unplanned') this.isUnplannedSelected = event.isSelected;

                if(!this.isUncommentedSelected && !this.isPlannedSelected && !this.isUnplannedSelected){
                    for(let i =0; i<this.downtimeSummary.length;i++){
                        // this.$set(this.downtimeSummary[i], 'filteredOut', this.isAllSelected);
                        this.$set(this.downtimeSummary[i], 'filteredOut', false);
                    }
                } else {
                    for(let i =0; i<this.downtimeSummary.length;i++){
                        if(this.downtimeSummary[i].reason){
                            if(this.downtimeSummary[i].reason.type==='unplanned'){
                                this.$set(this.downtimeSummary[i], 'filteredOut', !this.isUnplannedSelected);
                            } else if(this.downtimeSummary[i].reason.type==='planned'){
                                this.$set(this.downtimeSummary[i], 'filteredOut', !this.isPlannedSelected);
                            } else if (this.downtimeSummary[i].reason.type==='uncommented'){
                                this.$set(this.downtimeSummary[i], 'filteredOut', !this.isUncommentedSelected);
                            }
                        } else {
                            this.$set(this.downtimeSummary[i], 'filteredOut', !this.isAllSelected);
                            // this.$set(this.downtimeSummary[i], 'filteredOut', !this.isUncommentedSelected);
                        }
                    }
                }
            }
        },
        mounted: function () {
            const vm = this;
            DowntimeReasonService.fetchAllGroups(function (resData) {
                vm.reasonGroups = resData;
            });
            DowntimeReasonService.fetchAll(null,function (resData) {
                // console.log('all reasons')
                // console.log(JSON.stringify(resData['downtime_reason_list']))
                vm.allReasons = resData['downtime_reason_list'];
            });
            DowntimeSummaryService.getDowntimeSummary(
                moment(vm.date).format('DD-MM-YYYY'),
                vm.stationId,
                function (resData) {
                    vm.downtimeSummary = resData;
                    // console.log('downtime summary')
                    // console.log(resData)
                    vm.processDowntimeSummaryData();
                }
            )
        },
        props: {
            stationId : Number,
            date: Date
        },
    }
</script>

<style scoped>

</style>
