<template>
  <div>
    <div class="text-center ma-2">
      <v-snackbar v-model="snackbar" top="top" color="secondary" elevation="24">
        {{ response }}
      </v-snackbar>
    </div>

    <v-card>
      <v-card-title dense dark class="popup_background">
        Schedule(s)
        <v-spacer></v-spacer>
      </v-card-title>

      <v-card-text>
        <v-data-table
          dense
          :headers="headers_table"
          :items="schdulesList"
          model-value="data.id"
          :loading="loading"
          :options.sync="options"
          :footer-props="{
            itemsPerPageOptions: [10, 50, 100, 500, 1000],
          }"
          class="elevation-1"
          :server-items-length="totalRowsCount"
        >
          <template v-slot:item.sno="{ item, index }">
            {{
              currentPage
                ? (currentPage - 1) * perPage +
                  (cumulativeIndex + schdulesList.indexOf(item))
                : "-"
            }}
          </template>

          <template v-slot:item.shift_name="{ item }"
            >{{ item.shift && item.shift.name }}
          </template>
          <template v-slot:item.on_duty_time="{ item }"
            >{{ item.shift && item.shift.on_duty_time }} to
            {{ item.shift && item.shift.off_duty_time }}

            <div class="secondary-value">
              Hours: {{ item.shift && item.shift.working_hours }}
            </div>
          </template>

          <template v-slot:item.from_date="{ item }"
            >{{ $dateFormat.format1(item.from_date) }} <br />
            {{ $dateFormat.format1(item.to_date) }}
          </template>
          <template v-slot:item.days="{ item, index }">
            <span v-for="(day, index2) in item.shift.days"
              >{{ day
              }}<span v-if="index2 < item.shift.days.length - 1"> ,</span>
            </span>
          </template>
          <template v-slot:item.isOverTime="{ item }">
            <v-switch
              disabled
              v-model="item.isOverTime"
              lable="item.isOverTime"
            ></v-switch>
          </template>
          <template v-slot:item.shift_type="{ item }"
            >{{ item.shift_type.name }}
          </template>
        </v-data-table>
      </v-card-text>
    </v-card>
  </div>
</template>
<script>
export default {
  props: ["table_id", "employee_id", "system_user_id"],
  data: () => ({
    schdulesList: [],
    employees: [],
    cumulativeIndex: 1,
    perPage: 10,
    currentPage: 1,
    branchesList: [],
    branch_id: null,

    totalRowsCount: 0,

    snack: false,
    snackColor: "",
    snackText: "",
    displayNoRecords: false,

    Module: "Employee Schedule",
    shift_types: [],
    manual_shift: {},
    options: { perPage: 10 },
    options_dialog: {},
    endpoint: "scheduled_employees_index",
    endpoint_dialog: "scheduled_employees_list",
    search: "",
    shifts_for_filter: [],
    dialog_search: "",
    snackbar: false,
    dialog: false,
    editDialog: false,

    loading: false,
    loading_dialog: false,
    isEdit: false,
    total: 0,
    total_dialog: 0,

    headers_table: [
      {
        text: "#",
        align: "left",
        sortable: false,
        value: "sno",
        filterable: false,
      },
      {
        text: "Shift Name",
        align: "left",
        sortable: false,
        value: "shift_name",
        filterable: false,
      },
      {
        text: "Time",
        align: "left",
        sortable: false,
        value: "on_duty_time",
        filterable: false,
      },

      {
        text: "Date",
        align: "left",
        sortable: false,
        value: "from_date",
        filterable: false,
      },
      {
        text: "Days",
        align: "left",
        sortable: false,
        value: "days",
        filterable: false,
      },
      {
        text: "OverTime",
        align: "left",
        sortable: false,
        value: "isOverTime",
        filterable: false,
      },
      {
        text: "Shift Type",
        align: "left",
        sortable: false,
        value: "shift_type",
        filterable: false,
      },
    ],
    pagination: {
      current: 1,
      total: 0,
      per_page: 10,
    },
    response: "",
    data: [],

    errors: [],
    headers_ids: [],
  }),

  computed: {},

  watch: {
    options: {
      handler() {
        this.getDataFromApi();
      },
      deep: true,
    },
  },
  created() {
    this.getDataFromApi();
  },

  methods: {
    getDataFromApi(url = this.endpoint, filter_column = "", filter_value = "") {
      this.loading = true;

      let { sortBy, sortDesc, page, itemsPerPage } = this.options;

      let sortedBy = sortBy ? sortBy[0] : "";
      let sortedDesc = sortDesc ? sortDesc[0] : "";

      let options = {
        params: {
          page: page,
          sortBy: sortedBy,
          sortDesc: sortedDesc,
          per_page: itemsPerPage,
          pagination: true,
          company_id: this.$auth.user.company_id,
        },
      };

      //if (filter_value != "") options.params[filter_column] = filter_value;
      this.perPage = itemsPerPage;
      this.$axios
        .get(`get_shifts_by_employee/${this.system_user_id}`, {
          params: options.params,
        })
        .then(({ data }) => {
          // if (filter_column != "" && data.data.length == 0) {
          //   this.snack = true;
          //   this.snackColor = "error";
          //   this.snackText = "No Results Found";
          //   this.loading = false;
          //   return false;
          // }
          this.schdulesList = data.data;
          this.pagination.current = data.current_page;
          this.pagination.total = data.last_page;
          this.loading = false;

          if (this.schdulesList.length == 0) {
            this.displayNoRecords = true;
          }

          this.totalRowsCount = data.total;
        });

      //this.loading = false;
    },
  },
};
</script>
