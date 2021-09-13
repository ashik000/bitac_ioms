import axios from 'axios';

export default {
    login(data, success, error) {
        axios.post('login', data)
            .then(r => success(r.data))
            .catch(e => {
                error(e);
            })
    },
    logout(data, success, error) {
        axios.post('logout')
            .then(r => success(r.message))
            .catch(e => {
                error(e);
            })
    }
}
