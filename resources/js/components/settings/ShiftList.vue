<template>
    <div class="card shadow h-100">
        <div class="card-header">
            <div>
                {{ sectionHeader }}
            </div>

            <div class="d-flex">
                <div class="input-group remove-width">
                    <input type="text" v-model="searchString" class="form-control" placeholder="Search" aria-label="Shift search" aria-describedby="Shift search">
                    <button class="btn transparent-search-button" type="button">
                        <b-icon icon="search" aria-hidden="true"></b-icon>
                    </button>
                </div>

                <button type="button" class="btn btn-secondary card-header-button" @click="$emit('action-clicked')">
                    <b-icon icon="plus" font-scale="2"></b-icon> Add Shift</button>
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
    name: "ShiftList",
    data: () => {
        return {
            searchString: ''
        }
    },
    computed: {
        filteredItems : function () {
            return this.items.filter((item) => {
                if(this.searchString === '') return true;
                return item.name.toLowerCase().includes(this.searchString.toLowerCase());
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
