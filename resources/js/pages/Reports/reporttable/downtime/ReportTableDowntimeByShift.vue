<template>
    <table class="table reportTable">
        <thead>
        <tr>
            <th v-if="!stationShiftId">Shift</th>
            <th v-if="!stationShiftId">Station</th>
            <th v-if="!stationShiftId">Station Group</th>
            <th v-if="stationShiftId">Downtime Reason</th>
            <th v-if="stationShiftId">Downtime Reason Group</th>
            <th v-if="stationShiftId">Downtime Type</th>
            <th>Count</th>
            <th>Duration</th>
            <th>Stop %</th>
        </tr>
        </thead>
        <tbody>
            <tr v-for="row in tableData" v-if="showTable">
                <td v-if="!stationShiftId">{{ row.shift_name }}</td>
                <td v-if="!stationShiftId">{{ row.station_name }}</td>
                <td v-if="!stationShiftId">{{ row.station_group_name }}</td>
                <td v-if="stationShiftId">{{ row.name }}</td>
                <td v-if="stationShiftId">{{ row.reason_group_name }}</td>
                <td v-if="stationShiftId">{{ row.type }}</td>
                <td>{{ row.count }}</td>
                <td>{{ formatDuration(row.duration) }}</td>
                <td>{{ (row.stop_percent * 100).toFixed(2) }} %</td>
            </tr>
            <tr v-if="!showTable">
                <td colspan="6" style="text-align: center; color:red">No Data Found</td>
            </tr>
        </tbody>
    </table>
</template>

<script>
    import reportService from '../../../../services/Reports';
    import { formatDuration } from '../../../../utils';

    export default {
        name: "ReportTableDowntimeByShift",
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
            }
        },
        methods:{
            fetchData(){
                let vm = this;
                reportService.getDowntimeTableReportByStationShift({
                    'stationShiftId': vm.stationShiftId === '0' ? null : vm.stationShiftId,
                    'start' : moment(vm.start).format('DD-MM-YYYY'),
                    'end': moment(vm.end).format('DD-MM-YYYY')
                }, function(responseData){
                    vm.tableData = responseData;
                });
            },
            formatDuration
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
