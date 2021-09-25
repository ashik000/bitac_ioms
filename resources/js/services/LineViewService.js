import axios from 'axios';
import moment from "moment";

export default {
    fetchLineViewData: (filter, success, error) => {
        axios.get('lineview', {
            params: {
                station_id: filter.stationId,
                shift_id: filter.stationShiftId,
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
    fetchTopOperatorDowntimes: (filter, success, error) => {
        axios.get('topOperatorDowntimes', {
            params: {
                end: filter.end,
                start: filter.start
            }
        })
            .then(r => success(r.data))
            .catch(e => console.log(e))
    },
    fetchOperatorName: (filter, success, error) => {
        axios.get('getOperatorName', {
            params: {
                stationId: filter.stationId,
                date: filter.date
            }
        })
            .then(r => success(r.data))
            .catch(e => console.log(e))
    },
    storeDefects: (data, success, error) => {
        axios.post('storeLineviewDefects',data)
            .then( r=> success(r.data))
            .catch(e => {
                error(e);
        });
    }
}
