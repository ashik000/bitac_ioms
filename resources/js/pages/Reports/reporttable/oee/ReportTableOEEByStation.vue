<template>
    <table class="table">
        <thead>
        <tr>
            <th v-if="!stationId">Station</th>
            <th v-if="!stationId">Station Group</th>
            <th v-else>Time Duration</th>
            <th>Availability</th>
            <th>Quality</th>
            <th>Performance</th>
            <th>OEE</th>
        </tr>
        </thead>
        <tbody>
        <tr v-for="row in tableData" v-if="tableData && tableData.length >0">
            <td v-if="!stationId">{{ row.station_name }}</td>
            <td v-if="!stationId">{{ row.station_group_name }}</td>
            <td v-else>{{ row.time_duration }}</td>
            <td>{{ (row.availability * 100).toFixed(2) }} %</td>
            <td>{{ (row.quality * 100).toFixed(2) }} %</td>
            <td>{{ (row.performance * 100).toFixed(2) }} %</td>
            <td>{{ (row.oee * 100).toFixed(2) }} %</td>
        </tr>
        <tr v-else>
            <td colspan="6" v-if="!stationId">No Data Found</td>
            <td colspan="5" v-else>No Data Found</td>
        </tr>
        </tbody>
    </table>
</template>

<script>
    import reportService from '../../../../services/Reports';

    export default {
        name: "ReportTableOEEByStation",
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
            },
            type: {
                type: String,
                default: 'totals'
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
            },
            type(nw,old){
                this.fetchData();
            },
        },
        methods:{
            fetchData(){
                let vm = this;
                console.log("calling api getOEETableReportByStation. stationId:  " + vm.stationId);
                reportService.getOEETableReportByStation({
                    'stationId': vm.stationId ? vm.stationId : null,
                    'start' : moment(vm.start).format('DD-MM-YYYY'),
                    'end': moment(vm.end).format('DD-MM-YYYY'),
                    'type': vm.type
                }, function(responseData){
                    console.log("getOEETableReportByStation API response: " + JSON.stringify(responseData));
                    vm.tableData = responseData;
                });
            }
        },
        mounted(){
            const vm = this;
            vm.fetchData();
        }
    }
</script>
