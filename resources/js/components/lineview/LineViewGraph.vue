<template>
    <div class="table-responsive">
        <button class="btn" v-on:click="zoomIn()" style="background-color: #00b895; color: #fff; outline: none;">
            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-zoom-in" viewBox="0 0 16 16">
                <path fill-rule="evenodd" d="M6.5 12a5.5 5.5 0 1 0 0-11 5.5 5.5 0 0 0 0 11zM13 6.5a6.5 6.5 0 1 1-13 0 6.5 6.5 0 0 1 13 0z"/>
                <path d="M10.344 11.742c.03.04.062.078.098.115l3.85 3.85a1 1 0 0 0 1.415-1.414l-3.85-3.85a1.007 1.007 0 0 0-.115-.1 6.538 6.538 0 0 1-1.398 1.4z"/>
                <path fill-rule="evenodd" d="M6.5 3a.5.5 0 0 1 .5.5V6h2.5a.5.5 0 0 1 0 1H7v2.5a.5.5 0 0 1-1 0V7H3.5a.5.5 0 0 1 0-1H6V3.5a.5.5 0 0 1 .5-.5z"/>
            </svg>
        </button>
        <button class="btn" v-on:click="zoomOut()" style="background-color: #00b895; color: #fff; outline: none;">
            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-zoom-out" viewBox="0 0 16 16">
                <path fill-rule="evenodd" d="M6.5 12a5.5 5.5 0 1 0 0-11 5.5 5.5 0 0 0 0 11zM13 6.5a6.5 6.5 0 1 1-13 0 6.5 6.5 0 0 1 13 0z"/>
                <path d="M10.344 11.742c.03.04.062.078.098.115l3.85 3.85a1 1 0 0 0 1.415-1.414l-3.85-3.85a1.007 1.007 0 0 0-.115-.1 6.538 6.538 0 0 1-1.398 1.4z"/>
                <path fill-rule="evenodd" d="M3 6.5a.5.5 0 0 1 .5-.5h6a.5.5 0 0 1 0 1h-6a.5.5 0 0 1-.5-.5z"/>
            </svg>
        </button>

        <button class="btn" v-on:click="panLeft()" style="background-color: #00b895; color: #fff; outline: none;">
            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-chevron-left" viewBox="0 0 16 16">
                <path fill-rule="evenodd" d="M11.354 1.646a.5.5 0 0 1 0 .708L5.707 8l5.647 5.646a.5.5 0 0 1-.708.708l-6-6a.5.5 0 0 1 0-.708l6-6a.5.5 0 0 1 .708 0z"/>
            </svg>
        </button>
        <button class="btn" v-on:click="panRight()" style="background-color: #00b895; color: #fff; outline: none;">
            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-chevron-right" viewBox="0 0 16 16">
                <path fill-rule="evenodd" d="M4.646 1.646a.5.5 0 0 1 .708 0l6 6a.5.5 0 0 1 0 .708l-6 6a.5.5 0 0 1-.708-.708L10.293 8 4.646 2.354a.5.5 0 0 1 0-.708z"/>
            </svg>
        </button>


        <input type="time" :value="defects.defectTime" />
        <button class="btn btn-danger px-2" v-on:click="decreaseDefect()">-</button>
        <input type="number" class="defect_value" :value="defects.defectValue" />
        <button class="btn btn-danger px-2" v-on:click="increaseDefect()">+</button>
        <button class="btn btn-danger" v-on:click="reportDefect()">Report Defect</button>


        <select class="form-group" name="changeStationShift" id="changeStationShift"
                v-model="selectedStationShiftId" @change="onSelectChange($event)"
                style="float: right">
            <option v-for="stationShift in stationShiftsData" :value="stationShift.shift_id" :key="stationShift.shift_id">{{ stationShift.shift_name }}</option>
        </select>

        <table class="line-view-graph">
            <thead>
            <tr>
                <th style="width: 2%">Hour</th>
<!--                <th class="availability-column">:00</th>-->
<!--                <th class="availability-column"></th>-->
<!--                <th class="availability-column"></th>-->
<!--                <th class="availability-column">:15</th>-->
<!--                <th class="availability-column"></th>-->
<!--                <th class="availability-column"></th>-->
<!--                <th class="availability-column">:30</th>-->
<!--                <th class="availability-column"></th>-->
<!--                <th class="availability-column"></th>-->
<!--                <th class="availability-column">:45</th>-->
<!--                <th class="availability-column"></th>-->
<!--                <th class="availability-column"></th>-->

                <th v-bind:style="{width: headerWidth + '%' }" v-for="i in colspan[zoomIndex]" class="availability-column">{{barLabel(i-1)}}</th>

                <th style="width: 10%">Performance</th>

                <th style="width: 5%">Defect</th>
            </tr>
            </thead>
            <tbody>
            <tr v-for="logs in linedata" :key="logs.hour">
                <td style="width: 1%" class="hour-marker">{{ `${logs.hour}`.padStart(2, '0') }}</td>
                <td :colspan="colspan[zoomIndex]" class="hour-availability">
                    <span class="downtime-title" :key="'reason-' + bar.id"
                          v-for="bar in logs.data"
                          v-if="bar.type == 'downtime' && bar.reason"
                          :style="{ left: `${bar.second_of_hour / 36}%`, width: `${bar.duration / 36}%` }"
                          @click="$emit('downtime-clicked', bar)"
                          :title="`Start: ${formatStartTime(bar.start_time)}, Duration: ${formatDuration(bar.duration)}, ${bar.reason ? bar.reason.name : ''}`">
                        {{ bar.reason.name }}
                    </span>
                        <svg xmlns="http://www.w3.org/2000/svg" version="1.1" class="bar"
                            :viewBox="viewBoxAttributes" style="background: #222056;" preserveAspectRatio="none">
                            <template v-for="bar in logs.data">
                                <rect v-if="bar.type == 'log'" :key="bar.type + '-' + bar.id"
                                      :x="bar.second_of_hour"
                                      :width="bar.duration-.2"
                                      :fill="barColor(bar)"
                                      y="0" height="10"
                                ></rect>
                                <rect v-if="bar.type == 'log'" :key="'divider-' + bar.id"
                                      :x="bar.second_of_hour-0.2"
                                      width="0.2"
                                      fill="#000000"
                                      y="0" height="10"></rect>

                                <rect v-else :key="bar.type + '-' + bar.id"
                                      class="data-point"
                                      :x="bar.second_of_hour"
                                      :width="bar.duration"
                                      :fill="barColor(bar)"
                                      y="0" height="10"
                                      @click="$emit('downtime-clicked', bar)"
                                >
                                    <title>Start: {{ formatStartTime(bar.start_time) }}, Duration: {{formatDuration(bar.duration)}}, {{bar.reason ? bar.reason.name : ''}}</title>
                                </rect>
                        </template>
                    </svg>


                </td>
                <td style="width: 10%" class="hour-metric">
                    <div>
                        <span
                            class="produced"
                            :class="{ 'text-danger': (logs.produced * 100 / logs.expected) <= logs.performance_threshold  }">
                            {{ logs.produced }}
                        </span>
                        <span class="expected">{{ logs.expected }}</span>
                    </div>
                </td>
                <td>
                    <div>
                        <span class="defect_product">
                            {{ logs.scrapped }}
                        </span>
                    </div>
                </td>
            </tr>
            </tbody>
        </table>
    </div>
</template>

<script>
    export default {
        name: "LineViewGraph",
        props: {
            linedata: {
                type: Array
            },
            stationShiftsData: {
                type: Array
            }
        },
        computed: {
            headerWidth() {
                return 88/this.colspan[this.zoomIndex];
            }
        },
        data: () => ({
            showDowntimeReasonsModal: false,
            selectedReasonId: null,
            downtimeComment: null,
            bar: null,
            reasons: [],
            viewBoxAttributes: '0 0 3600 10',
            viewBox : {x:0,y:0,w:3600,h:10},
            colspan : [12, 6, 3, 2, 2, 2], //1hr,30min,15min,10min,5min,1min
            labelMultipliers: [5, 5, 5, 5, 2.5, 0.5],
            labelAdders: [0, 30, 15, 10, 5, 1],
            zoomValues : [3600, 1800, 900, 600, 300, 60],
            zoomIndex : 0,
            pageNo: 0,
            selectedStationShiftId: null,
            updatedDefectVal: null,
            defects: {
                defectValue: 1,
                defectDate: new Date(),
                defectTime: '00:00:00',
            },
        }),
        methods: {
            reportDefect (event) {
                this.$emit('reportDefects', this.defects);
            },
            increaseDefect() {
                this.defects.defectValue++;
                // console.log(this.defects.defectValue);
            },
            decreaseDefect() {
                if (this.defects.defectValue >= 1) {
                    this.defects.defectValue--;
                }
            },
            onSelectChange(event) {
                this.selectedStationShiftId = event.target.value;
                this.$emit('stationshift-selected', this.selectedStationShiftId);
            },
            barColor(item) {
                if(item.type === 'log') return '#2dc630';
                if(item.type === 'slow') return '#d8d213';
                if(item.type === 'downtime'){
                    if(item.reason && item.reason.type && item.reason.type === 'planned'){
                        return '#0077D8';
                    } else {
                        return '#C92E38';
                    }
                }
            },
            formatStartTime(time) {
                return moment(time).format('hh:mm:ss');
            },
            formatDuration(duration) {
                return moment.duration(duration, 'seconds').format('hh:mm:ss');
            },
            barLabel(i) {
                // return (((this.zoomValues[this.zoomIndex]/60)/this.colspan[this.zoomIndex]) * i);
                return (this.labelMultipliers[this.zoomIndex] * i) + this.labelAdders[this.zoomIndex] * this.pageNo;
            },
            zoomIn() {
                this.pageNo = 0;
                this.viewBox.x = 0;
                this.zoomIndex = Math.min(this.zoomIndex+1, this.zoomValues.length-1);
                this.viewBox.w = this.zoomValues[this.zoomIndex];
                this.applyViewBoxAttributes();
            },
            zoomOut() {
                this.viewBox.x = 0;
                this.pageNo = 0;
                this.zoomIndex = Math.max(this.zoomIndex-1, 0);
                this.viewBox.w = this.zoomValues[this.zoomIndex];
                this.applyViewBoxAttributes();
            },
            panRight() {
                this.pageNo = Math.min(this.pageNo+1, (3600/this.zoomValues[this.zoomIndex])-1);
                console.log(this.pageNo);
                this.viewBox.x = Math.min(this.zoomValues[0] - this.zoomValues[this.zoomIndex], this.viewBox.x+this.zoomValues[this.zoomIndex]);
                this.applyViewBoxAttributes();
            },
            panLeft() {
                // console.log(this.pageNo);
                this.pageNo = Math.max(0, this.pageNo-1);
                this.viewBox.x = Math.max(0, this.viewBox.x-this.zoomValues[this.zoomIndex]);
                this.applyViewBoxAttributes();
            },
            applyViewBoxAttributes() {
                // let allSvgs = document.getElementsByTagName('svg');
                // for (var i = 0; i < allSvgs.length; i++) {
                //     let topSvg = allSvgs[i];
                //     topSvg.setAttribute('viewBox', `${this.viewBox.x} ${this.viewBox.y} ${this.viewBox.w} ${this.viewBox.h}`);
                // }
                this.viewBoxAttributes = this.viewBox.x + " " + this.viewBox.y + " " + this.viewBox.w + " " + this.viewBox.h;
            },
        },
        mounted() {
            let today = new Date();
            let hour = today.getHours();
            let min = today.getMinutes();
            if (min < 10 && min > 0) {
                min = '0' + min;
            }
            if (hour < 10 && hour > 0) {
                hour = '0' + hour;
            }
            // console.log('today '+today);
            this.defects.defectTime = hour + ":" + min;
        },
        updated() {
            // console.log('updated');
            // console.log(this.stationShiftsData);
            if (this.selectedStationShiftId == null) {
                this.selectedStationShiftId = this.stationShiftsData[0].shift_id;
            }
        }
    }
</script>

<style scoped lang="scss">
    @import '#/app.scss';

    table.line-view-graph {
        @extend .table, .table-bordered;

        border: 0.5px solid grey !important;

        overflow: hidden;

        th, td {
            position: relative;
            padding: 0.25rem 0.75rem;

            border: 0.5px solid grey !important;

            color: #000;
            font-size: 1.2rem;
            font-weight: normal;
        }

        thead tr {
            position: sticky;
            background: rgba(255, 255, 255, .1);
        }

        tbody {
            overflow: auto;
        }

        .availability-column {
            position: relative;

            border: none !important;

            &:after {
                content: "";
                display: block;

                position: absolute;
                width: 1px;
                height: 1200px;

                top: 100%;
                right: -1px;
                z-index: 10;

                border: 1px dashed #ffffff;
            }

            &:nth-of-type(3n + 2) {
                border-left: 1px solid #ffffff !important;
            }

            &:nth-of-type(3n + 1) {
                &:after {
                    border: 1px solid #ffffff;
                }
            }
        }

        .hour-marker {
            text-align: center;
            font-weight: bold;
        }

        .hour-availability {
            position: relative;
            padding-left: 0;
            padding-right: 0;

            .downtime-title {
                position: absolute;
                text-align: left;
                display: block;
                white-space: nowrap;
                text-overflow: ellipsis;
                overflow:hidden;
            }

            svg {
                &.bar {
                    background: #222056;
                    overflow: hidden;
                    width: 100%;
                    height: 2rem;
                }

                &.buttons {
                    position: absolute;
                    left: 0.55rem;
                    bottom: 0.5rem;
                }

                .data-point {
                    cursor: pointer;
                }
            }
        }

        .hour-metric {
            position: relative;

            > div {
                position: relative;
                display: flex;
                flex-direction: row;
                justify-content: space-between;

                .produced,
                .expected {
                    display: inline-block;
                    text-align: right;
                }

                .produced {
                    flex-grow: 1;
                }

                .expected {
                    &:before {
                        content: "/";
                        display: inline-block;

                        padding-left: 1rem;
                        padding-right: 1rem;
                    }
                }
            }
        }
    }

    .section {
        .section-header {
            padding: 0.5rem 1rem;
            border: 1px solid darken(wheat, 20);
            background: wheat;
        }

        .section-list {
            list-style: none;
            padding: 0;

            li {
                padding: 0.5rem 1rem;

                &.list-header {
                    color: #000;
                }
            }
        }

        .section-table {
            thead {
            }
        }
    }
</style>
