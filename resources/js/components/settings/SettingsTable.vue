<template>
    <div>
        <header class="section-header">
            <span style="color: #ffffff">
                {{ sectionHeader }}
            </span>

            <button class="btn" style="color: #ffffff" @click="$emit('action-clicked')">
                <i class="icon material-icons" style="font-size:24px; color:#e6e6e6;">add_circle_outline</i>
            </button>
        </header>
        <div class="table-responsive">
            <table class="settings-table table">
                <thead>
                <slot name="columnHeaders">
                </slot>
                </thead>
                <tbody>
                <tr v-for="item in items" :key="item.id" :class="{ selected: item.id == selectedId }">
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
        name: "SettingsTable",
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
            background:#0c4da1;
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

    .settings-table {
        thead {
            background: #4e4d78;
            color: #b2b2b2;

            th {
                padding-top: 0.25rem;
                padding-bottom: 0.25rem;
                border: 1px solid #0B312A;
                font-weight: bold;
            }
        }

        tbody {
            /*tr {*/
            /*    &:nth-child(even) {*/
            /*        background: #fafafa;*/
            /*    }*/
            /*}*/
            td {
                background: transparent;
                border: 1px solid #0B312A;
                color: #ffffff;
                vertical-align: middle;
            }
        }
    }

    .selected{
        background: #1c3d5a;
    }
</style>

