import axios from 'axios';

export default {
    getMachineStatus(data, success, error) {
        axios.get('getMachineStatus', {
            params: {
                'station_id': data.stationId,
            }
        })
        .then(response => {
            success(response.data);
        }).catch(error => {
            console.log(error);
        });
    },
    fetchAllSummaryData(data, success, error) {
        axios.get('dashboard-summary', {
            params: {
                'station_ids': data.stationIds,
            }
        })
            .then(r => success(r.data))
            .catch(e => console.log(e))
    }
}
