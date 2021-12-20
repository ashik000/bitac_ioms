import axios from 'axios';

export default {
    fetchAllTeams(success, error) {
        axios.get('team', {

        })
            .then(r => success(r.data))
            .catch(e => console.log(e.response.data))
    },
    fetchOperatorListByTeamId( filter ,success, error) {
        axios.get('team/operators', {
            params: {
                team_id: filter.teamId,
            }
        })
            .then(r => success(r.data))
            .catch(e => console.log(e.response.data))
    },
    addGroup(data, success, error) {
        axios.post('team', data)
            .then(r => success(r.data))
            .catch(e => {
                error('Validation failed');
                console.log(e.response.data)
            })
    },
}
