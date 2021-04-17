import axios from 'axios';

export default {
    fetchLineViewData: (filter, success, error) => {
        axios.get('lineview', {
            params: {
                station_id: filter.stationId,
                date: filter.date
            }
        })
            .then(r => success(r.data))
            .catch(e => console.log(e))
    }
}
