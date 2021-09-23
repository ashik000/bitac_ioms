<template>
    <div class="card shadow h-100">
        <div class="card-header">
            <div>
                {{ sectionHeader }}
            </div>

            <div class="d-flex pe-1">
                <div class="input-group remove-width">
                    <input v-model="searchString" type="text" class="form-control rounded-2" placeholder="Search" aria-label="Shift search" aria-describedby="Shift search">
                    <button class="btn transparent-search-button" type="button">
                        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16"  class="bi bi-search" viewBox="0 0 16 16">
                            <path d="M11.742 10.344a6.5 6.5 0 1 0-1.397 1.398h-.001c.03.04.062.078.098.115l3.85 3.85a1 1 0 0 0 1.415-1.414l-3.85-3.85a1.007 1.007 0 0 0-.115-.1zM12 6.5a5.5 5.5 0 1 1-11 0 5.5 5.5 0 0 1 11 0z"></path>
                        </svg>
                    </button>
                </div>

                <button type="button" class="btn btn-secondary card-header-button" @click="$emit('action-clicked')">
                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-plus-lg" viewBox="0 0 16 16">
                        <path d="M8 0a1 1 0 0 1 1 1v6h6a1 1 0 1 1 0 2H9v6a1 1 0 1 1-2 0V9H1a1 1 0 0 1 0-2h6V1a1 1 0 0 1 1-1z"></path>
                    </svg>
                    Add Operator</button>
            </div>
        </div>
        <div class="card-body  y-scroll">

            <table class="table table-striped table-hover table-bordered">
                <thead>
                <slot name="columnHeaders"></slot>
                </thead>
                <tbody>
                <tr v-for="item in filteredItems" :key="item.id" :class="{ selected: item.id == selectedId }">
                    <slot :row="item" name="row">
                        <td>{{ item }}</td>
                    </slot>
                </tr>
                </tbody>
            </table>

        </div>
    </div>
</template>

<script>
export default {
    name: "OperatorList",
    data: () => {
        return {
            searchString: ''
        }
    },
    computed: {
        filteredItems : function () {
            return this.items.filter((item) => {
                if(this.searchString === '') return true;
                let fullName = item.first_name + ' ' + item.last_name;
                let nameCheck = fullName.toLowerCase().includes(this.searchString.toLowerCase());
                let operatorCodeCheck = item.code.toLowerCase().includes(this.searchString.toLowerCase());
                return nameCheck || operatorCodeCheck;
            });
        }
    },
    props: {
        items: {
            type: Array,
            required: true
        },
        sectionHeader: {
            type: String,
            required: true
        },
        selectedId: 0
    }
}
</script>
