<template>
    <div class="table-responsive">

        <div class="d-flex justify-content-between">

            <div class="d-flex">

                <div class="btn-group me-4">
                    <button class="btn rounded-0" v-on:click="zoomIn()" style="background-color: #00b895; color: #fff; outline: none; border-right: 2px solid #09C8A4 !important;">
                        <b-icon-zoom-in></b-icon-zoom-in>
                    </button>
                    <button class="btn rounded-0 me-2" v-on:click="zoomOut()" style="background-color: #00b895; color: #fff; outline: none;">
                        <b-icon-zoom-out></b-icon-zoom-out>
                    </button>

                    <button class="btn rounded-0" v-on:click="panLeft()" style="background-color: #00b895; color: #fff; outline: none; border-right: 2px solid #09C8A4 !important;">
                        <b-icon-chevron-left></b-icon-chevron-left>
                    </button>
                    <button class="btn rounded-0" v-on:click="panRight()" style="background-color: #00b895; color: #fff; outline: none;">
                        <b-icon-chevron-right></b-icon-chevron-right>
                    </button>
                </div>

                <div class="input-group rounded-3" style="padding: 0.25rem; background-color: rgba(245, 66, 66, 0.25);">
                    <input type="time"  class="form-control rounded-3 me-2" :value="defects.defectTime" style="border: 1px solid red;"/>
                    <button class="btn left-radius" v-on:click="decreaseDefect()" style="background-color: #FF0D0D; color: #FFFFFF;">
                        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-dash-lg" viewBox="0 0 16 16">
                            <path d="M0 8a1 1 0 0 1 1-1h14a1 1 0 1 1 0 2H1a1 1 0 0 1-1-1z"/>
                        </svg>
                    </button>

                    <input type="number" class="defect_value form-control text-center" :value="defects.defectValue" min="0" style="border: 1px solid red;"/>
                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" viewBox="0 0 16 16" class="bi bi-box-seam" style="position: absolute;left: 16.8em;top: .95em;z-index: 3;">
                        <path d="M8.186 1.113a.5.5 0 0 0-.372 0L1.846 3.5l2.404.961L10.404 2l-2.218-.887zm3.564 1.426L5.596 5 8 5.961 14.154 3.5l-2.404-.961zm3.25 1.7-6.5 2.6v7.922l6.5-2.6V4.24zM7.5 14.762V6.838L1 4.239v7.923l6.5 2.6zM7.443.184a1.5 1.5 0 0 1 1.114 0l7.129 2.852A.5.5 0 0 1 16 3.5v8.662a1 1 0 0 1-.629.928l-7.185 2.874a.5.5 0 0 1-.372 0L.63 13.09a1 1 0 0 1-.63-.928V3.5a.5.5 0 0 1 .314-.464L7.443.184z"></path>
                    </svg>
                    <button class="btn right-radius" v-on:click="increaseDefect()" style="background-color: #FF0D0D; color: #FFFFFF;">
                        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-plus-lg" viewBox="0 0 16 16">
                            <path d="M8 0a1 1 0 0 1 1 1v6h6a1 1 0 1 1 0 2H9v6a1 1 0 1 1-2 0V9H1a1 1 0 0 1 0-2h6V1a1 1 0 0 1 1-1z"/>
                        </svg>
                    </button>
                    <button class="btn ms-3 rounded-3" v-on:click="reportDefect()" style="background-color: #BB2000; color: #FFFFFF;">Report Defect</button>
                    <b-overlay :show="showInprogress" opacity="0.6" no-wrap></b-overlay>
                </div>
            </div>

            <select class="form-group rounded-0" name="changeStationShift" id="changeStationShift"
                    v-model="selectedStationShiftId" @change="onSelectChange($event)"
                    style="background-color: rgb(0, 184, 149); color: rgb(255, 255, 255); outline: none;">
                <option v-for="stationShift in stationShiftsData" :value="stationShift.shift_id" :key="stationShift.shift_id">{{ stationShift.shift_name }}</option>
            </select>
        </div>

        <div class="table-container y-scroll mt-2">
            <table class="line-view-graph">
                <thead>
                <tr class="text-center">
                    <th style="width: 2%; color: #009FFF;">HOUR</th>
                    <th v-bind:style="{width: headerWidth + '%' }" v-for="i in colspan[zoomIndex]" class="availability-column">{{barLabel(i-1)}}</th>

                    <th style="width: 10%; color: #49B92D;">PERFORMANCE</th>

                    <th style="width: 5%; color: #FF0D0D;">DEFECT</th>
                </tr>
                </thead>
                <tbody>
                <tr v-for="logs in linedata" :key="logs.hour">
                    <td style="width: 1%" class="hour-marker">{{ `${logs.hour}`.padStart(2, '0') }}</td>
                    <td :colspan="colspan[zoomIndex]" class="hour-availability text-white">
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
                    <td style="width: 10%" class="hour-metric text-center">
                        <div class="d-inline">
                        <span
                            class="produced"
                            :class="{ 'text-danger': (logs.produced * 100 / logs.expected) <= logs.performance_threshold  }">
                            {{ logs.produced }}
                        </span>/
                            <span class="expected">{{ logs.expected }}</span>
                        </div>
                    </td>
                    <td :style="{ color: logs.scrapped ? '#FF0D0D' : ''}" class="text-center">
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
                defectValue: 0,
                defectDate: new Date(),
                defectTime: '00:00:00',
            },
            currentTime: '',
            showInprogress: false,
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
            // this.$data._clock = () => {
            // };
            this.currentTime = moment().format('HH:mm');

            // console.log('current time')
            // console.log(this.currentTime)

            // setInterval(this.$data._clock, 300000);
        },
        updated() {
            if (this.selectedStationShiftId === null && this.stationShiftsData.length > 0) {
                this.selectedStationShiftId = this.stationShiftsData[0].shift_id;
                this.$emit('stationshift-selected', this.selectedStationShiftId);
            }

            this.defects.defectTime = this.currentTime;
        }
    }
</script>

<style scoped lang="scss">
    @import '#/app.scss';

    table.line-view-graph {
        @extend .table, .table-bordered;

        border: 0.5px solid #E5EDF4 !important;

        overflow: hidden;

        th, td {
            position: relative;
            padding: 0.25rem 0.75rem;

            border: 0.5px solid #E5EDF4 !important;

            color: #717F87;
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

            //border: none !important;

            &:after {
                content: "";
                display: block;

                position: absolute;
                width: 1px;
                height: 1200px;

                top: 100%;
                right: -1px;
                z-index: 10;

                //border: 1px dashed #ffffff;
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

    .left-radius{
        border-top-left-radius: .3rem !important;
        border-bottom-left-radius: .3rem !important;
    }

    .right-radius{
        border-top-right-radius: .3rem !important;
        border-bottom-right-radius: .3rem !important;
    }

    .table-container{border: 2px solid #C5E9FF;}

    @media only screen and (max-width: 3800px) {
        .table-container{
            height: 30rem;
        }
    }

    @media only screen and (max-width: 1600px) {
        .table-container{
            height: 18rem;
        }
    }
</style>
