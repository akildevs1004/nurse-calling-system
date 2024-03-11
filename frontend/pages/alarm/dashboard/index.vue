<template>
  <div
    style="width: 100%"
    v-if="can('dashboard_access') && can('dashboard_view')"
  >
    <v-overlay :value="overlay">
      <v-progress-circular indeterminate size="64"></v-progress-circular>
    </v-overlay>
    <v-row>
      <v-col
        style="
          position: fixed;
          top: -7px;
          z-index: 9999;
          margin-left: 20%;
          width: 50%;
        "
      >
        <v-bottom-navigation
          :value="topMenu"
          color="primary"
          style="width: 100%; box-shadow: none; background: transparent"
          elevation="0"
        >
          <v-btn
            :key="index"
            v-if="device.serial_number != null"
            @click="ChangeDevice(device.serial_number)"
            v-for="(device, index) in devicesList"
            style="width: auto; max-width: 100%"
            width="auto"
          >
            <span style="font-size: 15px">{{ device.name }}</span>
          </v-btn>
        </v-bottom-navigation>
      </v-col>
    </v-row>
    <v-row>
      <v-col cols="12" class="pt-5">
        <v-row style="width: 100%">
          <v-col lg="3" md="3" sm="12" xs="12">
            <v-row style="width: 100%; height: 250px">
              <v-card class="py-2" style="width: 100%">
                <v-row>
                  <v-col cols="8"
                    ><h3 class="pl-5">Today Temperature</h3></v-col
                  >
                  <v-col cols="4" class="pull-right"
                    ><v-icon @click="getDataFromApi(1)" style="float: right"
                      >mdi mdi-reload</v-icon
                    >
                  </v-col>
                </v-row>
                <v-col
                  lg="12"
                  md="12"
                  style="text-align: center; padding-top: 0px"
                >
                  <v-row>
                    <v-col
                      cols="2"
                      class="align-items-center justify-content-center pt-10"
                      ><img
                        src="../../../static/alarm-icons/temperature.png"
                        width="100px"
                    /></v-col>
                    <v-col cols="10">
                      <AlarmDashboardTemparatureChart1
                        :branch_id="branch_id"
                        :name="'AlarmDashboardTemparatureChart1'"
                        :height="'180'"
                        :temperature_latest="temperature_latest"
                        :temperature_date_time="temperature_date_time"
                        :key="key"
                      />
                    </v-col>
                  </v-row>
                </v-col>
              </v-card>
            </v-row>
          </v-col>
          <v-col lg="2" md="2" sm="12" xs="12">
            <v-row style="width: 100%; height: 260px">
              <v-card class="py-2" style="width: 100%">
                <h3 class="pl-5">Today</h3>
                <div
                  lg="12"
                  md="12"
                  style="text-align: center; padding-top: 0px; margin-top: -5px"
                >
                  <div style="text-align: center; font-size: 20px">Min</div>
                  <div class="bold text-h4 green--text">
                    <span v-html="temperature_min"></span>
                  </div>
                  <span
                    style="font-size: 10px"
                    v-if="temperature_min_date_time != '---'"
                  >
                    At : {{ temperature_min_date_time }}
                  </span>
                </div>

                <div
                  class="pt-5"
                  lg="12"
                  md="12"
                  style="text-align: center; padding-top: 0px"
                >
                  <div style="text-align: center; color: red; font-size: 20px">
                    Max
                  </div>
                  <div class="bold text-h4 red--text">
                    <span v-html="temperature_max"></span>
                  </div>
                  <span
                    style="font-size: 10px"
                    v-if="temperature_max_date_time != '---'"
                  >
                    At : {{ temperature_max_date_time }}</span
                  >
                </div>
                <div style="font-size: 10px; text-align: center">
                  Last Fire Alarm: {{ fire_alarm_start_datetime }}
                </div>
              </v-card>
            </v-row>
          </v-col>
          <v-col lg="7" md="7" sm="12" xs="12">
            <v-row style="width: 100%; height: 260px">
              <v-card class="py-2" style="width: 100%">
                <v-col
                  lg="12"
                  md="12"
                  style="text-align: center; padding-top: 0px"
                >
                  <AlarmDashboardTemparatureChart2
                    :name="'AlarmDashboardTemparatureChart2'"
                    :height="'220'"
                    :device_serial_number="device_serial_number"
                    :key="keyChart2"
                  />
                </v-col>
              </v-card> </v-row
          ></v-col>
        </v-row>
      </v-col>
      <v-col cols="12" class="pt-5">
        <v-row style="width: 100%">
          <v-col lg="3" md="3" sm="12" xs="12">
            <v-row style="width: 100%; height: 250px">
              <v-card class="py-2" style="width: 100%">
                <v-row>
                  <v-col cols="8"><h3 class="pl-5">Today Humidity</h3></v-col>
                  <v-col cols="4" class="pull-right"
                    ><v-icon @click="getDataFromApi()" style="float: right"
                      >mdi mdi-reload</v-icon
                    >
                  </v-col>
                </v-row>
                <v-col
                  lg="12"
                  md="12"
                  style="text-align: center; padding-top: 0px"
                >
                  <v-row>
                    <v-col
                      cols="2"
                      class="align-items-center justify-content-center pt-10"
                      ><img
                        src="../../../static/alarm-icons/humidity.png"
                        width="100px"
                    /></v-col>
                    <v-col cols="10">
                      <AlarmDashboardHumidityChart1
                        :branch_id="branch_id"
                        :name="'AlarmDashboardHumidityChart1'"
                        :height="'200'"
                        :humidity_latest="humidity_latest"
                        :humidity_date_time="humidity_date_time"
                        :key="key"
                      />
                    </v-col>
                  </v-row>
                </v-col>
              </v-card>
            </v-row>
          </v-col>
          <v-col lg="2" md="2" sm="12" xs="12">
            <v-row style="width: 100%; height: 260px">
              <v-card class="py-2" style="width: 100%">
                <h3 class="pl-5">Today</h3>
                <div
                  lg="12"
                  md="12"
                  style="text-align: center; padding-top: 0px; margin-top: -5px"
                >
                  <div style="text-align: center; font-size: 20px">Min</div>
                  <div class="bold text-h4 green--text">
                    {{ humidity_min }}
                  </div>
                  <span
                    style="font-size: 10px"
                    v-if="humidity_min_date_time != '---'"
                  >
                    At : {{ humidity_min_date_time }}
                  </span>
                </div>

                <div
                  class="pt-5"
                  lg="12"
                  md="12"
                  style="text-align: center; padding-top: 0px"
                >
                  <div style="text-align: center; color: red; font-size: 20px">
                    Max
                  </div>
                  <div class="bold text-h4 red--text">
                    {{ humidity_max }}
                  </div>
                  <span
                    style="font-size: 10px"
                    v-if="humidity_max_date_time != '---'"
                  >
                    At : {{ humidity_max_date_time }}</span
                  >
                </div>
              </v-card>
            </v-row>
          </v-col>
          <v-col lg="7" md="7" sm="12" xs="12">
            <v-row style="width: 100%; height: 260px">
              <v-card class="py-2" style="width: 100%">
                <v-col
                  lg="12"
                  md="12"
                  style="text-align: center; padding-top: 0px"
                >
                  <AlarmDashboardHumidityChart2
                    :branch_id="branch_id"
                    :name="'AlarmDashboardHumidityChart2'"
                    :height="'220'"
                    :device_serial_number="device_serial_number"
                    :key="keyChart2"
                  />
                </v-col>
              </v-card> </v-row
          ></v-col>
        </v-row>
      </v-col>
      <v-col clas="12" class="pt-10">
        <AlarmDashboardFooter :device="device" :key="key" />
      </v-col>
    </v-row>
  </div>

  <NoAccess v-else />
</template>

<script>
import AlarmDashboardTemparatureChart1 from "../../../components/Alarm/Dashboard/AlarmDashboardTemparatureChart1.vue";
import AlarmDashboardTemparatureChart2 from "../../../components/Alarm/Dashboard/AlarmDashboardTemparatureChart2.vue";

import AlarmDashboardHumidityChart1 from "../../../components/Alarm/Dashboard/AlarmDashboardHumidityChart1.vue";
import AlarmDashboardHumidityChart2 from "../../../components/Alarm/Dashboard/AlarmDashboardHumidityChart2.vue";

import AlarmDashboardFooter from "../../../components/Alarm/Dashboard/AlarmDashboardFooter.vue";

// import DashboardlastMultiStatistics from "../../components/dashboard2/DashboardlastMultiStatistics.vue";
export default {
  components: {
    AlarmDashboardTemparatureChart1,
    AlarmDashboardTemparatureChart2,
    AlarmDashboardHumidityChart1,
    AlarmDashboardHumidityChart2,
    AlarmDashboardFooter,
  },
  data() {
    return {
      audioSrc: null,
      topMenu: 0,
      key: 1,
      keyChart2: 1,
      branchList: [],
      selectedBranchName: "All Branches",
      seelctedBranchId: "",
      branch_id: "",
      overlay: false,
      temperature_latest: 0,
      temperature_date_time: "---",
      temperature_min: 0,
      temperature_max: 0,
      temperature_min_date_time: 0,
      temperature_max_date_time: 0,
      temperature_hourly_data: {},
      fire_alarm_start_datetime: "---",
      device_serial_number: "",

      humidity_latest: 0,
      humidity_date_time: "---",
      humidity_min: 0,
      humidity_max: 0,
      humidity_min_date_time: 0,
      humidity_max_date_time: 0,
      device: {
        fire_alarm_status: 0,
        fire_alarm_start_datetime: 0,
        water_alarm_status: 0,
        water_alarm_start_datetime: 0,
        power_alarm_status: 0,
        power_alarm_start_datetime: 0,
        door_open_status: 0,
        door_open_start_datetime: 0,
        smoke_alarm_status: 0,
        smoke_alarm_start_datetime: 0,
      },
      devicesList: {},
    };
  },
  watch: {
    // device_serial_number(val) {
    //   try {
    //     this.key++;
    //     this.keyChart2++;
    //     this.getDataFromApi(1);
    //   } catch (e) {}
    // },
  },
  mounted() {
    // if (this.$auth.user.user_type == "employee") {
    //   this.$router.push(`/dashboard/employee`);
    // }

    if (this.$auth.user.branch_id == 0 && this.$auth.user.is_master == false) {
      alert("You do not have permission to access this branch");
      //this.$router.push("/login");
      this.$axios.get(`/logout`).then(({ res }) => {
        this.$auth.logout();
        this.$router.push(`/login`);
      });

      this.$router.push(`/login`);
      return "";
    }
    this.loading = true;
    setTimeout(() => {}, 1000);
    ///this.getDataFromApi(1);

    setInterval(() => {
      if (this.$route.name == "alarm-dashboard") {
        this.getDataFromApi(1);
      }
    }, 1000 * 30);
  },
  async created() {
    if (this.$auth.user.branch_id == 0 && this.$auth.user.is_master == false) {
      alert("You do not have permission to access this branch");
      //this.$router.push("/login");
      this.$axios.get(`/logout`).then(({ res }) => {
        this.$auth.logout();
        this.$router.push(`/login`);
      });

      this.$router.push(`/login`);
      return "";
    }

    try {
      await this.$store.dispatch("fetchDropDowns", {
        key: "deviceList",
        endpoint: "device-list",
        refresh: true,
      });
      this.devicesList = this.$store.state.deviceList;
      if (this.$store.state.deviceList && this.$store.state.deviceList[0]) {
        this.device_serial_number = this.$store.state.deviceList[0].device_id;
        //this.getDataFromApi();
      }

      // await this.$store.dispatch("fetchDropDowns", {
      //   key: "employeeList",
      //   endpoint: "employee-list",
      //   refresh: true,
      // });
      this.branchList = await this.$store.dispatch("fetchDropDowns", {
        key: "branchList",
        endpoint: "branch-list",
        refresh: true,
      });
    } catch (error) {
      console.error("Error fetching data:", error);
    }
    this.getDataFromApi(1);
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
    ChangeDevice(serial_number) {
      try {
        this.device_serial_number = serial_number;
        this.key++;
        this.keyChart2++;

        this.getDataFromApi(1);
        //console.log(this.device_serial_number, " this.device_serial_number");
      } catch (e) {}
    },
    can(per) {
      return this.$pagePermission.can(per, this);
    },
    openalert(data) {
      alert("");
    },
    filterBranch(branch) {
      this.$emit("openalert", "");

      // if (branch) {
      //   this.selectedBranchName = branch.branch_name;
      //   this.seelctedBranchId = branch.id;
      //   this.branch_id = branch.id;
      // } else {
      //   this.selectedBranchName = "All Branches";
      //   this.seelctedBranchId = "";
      //   this.branch_id = "";
      // }
    },
    getDataFromApi(reset = 0) {
      if (reset == 1) {
        this.keyChart2++;
      }
      try {
        if (
          this.device_serial_number == "" ||
          this.device_serial_number == null
        )
          return false;
        if (this.$store.state.alarm_temparature_latest && reset == 0) {
          this.data = this.$store.state.alarm_temparature_latest;
        } else {
          this.$axios
            .get("alarm_dashboard_get_temparature_latest", {
              params: {
                company_id: this.$auth.user.company_id,

                device_serial_number: this.device_serial_number,
              },
            })
            .then(({ data }) => {
              this.data = data;
              this.device = data.device;
              this.loading = false;
              this.$store.commit(
                "AlarmDashboard/alarm_temparature_latest",
                data
              );

              this.temperature_latest = data.temperature_latest;
              this.temperature_date_time = this.$dateFormat.format4(
                data.temperature_date_time
              );

              this.temperature_min = data.temperature_min + "&deg;C";
              this.temperature_max = data.temperature_max + "&deg;C";
              this.temperature_min_date_time = this.$dateFormat.format6(
                data.temperature_min_date_time
              );
              this.temperature_max_date_time = this.$dateFormat.format6(
                data.temperature_max_date_time
              );
              // this.temperature_hourly_data = data.houry_data;
              if (data.fire_alarm_start_datetime)
                this.fire_alarm_start_datetime = this.$dateFormat.format4(
                  data.fire_alarm_start_datetime
                );

              //humidity
              this.humidity_latest = data.humidity_latest;
              this.humidity_date_time = this.$dateFormat.format4(
                data.humidity_date_time
              );

              this.humidity_min = data.humidity_min + "%";
              this.humidity_max = data.humidity_max + "%";
              this.humidity_min_date_time = this.$dateFormat.format6(
                data.humidity_min_date_time
              );
              this.humidity_max_date_time = this.$dateFormat.format6(
                data.humidity_max_date_time
              );

              this.key = this.key + 1;
            });
        }
      } catch (e) {}
    },
  },
};
</script>
