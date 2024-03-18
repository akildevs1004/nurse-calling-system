<template>
  <div
    class="pl-1"
    style="width: 100%"
    v-if="can('dashboard_access') && can('dashboard_view')"
  >
    <v-row>
      <v-col cols="12">
        <CustomFilter
          style="float: right; padding-top: 5px; z-index: 9999"
          @filter-attr="filterAttr"
          :default_date_from="date_from"
          :default_date_to="date_to"
          :defaultFilterType="1"
          :height="'40px'"
        />
      </v-col>
      <v-col cols="2"
        ><v-card class="py-2" style="width: 100%; height: 150px">
          <v-row>
            <v-col cols="4">
              <v-row style="height: 150px">
                <v-col class="fill-height">
                  <v-card
                    elevation="0"
                    class="text-center d-flex flex-column align-center justify-center"
                    height="100%"
                  >
                    <v-icon size="50" color="#e1c719"
                      >mdi mdi-bell-outline</v-icon
                    >
                  </v-card>
                </v-col>
              </v-row>
            </v-col>
            <v-col cols="8">
              <v-row style="height: 150px">
                <v-col class="fill-height">
                  <v-card
                    elevation="0"
                    class="text-left d-flex flex-column align-left justify-center"
                    height="100%"
                  >
                    <div style="font-weight: bold; font-size: 30px">
                      {{ statistics.totalCount ?? "---" }}
                    </div>
                    <div>Total Events</div>
                  </v-card>
                </v-col>
              </v-row>
            </v-col>
          </v-row>
        </v-card></v-col
      >
      <v-col cols="2">
        <v-card class="py-2" style="width: 100%; height: 150px">
          <v-row>
            <v-col cols="4">
              <v-row style="height: 150px">
                <v-col class="fill-height">
                  <v-card
                    elevation="0"
                    class="text-center d-flex flex-column align-center justify-center"
                    height="100%"
                  >
                    <v-icon size="50" color="red">mdi mdi-bell-ring</v-icon>
                  </v-card>
                </v-col>
              </v-row>
            </v-col>
            <v-col cols="8">
              <v-row style="height: 150px">
                <v-col class="fill-height">
                  <v-card
                    elevation="0"
                    class="text-left d-flex flex-column align-left justify-center"
                    height="100%"
                  >
                    <div style="font-weight: bold; font-size: 30px">
                      {{ statistics.alarmCount ?? "---" }}
                    </div>
                    <div>Alarm Events</div>
                  </v-card>
                </v-col>
              </v-row>
            </v-col>
          </v-row>
        </v-card>
      </v-col>
      <v-col cols="2">
        <v-card class="py-2" style="width: 100%; height: 150px">
          <v-row>
            <v-col cols="4">
              <v-row style="height: 150px">
                <v-col class="fill-height">
                  <v-card
                    elevation="0"
                    class="text-center d-flex flex-column align-center justify-center"
                    height="100%"
                  >
                    <v-icon size="50" color="#00b050"
                      >mdi mdi-battery-alert</v-icon
                    >
                  </v-card>
                </v-col>
              </v-row>
            </v-col>
            <v-col cols="8">
              <v-row style="height: 150px">
                <v-col class="fill-height">
                  <v-card
                    elevation="0"
                    class="text-left d-flex flex-column align-left justify-center"
                    height="100%"
                  >
                    <div style="font-weight: bold; font-size: 30px">
                      {{ statistics.batteryCount ?? "---" }}
                    </div>
                    <div>Battery Events</div>
                  </v-card>
                </v-col>
              </v-row>
            </v-col>
          </v-row>
        </v-card>
      </v-col>

      <v-col cols="2">
        <v-card class="py-2" style="width: 100%; height: 150px">
          <v-row>
            <v-col cols="4">
              <v-row style="height: 150px">
                <v-col class="fill-height">
                  <v-card
                    elevation="0"
                    class="text-center d-flex flex-column align-center justify-center"
                    height="100%"
                  >
                    <v-icon size="50" color="#fede06"
                      >mdi mdi-clock-time-three</v-icon
                    >
                  </v-card>
                </v-col>
              </v-row>
            </v-col>
            <v-col cols="8">
              <v-row style="height: 150px">
                <v-col class="fill-height">
                  <v-card
                    elevation="0"
                    class="text-left d-flex flex-column align-left justify-center"
                    height="100%"
                  >
                    <div style="font-weight: bold; font-size: 30px">
                      {{
                        statistics.avgResponse != null
                          ? $dateFormat.minutesToHHMM(statistics.avgResponse)
                          : "---"
                      }}
                    </div>
                    <div>Avg Resposne TIme</div>
                  </v-card>
                </v-col>
              </v-row>
            </v-col>
          </v-row>
        </v-card>
      </v-col>
      <v-col cols="2">
        <v-card class="py-2" style="width: 100%; height: 150px">
          <v-row>
            <v-col cols="4">
              <v-row style="height: 150px">
                <v-col class="fill-height">
                  <v-card
                    elevation="0"
                    class="text-center d-flex flex-column align-center justify-center"
                    height="100%"
                  >
                    <v-icon size="50" color="green">mdi mdi-medal</v-icon>
                  </v-card>
                </v-col>
              </v-row>
            </v-col>
            <v-col cols="8">
              <v-row style="height: 150px">
                <v-col class="fill-height">
                  <v-card
                    elevation="0"
                    class="text-left d-flex flex-column align-left justify-center"
                    height="100%"
                  >
                    <div style="font-weight: bold; font-size: 30px">
                      {{
                        statistics.avgResponse != null
                          ? $dateFormat.minutesToHHMM(
                              statistics.fastestResponse
                            )
                          : "---"
                      }}
                    </div>
                    <div>Fastest Response</div>
                  </v-card>
                </v-col>
              </v-row>
            </v-col>
          </v-row>
        </v-card>
      </v-col>
      <v-col cols="2">
        <v-card class="py-2" style="width: 100%; height: 150px">
          <v-row>
            <v-col cols="4">
              <v-row style="height: 150px">
                <v-col class="fill-height">
                  <v-card
                    elevation="0"
                    class="text-center d-flex flex-column align-center justify-center"
                    height="100%"
                  >
                    <v-icon size="50" color="red">mdi mdi-watch</v-icon>
                  </v-card>
                </v-col>
              </v-row>
            </v-col>
            <v-col cols="8">
              <v-row style="height: 150px">
                <v-col class="fill-height">
                  <v-card
                    elevation="0"
                    class="text-left d-flex flex-column align-left justify-center"
                    height="100%"
                  >
                    <div style="font-weight: bold; font-size: 30px">
                      {{
                        statistics.avgResponse != null
                          ? $dateFormat.minutesToHHMM(
                              statistics.slowestResponse
                            )
                          : "---"
                      }}
                    </div>
                    <div>Slowest Response</div>
                  </v-card>
                </v-col>
              </v-row>
            </v-col>
          </v-row>
        </v-card>
      </v-col>
    </v-row>
    <v-row>
      <v-col cols="8">
        <!-- {{ AlarmDashboardHourlyChartheight }} -->
        <!--  :height="AlarmDashboardHourlyChartheight"-->
        <v-card>
          <v-card-text>
            <div>
              <AlarmDashboardHourlyChart
                :name="'AlarmDashboardHourlyChart'"
                :height="'400px'"
                :key="keyChart3"
                :date_from="date_from"
                :date_to="date_to"
              />
            </div>
          </v-card-text>
        </v-card>
        <!-- <div :height="AlarmDashboardHourlyChartheight"></div> -->
        <!-- <v-card style="height: 200px">
          <v-card-text> </v-card-text>
        </v-card> -->
      </v-col>
      <v-col cols="4">
        <v-card ref="AlamDashboardPieChartDiv">
          <v-card-text>
            <AlamDashboardPieChart
              :name="'AlamDashboardPieChart'"
              :height="'400px'"
              :key="keyChart2"
              :date_from="date_from"
              :date_to="date_to"
          /></v-card-text>
        </v-card>
      </v-col>
    </v-row>
  </div>

  <NoAccess v-else />
</template>

<script>
// import DashboardlastMultiStatistics from "../../components/dashboard2/DashboardlastMultiStatistics.vue";
import AlarmDashboardHourlyChart from "../../components/NurseCalling/AlarmDashboardHourlyChart.vue";
import AlamDashboardPieChart from "../../components/NurseCalling/AlamDashboardPieChart.vue";

import CustomFilter from "../../components/Snippets/CustomFilter.vue";
export default {
  components: {
    AlarmDashboardHourlyChart,
    CustomFilter,
    AlamDashboardPieChart,
  },
  data() {
    return {
      AlarmDashboardHourlyChartheight: 0,
      device_serial_number: "",
      keyChart2: 0,
      keyChart3: 0,
      date_from: "",
      date_to: "",
      statistics: {},
    };
  },
  watch: {},
  mounted() {
    //this.adjustGraphHeight();
  },
  created() {
    // Get today's date
    let today = new Date();

    let sevenDaysAgo = new Date(today);
    sevenDaysAgo.setDate(today.getDate() - 0);
    // Format the dates (optional)
    this.date_to = today.toISOString().split("T")[0];
    this.date_from = sevenDaysAgo.toISOString().split("T")[0];
    this.getDataFromApi();
    // this.display_title =
    //   "Attendance : " + this.date_from + " to " + this.date_to;
  },
  // watch: {
  //   overlay(val) {
  //     val &&
  //       setTimeout(() => {
  //         this.overlay = false;
  //       }, 3000);
  //   },
  // },
  methods: {
    can(per) {
      return this.$pagePermission.can(per, this);
    },
    adjustGraphHeight() {
      setTimeout(() => {
        if (this.$refs.AlamDashboardPieChartDiv) {
          console.log(
            "this.$refs.AlamDashboardPieChartDiv",
            this.$refs.AlamDashboardPieChartDiv
          );
          let height = this.$refs.AlamDashboardPieChartDiv.$el.clientHeight;
          console.log("height", height);
          this.AlarmDashboardHourlyChartheight = height - 100;

          //this.keyChart3++;
        }
      }, 4000);
    },
    filterAttr(data) {
      this.date_from = data.from;
      this.date_to = data.to;
      this.keyChart2++;
      this.keyChart3++;
      this.getDataFromApi();
      //this.adjustGraphHeight();
      //this.filterType = "Monthly";
    },

    getDataFromApi() {
      let options = {
        params: {
          per_page: 1000,
          company_id: this.$auth.user.company_id,

          date_from: this.date_from,
          date_to: this.date_to,
        },
      };

      this.$axios
        .get(`/alarm_dashboard_get_statistics`, options)
        .then(({ data }) => {
          this.statistics = data;
        });
    },
  },
};
</script>
