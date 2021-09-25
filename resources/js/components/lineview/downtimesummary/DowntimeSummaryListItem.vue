<template>
    <li class="list-group-item listitemm" :class="getListItemClassCondition(downtime)">
        <div class="row">
            <div class="col-sm-5">
                <span v-if="downtime.reason">{{downtime.reason.name}}</span>
                <span style="color:red" v-else>Uncommented Downtime</span>
                <pre>Start {{ formatStartTime(downtime.start_time)}} - Duration {{formatDuration(downtime.duration)}} </pre>
            </div>
            <div class="col-sm-3">
                <div class="dropdown">
                    <button class="btn btn-light dropdown-toggle" type="button" id="station-picker" data-bs-toggle="dropdown" aria-expanded="false">
                        {{ downtime.selectedReasonGroupName }}
                    </button>
                    <ul class="dropdown-menu" aria-labelledby="dropdownMenuButton1">
                        <li v-for="reasonGroup in reasonGroups" :key="reasonGroup.id"
                            @click="reasonGroupSelected(downtime,reasonGroup);">
                            <a class="dropdown-item" href="#">{{reasonGroup.name}}</a>
                        </li>
                    </ul>
                </div>
            </div>
            <div class="col-sm-3">
                <div class="dropdown">
                    <button class="btn btn-light dropdown-toggle" type="button" id="station-picker" data-bs-toggle="dropdown" aria-expanded="false">
                        {{ downtime.selectedReasonName }}
                    </button>
                    <div class="dropdown-menu" aria-labelledby="dropdownMenuButton">
                        <a class="dropdown-item" href="#" v-for="reason in filteredReasons" :key="reason.id" @click="reasonSelected(downtime,reason)">{{ reason.name}}</a>
                    </div>
                </div>
            </div>
            <div class="col-sm-1">
                <button class="btn" style="background-color: white;border-color: white;" @click="reasonSelected(downtime,null)">
                    <b-icon icon="trash" class="pb-sm-1" font-scale="1.30"></b-icon>
                </button>
            </div>
        </div>
    </li>
</template>

<script>
    import {formatDuration} from '../../../utils';

    export default {
        name: "DowntimeSummaryListItem.vue",
        data: ()=>{
            return {
                selectedReasonGroupId: null
            }
        },
        computed: {
            filteredReasons: function () { 
                // console.log(this.selectedReasonGroupId);
                var id = this.selectedReasonGroupId;
                return this.allReasons.filter(function(i) {
                    // console.log(i);
                    return id === null || i.downtime_reason_group.id === id;
                });
            }            
        },
        props:{
            downtime : Object,
            reasonGroups: Array,
            allReasons: Array
        },
        methods: {
            getListItemClassCondition (downtime){
                if(!downtime.reason) return 'listitemm-uncommented';
                if(downtime.reason.type==='unplanned') return 'listitemm-unplanned';
                if(downtime.reason.type==='planned') return 'listitemm-planned';
            },
            formatDuration,
            formatStartTime(start_time){
                return moment(start_time).format("hh:mm A");
            },
            reasonGroupSelected(downtime, reasonGroup){
                downtime.selectedReasonGroupName = reasonGroup.name;
                downtime.selectedReasonName = "Please Select";
                this.selectedReasonGroupId = reasonGroup.id;

            },
            reasonSelected(downtime, reason){
                downtime.reason = reason;
                if(reason){
                    downtime.selectedReasonName = reason.name;
                } else {
                    downtime.selectedReasonGroupName = "Please Select";
                    downtime.selectedReasonName = "Please Select";
                }

                // console.log(downtime);
                this.$emit("downtimeReasonAssigned", {
                    downtime: downtime,
                    downtimeReason: reason,
                });
            },
        }
    }
</script>

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
    }

    /*li {*/
    /*    transition: all 300ms ease-in-out;*/
    /*    &:hover {*/
    /*        margin-right:40px;*/
    /*    }*/
    /*}*/
    .listitemm-uncommented {
        border-left: 3px solid yellow;
    }
    .listitemm-unplanned {
        border-left: 3px solid red;
    }
    .listitemm-planned {
        border-left: 3px solid blue;
    }

    .list-item-dropdown {
        color: black;
        background-color: white;
        border-color: white;
        width: 80%;
    }
</style>
