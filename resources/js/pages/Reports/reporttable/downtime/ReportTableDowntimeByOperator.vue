<template>
  <div>
    <button class="btn btn-primary mb-4" @click="downloadExcel()">Download Excel</button>
    <table class="table reportTable">
      <thead>
      <tr>
        <th v-if="!stationOperatorId">Operator</th>
        <th v-if="!stationOperatorId">Station</th>
        <th v-if="!stationOperatorId">Station Group</th>
        <th v-if="stationOperatorId">Downtime Reason</th>
        <th v-if="stationOperatorId">Downtime Reason Group</th>
        <th v-if="stationOperatorId">Downtime Type</th>
        <th>Count</th>
        <th>Duration</th>
        <th>Stop %</th>
      </tr>
      </thead>
      <tbody>
      <tr v-for="row in tableData" v-if="showTable">
        <td v-if="!stationOperatorId">{{ row.operator_name }}</td>
        <td v-if="!stationOperatorId">{{ row.station_name }}</td>
        <td v-if="!stationOperatorId">{{ row.station_group_name }}</td>
        <td v-if="stationOperatorId">{{ row.name }}</td>
        <td v-if="stationOperatorId">{{ row.reason_group_name }}</td>
        <td v-if="stationOperatorId">{{ row.type }}</td>
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
  name: "ReportTableDowntimeByOperator",
  data: () => ({
    tableData: []
  }),
  props: {
    stationOperatorId: {
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
    stationOperatorId(nw, old) {
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
      reportService.getDowntimeTableReportByStationOperator({
        'stationOperatorId': vm.stationOperatorId === '0' ? null : vm.stationOperatorId,
        'start': moment(vm.start).format('DD-MM-YYYY'),
        'end': moment(vm.end).format('DD-MM-YYYY')
      }, function (responseData) {
        vm.tableData = responseData;
      });
    },
    getRequestParams(){
      return {
        'stationOperatorId': this.stationOperatorId === '0' ? null : this.stationOperatorId,
        'start' : moment(this.start).format('DD-MM-YYYY'),
        'end': moment(this.end).format('DD-MM-YYYY'),
      }
    },
    downloadExcel() {
      let requestParams = this.getRequestParams();
      reportService.getDowntimeTableReportByStationOperatorExcel(requestParams);
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

