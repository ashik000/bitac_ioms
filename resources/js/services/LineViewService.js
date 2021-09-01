import axios from 'axios';
import moment from "moment";

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
    },
    fetchStationShift: (filter, success, error) => {
        axios.get('lineviewStationShift', {
            params: {

            }
        })
            .then(r => success(r.data))
            .catch(e => console.log(e))
    },
    fetchTopDowntimeReasons: (filter, success, error) => {
        axios.get('topDowntimeReasons', {
            params: {
                end: filter.end,
                start: filter.start
            }
        })
            .then(r => success(r.data))
            .catch(e => console.log(e))
    },
    fetchTopOperatorDowntimeReasons: (filter, success, error) => {
        axios.get('topOperatorDowntimeReasons', {
            params: {
                end: filter.end,
                start: filter.start
            }
        })
            .then(r => success(r.data))
            .catch(e => console.log(e))
    }
}
