<template>
    <div class="card shadow h-100">

        <div class="card-header">
            <div>
                {{ sectionHeader }}
            </div>

            <div>
                <button type="button" class="btn btn-secondary card-header-button" @click="$emit('action-clicked')">
                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-plus-lg" viewBox="0 0 16 16">
                        <path d="M8 0a1 1 0 0 1 1 1v6h6a1 1 0 1 1 0 2H9v6a1 1 0 1 1-2 0V9H1a1 1 0 0 1 0-2h6V1a1 1 0 0 1 1-1z"></path>
                    </svg>
                    {{ buttonText }}
                </button>
            </div>
        </div>

        <ul class="card-body p-0 y-scroll list-group">
            <li v-for="(item, index) in items" :key="item.id" @click="$emit('item-selected', item); toggleActive(index)" :class="[{active: activeItem === index}, 'list-group-item', 'border-0']">
                <slot :item="item">
                    {{ item }}
                </slot>
            </li>
        </ul>

    </div>
</template>

<script>
export default {
    name: 'DowntimeReasonGroup',
    data: () => ({
        activeItem: 0,
    }),
    props: {
        sectionHeader: {
            type: String,
            required: true,
        },
        buttonText:{
            type: String,
            required: true,
        },
        listHeader: {
            type: String,
            required: false,
        },
        items: {
            type: Array,
            required: true
        }
    },
    methods: {
        toggleActive: function(index) {
            this.activeItem = index;
        }
    }
}
</script>
