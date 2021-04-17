import axios from 'axios';

export default {
    uploadHourlyScrapCount(data, success, error) {
        axios.post('uploadHourlyScrapCount', data)
            .then(r => success(r.data))
            .catch(e => console.error(e))
    },
}
