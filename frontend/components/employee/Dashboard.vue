<template>
  <div>
    <div>
      <v-dialog persistent>
        v-model="dialogGeneralreport" :fullscreen="false" max-width="80%" >
        <v-card>
          <!-- <v-card-actions>
              <v-spacer></v-spacer>
              <v-btn class="error" small @click="closeDialogGeneralreport">
                Close <span class="mdi mdi-close-circle"></span>
              </v-btn>
            </v-card-actions> -->
          <v-card-text style="padding: 0px">
            <v-container style="max-width: 100%; padding: 0px">
              <v-row>
                <v-col cols="12">
                  <iframe
                    v-if="iframeDisplay"
                    :src="iframeUrl"
                    frameborder="0"
                    style="width: 100%; height: 600px"
                  ></iframe>
                </v-col>
              </v-row>
            </v-container>
          </v-card-text>
        </v-card>
      </v-dialog>
      <v-row>
        <v-col
          cols="12"
          xs="12"
          sm="12"
          md="2"
          lg="2"
          xl="2"
          v-for="(i, index) in items"
          :key="index"
        >
          <div class="card p-2" :class="i.color" style="min-height: 150px">
            <div class="card-statistic-3">
              <div class="card-icon card-icon-large">
                <i :class="i.icon"></i>
              </div>
              <div class="card-content">
                <h4 class="card-title text-capitalize">{{ i.title }}</h4>
                <span class="data-1"> {{ i.value }} / {{ daysCount }}</span>
                <p class="mb-0 text-sm">
                  <span
                    class="handcursor font-11"
                    @click="showDialogGeneralreport(i.link)"
                  >
                    <span class="mr-2">
                      <v-icon dark small>mdi-arrow-right</v-icon>
                    </span>
                    <span class="text-nowrap regular-font">View Report</span>
                  </span>
                </p>
              </div>
            </div>
          </div>
        </v-col>
        <v-col cols="12" xs="12" sm="12" md="6" lg="6" xl="6">
          <v-card elevation="1" style="min-height: 445px">
            <v-toolbar class="background" dense dark>
              <div><b>Attendance Logs (Recent Logs)</b></div>
              <v-spacer></v-spacer>
              <v-icon @click="navigateToRoute(`/employees/logs`)"
                >mdi-eye-outline</v-icon
              >
            </v-toolbar>
            <v-container>
              <NoRecordFound :data="attendance_logs" />

              <v-simple-table dense>
                <template v-slot:default>
                  <thead>
                    <tr>
                      <th class="text-left">Employee Device Id/User ID</th>
                      <th class="text-left">Log Time</th>
                      <th class="text-left">Device Name</th>
                      <th class="text-left">Device ID</th>
                    </tr>
                  </thead>
                  <tbody>
                    <tr v-for="(item, index) in attendance_logs" :key="index">
                      <td>{{ item.UserID }}</td>
                      <td>{{ item.date }} {{ item.time }}</td>
                      <td>{{ item.device.name }}</td>
                      <td>{{ item.device.device_id }}</td>
                    </tr>
                  </tbody>
                </template>
              </v-simple-table>
            </v-container>
            <v-card class="mx-auto" max-width="500">
              <div class="text-center">
                <v-dialog persistent v-model="dialog" width="600">
                  <v-card>
                    <v-card-title class="text-h5 primary white--text">
                      Announcement Detail
                      <v-spacer></v-spacer>
                      <v-icon color="background" dark @click="dialog = false"
                        >mdi-close</v-icon
                      >
                    </v-card-title>

                    <v-card-text class="mt-3">
                      <table
                        style="
                          font-family: arial, sans-serif;
                          border-collapse: collapse;
                          width: 100%;
                        "
                      >
                        <tr>
                          <th
                            style="
                              border: 1px solid #dddddd;
                              text-align: left;
                              padding: 8px;
                            "
                          >
                            Title
                          </th>
                          <td
                            style="
                              border: 1px solid #dddddd;
                              text-align: left;
                              padding: 8px;
                            "
                          >
                            {{ dialogData.title }}
                          </td>
                        </tr>
                        <tr>
                          <th
                            style="
                              border: 1px solid #dddddd;
                              text-align: left;
                              padding: 8px;
                            "
                          >
                            Description
                          </th>
                          <td
                            style="
                              border: 1px solid #dddddd;
                              text-align: left;
                              padding: 8px;
                            "
                          >
                            {{ dialogData.description }}
                          </td>
                        </tr>

                        <tr>
                          <th
                            style="
                              border: 1px solid #dddddd;
                              text-align: left;
                              padding: 8px;
                            "
                          >
                            When
                          </th>
                          <td
                            style="
                              border: 1px solid #dddddd;
                              text-align: left;
                              padding: 8px;
                            "
                          >
                            <b
                              class="primary--text"
                              v-if="getCurrentDate == dialogData.start_date"
                              >{{ dialogData.start_date }}</b
                            >
                            <span v-else>{{ dialogData.start_date }}</span>
                            -
                            <b
                              class="primary--text"
                              v-if="getCurrentDate == dialogData.end_date"
                              >{{ dialogData.end_date }}</b
                            >
                            <span v-else>{{ dialogData.end_date }}</span>
                          </td>
                        </tr>
                      </table>
                    </v-card-text>
                  </v-card>
                </v-dialog>
              </div>
            </v-card>
          </v-card>
        </v-col>
        <v-col cols="12" xs="12" sm="12" md="6" lg="6" xl="3">
          <v-card elevation="1" style="min-height: 445px">
            <v-toolbar class="background" dense dark>
              <div><b>Announcements</b></div>
              <v-spacer></v-spacer>
              <v-icon @click="navigateToRoute(`/employees/announcements`)"
                >mdi-eye-outline</v-icon
              >
            </v-toolbar>
            <v-container>
              <NoRecordFound :data="announcements" />

              <v-list>
                <v-list-item
                  v-for="(announcement, index) in announcements"
                  :key="index"
                >
                  <v-list-item-content
                    style="
                      border-top: 1px dotted grey;
                      border-bottom: 1px dotted grey;
                    "
                  >
                    <v-list-item-title>{{
                      announcement.title
                    }}</v-list-item-title>
                    <v-list-item-subtitle
                      >{{ getExcerpt(announcement.description, 30) }}&nbsp;
                      <!-- <v-chip
                        x-small
                        color="background"
                        dark
                        @click="openDialog(announcement)"
                        >Read More
                        <v-icon x-small>mdi-chevron-right</v-icon></v-chip
                      > -->
                    </v-list-item-subtitle>
                    <v-list-item-subtitle
                      >When:
                      <b
                        class="primary--text"
                        v-if="getCurrentDate == announcement.start_date"
                        >{{ announcement.start_date }}</b
                      >
                      <span v-else>{{ announcement.start_date }}</span>
                      -
                      <b
                        class="primary--text"
                        v-if="getCurrentDate == announcement.end_date"
                        >{{ announcement.end_date }}</b
                      >
                      <span v-else>{{ announcement.end_date }}</span>
                    </v-list-item-subtitle>
                  </v-list-item-content>
                </v-list-item>
              </v-list>
              <v-card class="mx-auto" max-width="500">
                <div class="text-center">
                  <v-dialog persistent v-model="dialog" width="600">
                    <v-card>
                      <v-card-title class="text-h5 primary white--text">
                        Announcement Detail
                        <v-spacer></v-spacer>
                        <v-icon color="background" dark @click="dialog = false"
                          >mdi-close</v-icon
                        >
                      </v-card-title>

                      <v-card-text class="mt-3">
                        <table
                          style="
                            font-family: arial, sans-serif;
                            border-collapse: collapse;
                            width: 100%;
                          "
                        >
                          <tr>
                            <th
                              style="
                                border: 1px solid #dddddd;
                                text-align: left;
                                padding: 8px;
                              "
                            >
                              Title
                            </th>
                            <td
                              style="
                                border: 1px solid #dddddd;
                                text-align: left;
                                padding: 8px;
                              "
                            >
                              {{ dialogData.title }}
                            </td>
                          </tr>
                          <tr>
                            <th
                              style="
                                border: 1px solid #dddddd;
                                text-align: left;
                                padding: 8px;
                              "
                            >
                              Description
                            </th>
                            <td
                              style="
                                border: 1px solid #dddddd;
                                text-align: left;
                                padding: 8px;
                              "
                            >
                              {{ dialogData.description }}
                            </td>
                          </tr>
                          <tr>
                            <th
                              style="
                                border: 1px solid #dddddd;
                                text-align: left;
                                padding: 8px;
                              "
                            >
                              Departments
                            </th>
                            <td
                              style="
                                border: 1px solid #dddddd;
                                text-align: left;
                                padding: 8px;
                              "
                            >
                              <v-chip
                                class="primary mx-1"
                                x-small
                                v-for="(
                                  department, dIndex
                                ) in dialogData.departments"
                                :key="dIndex"
                                >{{ department.name }}</v-chip
                              >
                            </td>
                          </tr>
                          <tr>
                            <th
                              style="
                                border: 1px solid #dddddd;
                                text-align: left;
                                padding: 8px;
                              "
                            >
                              Employees
                            </th>
                            <td
                              style="
                                border: 1px solid #dddddd;
                                text-align: left;
                                padding: 8px;
                              "
                            >
                              <v-chip
                                class="primary mx-1"
                                x-small
                                v-for="(
                                  employee, eIndex
                                ) in dialogData.employees"
                                :key="eIndex"
                                >{{ employee.display_name }}</v-chip
                              >
                            </td>
                          </tr>
                          <tr>
                            <th
                              style="
                                border: 1px solid #dddddd;
                                text-align: left;
                                padding: 8px;
                              "
                            >
                              When
                            </th>
                            <td
                              style="
                                border: 1px solid #dddddd;
                                text-align: left;
                                padding: 8px;
                              "
                            >
                              <b
                                class="primary--text"
                                v-if="getCurrentDate == dialogData.start_date"
                                >{{ dialogData.start_date }}</b
                              >
                              <span v-else>{{ dialogData.start_date }}</span>
                              -
                              <b
                                class="primary--text"
                                v-if="getCurrentDate == dialogData.end_date"
                                >{{ dialogData.end_date }}</b
                              >
                              <span v-else>{{ dialogData.end_date }}</span>
                            </td>
                          </tr>
                        </table>
                      </v-card-text>
                    </v-card>
                  </v-dialog>
                </div>
              </v-card>
            </v-container>
          </v-card>
        </v-col>
        <v-col cols="12" xs="12" sm="12" md="6" lg="6" xl="3">
          <v-card elevation="1" style="min-height: 445px">
            <v-toolbar class="background" dense dark>
              <div><b>Attendance Report (Current Month)</b></div>
            </v-toolbar>
            <v-container>
              <PIE class="mt-5" />
            </v-container>
          </v-card>
        </v-col>
        <v-col cols="12" xs="12" sm="12" md="6" lg="6" xl="3">
          <v-card elevation="1">
            <v-toolbar class="background" dense dark>
              <div><b>Activity</b></div>
            </v-toolbar>
            <v-container>
              <Activity />
            </v-container>
          </v-card>
        </v-col>
      </v-row>
    </div>
  </div>
  <!-- <NoAccess v-else /> -->
</template>
<script>
import PIE from "../../components/charts/PIE.vue";
import Logs from "../../components/employee/Logs.vue";
export default {
  components: { PIE, Logs },

  data() {
    return {
      payloadOptions: {},
      currentValue: 0,
      animationDuration: 4000, // Animation duration in milliseconds
      first_login_auth: 1,
      loading: true,
      dialogGeneralreport: false,
      generalreportIframeSrc: "",
      iframeDisplay: false,
      iframeUrl: "",
      items: [],
      timelineItems: [
        {
          title: "Event 1",
          subtitle: "Description of Event 1",
        },
        {
          title: "Event 2",
          subtitle: "Description of Event 2",
        },
        {
          title: "Event 3",
          subtitle: "Description of Event 3",
        },
        // Add more timeline items as needed
      ],
      activities: [
        {
          id: 1,
          title: "Logged in",
          description: "User John Doe logged in to the system.",
          color: "green",
          icon: "mdi-login",
        },
        {
          id: 2,
          title: "Updated profile",
          description: "User John Doe updated their profile information.",
          color: "blue",
          icon: "mdi-account-edit",
        },
        {
          id: 3,
          title: "Changed password",
          description: "User John Doe changes his password.",
          color: "blue",
          icon: "mdi-account-edit",
        },
        // Add more activity objects as needed
      ],
      LoggedIns: [
        {
          name: "Logged in At 1:10",
        },
        {
          name: "Logged in At 1:40pm",
        },
        {
          name: "Logged in At 2:14pm",
        },
        {
          name: "Logged in At 4:20pm",
        },
        {
          name: "Logged in At 7:25pm",
        },
        {
          name: "Logged in At 8:00pm",
        },
      ],
      dialog: false,
      dialogData: {},
      announcements: [],
      attendance_logs: [],
      daysCount: 0,
    };
  },
  created() {
    this.$router.push("/employees/profile");
    this.initialize();
    this.get_announcements();
    this.first_login_auth = this.$auth.user.first_login;

    const date = new Date();
    const year = date.getFullYear();
    const month = date.getMonth() + 1; // Months are zero-based, so we add 1

    this.daysCount = new Date(year, month, 0).getDate();

    // this.generalreportIframeSrc=  this.$axios.defaults.baseURL +
    //     "daily?company_id=8&status=SA&daily_date=2023-05-31&department_id=-1&report_type=Daily",
  },
  computed: {
    first_login() {
      return this.$store.state.first_login;
    },
    getmeth() {
      this.txt = this.$store.state.main_report_type;
    },
  },
  methods: {
    navigateToRoute(value) {
      this.$router.push(value);
    },
    getCurrentDate() {
      // Get the current date
      const date = new Date();

      // Get the year, month, and day from the date object
      const year = date.getFullYear();
      const month = String(date.getMonth() + 1).padStart(2, "0");
      const day = String(date.getDate()).padStart(2, "0");

      return `${year}-${month}-${day}`;
    },
    openDialog(announcement) {
      this.dialogData = announcement;
      this.dialog = true;
    },
    getExcerpt(text, maxLength) {
      if (text.length <= maxLength) {
        return text;
      } else {
        return text.substring(0, maxLength) + "...";
      }
    },
    updateLink(url) {
      if (
        this.$axios.defaults.baseURL !=
        "https://stagingbackend.ideahrms.com/api"
      ) {
        url = url.replace(
          "https://stagingbackend.ideahrms.com/api",
          this.$axios.defaults.baseURL
        );
      }

      return url;
    },
    closeDialogGeneralreport() {
      this.iframeDisplay = false;
      this.dialogGeneralreport = false;
      //this.iframeUrl = "#";
    },
    showDialogGeneralreport(iframeUrl) {
      this.iframeDisplay = false;
      this.iframeUrl = this.updateLink(iframeUrl);
      this.dialogGeneralreport = true;

      this.iframeDisplay = true;
    },
    can(per) {
      return this.$pagePermission.can(per, this);
    },
    
    initialize() {
      this.loading = false;

      const { employee } = this.$auth.user;

      let options = {
        company_id: this.$auth.user.company_id,
        employee_id: employee.system_user_id,
        department_id: employee.department_id,
        shift_type_id: employee.schedule.shift_type_id,
      };
      this.$axios
        .get(`employee-statistics`, { params: options })
        .then(({ data }) => {
          this.items = data;
          this.loading = false;
        });

      this.payloadOptions = {
        params: {
          per_page: 10,
          company_id: this.$auth.user.company_id,
          UserID: this.$auth.user.employee.system_user_id,
        },
      };
      this.$axios
        .get(`attendance_logs?page=1`, this.payloadOptions)
        .then(({ data }) => {
          this.attendance_logs = data.data;
          this.loading = false;
        });
    },
    get_announcements() {
      const id = this.$auth.user.employee.id;
      const company_id = this.$auth.user.company_id;
      const url = `employee-today-announcements`;

      this.$axios
        .get(`${url}/${id}?page=1&company_id=${company_id}&per_page=4`)
        .then(({ data }) => {
          this.announcements = data.data;
        });
    },
  },
};
</script>
<style scoped src="@/assets/dashboard.css"></style>
