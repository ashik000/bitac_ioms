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
            .catch(e => {
                error('Validation error');
                console.log(e.response.data)
            });
    },

    updateOperator: function (id, data, success, error) {
        axios.put(`operators/${id}`, data)
            .then(r => success(r.data.operator_list))
            .catch(e => {
                error('Try again');
                console.log(e.response.data)
            });
    },

    deleteOperator: function (id, success, error) {
        axios.delete(`operators/${id}`)
            .then(r => success(r.data.operator_list))
            .catch(e => {
                error('Try again');
                console.log(e.response.data)
            });
    }
}
