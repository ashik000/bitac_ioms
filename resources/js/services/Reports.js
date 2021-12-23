import axios from 'axios';

export default {
    fetchReports(data, success, error) {
        axios.get('report', {
            params: {
                'type': data.type,
                'station_id': data.stationId,
                'station_product_id': data.stationProductId,
                'station_shift_id' : data.stationShiftId,
                'station_operator_id' : data.stationOperatorId,
                'end': data.endTime,
                'start': data.start
            }
        }).then(r => success(r.data))
        .catch(e => console.log(e.response.data))
    },
    fetchDowntimeData(data,success, error) {
        console.log("Fetching with params");
        console.log(JSON.stringify(data));
        axios.get('downtimeReport',{
            params :{
                'station_id' : data.stationId,
                'station_product_id': data.stationProductId,
                'station_shift_id': data.stationShiftId,
                'station_operator_id': data.stationOperatorId,
                'end': data.endTime,
                'start': data.start
            }
        }).then(r => success(r.data))
            .catch(e => console.log(e.response.data))
            .catch(e => console.log(e.response.data))
    },
    getHourlyProducedAndScrapedCountOfADay(data, success, error) {
        axios.get('getHourlyProducedAndScrapedCountOfADay', {
            params: {
                'stationId': data.stationId,
                'date': data.date
            }
        }).then(r => success(r.data))
            .catch(e => console.error(e))
    },
    getOEETableReportByStation(data, success, error) {
        axios.get('getOEETableReportByStation', {
            params: {
                'stationId': data.stationId,
                'end' : data.end,
                'start' : data.start,
                'type' : data.type
            }
        }).then(r => success(r.data))
            .catch(e => console.error(e))
    },
    getOEETableReportByStationProduct(data, success, error) {
        axios.get('getOEETableReportByStationProduct', {
            params: {
                'stationProductId': data.stationProductId,
                'end' : data.end,
                'start' : data.start,
                'type' : data.type
            }
        }).then(r => success(r.data))
            .catch(e => console.error(e))
    },
    getOEETableReportByStationShift(data, success, error) {
        axios.get('getOEETableReportByStationShift', {
            params: {
                'stationShiftId': data.stationShiftId,
                'end' : data.end,
                'start' : data.start,
                'type' : data.type
            }
        }).then(r => success(r.data))
            .catch(e => console.error(e))
    },
    getOEETableReportByStationOperator(data, success, error) {
        axios.get('getOEETableReportByStationOperator', {
            params: {
                'stationOperatorId': data.stationOperatorId,
                'end' : data.end,
                'start' : data.start,
                'type' : data.type
            }
        }).then(r => success(r.data))
            .catch(e => console.error(e))
    },
    getOEETableReportByStationTeam(data, success, error) {
        axios.get('getOEETableReportByStationTeam', {
            params: {
                'stationTeamId': data.stationTeamId,
                'end' : data.end,
                'start' : data.start,
                'type' : data.type
            }
        }).then(r => success(r.data))
            .catch(e => console.error(e))
    },
    getDowntimeTableReportByStation(data, success, error) {
        axios.get('report/downtime/by/station', {
            params: {
                'stationId': data.stationId,
                'end' : data.end,
                'start' : data.start
            }
        }).then(r => success(r.data))
            .catch(e => console.error(e))
    },
    getDowntimeTableReportByStationProduct(data, success, error) {
        axios.get('report/downtime/by/product', {
            params: {
                'stationProductId': data.stationProductId,
                'end' : data.end,
                'start' : data.start
            }
        }).then(r => success(r.data))
            .catch(e => console.error(e))
    },
    getDowntimeTableReportByStationShift(data, success, error) {
        axios.get('report/downtime/by/shift', {
            params: {
                'stationShiftId': data.stationShiftId,
                'end' : data.end,
                'start' : data.start
            }
        }).then(r => success(r.data))
            .catch(e => console.error(e))
    },
    getDowntimeTableReportByStationOperator(data, success, error) {
        axios.get('report/downtime/by/operator', {
            params: {
                'stationOperatorId': data.stationOperatorId,
                'end' : data.end,
                'start' : data.start
            }
        }).then(r => success(r.data))
            .catch(e => console.error(e))
    },

    getDowntimeTableReportByStationTeam(data, success, error) {
        axios.get('report/downtime/by/team', {
            params: {
                'stationTeamId': data.stationTeamId,
                'end' : data.end,
                'start' : data.start
            }
        }).then(r => success(r.data))
            .catch(e => console.error(e))
    }

}
