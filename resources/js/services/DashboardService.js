import axios from 'axios';

export default {
    getMachineStatus(success, error) {
        axios.get('getMachineStatus').
        then(response => {
            success(response.data);
        }).catch(error => {
            console.log(error);
        });
    }
}
