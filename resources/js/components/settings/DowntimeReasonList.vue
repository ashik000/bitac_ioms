<template>
    <div class="card shadow h-100">
        <div class="card-header">
            <div>
                {{ sectionHeader }}
            </div>

            <div class="d-flex">
                <div class="input-group remove-width">
                    <input v-model="searchString" type="text" class="form-control rounded-2 btn-sm" placeholder="Search" aria-label="Downtime reason search" aria-describedby="Downtime Reason search">
                    <button class="btn transparent-search-button" type="button">
                        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16"  class="bi bi-search" viewBox="0 0 16 16">
                            <path d="M11.742 10.344a6.5 6.5 0 1 0-1.397 1.398h-.001c.03.04.062.078.098.115l3.85 3.85a1 1 0 0 0 1.415-1.414l-3.85-3.85a1.007 1.007 0 0 0-.115-.1zM12 6.5a5.5 5.5 0 1 1-11 0 5.5 5.5 0 0 1 11 0z"></path>
                        </svg>
                    </button>
                </div>

                <button type="button" class="btn btn-sm btn-secondary card-header-button" @click="$emit('action-clicked')">
                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-plus-lg" viewBox="0 0 16 16">
                        <path d="M8 0a1 1 0 0 1 1 1v6h6a1 1 0 1 1 0 2H9v6a1 1 0 1 1-2 0V9H1a1 1 0 0 1 0-2h6V1a1 1 0 0 1 1-1z"></path>
                    </svg>
                    Add Reason</button>
            </div>
        </div>
        <div class="card-body y-scroll">
            <div class="list-group">
                <li v-for="item in filteredItems" :key="item.id" :class="{ selected: item.id == selectedId }" class="list-group-item">
                    <slot :row="item" name="row">
                        {{ item }}
                    </slot>
                </li>
            </div>

        </div>
    </div>
</template>

<script>
import LineView from "../../pages/LineView";
export default {
    name: "DowntimeReasonList",
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
    components: {LineView},
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
