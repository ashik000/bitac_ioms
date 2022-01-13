import axios from 'axios';

export default {
    getScada( params,success, error) {
        axios.get('scada-api', {params: {filter: params.filter} } )
            .then(r => success(r.data))
            .catch(e => console.log(e.response))
    },
}
