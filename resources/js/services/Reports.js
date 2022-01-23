import axios from 'axios';

function getFileFromRequest(response, fileName){
    if (window.navigator && window.navigator.msSaveOrOpenBlob) {
        // IE variant
        window.navigator.msSaveOrOpenBlob(new Blob([response.data], { type: "application/vnd.openxmlformats-officedocument.spreadsheetml.sheet" }), fileName);
    } else {
        const url = window.URL.createObjectURL(new Blob([response.data], { type: "application/vnd.openxmlformats-officedocument.spreadsheetml.sheet" }));
        const link = document.createElement("a");
        link.href = url;
        link.setAttribute("download", fileName);
        document.body.appendChild(link);
        link.click();
    }
}

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
                'start': data.start,
                'report_type': data.reportType,
            }
        }).then(r => success(r.data))
        .catch(e => console.log(e.response.data))
    },
    fetchDowntimeData(data,success, error) {
        // console.log("Fetching with params");
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
                'type' : data.type,
                'reportType' : data.reportType
            }
        }).then(r => success(r.data))
            .catch(e => console.error(e))
    },
    getOEETableReportByStationExcel(data) {
        axios.get('getOEETableReportByStationExcel', {
            responseType: 'blob',
            params: {
                'stationId': data.stationId,
                'end' : data.end,
                'start' : data.start,
                'type' : data.type,
                'reportType' : data.reportType
            }
        }).then(r => getFileFromRequest(r, "Station Report"))
            .catch(e => console.error(e))
    },
    getOEETableReportByStationProduct(data, success, error) {
        axios.get('getOEETableReportByStationProduct', {
            params: {
                'stationProductId': data.stationProductId,
                'end' : data.end,
                'start' : data.start,
                'type' : data.type,
                'reportType' : data.reportType
            }
        }).then(r => success(r.data))
            .catch(e => console.error(e))
    },
    getOEETableReportByStationProductExcel(data, success, error) {
        axios.get('getOEETableReportByStationProductExcel', {
            responseType : 'blob',
            params: {
                'stationProductId': data.stationProductId,
                'end' : data.end,
                'start' : data.start,
                'type' : data.type,
                'reportType' : data.reportType
            }
        }).then(r => getFileFromRequest(r, 'Product Report'))
            .catch(e => console.error(e))
    },
    getOEETableReportByStationShift(data, success, error) {
        axios.get('getOEETableReportByStationShift', {
            params: {
                'stationShiftId': data.stationShiftId,
                'end' : data.end,
                'start' : data.start,
                'type' : data.type,
                'reportType' : data.reportType
            }
        }).then(r => success(r.data))
            .catch(e => console.error(e))
    },

    getOEETableReportByStationShiftExcel(data, success, error) {
        axios.get('getOEETableReportByStationShiftExcel', {
            responseType: "blob",
            params: {
                'stationShiftId': data.stationShiftId,
                'end' : data.end,
                'start' : data.start,
                'type' : data.type,
                'reportType' : data.reportType
            }
        }).then(r => getFileFromRequest(r, "Shift Report"))
            .catch(e => console.error(e))
    },
    getOEETableReportByStationOperator(data, success, error) {
        axios.get('getOEETableReportByStationOperator', {
            params: {
                'stationOperatorId': data.stationOperatorId,
                'end' : data.end,
                'start' : data.start,
                'type' : data.type,
                'reportType' : data.reportType
            }
        }).then(r => success(r.data))
            .catch(e => console.error(e))
    },
    getOEETableReportByStationOperatorExcel(data) {
        axios.get('getOEETableReportByStationExcel', {
            responseType: "blob",
            params: {
                'stationOperatorId': data.stationOperatorId,
                'end' : data.end,
                'start' : data.start,
                'type' : data.type,
                'reportType' : data.reportType
            }
        }).then(function (response) {
            getFileFromRequest(response, "Station Report")
        });
    },
    getOEETableReportByStationTeam(data, success, error) {
        axios.get('getOEETableReportByStationTeam', {
            params: {
                'stationTeamId': data.stationTeamId,
                'end' : data.end,
                'start' : data.start,
                'type' : data.type,
                'reportType' : data.reportType
            }
        }).then(r => success(r.data))
            .catch(e => console.error(e))
    },
    getOEETableReportByStationTeamExcel(data, success, error) {
        axios.get('getOEETableReportByStationTeamExcel', {
            responseType: "blob",
            params: {
                'stationTeamId': data.stationTeamId,
                'end' : data.end,
                'start' : data.start,
                'type' : data.type,
                'reportType' : data.reportType
            }
        }).then(function (response) {
            getFileFromRequest(response, "Team Report");
        });
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
    getDowntimeTableReportByStationExcel(data) {
        axios.get('report/downtime/by/station/excel', {
            responseType: 'blob',
            params: {
                'stationId': data.stationId,
                'end' : data.end,
                'start' : data.start
            }
        }).then(r => getFileFromRequest(r, "Station Report"))
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
    getDowntimeTableReportByStationProductExcel(data, success, error) {
        axios.get('report/downtime/by/product/excel', {
            responseType : 'blob',
            params: {
                'stationProductId': data.stationProductId,
                'end' : data.end,
                'start' : data.start
            }
        }).then(r => getFileFromRequest(r, 'Product Report'))
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
    getDowntimeTableReportByStationShiftExcel(data, success, error) {
        axios.get('report/downtime/by/shift/excel', {
            responseType: "blob",
            params: {
                'stationShiftId': data.stationShiftId,
                'end' : data.end,
                'start' : data.start
            }
        }).then(r => getFileFromRequest(r, "Shift Report"))
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
    getDowntimeTableReportByStationOperatorExcel(data) {
        axios.get('report/downtime/by/operator/excel', {
            responseType: "blob",
            params: {
                'stationOperatorId': data.stationOperatorId,
                'end' : data.end,
                'start' : data.start,
            }
        }).then(function (response) {
            getFileFromRequest(response, "Operator Report")
        });
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
    },
    getDowntimeTableReportByStationTeamExcel(data, success, error) {
        axios.get('report/downtime/by/team/excel', {
            responseType: "blob",
            params: {
                'stationTeamId': data.stationTeamId,
                'end' : data.end,
                'start' : data.start
            }
        }).then(function (response) {
            getFileFromRequest(response, "Team Report");
        });
    },
}
