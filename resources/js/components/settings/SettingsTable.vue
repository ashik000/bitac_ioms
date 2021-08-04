<template>
    <div class="card shadow h-100">
        <div class="card-header">
            <div>
                {{ sectionHeader }}
            </div>

          <div class="d-flex">
            <div class="input-group remove-width">
              <input type="text" class="form-control" placeholder="Search" aria-label="Stations search" aria-describedby="Stations search">
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
              Add Stations</button>
          </div>
        </div>
        <div class="card-body  y-scroll">
          <div class="accordion">
                <span v-for="item in items" :key="item.id" :class="{ selected: item.id == selectedId }">
                    <slot :row="item" name="row">
                        {{ item }}
                    </slot>
                </span>
          </div>

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

