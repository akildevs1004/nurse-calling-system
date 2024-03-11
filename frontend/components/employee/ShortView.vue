<template>
  <v-row no-gutters v-if="item && item.id">
    
    <v-col cols="4" style="border-right: 1px solid #dddddd">
      <v-divider></v-divider>
      <v-row class="pa-0 ma-0">
        <v-col cols="12">
          <v-row no-gutters>
            <v-col cols="12" class="text-center">
              {{ item.employee.profile_pictrue }}
              <v-avatar size="100">
                <img
                  style="width: 100%"
                  :src="
                    item.employee && item.employee.profile_picture
                      ? item.employee.profile_picture
                      : '/no-profile-image.jpg'
                  "
                  alt="Avatar"
                />
              </v-avatar>
            </v-col>
            <v-col cols="12" class="text-center">
              <div style="height: 15px; font-size: 12px" class="mt-1">
                {{ item.employee.first_name ?? "---" }}
                {{ item.employee.last_name ?? "---" }}
              </div>
              <div style="height: 15px; font-size: 12px">
                <small>
                  {{ item?.employee?.designation?.name ?? "---" }}
                </small>
              </div>

              <div style="font-size: 12px">
                <small >
                  {{ item.employee.phone_number ?? "---" }}
                </small>
              </div>
            </v-col>
          </v-row>
        </v-col>
        <v-col cols="12">
          <v-divider></v-divider>
          <v-row
            no-gutters
            v-for="(item, index) in employee_stats.slice(0, 6)"
            :key="index"
            style="font-size: 15px;height:20px;"
          >
            <v-col cols="6">
              <small> {{ item.title }}</small>
            </v-col>
            <v-col cols="6" class="text-right">
              <small> {{ item.value }}</small>
            </v-col>
          </v-row>
        </v-col>
      </v-row>
    </v-col>

    <v-col cols="8">
      <v-divider></v-divider>
      <v-row no-gutters class="mx-2">
        <v-col
          cols="4"
          class="text-center"
          style="
            border-bottom: 1px solid #dddddd;
            border-right: 1px solid #dddddd;
          "
        >
          <b style="display: block; height: 20px">
            <small>{{ todayAttendance && todayAttendance.total_hrs }}</small>
          </b>
          <div style="font-size: 12px">Work Time</div>
        </v-col>
        <v-col
          cols="4"
          class="text-center"
          style="
            border-bottom: 1px solid #dddddd;
          "
        >
          <b style="display: block; height: 20px">
            <small>{{ remainingTime }}</small>
          </b>
          <div style="font-size: 12px">Remaing Hours</div>
        </v-col>
        <v-col
          cols="4"
          class="text-center"
          style="
            border-bottom: 1px solid #dddddd;
            border-left: 1px solid #dddddd;
          "
        >
          <b style="display: block; height: 20px">
            <small>
              {{ todayAttendance && todayAttendance.ot }}
            </small>
          </b>
          <div style="font-size: 12px">OverTime</div>
        </v-col>
      </v-row>
      <v-row no-gutters class="px-3">
        <v-col cols="12">
          <ComonPreloader icon="face-scan" v-if="!logs_data.length" />
          <table v-else class="mt-4" style="width:100%;">
            <tr>
              <td style="font-size: 12px">
                <small style="">
                  <b>#</b>
                </small>
              </td>
              <td style="font-size: 12px">
                <small style="">
                  <b>Date Time</b>
                </small>
              </td>
              <td style="font-size: 12px">
                <small><b>Device</b></small>
              </td>
            
            </tr>
            <tr
              v-for="(item, index) in logs_data"
              :key="item.id"
            >
              <td style="font-size: 14px;border-bottom: 1px solid #dddddd">
                <small>{{ index + 1 }}</small>
              </td>
              <td style="font-size: 14px;border-bottom: 1px solid #dddddd">
                <small>{{ item.date }} {{ item.time }}</small>
              </td>
              <td style="font-size: 14px;border-bottom: 1px solid #dddddd">
                <small>{{ item.device ? item.device.name : "---" }}</small>
              </td>
            </tr>
          </table>
          <!-- <v-data-table
            dense
            :headers="log_headers"
            :items="logs_data"
            hide-default-footer
          >
            <template v-slot:item.id="{ item, index }">
              <small>
                {{ index + 1 }}
              </small>
            </template>
            <template v-slot:item.LogTime="{ item }">
              <small> {{ item.date }} {{ item.time }}</small>
            </template>
            <template v-slot:item.device="{ item }">
              <small>{{ item.device.name || "---" }}</small>
            </template>
          </v-data-table> -->
        </v-col>
      </v-row>
    </v-col>
  </v-row>
</template>

<script>
export default {
  props: ["item"],
  data: () => ({
    logs_data: [],
    log_endpoint: "attendance_logs",
    log_headers: [
      {
        text: "#",
        align: "left",
        sortable: false,
        key: "id",
        value: "id",
        width: "10px",
      },
      {
        text: "DateTime",
        align: "left",
        sortable: false,
        key: "date_range",
        value: "LogTime",
        fieldType: "date_range_picker",
      },

      {
        text: "Device",
        align: "left",
        sortable: true,
        key: "device",
        value: "device",
        filterable: true,
        filterSpecial: true,
      },
    ],

    dialog: false,
    UserID: null,
    profile_pictrue: "no-profile-image.jpg",
    company_id: 0,
    employee_stats: [],
    todayAttendance: null,
    remainingTime: "00:00",
  }),

  async created() {
    this.company_id = this.item.employee.company_id;

    await this.getEmployeeStats();
    await this.getTodayAttendance();
    await this.getLogs();
  },
  methods: {
    getDate() {
      const date = new Date();
      const year = date.getFullYear();
      const month = (date.getMonth() + 1).toString().padStart(2, "0");
      const day = date.getDate().toString().padStart(2, "0");
      return `${year}-${month}-${day}`;
    },
    async getLogs() {
      this.$axios
        .get(this.log_endpoint, {
          params: {
            per_page: 10,
            company_id: this.company_id,
            UserID: this.item.employee.system_user_id,
            from_date: this.getDate(),
            to_date: this.getDate(),
          },
        })
        .then(({ data }) => {
          this.logs_data = data.data;
        });
    },
    async getTodayAttendance() {
      this.$axios
        .get(`report`, {
          params: {
            company_id: this.company_id,
            employee_id: this.item.employee.system_user_id,
            from_date: this.getFormattedDate(),
            to_date: this.getFormattedDate(),
          },
        })
        .then(({ data }) => {
          if (!data.data.length) {
            this.getRemainingTime("00:00", "00:00");
            this.todayAttendance = { total_hrs: "00:00", ot: "00:00" };
            return;
          }

          const { total_hrs, ot, shift } = data.data[0];

          if (!shift) {
            this.getRemainingTime("00:00", "00:00");
            this.todayAttendance = { total_hrs: "00:00", ot: "00:00" };
            return;
          }

          this.todayAttendance = {
            total_hrs: this.timeHandler(total_hrs),
            ot: this.timeHandler(ot),
          };
          this.getRemainingTime(
            this.timeHandler(total_hrs),
            this.timeHandler(shift.working_hours)
          );
        });
    },
    timeHandler(value) {
      return value === "---" ? "00:00" : value;
    },
    async getEmployeeStats() {
      this.$axios
        .get(`employee-statistics`, {
          params: {
            company_id: this.company_id,
            employee_id: this.item.employee.system_user_id,
          },
        })
        .then(({ data }) => {
          this.employee_stats = data;
        });
    },
    getFormattedDate() {
      const now = new Date();
      return `${now.getFullYear()}-${String(now.getMonth() + 1).padStart(
        2,
        "0"
      )}-${String(now.getDate()).padStart(2, "0")}`;
    },
    getRemainingTime(totalHours, performedHours) {
      const [totalHoursStr, totalMinutesStr] = totalHours
        .split(":")
        .map(Number);
      const [performedHoursStr, performedMinutesStr] = performedHours
        .split(":")
        .map(Number);

      const totalMinutes = totalHoursStr * 60 + totalMinutesStr;
      const performedMinutes = performedHoursStr * 60 + performedMinutesStr;

      const remainingMinutes = totalMinutes - performedMinutes;

      if (remainingMinutes < 0) {
        const remainingHours = Math.abs(Math.ceil(remainingMinutes / 60));
        const remainingMinutesPart = Math.abs(remainingMinutes % 60);
        this.remainingTime = `${String(remainingHours).padStart(
          2,
          "0"
        )}:${String(remainingMinutesPart).padStart(2, "0")}`;
      }
    },
  },
};
</script>
