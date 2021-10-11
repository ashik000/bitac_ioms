<template>
    <table class="table reportTable">
        <thead>
        <tr>
            <th v-if="stationId == null || stationId == 0">Station</th>
            <th v-if="stationId == null || stationId == 0">Station Group</th>
            <th v-if="stationId">Downtime Reason</th>
            <th v-if="stationId">Downtime Reason Group</th>
            <th v-if="stationId">Downtime Type</th>
            <th>Count</th>
            <th>Duration</th>
            <th>Stop %</th>
        </tr>
        </thead>
        <tbody>
        <tr v-for="row in tableData" v-if="showTable">
            <td v-if="!stationId">{{ row.station_name }}</td>
            <td v-if="!stationId">{{ row.station_group_name }}</td>
            <td v-if="stationId">{{ row.name }}</td>
            <td v-if="stationId">{{ row.reason_group_name }}</td>
            <td v-if="stationId">{{ row.type }}</td>
            <td>{{ row.count }}</td>
            <td>{{ formatDuration(row.duration) }}</td>
            <td>{{ (row.stop_percent * 100).toFixed(2) }} %</td>
        </tr>
        <tr v-if="!showTable">
            <td colspan="5" v-if="!stationId" style="text-align: center; color:red;">No Data Found</td>
            <td colspan="6" v-else style="text-align: center; color:red">No Data Found</td>
        </tr>
        </tbody>
    </table>
</template>

<script>
    import reportService from '../../../../services/Reports';
    import { formatDuration } from '../../../../utils';
    export default {
        name: "ReportTableDowntimeByStation",
        data: ()=>({
            tableData: []
        }),
        props:{
            stationId: {
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
            stationId(nw,old){
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
                reportService.getDowntimeTableReportByStation({
                    'stationId': vm.stationId === '0' ? null : vm.stationId,
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

