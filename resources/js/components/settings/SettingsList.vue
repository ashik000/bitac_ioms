<template>
    <div>
        <header class="section-header">
            <span style="color:#ffffff ">
                {{ sectionHeader }}
            </span>

            <button class="btn" style="color: #ffffff" @click="$emit('action-clicked')">
                <i class="icon material-icons" style="font-size:24px; color:#e6e6e6;">add_circle_outline</i>
            </button>
        </header>
        <ul class="section-list">
            <li class="list-header" v-if="listHeader">{{ listHeader }}</li>
            <li v-for="item in items" :key="item.id" @click="$emit('item-selected', item)">
                <slot :item="item">
                    {{ item }}
                </slot>
            </li>
        </ul>
    </div>
</template>

<script>
    export default {
        name: 'SettingsList',
        props: {
            sectionHeader: {
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
        }
    }
</script>

<style scoped lang="scss">
    .section-header {
             padding: 0.5rem 1rem;
             background: #232156;

             display: flex;
             flex-direction: row;
             justify-content: flex-start;
             align-items: center;

             > span {
                 flex-grow: 1;
             }

        .action {
            background: #0c4da1;
            border: None;
            border-radius: 50%;

            padding: 0;
            width: 1.5rem;
            height: 1.5rem;

            font-size: 1.25rem;
            line-height: 1.5rem;

            box-shadow: 1px 1px 1px 1px #0c4da1;
        }
    }

    .section-list {
        list-style: none;
        margin: 0;
        padding: 0;

        li {
            padding: 0.5rem 1rem;
            cursor: pointer;

            &.list-header {
                background: transparent;
                cursor: default;
            }
            color:#ffffff;
            border: 1px solid #0B312A;
            background: transparent;
        }
    }
</style>
