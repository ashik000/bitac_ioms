import axios from 'axios';

function getFileFromRequest(response, fileName){
    if (window.navigator && window.navigator.msSaveOrOpenBlob) {
        // IE variant
        window.navigator.msSaveOrOpenBlob(new Blob([response.data], { type: "application/vnd.openxmlformats-officedocument.spreadsheetml.sheet" }), fileName);
    } else {
        const url = window.URL.createObjectURL(new Blob([response.data], { type: "application/vnd.openxmlformats-officedocument.spreadsheetml.sheet" }));
        const link = document.createElement("a");
        link.href = url;
        link.setAttribute("download", fileName);
        document.body.appendChild(link);
        link.click();
    }
}

export default {
    fetchAll(data, success, error) {
        axios.get('machiningData', {
            params: {
                'start_time': data.startTime,
                'end_time': data.endTime,
            }
        }).then(r => success(r.data))
            .catch(e => console.error(e))
    },
    downloadExcel(data) {
        axios.get('getMachiningDataExcel', {
            responseType: 'blob',
            params: {
                'start_time': data.startTime,
                'end_time': data.endTime,
            }
        }).then(r => getFileFromRequest(r, "Station Report"))
            .catch(e => console.error(e))
    },
}
