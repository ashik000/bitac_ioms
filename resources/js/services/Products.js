import axios from 'axios';

export default {
    fetchAllGroups(success, error) {
        axios.get('productGroups',)
            .then(r => success(r.data))
            .catch(e => console.log(e.response.data))
    },
    fetchAll(filter, success, error) {
        axios.get('products', {
            params: filter
        })
            .then(r => success(r.data.product_list))
            .catch(e => console.log(e.response.data))
    },
    fetchAllProductsByGroupId(groupId, success, error) {
        axios.get('productsByGroupId/' + groupId, {

        })
            .then(r => success(r.data.product_list))
            .catch(e => console.log(e.response.data))
    },
    addGroup(data, success, error) {
        axios.post('productGroups', data)
            .then(r => success(r.data))
            .catch(e => console.log(e.response.data))
    },
    addProduct(data, success, error) {
        axios.post('products', data)
            .then(r => success(r.data.product_list))
            .catch(e => console.log(e.response.data))
    },
    updateGroup(id, data, success, error) {
        axios.put(`productGroups/${id}`, data)
            .then(r => success(r.data))
            .catch(e => console.log(e.response.data))
    },
    deleteGroup(id,success, error) {
        axios.delete(`productGroups/${id}`)
            .then(r => success(r.data))
            .catch(e => console.log(e.response.data))
    },
    updateProduct(id, data, success, error) {
        axios.put(`products/${id}`, data)
            .then(r => success(r.data.product_list))
            .catch(e => console.log(e.response.data))
    },
    deleteProduct(id, success, error) {
        axios.delete(`products/${id}`)
            .then(r => success(r.data.product_list))
            .catch(e => console.log(e.response.data))
    }
}
