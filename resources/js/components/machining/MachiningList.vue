<template>
    <div class="card shadow h-100">
        <div class="card-header">
            <div>
                {{ sectionHeader }}
            </div>

            <div class="d-flex pe-1">

                <div class="d-flex date-range-picker" style="; float: right;">

                    <v-date-picker v-model="range" is-range @input="onDateRangeChanged" v-show="calendarIsVisible" >
                        <template v-slot="{ inputValue, inputEvents }">
                            <input
                                :value="`${inputValue.start} - ${inputValue.end}`"
                                v-on="inputEvents.start"
                                class="border px-2 py-1 w-32 rounded focus:outline-none focus:border-indigo-300"
                            />
                        </template>
                    </v-date-picker>

                    <div class="dropdown me-1" style="z-index: 200;">
                        <button class="btn btn-light dropdown-toggle" type="button" id="dropdownMenuButton1" data-bs-toggle="dropdown" aria-expanded="false">
                            {{ selectedRange.title }}
                        </button>
                        <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="dropdownMenuButton1">
                            <li>
                            <a href="#" class="dropdown-item"
                                v-for="item in rangeSelections" :key="item.id" @click="changeSelectedRange(item)">
                                {{ item.title }}
                            </a>

                            <a href="#" class="dropdown-item"
                                @click="changeSelectedRange({ tag: 'custom', title: 'Custom' })">
                                Custom
                            </a>
                            </li>
                        </ul>
                    </div>

                </div>

            
                <div class="input-group remove-width">
                    <input v-model="searchString" type="text" class="form-control rounded-2" placeholder="Search" aria-label="Shift search" aria-describedby="Shift search">
                    <button class="btn transparent-search-button" type="button">
                        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16"  class="bi bi-search" viewBox="0 0 16 16">
                            <path d="M11.742 10.344a6.5 6.5 0 1 0-1.397 1.398h-.001c.03.04.062.078.098.115l3.85 3.85a1 1 0 0 0 1.415-1.414l-3.85-3.85a1.007 1.007 0 0 0-.115-.1zM12 6.5a5.5 5.5 0 1 1-11 0 5.5 5.5 0 0 1 11 0z"></path>
                        </svg>
                    </button>
                </div>

                <button type="button" class="btn btn-secondary" @click="downloadExcel()">
                    <b-icon icon="download" class="pb-sm-1" font-scale="1.30"></b-icon>
                    Download</button>
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
import moment from "moment";
export default {
    name: "MachiningList",
    data: () => {
        return {
            searchString: '',
            calendarIsVisible: false,
            selectedRange: {
                tag: 'today',
                title: 'Today'
            },
            range: {
                start: moment().startOf('day').toDate(),
                end: moment().endOf('day').toDate()
            },
            selectedRangeX: {
                start: moment().startOf('day').toDate(),
                end: moment().endOf('day').toDate()
            },
        }
    },
    computed: {
        filteredItems : function () {
            return this.items.filter((item) => {
                if(this.searchString === '') return true;
                
                let station_name = item.station_name;
                let program_name = item.program_name;
                let spindle_speed = item.spindle_speed;
                let feed_rate = item.feed_rate;

                let station_name_check = station_name.toLowerCase().includes(this.searchString.toLowerCase());
                let program_name_check = program_name.toLowerCase().includes(this.searchString.toLowerCase());
                let spindle_speed_check = spindle_speed.toLowerCase().includes(this.searchString.toLowerCase());
                let feed_rate_check = feed_rate.toLowerCase().includes(this.searchString.toLowerCase());
                
                return station_name_check || program_name_check || spindle_speed_check || feed_rate_check;
            });
        },
        rangeSelections() {
            return [
                {
                    tag: 'today',
                    title: 'Today'
                },
                {
                    tag: 'yesterday',
                    title: 'Yesterday'
                },
                {
                    tag: 'this_week',
                    title: 'This Week'
                },
                {
                    tag: 'last_week',
                    title: 'Last Week'
                }
            ];
        },
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
    },
    methods: {
        changeSelectedRange(item) {
            this.selectedRange = item;

            switch (item.tag) {
                case 'today':
                this.range.start = moment().startOf('day').toDate();
                this.range.end = moment().endOf('day').toDate();
                this.calendarIsVisible = false;
                break;

                case 'yesterday':
                this.range.start = moment().subtract(1, "days").startOf('day').toDate();
                this.range.end = moment().subtract(1, "days").endOf('day').toDate();
                this.calendarIsVisible = false;
                break;

                case 'this_week':
                this.range.start = moment().startOf('week').toDate();
                this.range.end = moment().endOf('week').toDate();
                this.calendarIsVisible = false;
                break;

                case 'last_week':
                this.range.start = moment().startOf('week').subtract(7, "days").toDate();
                this.range.end = moment().endOf('week').subtract(7, "days").toDate();
                this.calendarIsVisible = false;
                break;

                case 'custom':
                this.calendarIsVisible = true;
                break;
            }

            this.onDateRangeChanged(this.range)
        },
        onDateRangeChanged(range) {
            this.$emit('update-range', range);
            // console.log('date range changed')
        },
        downloadExcel() {
            this.$emit('download-excel');
        }
    }
}
</script>
<style scoped>
.date-range-picker.vc-container.vc-blue{
    cursor: pointer;
    position: absolute;
    right: 10em;
    z-index: 9999;
}

</style>