<template>
  <div>
    <button class="btn btn-primary mb-4" @click="downloadReport">Download Excel</button>
    <table class="table reportTable">
      <thead>
      <tr>
        <th v-if="!stationTeamId">Team</th>
        <th v-if="!stationTeamId">Station</th>
        <th v-if="!stationTeamId">Station Group</th>
        <th v-else>Time Duration</th>
        <th>Availability</th>
        <th>Quality</th>
        <th>Performance</th>
        <th>OEE</th>
      </tr>
      </thead>
      <tbody>
      <tr v-for="row in tableData" v-if="showTable">
        <td v-if="!stationTeamId">{{ row.name }}</td>
        <td v-if="!stationTeamId">{{ row.station_name }}</td>
        <td v-if="!stationTeamId">{{ row.station_group_name }}</td>
        <td v-else>{{ row.time_duration }}</td>
        <td>{{ (row.availability * 100).toFixed(2) }} %</td>
        <td>{{ (row.quality * 100).toFixed(2) }} %</td>
        <td>{{ (row.performance * 100).toFixed(2) }} %</td>
        <td>{{ (row.oee * 100).toFixed(2) }} %</td>
      </tr>
      <tr v-if="!showTable">
        <td colspan="7" v-if="!stationTeamId" style="text-align: center; color:red;">No Data Found</td>
        <td colspan="6" v-else style="text-align: center; color:red">No Data Found</td>
      </tr>
      </tbody>
    </table>
  </div>

</template>

<script>
import reportService from '../../../../services/Reports';

export default {
    name: "ReportTableOEEByTeam",
    data: ()=>({
        tableData: []
    }),
    props:{
        stationTeamId: {
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
        stationTeamId(nw,old){
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
            let requestParams = this.getRequestParams();
            reportService.getOEETableReportByStationTeam(requestParams, function(responseData){
                vm.tableData = responseData;
            });
        },
        getRequestParams(){
          return {
            'stationTeamId': this.stationTeamId === '0' ? null : this.stationTeamId,
            'start': moment(this.start).format('DD-MM-YYYY'),
            'end': moment(this.end).format('DD-MM-YYYY'),
            'type': this.type
          }
        },
        downloadReport(){
            let requestParams = this.getRequestParams();
            reportService.getOEETableReportByStationTeamExcel(requestParams);
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
