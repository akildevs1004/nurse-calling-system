<template>
  <div v-if="can(`attendance_report_view`)">
    <v-card class="mb-5 mt-5" elevation="0">
      <div v-if="can(`attendance_report_access`)">
        <div class="text-center">
          <v-snackbar
            v-model="snackbar"
            top="top"
            color="secondary"
            elevation="24"
          >
            {{ response }}
          </v-snackbar>
          <v-snackbar v-model="snack" :timeout="3000" :color="snackColor">
            {{ snackText }}

            <template v-slot:action="{ attrs }">
              <v-btn v-bind="attrs" text @click="snack = false"> Close </v-btn>
            </template>
          </v-snackbar>
        </div>
        <v-card class="mb-5" elevation="0">
          <v-toolbar class="backgrounds" dense flat>
            <v-toolbar-title
              ><span>{{ report_type }} Rooms </span></v-toolbar-title
            >
            <span>
              <v-btn
                dense
                class="ma-0 px-0"
                x-small
                :ripple="false"
                text
                title="Reload"
              >
                <v-icon class="ml-2" @click="getDataFromApi" dark
                  >mdi-reload</v-icon
                >
              </v-btn>
            </span>

            <v-spacer></v-spacer>
            <span style="padding-left: 15px"
              ><img
                title="Print"
                style="cursor: pointer"
                @click="process_file('print_pdf')"
                src="/icons/icon_print.png"
                class="iconsize"
            /></span>
            <span style="padding-left: 15px"
              ><img
                title="Download Pdf"
                style="cursor: pointer"
                @click="process_file('download_pdf')"
                src="/icons/icon_pdf.png"
                class="iconsize"
            /></span>
          </v-toolbar>

          <v-data-table
            dense
            :headers="headers"
            :items="data"
            :loading="loading"
            :options.sync="options"
            :footer-props="{
              itemsPerPageOptions: [10, 50, 100, 500, 1000],
            }"
            class="elevation-1"
            model-value="data.id"
            :server-items-length="totalRowsCount"
            fixed-header
            :height="tableHeight"
          >
            <template v-slot:item.id="{ item, index }">
              {{ index + 1 }}
            </template>
            <template v-slot:item.full_name="{ item }" style="padding: 0px">
              <v-row no-gutters v-if="item.tanent">
                <v-col
                  md="2"
                  style="
                    padding: 3px;
                    padding-left: 0px;
                    width: 45px;
                    max-width: 45px;
                  "
                >
                  <v-avatar>
                    <v-img
                      :src="
                        item.tanent && item.tanent.profile_picture
                          ? item.tanent.profile_picture
                          : '/no-profile-image.jpg'
                      "
                    >
                    </v-img>
                  </v-avatar>
                </v-col>
                <v-col>
                  <div class="mx-2 mt-2" style="height: 15px">
                    {{ item.tanent.full_name || "---" }}
                  </div>
                  <small class="mx-2 mt-2">
                    {{ item.tanent.nationality || "---" }}</small
                  >
                </v-col>
              </v-row>
            </template>
            <template v-slot:item.status_id="{ item }">
              <v-chip
                small
                class="ma-1"
                :color="item?.tanent?.id ? `grey` : `green`"
                dark
              >
                {{ item?.tanent?.id ? "Occupied" : "Available" }}</v-chip
              >
            </template>
          </v-data-table>
        </v-card>
      </div>
    </v-card>
  </div>

  <NoAccess v-else />
</template>
<script>
export default {
  props: ["report_type"],

  data: () => ({
    tableHeight: 750,
    status: "",
    department_ids: "",
    daily_date: "",
    to_date: "",

    isFilter: false,
    totalRowsCount: 0,
    snack: false,
    snackColor: "",
    snackText: "",
    date: null,
    menu: false,
    options: {},
    date: null,
    menu: false,
    loading: false,
    time_menu: false,
    Model: "Available Room",
    endpoint: "room_report",
    search: "",
    snackbar: false,
    add_manual_log: false,
    dialog: false,
    generateLogsDialog: false,
    reportSync: false,
    from_menu: false,
    to_menu: false,
    ids: [],
    departments: [],
    employees: [],
    DateRange: true,
    devices: [],

    loading: false,
    total: 0,

    payload: {
      report_type: "Available",
      from_date: null,
      to_date: null,
      daily_date: null,
      UserID: "",
      department_ids: [],
      status: "-1",
      DeviceID: "",
      branch_id: "",
      include_device_types: ["all", "Access Control"],
    },

    response: "",
    data: [],
    errors: [],
    report_template: "Template1",
    headers: [
      {
        text: "#",
        align: "left",
        sortable: true,
        key: "id",
        value: "id",
        width: "50px",
      },
      {
        text: "Room Number",
        align: "left",
        sortable: true,
        key: "room_number",
        value: "room_number",
        width: "50px",
      },
      {
        text: "Floor",
        align: "left",
        sortable: true,
        key: "floor_id",
        value: "floor_id",
        width: "50px",
      },
      {
        text: "Room Category",
        align: "left",
        sortable: true,
        key: "room_category.name",
        value: "room_category.name",
        width: "50px",
      },
      {
        text: "Tanent",
        align: "left",
        sortable: true,
        key: "full_name",
        value: "full_name",
        width: "50px",
      },
      {
        text: "Contract Start",
        align: "left",
        sortable: true,
        key: "tanent.start_date",
        value: "tanent.start_date",
        width: "50px",
      },
      {
        text: "Contract End",
        align: "left",
        sortable: true,
        key: "tanent.end_date",
        value: "tanent.end_date",
        width: "50px",
      },
      {
        text: "Status",
        align: "left",
        sortable: true,
        key: "status_id",
        value: "status_id",
        width: "50px",
      },
    ],
    max_date: null,

    isCompany: true,
    branches: [],
  }),

  watch: {
    options: {
      handler() {
        this.getDataFromApi();
      },
      deep: true,
    },
  },
  mounted() {
    this.tableHeight = window.innerHeight - 370;
    window.addEventListener("resize", () => {
      this.tableHeight = window.innerHeight - 370;
    });
  },
  created() {
    this.payload.report_type = this.report_type;
    this.setFromDate();
  },
  methods: {
    filterAttr(data) {
      this.payload.from_date = data.from;
      this.payload.to_date = data.to;
      this.getDataFromApi();
    },

    setFromDate() {
      if (this.payload.from_date == null) {
        const dt = new Date();
        const y = dt.getFullYear();
        const m = dt.getMonth() + 1;
        const formattedMonth = m < 10 ? "0" + m : m;
        this.payload.from_date = `${y}-${formattedMonth}-01`;
      }
    },
    can(per) {
      return this.$pagePermission.can(per, this);
    },

    async getDataFromApi() {
      if (!this.payload.from_date) return false;

      if (this.payload.from_date) {
        this.payload.from_date = this.payload.from_date;
      }

      if (this.payload.to_date) {
        this.payload.to_date = this.payload.to_date;
      }
      this.loading = true;
      const { data, total } = await this.$store.dispatch("fetchData", {
        key: "access_control_report",
        options: this.options,
        refresh: true,
        endpoint: this.endpoint,
        filters: this.payload,
      
      });
      this.data = data;
      this.totalRowsCount = total;
      this.loading = false;
    },

    pdfDownload() {
      let path = process.env.BACKEND_URL + "/pdf";
      let pdf = document.createElement("a");
      pdf.setAttribute("href", path);
      pdf.setAttribute("target", "_blank");
      pdf.click();
    },

    async process_file(type) {
      try {
        if (!this.data || !this.data.length) {
          alert("No data found");
          return;
        }

        const backendUrl = process.env.BACKEND_URL;
        const queryParams = {
          company_id: this.$auth.user.company_id,
          branch_id: this.payload.branch_id,
          UserID: this.payload.UserID,
          DeviceID: this.payload.DeviceID,
          from_date: this.payload.from_date,
          to_date: this.payload.to_date,
          report_type: this.report_type,
          user_type: this.payload.user_type,
        };

        if (this.payload.from_date) {
          queryParams.from_date = this.payload.from_date;
        }

        if (this.payload.to_date) {
          queryParams.to_date = this.payload.to_date;
        }

        const queryString = Object.keys(queryParams)
          .map(
            (key) =>
              `${encodeURIComponent(key)}=${encodeURIComponent(
                queryParams[key]
              )}`
          )
          .join("&");

        const reportUrl = `${backendUrl}/room_report_${type.toLowerCase()}?${queryString}&include_device_types[]=all&include_device_types[]=Access Control`;

        const report = document.createElement("a");
        report.setAttribute("href", reportUrl);
        report.setAttribute("target", "_blank");
        report.click();
      } catch (error) {
        console.error("Error processing file:", error.message);
        // Handle the error (e.g., show an error message to the user)
      }
    },
  },
};
</script>
