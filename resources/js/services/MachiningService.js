import axios from 'axios';

export default {
    fetchAll: function(success, error) {
        axios.get('machiningData')
            .then(r => success(r.data))
            .catch(e => error(e));
    },
}
