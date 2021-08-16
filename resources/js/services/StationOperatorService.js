import axios from 'axios';

export default {
    fetchAll: function(stationId,success, error) {
        axios.get('stationOperators/findByStationId',{
            params:{
                station_id : stationId
            }
        })
            .then(r => success(r.data))
            .catch(e => error(e.response.data.message));
    },

    createOrUpdateStationOperator: function (data, success, error) {
        axios.post('stationOperators',data)
            .then( r=> success(r.data))
            .catch(e => {
                error(e);
            });
    },
    deleteStationOperator: function (stationOperatorId, success, error) {
        axios.delete('stationOperators/'+stationOperatorId)
            .then( r=> success(r.data))
            .catch(e => error(e))
    },
    allStationOperatorsForReportDropdown: function (success, error) {
        axios.get('stationOperators')
            .then( r=> success(r.data))
            .catch(e => error(e))
    }
}
