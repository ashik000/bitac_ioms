import axios from 'axios';

export default {
    getDowntimeSummary( date, stationId, success, error) {
        axios.get(`getDowntimeSummary?date=${date}&stationId=${stationId}`)
            .then(r => success(r.data))
            .catch(e => {
                console.error("Error while calling getDowntimeSummary: ", e.response.data);
            })
    }
}
