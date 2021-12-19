import axios from 'axios';

export default {
    fetchAllTeams(success, error) {
        axios.get('team', {

        })
            .then(r => success(r.data))
            .catch(e => console.log(e.response.data))
    },
    fetchOperatorListByTeamId( filter ,success, error) {
        axios.get('team/operator', {
            params: {
                teamId: filter.teamId,
            }
        })
            .then(r => success(r.data))
            .catch(e => console.log(e.response.data))
    },
}
