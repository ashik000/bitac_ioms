<template>
    <table class="table">
        <thead>
        <tr>
            <th v-if="!stationProductId">Product</th>
            <th v-if="!stationProductId">Product Group</th>
            <th v-if="!stationProductId">Station</th>
            <th v-if="!stationProductId">Station Group</th>
            <th v-else>Time Duration</th>
            <th>Availability</th>
            <th>Quality</th>
            <th>Performance</th>
            <th>OEE</th>
        </tr>
        </thead>
        <tbody>
        <tr v-for="row in tableData" v-if="tableData && tableData.length >0">
            <td v-if="!stationProductId">{{ row.product_name }}</td>
            <td v-if="!stationProductId">{{ row.product_group_name }}</td>
            <td v-if="!stationProductId">{{ row.station_name }}</td>
            <td v-if="!stationProductId">{{ row.station_group_name }}</td>
            <td v-else>{{ row.time_duration }}</td>
            <td>{{ (row.availability * 100).toFixed(2) }} %</td>
            <td>{{ (row.quality * 100).toFixed(2) }} %</td>
            <td>{{ (row.performance * 100).toFixed(2) }} %</td>
            <td>{{ (row.oee * 100).toFixed(2) }} %</td>
        </tr>
        <tr v-else>
            <td colspan="8" v-if="!stationId" style="text-align: center; color:red;">No Data Found</td>
            <td colspan="7" v-else style="text-align: center; color:red">No Data Found</td>
        </tr>
        </tbody>
    </table>
</template>

<script>
    import reportService from '../../../../services/Reports';

    export default {
        name: "ReportTableOEEByProduct",
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
            type: {
                type: String,
                default: 'totals'
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
            },
            type(nw,old){
                this.fetchData();
            },
        },
        methods:{
            fetchData(){
                let vm = this;
                console.log("calling api getOEETableReportByProduct. stationProductId:  " + vm.stationProductId);
                reportService.getOEETableReportByStationProduct({
                    'stationProductId': vm.stationProductId ? vm.stationProductId : null,
                    'start' : moment(vm.start).format('DD-MM-YYYY'),
                    'end': moment(vm.end).format('DD-MM-YYYY'),
                    'type': vm.type
                }, function(responseData){
                    console.log("getOEETableReportByProduct API response: " + JSON.stringify(responseData));
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

<style scoped>
    table {
        border: 1px solid black !important;
    }

    thead {
        background-color: #0f0f0f;
        color: white;
        border: 1px solid black !important;
    }

    th, td, tr {
        border: 1px solid black !important;
    }

    tbody {
        color: #dddddd
    }
</style>
