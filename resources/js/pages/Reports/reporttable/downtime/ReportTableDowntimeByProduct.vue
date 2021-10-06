<template>
    <table class="table reportTable">
        <thead>
        <tr>
            <th v-if="!stationProductId">Product</th>
            <th v-if="!stationProductId">Product Group</th>
            <th v-if="!stationProductId">Station</th>
            <th v-if="!stationProductId">Station Group</th>
            <th v-if="stationProductId">Downtime Reason</th>
            <th v-if="stationProductId">Downtime Reason Group</th>
            <th v-if="stationProductId">Downtime Type</th>
            <th>Count</th>
            <th>Duration</th>
            <th>Stop %</th>
        </tr>
        </thead>
        <tbody>
        <tr v-for="row in tableData" v-if="showTable">
            <td v-if="!stationProductId">{{ row.product_name }}</td>
            <td v-if="!stationProductId">{{ row.product_group_name }}</td>
            <td v-if="!stationProductId">{{ row.station_name }}</td>
            <td v-if="!stationProductId">{{ row.station_group_name }}</td>
            <td v-if="stationProductId">{{ row.name }}</td>
            <td v-if="stationProductId">{{ row.reason_group_name }}</td>
            <td v-if="stationProductId">{{ row.type }}</td>
            <td>{{ row.count }}</td>
            <td>{{ formatDuration(row.duration) }}</td>
            <td>{{ (row.stop_percent * 100).toFixed(2) }} %</td>
        </tr>
        <tr v-if="!showTable">
            <td colspan="7" v-if="!stationProductId" style="text-align: center; color:red;">No Data Found</td>
            <td colspan="6" v-else style="text-align: center; color:red">No Data Found</td>
        </tr>
        </tbody>
    </table>
</template>

<script>
    import reportService from '../../../../services/Reports';
    import { formatDuration } from '../../../../utils';

    export default {
        name: "ReportTableDowntimeByProduct",
        data: ()=>({
            tableData: []
        }),
        props:{
            stationProductId: {
                default: 0
            },
            start: {
                type: Date
            },
            end: {
                type: Date
            },
            stationId: {
                type: Number,
                default: 0
            }
        },
        watch:{
            stationProductId(nw,old){
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
                console.log("calling getDowntimeTableReportByStationProduct api");
                reportService.getDowntimeTableReportByStationProduct({
                    'stationProductId': vm.stationProductId === '0' ? null : vm.stationProductId,
                    'start' : moment(vm.start).format('DD-MM-YYYY'),
                    'end': moment(vm.end).format('DD-MM-YYYY')
                }, function(responseData){
                    console.log("getDowntimeTableReportByStationProduct API response: " + JSON.stringify(responseData));
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

