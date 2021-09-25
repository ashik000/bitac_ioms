import axios from 'axios';

export default {
    fetchAll: function(stationId,success, error) {
        axios.get('stationProducts/all',{
            params:{
                station_id : stationId
            }
        })
            .then(r => success(r.data))
            .catch(e => error(e.response.data.message));
    },
    createOrUpdateStationProduct: function (data, success, error) {
        axios.post('stationProducts',data)
            .then( r=> success(r.data))
            .catch(e => {
                error(e);
            });
    },
    deleteStationProduct: function (stationProductId, success, error) {
        axios.delete('stationProducts/'+stationProductId)
            .then( r=> success(r.data))
            .catch(e => error(e))
    },
    allStationProductsForReportDropdown: function (success, error) {
        axios.get('stationProducts')
            .then( r=> success(r.data))
            .catch(e => error(e))
    },
    assignProductToStation: (payload, success, error) => {
      axios.post('assignProductToStation', payload)
        .then(r=> success(r.data))
        .catch(e => {
          error(e);
      });
    }
}
