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
    updateGroup(id, data, success, error) {
        axios.post(`teamUpdate/${id}`, data)
            .then(r => success(r.data))
            .catch(e => {
                error('Try again');
                console.log(e.response.data)
            })
    },
    deleteGroup(id,success, error) {
        axios.post(`teamDelete/${id}`)
            .then(r => success(r.data))
            .catch(e => {
                error('Try again');
                console.log(e.response.data)
            })
    },
    addOperatorsToTeam(data, success, error) {
        axios.post('team/operators', data)
            .then(r => success(r.data))
            .catch(e => {
                error('Validation failed');
                console.log(e.response.data)
            })
    },
    deleteOperatorFromTeam(payload, success, error) {
        axios.post('team/operators/delete', payload)
            .then(r => success(r.data))
            .catch(e => {
                error('Try again');
                console.log(e.response.data)
            })
    }
}
