import axios from 'axios';

export default {
    fetchAll: function(success, error) {
        axios.get('shifts')
            .then(r => success(r.data.shift_list))
            .catch(e => error(e));
    },

    createShift: function (data, success, error) {
        axios.post('shifts', data)
            .then(r => success(r.data.shift_list))
            .catch(e => error(e));
    },

    updateShift: function (id, data, success, error) {
        axios.put(`shifts/${id}`, data)
            .then(r => success(r.data.shift_list))
            .catch(e => error(e));
    },

    deleteShift: function (id, success, error) {
        axios.delete(`shifts/${id}`)
            .then(r => success(r.data.shift_list))
            .catch(e => error(e));
    }
}

