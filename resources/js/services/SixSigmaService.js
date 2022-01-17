import axios from 'axios';

export default {
    getSixSigmaReport(data, success, error) {
        axios.get('six-sigma-report', {
            params: {
                'start_time': data.startTime,
                'end_time': data.endTime,
            }
        }).then(r => success(r.data))
            .catch(e => console.error(e))
    },
}
