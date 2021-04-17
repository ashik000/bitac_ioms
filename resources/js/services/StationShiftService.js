import axios from 'axios';

export default {
    fetchAll: function(stationId,success, error) {
        axios.get('stationShifts/all',{
            params:{
                station_id : stationId
            }
        })
            .then(r => success(r.data))
            .catch(e => error(e.response.data.message));
    },
    createOrUpdateStationShift: function (data, success, error) {
        axios.post('stationShifts',data)
            .then( r=> success(r.data))
            .catch(e => error(e))
    },
    deleteStationShift: function (stationShiftId, success, error) {
        axios.delete('stationShifts/'+stationShiftId)
            .then( r=> success(r.data))
            .catch(e => error(e))
    },
    allStationShiftsForReportDropdown: function (success, error) {
        axios.get('stationShifts')
            .then( r=> success(r.data))
            .catch(e => error(e))
    }
}
