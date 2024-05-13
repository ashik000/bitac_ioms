<template>
    <span>
        <div class="container-fluid wrapper" style="top: 4em; margin-bottom: 4.5rem;">
            <div class="card-wrapper row">
                <b-overlay :show="showInprogress" opacity="0.6" class="h-100">
                    <div class="col-12 h-100">
                        <MachiningList :items="records" sectionHeader="Machining Report" @update-range="updateRange" @download-excel="downloadExcel">
                            <template v-slot:columnHeaders>
                                <tr>
                                    <th style="width: 10%;">Station Name</th>
                                    <th style="width: 10%;">Program Name</th>
                                    <th style="width: 10%;">Power Status</th>
                                    <th style="width: 6%;">Spindle Speed</th>
                                    <th style="width: 6%;">Spindle Speed Active</th>
                                    <th style="width: 6%;">Feed Rate</th>
                                    <th style="width: 6%;">Feed Rate Active</th>
                                    <th style="width: 6%;">This Cycle</th>
                                    <th style="width: 6%;">M30 Counter 1</th>
                                    <th style="width: 6%;">M30 Counter 2</th>
                                    <th style="width: 6%;">Machining Mode</th>
                                    <th style="width: 6%;">Tool Life</th>
                                    <th style="width: 6%;">Load On</th>
                                    <th style="width: 10%;">Produced At</th>
                                </tr>
                            </template>
                            <div></div>
                            <template v-slot:row="{ row }">
                                <td>{{ row.station_name }}</td>
                                <td>{{ row.program_name }}</td>
                                <td>{{ row.power_status }}</td>
                                <td>{{ floatFixed(row.spindle_speed,2) }}</td>
                                <td>{{ floatFixed(row.spindle_speed_active,2) }}</td>
                                <td>{{ floatFixed(row.feed_rate,2) }}</td>
                                <td>{{ floatFixed(row.feed_rate_active,2) }}</td>
                                <td>{{ row.cycle_time }}</td>
                                <td>{{ row.production_counter1 }}</td>
                                <td>{{ row.production_counter2 }}</td>
                                <td>{{ row.machining_mode }}</td>
                                <td>{{ row.tool_life }}</td>
                                <td>{{ row.load_on_table }}</td>
                                <td>{{ row.produced_at }}</td>
                            </template>
                            <template v-slot:no-data>
                                <tr>
                                    <td colspan="5" class="text-center">No data found</td>
                                </tr>
                            </template>
                        </MachiningList>

                        <div class="col-12 mt-2" id="pagiX" v-cloak>
                            <pagination :records="recordsLength" v-model="page" :per-page="perPage" @paginate="fetchMachiningReport"></pagination>
                        </div>

                    </div>
                </b-overlay>
            </div>
        </div>
        <!-- modal -->
    </span>
</template>


<script>
    import MachiningList from "../../components/machining/MachiningList";
    import machiningService from '../../services/MachiningService';
    import groupMixin from '../../mixins/groupMixin';

    export default {
        name: "machiningReport",
        components: {MachiningList},
        mixins:[groupMixin],
        data: function () {
            return {
                page: 1,
                perPage: 100,
                records: [],
                recordsLength: 0,
                hide: true,
                showInprogress: false,
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
            };
        },
        methods: {
            fetchMachiningReport: function(page=1) {
                let data = {
                    startTime: moment(this.range.start).format('YYYY-MM-DD'),
                    endTime: moment(this.range.end).format('YYYY-MM-DD'),
                    page: page
                };
                this.showInprogress = true;
                machiningService.fetchAll(data, response => {
                    this.records = response.data;
                    this.recordsLength = response.total;
                    this.showInprogress = false;
                }, error => {
                    console.error(error);
                });
            },
            updateRange(range) {
                this.range.start = range.start;
                this.range.end = range.end;
                this.fetchMachiningReport();
            },
            downloadExcel() {
                let data = {
                    startTime: moment(this.range.start).format('YYYY-MM-DD'),
                    endTime: moment(this.range.end).format('YYYY-MM-DD'),
                };
                machiningService.downloadExcel(data);
            },
            floatFixed(number,point){
                if(number==null) return "";
                if(isNaN(number)) number = 0;
                return parseFloat(number).toFixed(point);
            }
        },
        mounted() {
            this.fetchMachiningReport();
        },
        computed: {
            displayedRecords() {
                const startIndex = this.perPage * (this.page - 1);
                const endIndex = startIndex + this.perPage;
                return this.records.slice(startIndex, endIndex);
            }
        },
    }
</script>

<style scoped>
    .VuePagination {
        display: flex;
        justify-content: center;
        text-align: center;
    }
    [v-cloak] {
        display: none;
    }
</style>
<style scoped>
.toast-title{font-weight:700}.toast-message{-ms-word-wrap:break-word;word-wrap:break-word}.toast-message a,.toast-message label{color:#FFF}.toast-message a:hover{color:#CCC;text-decoration:none}.toast-close-button{position:relative;right:-.3em;top:-.3em;float:right;font-size:20px;font-weight:700;color:#FFF;-webkit-text-shadow:0 1px 0 #fff;text-shadow:0 1px 0 #fff;opacity:.8;-ms-filter:progid:DXImageTransform.Microsoft.Alpha(Opacity=80);filter:alpha(opacity=80);line-height:1}.toast-close-button:focus,.toast-close-button:hover{color:#000;text-decoration:none;cursor:pointer;opacity:.4;-ms-filter:progid:DXImageTransform.Microsoft.Alpha(Opacity=40);filter:alpha(opacity=40)}.rtl .toast-close-button{left:-.3em;float:left;right:.3em}button.toast-close-button{padding:0;cursor:pointer;background:0 0;border:0;-webkit-appearance:none}.toast-top-center{top:0;right:0;width:100%}.toast-bottom-center{bottom:0;right:0;width:100%}.toast-top-full-width{top:0;right:0;width:100%}.toast-bottom-full-width{bottom:0;right:0;width:100%}.toast-top-left{top:12px;left:12px}.toast-top-right{top:12px;right:12px}.toast-bottom-right{right:12px;bottom:12px}.toast-bottom-left{bottom:12px;left:12px}#toast-container{position:fixed;z-index:999999;pointer-events:none}#toast-container *{-moz-box-sizing:border-box;-webkit-box-sizing:border-box;box-sizing:border-box}#toast-container>div{position:relative;pointer-events:auto;overflow:hidden;margin:0 0 6px;padding:15px 15px 15px 50px;width:300px;-moz-border-radius:3px;-webkit-border-radius:3px;border-radius:3px;background-position:15px center;background-repeat:no-repeat;-moz-box-shadow:0 0 12px #999;-webkit-box-shadow:0 0 12px #999;box-shadow:0 0 12px #999;color:#FFF;opacity:.8;-ms-filter:progid:DXImageTransform.Microsoft.Alpha(Opacity=80);filter:alpha(opacity=80)}#toast-container>div.rtl{direction:rtl;padding:15px 50px 15px 15px;background-position:right 15px center}#toast-container>div:hover{-moz-box-shadow:0 0 12px #000;-webkit-box-shadow:0 0 12px #000;box-shadow:0 0 12px #000;opacity:1;-ms-filter:progid:DXImageTransform.Microsoft.Alpha(Opacity=100);filter:alpha(opacity=100);cursor:pointer}#toast-container>.toast-info{background-image:url(data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAABgAAAAYCAYAAADgdz34AAAAAXNSR0IArs4c6QAAAARnQU1BAACxjwv8YQUAAAAJcEhZcwAADsMAAA7DAcdvqGQAAAGwSURBVEhLtZa9SgNBEMc9sUxxRcoUKSzSWIhXpFMhhYWFhaBg4yPYiWCXZxBLERsLRS3EQkEfwCKdjWJAwSKCgoKCcudv4O5YLrt7EzgXhiU3/4+b2ckmwVjJSpKkQ6wAi4gwhT+z3wRBcEz0yjSseUTrcRyfsHsXmD0AmbHOC9Ii8VImnuXBPglHpQ5wwSVM7sNnTG7Za4JwDdCjxyAiH3nyA2mtaTJufiDZ5dCaqlItILh1NHatfN5skvjx9Z38m69CgzuXmZgVrPIGE763Jx9qKsRozWYw6xOHdER+nn2KkO+Bb+UV5CBN6WC6QtBgbRVozrahAbmm6HtUsgtPC19tFdxXZYBOfkbmFJ1VaHA1VAHjd0pp70oTZzvR+EVrx2Ygfdsq6eu55BHYR8hlcki+n+kERUFG8BrA0BwjeAv2M8WLQBtcy+SD6fNsmnB3AlBLrgTtVW1c2QN4bVWLATaIS60J2Du5y1TiJgjSBvFVZgTmwCU+dAZFoPxGEEs8nyHC9Bwe2GvEJv2WXZb0vjdyFT4Cxk3e/kIqlOGoVLwwPevpYHT+00T+hWwXDf4AJAOUqWcDhbwAAAAASUVORK5CYII=)!important}#toast-container>.toast-error{background-image:url(data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAABgAAAAYCAYAAADgdz34AAAAAXNSR0IArs4c6QAAAARnQU1BAACxjwv8YQUAAAAJcEhZcwAADsMAAA7DAcdvqGQAAAHOSURBVEhLrZa/SgNBEMZzh0WKCClSCKaIYOED+AAKeQQLG8HWztLCImBrYadgIdY+gIKNYkBFSwu7CAoqCgkkoGBI/E28PdbLZmeDLgzZzcx83/zZ2SSXC1j9fr+I1Hq93g2yxH4iwM1vkoBWAdxCmpzTxfkN2RcyZNaHFIkSo10+8kgxkXIURV5HGxTmFuc75B2RfQkpxHG8aAgaAFa0tAHqYFfQ7Iwe2yhODk8+J4C7yAoRTWI3w/4klGRgR4lO7Rpn9+gvMyWp+uxFh8+H+ARlgN1nJuJuQAYvNkEnwGFck18Er4q3egEc/oO+mhLdKgRyhdNFiacC0rlOCbhNVz4H9FnAYgDBvU3QIioZlJFLJtsoHYRDfiZoUyIxqCtRpVlANq0EU4dApjrtgezPFad5S19Wgjkc0hNVnuF4HjVA6C7QrSIbylB+oZe3aHgBsqlNqKYH48jXyJKMuAbiyVJ8KzaB3eRc0pg9VwQ4niFryI68qiOi3AbjwdsfnAtk0bCjTLJKr6mrD9g8iq/S/B81hguOMlQTnVyG40wAcjnmgsCNESDrjme7wfftP4P7SP4N3CJZdvzoNyGq2c/HWOXJGsvVg+RA/k2MC/wN6I2YA2Pt8GkAAAAASUVORK5CYII=)!important}#toast-container>.toast-success{background-image:url(data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAABgAAAAYCAYAAADgdz34AAAAAXNSR0IArs4c6QAAAARnQU1BAACxjwv8YQUAAAAJcEhZcwAADsMAAA7DAcdvqGQAAADsSURBVEhLY2AYBfQMgf///3P8+/evAIgvA/FsIF+BavYDDWMBGroaSMMBiE8VC7AZDrIFaMFnii3AZTjUgsUUWUDA8OdAH6iQbQEhw4HyGsPEcKBXBIC4ARhex4G4BsjmweU1soIFaGg/WtoFZRIZdEvIMhxkCCjXIVsATV6gFGACs4Rsw0EGgIIH3QJYJgHSARQZDrWAB+jawzgs+Q2UO49D7jnRSRGoEFRILcdmEMWGI0cm0JJ2QpYA1RDvcmzJEWhABhD/pqrL0S0CWuABKgnRki9lLseS7g2AlqwHWQSKH4oKLrILpRGhEQCw2LiRUIa4lwAAAABJRU5ErkJggg==)!important}#toast-container>.toast-warning{background-image:url(data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAABgAAAAYCAYAAADgdz34AAAAAXNSR0IArs4c6QAAAARnQU1BAACxjwv8YQUAAAAJcEhZcwAADsMAAA7DAcdvqGQAAAGYSURBVEhL5ZSvTsNQFMbXZGICMYGYmJhAQIJAICYQPAACiSDB8AiICQQJT4CqQEwgJvYASAQCiZiYmJhAIBATCARJy+9rTsldd8sKu1M0+dLb057v6/lbq/2rK0mS/TRNj9cWNAKPYIJII7gIxCcQ51cvqID+GIEX8ASG4B1bK5gIZFeQfoJdEXOfgX4QAQg7kH2A65yQ87lyxb27sggkAzAuFhbbg1K2kgCkB1bVwyIR9m2L7PRPIhDUIXgGtyKw575yz3lTNs6X4JXnjV+LKM/m3MydnTbtOKIjtz6VhCBq4vSm3ncdrD2lk0VgUXSVKjVDJXJzijW1RQdsU7F77He8u68koNZTz8Oz5yGa6J3H3lZ0xYgXBK2QymlWWA+RWnYhskLBv2vmE+hBMCtbA7KX5drWyRT/2JsqZ2IvfB9Y4bWDNMFbJRFmC9E74SoS0CqulwjkC0+5bpcV1CZ8NMej4pjy0U+doDQsGyo1hzVJttIjhQ7GnBtRFN1UarUlH8F3xict+HY07rEzoUGPlWcjRFRr4/gChZgc3ZL2d8oAAAAASUVORK5CYII=)!important}#toast-container.toast-bottom-center>div,#toast-container.toast-top-center>div{width:300px;margin-left:auto;margin-right:auto}#toast-container.toast-bottom-full-width>div,#toast-container.toast-top-full-width>div{width:96%;margin-left:auto;margin-right:auto}.toast{background-color:#030303}.toast-success{background-color:#51A351}.toast-error{background-color:#BD362F}.toast-info{background-color:#2F96B4}.toast-warning{background-color:#F89406}.toast-progress{position:absolute;left:0;bottom:0;height:4px;background-color:#000;opacity:.4;-ms-filter:progid:DXImageTransform.Microsoft.Alpha(Opacity=40);filter:alpha(opacity=40)}@media all and (max-width:240px){#toast-container>div{padding:8px 8px 8px 50px;width:11em}#toast-container>div.rtl{padding:8px 50px 8px 8px}#toast-container .toast-close-button{right:-.2em;top:-.2em}#toast-container .rtl .toast-close-button{left:-.2em;right:.2em}}@media all and (min-width:241px) and (max-width:480px){#toast-container>div{padding:8px 8px 8px 50px;width:18em}#toast-container>div.rtl{padding:8px 50px 8px 8px}#toast-container .toast-close-button{right:-.2em;top:-.2em}#toast-container .rtl .toast-close-button{left:-.2em;right:.2em}}@media all and (min-width:481px) and (max-width:768px){#toast-container>div{padding:15px 15px 15px 50px;width:25em}#toast-container>div.rtl{padding:15px 50px 15px 15px}}
</style>
