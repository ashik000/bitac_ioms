import axios from 'axios';

export default {
    fetchAll: function(success, error) {
        axios.get('operators')
            .then(r => success(r.data.operator_list))
            .catch(e => error(e));
    },

    createOperator: function (data, success, error) {
        axios.post('operators', data)
            .then(r => success(r.data.operator_list))
            .catch(e => error(e));
    },

    updateOperator: function (id, data, success, error) {
        axios.put(`operators/${id}`, data)
            .then(r => success(r.data.operator_list))
            .catch(e => error(e));
    },

    deleteOperator: function (id, success, error) {
        axios.delete(`operators/${id}`)
            .then(r => success(r.data.operator_list))
            .catch(e => error(e));
    }
}
