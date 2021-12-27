import axios from 'axios';

export default {
    getScada(success, error) {
        axios.get('scada-api',)
            .then(r => success(r.data))
            .catch(e => console.log(e.response))
    },
}
