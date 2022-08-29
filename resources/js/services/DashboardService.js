import axios from 'axios';

export default {

    fetchAllSummaryData(success, error) {
        axios.get('dashboard-summary')
            .then(r => success(r.data))
            .catch(e => console.log(e))
    },
}
