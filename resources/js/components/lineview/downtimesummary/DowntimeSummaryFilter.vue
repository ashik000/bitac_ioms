<template>
    <!--<div class="category-filter" :class="{'category-filter-selected': isSelected}" @click="isSelected = !isSelected">-->
    <div class="category-filter" :class="{'category-filter-selected': this.type === this.currentlySelected}" @click="filterSelected">
        <div class="row">
            <div class="col-sm-3">
                <svg class="bd-placeholder-img rounded-circle" width="50" height="50" focusable="false" >
                    <rect width="100%" height="100%" :fill="circleBackgroundColor"></rect>
                    <text x="50%" y="45%" dominant-baseline="middle" text-anchor="middle" fill="#dee2e6" dy=".3em">{{ count }}</text>
                </svg>
            </div>
            <div class="col-sm-9" style="text-align: left">
                <p style="font-size: 18px; margin-bottom:0;" :style=" { color : textColor}">{{filterTitle}}</p>
                <small :style="{ color: textColor }"> Total Hour {{ totalHourText }}</small>
            </div>
        </div>
    </div>
</template>

<script>
    import {formatDuration} from '../../../utils';
    export default {
        name: "DowntimeSummaryFilter.vue",
        data: ()=>{
            return {
                isSelected: false
            };
        },
        props: {
            type: String,
            count: Number,
            totalHour: Number,
            currentlySelected: String
        },
        methods:{
            filterSelected(){
                this.isSelected = true;
                this.$emit("downtimeFilterSelected", {
                    'type': this.type,
                    'isSelected': this.isSelected
                });
            }
        },
        computed: {
            filterTitle() {
                if (this.type === 'uncommented'){
                    return "Uncommented";
                } else if (this.type === 'planned') {
                    return "Planned";
                } else if (this.type === 'unplanned') {
                    return "Unplanned";
                } else {
                    return "All";
                }
            },
            totalHourText(){
                return formatDuration(this.totalHour);
            },
            circleBackgroundColor(){
                if (this.type === 'uncommented'){
                    return "#868e96";
                } else if (this.type === 'planned') {
                    return "#009fff";
                } else if (this.type === 'unplanned') {
                    return "#FF0000";
                } else {
                    return "#000000";
                }
            },
            textColor(){
                if (this.type === 'uncommented'){
                    return "#000000";
                } else if (this.type === 'planned') {
                    return "#000000";
                } else {
                    return "#000000";
                }
            }
        }
    }
</script>

<style scoped>
    .category-filter {
        padding: 20px 0px 20px 10px !important;
        background: #e7e9ec;
        border: 1px solid #dbdddf;
    }

    .category-filter:hover {
        -webkit-box-shadow: 1px 2px 5px -1px rgba(0, 0, 0, 0.5);
        -moz-box-shadow: 1px 2px 5px -1px rgba(0, 0, 0, 0.5);
        box-shadow: 1px 2px 5px -1px rgba(0, 0, 0, 0.5);
    }

    .category-filter-selected {
        background: #ffffff;
    }
</style>
