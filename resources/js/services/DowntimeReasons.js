import axios from 'axios';

export default {
    fetchAllGroups(success, error) {
        axios.get('downtimeReasonGroups',)
            .then(r => success(r.data))
            .catch(e => console.log(e))
    },
    fetchAll(filter, success, error) {
        axios.get('downtimeReasons', {
            params: filter
        })
            .then(r => success(r.data))
            .catch(e => console.log(e))

    },
    addGroup(data, success, error) {
        axios.post('downtimeReasonGroups', data)
            .then(r => success(r.data))
            .catch(e => console.log(e))
    },
    addReason(data, success, error) {

        axios.post('downtimeReasons', data)
            .then(r => success(r.data.downtime_reason_list))
            .catch(e => console.log(e))
    },
    updateGroup(id, data, success, error) {
        axios.put(`downtimeReasonGroups/${id}`, data)
            .then(r => success(r.data))
            .catch(e => console.log(e))
    },
    deleteGroup(id, success, error) {
        axios.delete(`downtimeReasonGroups/${id}`)
            .then(r => success(r.data))
            .catch(e => console.log(e))
    },
    updateReason(id, data, success, error) {
        axios.put(`downtimeReasons/${id}`, data)
            .then(r => success(r.data.downtime_reason_list))
            .catch(e => console.log(e.response.data))
    },
    deleteReason(id, success, error) {
        axios.delete(`downtimeReasons/${id}`)
            .then(r => {
                console.log(JSON.stringify(r.data))
                success(r.data.downtime_reason_list)
            })
            .catch(e => console.log(e.response.data))
    },
    assignDowntime(data, success, error) {
        axios.post(`assignDowntimeReason`, data)
            .then(r => success(r.data))
            .catch(e => console.log(e))
    }
}
