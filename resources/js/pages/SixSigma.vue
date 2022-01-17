<template>
  <div class="container-fluid wrapper" style="top: 4em;">
    <div class="card-wrapper row">
      <div class="col-12 h-100">
        <div class="card shadow h-100">

          <div class="card-header">
            <div>Six Sigma Report</div>
            <div class="d-flex" style="z-index: 400">

              <div class="date-range-picker-wrap mt-3" style="float: right;">
                <v-date-picker class="date-range-picker" v-show="selectedRange.tag === 'custom'"
                               :input-props="{ style: `
                                background-color: #ffffff;
                                color: #3D3B30;
                                padding: 0.5rem;
                                border-radius: 0.25rem;
                                margin: 0.1rem 0;
                                width: 200px;
                                text-align: center;
                                cursor: pointer;
                                z-index: 300;
                            `
                             }"
                               is-range
                               v-model="range"
                               @input="onDateRangeChanged"
                >
                </v-date-picker>


                <div class="dropdown" style="z-index: 200;">
                  <button class="btn btn-light dropdown-toggle" type="button" id="dropdownMenuButton1" data-bs-toggle="dropdown" aria-expanded="false">
                    {{ selectedRange.title }}
                  </button>
                  <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="dropdownMenuButton1">
                    <li>
                      <a href="#" class="dropdown-item"
                         v-for="item in rangeSelections" @click="changeSelectedRange(item)">
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

            </div>
          </div>

          <div class="card-body y-scroll">
            <div class="row">
              <div v-for="data in sixSigmaData" class="progress-bar-wrapper">
                <div class="d-flex">
                  <div style="width: 30%;">
                    <span style="font-size: 18px; font-weight: 600;">{{ data.stationName }}</span>
                  </div>
                  <div class="six-sigma progress-bar">
                    <span style="position: absolute; margin: 0 0 0 12px; color: #fff;font-size: 18px;font-weight: 600;z-index: 2;">{{ data.quality }}%</span>
                    <span class="progress-bar-fill" :style="getWidthPercentageForSixSigma(data)"></span>
                    <div class="d-flex" style="
                    position: absolute;
                    top:0;
                    bottom: 0;
                    left: 0;
                    right: 0;
                    height: 50px;
                    text-align: left;
                    ">
                    <span style="width: 16.67%;border-right: 2px dashed white"></span>
                    <span style="width: 16.67%;border-right: 2px dashed white"></span>
                    <span style="width: 16.67%;border-right: 2px dashed white"></span>
                    <span style="width: 16.67%;border-right: 2px dashed white"></span>
                    <span style="width: 16.67%;border-right: 2px dashed white"></span>
                    <span style="width: 16.67%;border-right: 2px dashed white"></span>
                    </div>
                  </div>
                </div>
              </div>

            </div>
            <div class="row">
              <div class="progress-bar-wrapper">
                <div class="d-flex">
                  <div style="width: 30%;"></div>
                  <div class="six-sigma">
                    <div class="d-flex">
                      <span style="text-align:right;width: 16.67%;">1σ</span>
                      <span style="text-align:right;width: 16.67%;">2σ</span>
                      <span style="text-align:right;width: 16.67%;">3σ</span>
                      <span style="text-align:right;width: 16.67%;">4σ</span>
                      <span style="text-align:right;width: 16.67%;">5σ</span>
                      <span style="text-align:right;width: 16.67%;">6σ</span>
                    </div>
                  </div>
                </div>
              </div>
            </div>
          </div>

        </div>
      </div>
    </div>
  </div>
</template>

<script>
import sixSigmaService from "../services/SixSigmaService";
import moment from "moment";
export default {
  name: "SixSigma",
  components: {},
  data: () => ({
    sixSigmaData: null,
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
  }),
  methods: {
    fetchSixSigmaReport: function() {
      var vm = this;
      let data = {
        startTime: moment(this.selectedRangeX.start).format('YYYY-MM-DD'),
        endTime: moment(this.selectedRangeX.end).format('YYYY-MM-DD'),
      };
      sixSigmaService.getSixSigmaReport(data, response => {
        vm.sixSigmaData = response;
      }, error => {
        console.error(error);
      });
    },
    getWidthPercentageForSixSigma: function (data) {
      var sigmaValues = [0, 31, 69, 93.3, 99.38, 99.977, 99.99966];
      var quality = data.quality;
      // quality = 69;
      var lowerIndex = 0;
      if(quality <= 0) return "width:0%";
      for(var i = 0; i < sigmaValues.length; i++) {
        if(quality <= sigmaValues[i]) {
          lowerIndex = i - 1;
          break;
        }
      }
      var lowerValue = sigmaValues[lowerIndex];
      var upperValue = (lowerIndex === sigmaValues.length - 1)? lowerValue : sigmaValues[lowerIndex+1];
      var widthPercentage = (lowerIndex * 16.67) + ((16.67 * (quality - lowerValue)) / (upperValue - lowerValue));
      return "width:" + widthPercentage + "%";
    },
    dateSelected(){
      this.calendarIsVisible = false;
    },
    toggleCalendarVisibilityIfCustom(){
      if (this.filter === "custom"){
        this.calendarIsVisible = true;
      }
    },
    changeSelectedRange(item) {
      this.selectedRange = item;

      switch (item.tag) {
        case 'today':
          this.range.start = moment().startOf('day').toDate();
          this.range.end = moment().endOf('day').toDate();
          break;

        case 'yesterday':
          this.range.start = moment().subtract(1, "days").startOf('day').toDate();
          this.range.end = moment().subtract(1, "days").endOf('day').toDate();
          break;

        case 'this_week':
          this.range.start = moment().startOf('week').toDate();
          this.range.end = moment().endOf('week').toDate();
          break;

        case 'last_week':
          this.range.start = moment().startOf('week').subtract(7, "days").toDate();
          this.range.end = moment().endOf('week').subtract(7, "days").toDate();
          break;
      }

      this.onDateRangeChanged(this.range)
    },
    onDateRangeChanged(range) {
      this.fetchSixSigmaReport();
    },
  },
  mounted() {
    this.fetchSixSigmaReport();
  },
  destroyed() {
    // clearInterval(this.dataUpdateTimer);
  },
  computed: {
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
  }
}
</script>

<style scoped>
.progress-bar-wrapper {
  padding: 4px;
}

.progress-bar {
  position: relative;
  width: 100%;
  background-color: #a80202 !important;
  padding: 1px;
  box-shadow: inset 0 1px 3px rgba(0, 0, 0, .2);
  z-index: 0;
}

.progress-bar-fill {
  display: block;
  height: 50px;
  background-color: #025b0e;
  transition: width 500ms ease-in-out;
  z-index: 0;
}

.six-sigma {
  min-height: 67%;
  /*background:*/
  /*    linear-gradient(black 0 0) calc(1*100%/6),*/
  /*    linear-gradient(black 0 0) calc(2*100%/6),*/
  /*    linear-gradient(black 0 0) calc(3*100%/6),*/
  /*    linear-gradient(black 0 0) calc(4*100%/6),*/
  /*    linear-gradient(black 0 0) calc(5*100%/6),*/
  /*    linear-gradient(black 0 0) calc(6*100%/6);*/
  background-size:5px 100%; /* Control the width here*/
  background-repeat:no-repeat;
  float: right;
  width: 100%;
  z-index: 100!important;
}
</style>