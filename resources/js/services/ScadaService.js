import axios from 'axios';

export default {
    getScada( params,success, error) {
        axios.get('scada-api', {params: {filter: params.filter, date_range: params.date_range} } )
            .then(r => success(r.data))
            .catch(e => console.log(e.response))
    },
}
