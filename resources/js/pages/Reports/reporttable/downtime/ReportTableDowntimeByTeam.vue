<template>
  <div>
    <button class="btn btn-primary mb-4" @click="downloadReport">Download Excel</button>
    <table class="table reportTable">
      <thead>
      <tr>
        <th v-if="!stationTeamId">Team</th>
        <th v-if="!stationTeamId">Station</th>
        <th v-if="!stationTeamId">Station Group</th>
        <th v-if="stationTeamId">Downtime Reason</th>
        <th v-if="stationTeamId">Downtime Reason Group</th>
        <th v-if="stationTeamId">Downtime Type</th>
        <th>Count</th>
        <th>Duration</th>
        <th>Stop %</th>
      </tr>
      </thead>
      <tbody>
      <tr v-for="row in tableData" v-if="showTable">
        <td v-if="!stationTeamId">{{ row.team_name }}</td>
        <td v-if="!stationTeamId">{{ row.station_name }}</td>
        <td v-if="!stationTeamId">{{ row.station_group_name }}</td>
        <td v-if="stationTeamId">{{ row.name }}</td>
        <td v-if="stationTeamId">{{ row.reason_group_name }}</td>
        <td v-if="stationTeamId">{{ row.type }}</td>
        <td>{{ row.count }}</td>
        <td>{{ formatDuration(row.duration) }}</td>
        <td>{{ (row.stop_percent * 100).toFixed(2) }} %</td>
      </tr>
      <tr v-if="!showTable">
        <td colspan="6" style="text-align: center; color:red">No Data Found</td>
      </tr>
      </tbody>
    </table>
  </div>
</template>

<script>
import reportService from '../../../../services/Reports';
import {formatDuration} from '../../../../utils';

export default {
  name: "ReportTableDowntimeByTeam",
  data: () => ({
    tableData: []
  }),
  props: {
    stationTeamId: {
      default: 0
    },
    start: {
      type: Date
    },
    end: {
      type: Date
    }
  },
  watch: {
    stationTeamId(nw, old) {
      this.fetchData();
    },
    start(nw, old) {
      this.fetchData();
    },
    end(nw, old) {
      this.fetchData();
    }
  },
  methods: {
    fetchData() {
      let vm = this;
      reportService.getDowntimeTableReportByStationTeam({
        'stationTeamId': vm.stationTeamId === '0' ? null : vm.stationTeamId,
        'start': moment(vm.start).format('DD-MM-YYYY'),
        'end': moment(vm.end).format('DD-MM-YYYY')
      }, function (responseData) {
        vm.tableData = responseData;
      });
    },
    getRequestParams() {
      return {
        'stationTeamId': this.stationTeamId === '0' ? null : this.stationTeamId,
        'start': moment(this.start).format('DD-MM-YYYY'),
        'end': moment(this.end).format('DD-MM-YYYY')
      }
    },
    downloadReport() {
      let requestParams = this.getRequestParams();
      reportService.getDowntimeTableReportByStationTeamExcel(requestParams);
    },
    formatDuration
  },
  mounted() {
    const vm = this;
    vm.fetchData();
  },
  computed: {
    showTable() {
      return this.tableData && this.tableData.length > 0;
    }
  }
}
</script>

