import axios from 'axios';

export default {
    fetchAll: function(stationId,success, error) {
        axios.get('stationTeams/findByStationId',{
            params:{
                station_id : stationId
            }
        })
            .then(r => success(r.data))
            .catch(e => error(e.response.data.message));
    },
    createOrUpdateStationTeam: function (data, success, error) {
        axios.post('stationTeams',data)
            .then( r=> success(r.data))
            .catch(e => {
                error(e);
            });
    },
    deleteStationTeam: function (stationTeamId, success, error) {
        axios.delete('stationTeams/'+stationTeamId)
            .then( r=> success(r.data))
            .catch(e => error(e))
    },
    allStationTeamsForReportDropdown: function (success, error) {
        axios.get('stationTeams')
            .then( r=> success(r.data))
            .catch(e => error(e))
    },
    assignTeamToStation: (data, success, error) => {
        axios.post('stationTeams',data)
            .then( r=> success(r.data))
            .catch(e => {
                error(e);
        });
    }
}
