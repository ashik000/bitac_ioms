<template>
    <table class="table reportTable">
        <thead>
        <tr>
            <th v-if="!stationShiftId">Shift</th>
            <th v-if="!stationShiftId">Station</th>
            <th v-if="!stationShiftId">Station Group</th>
            <th v-else>Time Duration</th>
            <th>Availability</th>
            <th>Quality</th>
            <th>Performance</th>
            <th>OEE</th>
        </tr>
        </thead>
        <tbody>
        <tr v-for="row in tableData" v-if="showTable">
            <td v-if="!stationShiftId">{{ row.shift_name }}</td>
            <td v-if="!stationShiftId">{{ row.station_name }}</td>
            <td v-if="!stationShiftId">{{ row.station_group_name }}</td>
            <td v-else>{{ row.time_duration }}</td>
            <td>{{ (row.availability * 100).toFixed(2) }} %</td>
            <td>{{ (row.quality * 100).toFixed(2) }} %</td>
            <td>{{ (row.performance * 100).toFixed(2) }} %</td>
            <td>{{ (row.oee * 100).toFixed(2) }} %</td>
        </tr>
        <tr v-if="!showTable">
            <td colspan="7" v-if="!stationShiftId" style="text-align: center; color:red;">No Data Found</td>
            <td colspan="6" v-else style="text-align: center; color:red">No Data Found</td>
        </tr>
        </tbody>
    </table>
</template>

<script>
    import reportService from '../../../../services/Reports';

    export default {
        name: "ReportTableOEEByShift",
        data: ()=>({
            tableData: []
        }),
        props:{
            stationShiftId: {
                default: 0
            },
            start: {
                type: Date
            },
            end: {
                type: Date
            },
            type: {
                type: String,
                default: 'totals'
            }
        },
        watch:{
            stationShiftId(nw,old){
                this.fetchData();
            },
            start(nw,old){
                this.fetchData();
            },
            end(nw,old){
                this.fetchData();
            },
            type(nw,old){
                this.fetchData();
            },
        },
        methods:{
            fetchData(){
                let vm = this;
                console.log("calling api getOEETableReportByShift. stationShiftId:  " + vm.stationShiftId);
                reportService.getOEETableReportByStationShift({
                    'stationShiftId': vm.stationShiftId === '0' ? null : vm.stationShiftId,
                    'start' : moment(vm.start).format('DD-MM-YYYY'),
                    'end': moment(vm.end).format('DD-MM-YYYY'),
                    'type': vm.type
                }, function(responseData){
                    console.log("getOEETableReportByShift API response: " + JSON.stringify(responseData));
                    vm.tableData = responseData;
                });
            }
        },
        mounted(){
            const vm = this;
            vm.fetchData();
        },
        computed:{
            showTable(){
                return this.tableData && this.tableData.length > 0;
            }
        }
    }
</script>
