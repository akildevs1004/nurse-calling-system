<template>
  <div
    style="width: 100%"
    v-if="can('dashboard_access') && can('dashboard_view')"
  >
    <v-overlay :value="overlay">
      <v-progress-circular indeterminate size="64"></v-progress-circular>
    </v-overlay>
    <v-row>
      <v-col lg="3" md="3" sm="12" xs="12">
        <v-row style="width: 100%; height: 250px">
          <v-card class="py-2" style="width: 100%">
            <v-col lg="12" md="12" style="text-align: center; padding-top: 0px">
              <h3 style="text-align: left">Smoke/Fire Alarm</h3>

              <v-row style="font-size: 20px">
                <v-col cols="4">
                  <img
                    class="pa-10"
                    src="../../../static/alarm-icons/smoke_alarm.png"
                    style="width: 160px"
                  />
                </v-col>
                <v-col cols="8">
                  <div>Status</div>
                  <div class="green--text">
                    <div v-if="device.smoke_alarm_status == 0">Normal</div>
                    <div v-else style="color: red">Warning</div>
                  </div>
                  <br />
                  <div style="font-size: 13px">Last Alarm</div>
                  <div :style="getPriorityColor(device.smoke_alarm_status)">
                    {{
                      device.smoke_alarm_start_datetime == null
                        ? "---"
                        : $dateFormat.format4(device.smoke_alarm_start_datetime)
                    }}
                  </div>
                </v-col>
              </v-row>
            </v-col>
          </v-card>
        </v-row>
      </v-col>
      <v-col lg="3" md="3" sm="12" xs="12">
        <v-row style="width: 100%; height: 250px">
          <v-card class="py-2" style="width: 100%">
            <v-col lg="12" md="12" style="text-align: center; padding-top: 0px">
              <h3 style="text-align: left">Water Leakage Alarm</h3>

              <v-row style="font-size: 20px">
                <v-col cols="4">
                  <img
                    class="pa-10"
                    src="../../../static/alarm-icons/water-leakage.png"
                    style="width: 160px"
                  />
                </v-col>
                <v-col cols="8">
                  <div>Status</div>
                  <div class="green--text">
                    <div v-if="device.water_alarm_status == 0">Normal</div>
                    <div v-else style="color: red">Warning</div>
                  </div>
                  <br />
                  <div style="font-size: 13px">Last Alarm</div>
                  <div :style="getPriorityColor(device.water_alarm_status)">
                    {{
                      device.water_alarm_start_datetime == null
                        ? "---"
                        : $dateFormat.format4(device.water_alarm_start_datetime)
                    }}
                  </div>
                </v-col>
              </v-row>
            </v-col>
          </v-card>
        </v-row>
      </v-col>
      <v-col lg="3" md="3" sm="12" xs="12">
        <v-row style="width: 100%; height: 250px">
          <v-card class="py-2" style="width: 100%">
            <v-col lg="12" md="12" style="text-align: center; padding-top: 0px">
              <h3 style="text-align: left">A/C Power Failure Alarm</h3>

              <v-row style="font-size: 20px">
                <v-col cols="4">
                  <img
                    class="pa-10"
                    src="../../../static/alarm-icons/acpower.png"
                    style="width: 160px"
                  />
                </v-col>
                <v-col cols="8">
                  <div>Status</div>
                  <div class="green--text">
                    <div v-if="device.power_alarm_status == 0">Normal</div>
                    <div v-else style="color: red">Warning</div>
                  </div>
                  <br />
                  <div style="font-size: 13px">Last Alarm</div>
                  <div :style="getPriorityColor(device.power_alarm_status)">
                    {{
                      device.power_alarm_start_datetime == null
                        ? "---"
                        : $dateFormat.format4(device.power_alarm_start_datetime)
                    }}
                  </div>
                </v-col>
              </v-row>
            </v-col>
          </v-card>
        </v-row>
      </v-col>
      <v-col lg="3" md="3" sm="12" xs="12">
        <v-row style="width: 100%; height: 250px">
          <v-card class="py-2" style="width: 100%">
            <v-col lg="12" md="12" style="text-align: center; padding-top: 0px">
              <h3 style="text-align: left">Door Open Status</h3>

              <v-row style="font-size: 20px">
                <v-col cols="4">
                  <img
                    class="pa-10"
                    src="../../../static/alarm-icons/dooropen.png"
                    style="width: 160px"
                  />
                </v-col>
                <v-col cols="8">
                  <div>Status</div>
                  <div class="green--text">
                    <div v-if="device.door_open_status == 0">Closed</div>
                    <div v-else style="color: red">Open</div>
                  </div>
                  <br />
                  <div style="font-size: 13px">Last Open</div>
                  <div :style="getPriorityColor(device.door_open_status)">
                    {{
                      device.door_open_start_datetime == null
                        ? "---"
                        : $dateFormat.format4(device.door_open_start_datetime)
                    }}
                  </div>
                </v-col>
              </v-row>
            </v-col>
          </v-card>
        </v-row>
      </v-col>
    </v-row>
  </div>

  <NoAccess v-else />
</template>

<script>
export default {
  props: ["device"],
  data() {
    return {
      branchList: [],
      selectedBranchName: "All Branches",
      seelctedBranchId: "",
      branch_id: "",
      overlay: false,
    };
  },
  // watch: {
  //   branch_id(branch_id) {
  //     return branch_id > 0 ? branch_id : null;
  //   },
  // },
  mounted() {
    // // if (this.$auth.user.user_type == "employee") {
    // //   this.$router.push(`/dashboard/employee`);
    // // }
    // if (this.$auth.user.branch_id == 0 && this.$auth.user.is_master == false) {
    //   alert("You do not have permission to access this branch");
    //   //this.$router.push("/login");
    //   this.$axios.get(`/logout`).then(({ res }) => {
    //     this.$auth.logout();
    //     this.$router.push(`/login`);
    //   });
    //   this.$router.push(`/login`);
    //   return "";
    // }
  },
  async created() {
    // if (this.$auth.user.branch_id == 0 && this.$auth.user.is_master == false) {
    //   alert("You do not have permission to access this branch");
    //   //this.$router.push("/login");
    //   this.$axios.get(`/logout`).then(({ res }) => {
    //     this.$auth.logout();
    //     this.$router.push(`/login`);
    //   });
    //   this.$router.push(`/login`);
    //   return "";
    // }
    // try {
    //   await this.$store.dispatch("fetchDropDowns", {
    //     key: "deviceList",
    //     endpoint: "device-list",
    //     refresh: true,
    //   });
    //   await this.$store.dispatch("fetchDropDowns", {
    //     key: "employeeList",
    //     endpoint: "employee-list",
    //     refresh: true,
    //   });
    //   this.branchList = await this.$store.dispatch("fetchDropDowns", {
    //     key: "branchList",
    //     endpoint: "branch-list",
    //     refresh: true,
    //   });
    // } catch (error) {
    //   console.error("Error fetching data:", error);
    // }
  },
  watch: {
    // overlay(val) {
    //   val &&
    //     setTimeout(() => {
    //       this.overlay = false;
    //     }, 3000);
    // },
  },
  methods: {
    getPriorityColor(status) {
      if (status == 1) {
        return "color:red";
      }
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
  },
};
</script>
