import axios from 'axios';

export default {
    fetchAllGroups(success, error) {
        axios.get('stationGroups',)
            .then(r => success(r.data))
            .catch(e => console.log(e));
    },
    fetchAll(filter, success, error) {
        axios.get('stations', {
            params: filter
        })
            .then(r => success(r.data.station_list))
            .catch(e => console.log(e));

    },
    addGroup(data, success, error) {
        axios.post('stationGroups', data)
            .then(r => success(r.data))
            .catch(e => {
                error('Validation error');
                console.log(e)
            });
    },
    addStation(data, success, error) {
        axios.post('stations', data)
            .then(r => success(r.data.station_list))
            .catch(e => {
                error('Validation error');
                console.log(e)
            });
    },
    updateGroup(id, data, success, error) {
        axios.put(`stationGroups/${id}`, data)
            .then(r => success(r.data))
            .catch(e => {
                error('Try again');
                console.log(e)
            });
    },
    deleteGroup(id, success, error) {
        axios.delete(`stationGroups/${id}`)
            .then(r => success(r.data))
            .catch(e => {
                error('Try again');
                console.log(e)
            });
    },
    updateStation(id, data, success, error) {
        axios.put(`stations/${id}`, data)
            .then(r => success(r.data.station_list))
            .catch(e => {
                error('Validation failed');
                console.log(e)
            });
    },
    deleteStation(id, success, error) {
        axios.delete(`stations/${id}`)
            .then(r => success(r.data.station_list))
            .catch(e => {
                error('Try again');
                console.log(e)
            });
    },
    fetchAllStationsByGroupId(groupId, success, error) {
        axios.get('stationsByGroupId/' + groupId, {

        })
            .then(r => success(r.data.station_list))
            .catch(e => console.log(e))
    }
}
