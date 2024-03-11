<template>
  <div v-if="can('automation_access')">
    <div class="text-center ma-2">
      <v-snackbar v-model="snackbar" top="top" color="secondary" elevation="24">
        {{ response }}
      </v-snackbar>
    </div>

    <v-dialog v-model="dialogviewHisotry" width="900" :key="newDialogKey">
      <v-card>
        <v-card-title dense class="popup_background">
          History
          <v-spacer></v-spacer>
          <v-icon @click="dialogviewHisotry = false" outlined dark>
            mdi mdi-close-circle
          </v-icon>
        </v-card-title>
        <v-card-text>
          <v-data-table
            flat
            dense
            :headers="headers_table_history"
            :items="data_history"
            :options.sync="options_history"
            :footer-props="{
              itemsPerPageOptions: [10, 50, 100, 500, 1000],
            }"
            class="elevation-1"
            :server-items-length="totalRowsCount_history"
          >
            <template v-slot:item.sno="{ item, index }">
              {{
                currentPage_history
                  ? (currentPage_history - 1) * perPage_history +
                    (cumulativeIndex_history + data_history.indexOf(item))
                  : cumulativeIndex_history + data_history.indexOf(item)
              }}
            </template>
            <template v-slot:item.created_at="{ item }">
              {{ $dateFormat.format4(item.created_at) }}
            </template>
            <template v-slot:item.whatsapp_number="{ item }">
              {{ item.whatsapp_number || "---" }}
            </template>
            <template v-slot:item.email="{ item }">
              {{ item.email || "---" }}
            </template>
          </v-data-table>
        </v-card-text>
      </v-card>
    </v-dialog>
    <div v-if="!preloader">
      <div class="text-center">
        <v-dialog persistent v-model="dialog" width="500">
          <v-card>
            <v-card-title dense class="popup_background">
              Send Test message to Whatsapp
              <v-spacer></v-spacer>
              <v-icon @click="dialog = false" outlined dark>
                mdi mdi-close-circle
              </v-icon>
            </v-card-title>
            <v-card-text class="mt-4">
              <v-text-field
                dense
                outlined
                placeholder="number"
                v-model="number"
              >
              </v-text-field>
              <v-textarea
                dense
                outlined
                placeholder="message"
                v-model="message"
              >
                Hello
              </v-textarea>
            </v-card-text>

            <!-- <v-divider></v-divider> -->

            <v-card-actions>
              <v-spacer></v-spacer>
              <v-btn class="primary" @click="send" small dense> Send </v-btn>
            </v-card-actions>
          </v-card>
        </v-dialog>
      </div>

      <v-navigation-drawer
        v-model="dialogNew"
        bottom
        temporary
        right
        fixed
        style="width: 300px"
      >
        <v-card>
          <v-card-title dense class="popup_background">
            {{ editItemPayload ? "Edit" : "New" }} Automation
            <v-spacer></v-spacer>
            <v-icon @click="dialogNew = false" outlined dark>
              mdi mdi-close-circle
            </v-icon>
          </v-card-title>
          <v-card-text class="mt-4">
            <Automation
              @getDataFromApi="getDataFromApi"
              :editItemPayload="editItemPayload"
              :key="newDialogKey"
              @closePopup="closePopup"
            ></Automation>
          </v-card-text>
        </v-card>
      </v-navigation-drawer>
      <v-card class="mb-5 mt-2" elevation="0">
        <v-toolbar class="rounded-md" dense flat>
          <v-toolbar-title><span> Automation List</span></v-toolbar-title>
          <span>
            <v-btn
              dense
              class="ma-0 px-0"
              x-small
              :ripple="false"
              text
              title="Reload"
            >
              <v-icon class="ml-2" @click="getDataFromApi()" dark
                >mdi mdi-reload</v-icon
              >
            </v-btn>
          </span>
          <span>
            <v-btn
              x-small
              :ripple="false"
              text
              title="Filter"
              @click="toggleFilter()"
            >
              <v-icon dark>mdi-filter</v-icon>
            </v-btn>
          </span>

          <v-spacer></v-spacer>

          <span>
            <v-btn
              v-if="can(`automation_contnet_access`)"
              x-small
              :ripple="false"
              title="EMail Content"
              @click="gotoPage('/automation_mail_content')"
              color="violet"
              class="primary mr-5"
            >
              Email Content
            </v-btn>
          </span>
          <span>
            <v-btn
              v-if="can('automation_create')"
              x-small
              :ripple="false"
              title="Add Notification"
              @click="openNewPage()"
              color="violet"
              class="primary"
            >
              Add+
            </v-btn>
          </span>
        </v-toolbar>

        <v-snackbar v-model="snack" :timeout="3000" :color="snackColor">
          {{ snackText }}

          <template v-slot:action="{ attrs }">
            <v-btn v-bind="attrs" text @click="snack = false"> Close </v-btn>
          </template>
        </v-snackbar>
        <v-data-table
          flat
          dense
          :headers="headers_table"
          :items="data"
          model-value="data.id"
          :loading="loading"
          :options.sync="options"
          :footer-props="{
            itemsPerPageOptions: [10, 50, 100, 500, 1000],
          }"
          class="elevation-1"
          :server-items-length="totalRowsCount"
        >
          <template v-slot:header="{ props: { headers } }">
            <tr v-if="isFilter">
              <td v-for="header in headers" :key="header.text">
                <v-container>
                  <v-text-field
                    clearable
                    :hide-details="true"
                    v-if="header.filterable && !header.filterSpecial"
                    v-model="filters[header.value]"
                    :id="header.value"
                    @input="applyFilters(header.key, $event)"
                    outlined
                    dense
                    autocomplete="off"
                  ></v-text-field>

                  <v-select
                    :id="header.key"
                    :hide-details="true"
                    v-if="
                      header.filterSpecial &&
                      header.value == 'branch.branch_name'
                    "
                    clearable
                    outlined
                    dense
                    small
                    v-model="filters[header.key]"
                    item-text="branch_name"
                    item-value="id"
                    :items="[
                      { branch_name: `All Branches`, id: `` },
                      ...branchesList,
                    ]"
                    placeholder="Branch"
                    @change="applyFilters(header.key, id)"
                  ></v-select>
                  <v-select
                    :id="header.key"
                    :hide-details="true"
                    v-if="header.filterSpecial && header.value == 'frequency'"
                    clearable
                    outlined
                    dense
                    small
                    v-model="filters[header.key]"
                    item-text="name"
                    item-value="id"
                    :items="[
                      { name: `All`, id: `` },
                      { name: `Daily`, id: `Daily` },
                      { name: `Weekly`, id: `Weekly` },
                      { name: `Monthly`, id: `Monthly` },
                    ]"
                    placeholder="Report Type"
                    @change="applyFilters(header.key, id)"
                  ></v-select>
                  <v-select
                    :id="header.key"
                    :hide-details="true"
                    v-if="header.filterSpecial && header.value == 'medium'"
                    clearable
                    outlined
                    dense
                    small
                    v-model="filters[header.key]"
                    item-text="name"
                    item-value="id"
                    :items="[
                      { name: `All`, id: `` },
                      { name: `Email`, id: `Email` },
                      { name: `Whatsapp`, id: `Whatsapp` },
                    ]"
                    placeholder="Medium"
                    @change="applyFilters(header.key, id)"
                  ></v-select>
                </v-container>
              </td>
            </tr>
          </template>
          <template v-slot:item.sno="{ item, index }">
            {{
              currentPage
                ? (currentPage - 1) * perPage +
                  (cumulativeIndex + data.indexOf(item))
                : ""
            }}
          </template>
          <template v-slot:item.medium="{ item }">
            <div
              v-for="(medium, i) in item.mediums"
              :key="i"
              class="ma-1"
              small
              color="primary"
            >
              {{ medium }}

              <br />
            </div>
          </template>
          <template v-slot:item.frequency="{ item, index }">
            {{ item.frequency }}
            <div class="secondary-value">
              {{ item.frequency == "Weekly" ? getDayName(item.day) : "" }}
              {{ item.frequency == "Monthly" ? item.day : "" }}
            </div>
          </template>
          <template v-slot:item.manager1="{ item }">
            {{
              (item.managers && item.managers[0] && item.managers[0].name) ||
              "---"
            }}
            <div class="secondary-value">
              {{
                (item.managers && item.managers[0] && item.managers[0].email) ||
                "---"
              }}
              <br />
              {{
                (item.managers &&
                  item.managers[0] &&
                  item.managers[0].whatsapp_number) ||
                "---"
              }}
            </div>
          </template>
          <template v-slot:item.manager2="{ item }">
            {{
              (item.managers && item.managers[1] && item.managers[1].name) ||
              "---"
            }}
            <div class="secondary-value">
              {{
                (item.managers && item.managers[1] && item.managers[1].email) ||
                "---"
              }}
              <br />
              {{
                (item.managers &&
                  item.managers[1] &&
                  item.managers[1].whatsapp_number) ||
                "---"
              }}
            </div>
          </template>
          <template v-slot:item.manager3="{ item }">
            {{
              (item.managers && item.managers[2] && item.managers[2].name) ||
              "---"
            }}
            <div class="secondary-value">
              {{
                (item.managers && item.managers[2] && item.managers[2].email) ||
                "---"
              }}
              <br />
              {{
                (item.managers &&
                  item.managers[2] &&
                  item.managers[2].whatsapp_number) ||
                "---"
              }}
            </div>
          </template>
          <template v-slot:item.last_sent="{ item }">
            {{
              (item.logs &&
                item.logs[0] &&
                $dateFormat.format4(
                  item.logs[item.logs.length - 1].created_at
                )) ||
              "---"
            }}
          </template>

          <template v-slot:item.options="{ item }">
            <v-menu bottom left>
              <template v-slot:activator="{ on, attrs }">
                <v-btn dark-2 icon v-bind="attrs" v-on="on">
                  <v-icon>mdi-dots-vertical</v-icon>
                </v-btn>
              </template>
              <v-list width="120" dense>
                <v-list-item
                  v-if="can('automation_view')"
                  @click="viewItem(item)"
                >
                  <v-list-item-title style="cursor: pointer">
                    <v-icon color="secondary" small> mdi-eye </v-icon>
                    View
                  </v-list-item-title>
                </v-list-item>
                <v-list-item
                  v-if="can('automation_edit')"
                  @click="editItem(item)"
                >
                  <v-list-item-title style="cursor: pointer">
                    <v-icon color="secondary" small> mdi-pencil </v-icon>
                    Edit
                  </v-list-item-title>
                </v-list-item>
                <v-list-item
                  v-if="can('automation_delete')"
                  @click="deleteItem(item)"
                >
                  <v-list-item-title style="cursor: pointer">
                    <v-icon color="error" small> mdi-delete </v-icon>
                    Delete
                  </v-list-item-title>
                </v-list-item>
              </v-list>
            </v-menu>
          </template>
        </v-data-table>
      </v-card>
    </div>
    <Preloader v-else />
  </div>
  <NoAccess v-else />
</template>

<script>
import Automation from "../../components/automation.vue";

export default {
  components: { Automation },

  data: () => ({
    dialogviewHisotry: false,
    totalRowsCount_history: 10,
    branchesList: [],
    editItemPayload: null,
    newDialogKey: 1,

    perPage_history: 10,
    cumulativeIndex_history: 1,
    currentPage_history: 1,

    perPage: 10,
    cumulativeIndex: 1,
    currentPage: 1,
    dialogNew: false,
    showFilters: false,
    filters: {},
    isFilter: false,
    totalRowsCount: 0,
    datatable_search_textbox: "",
    filter_employeeid: "",
    snack: false,
    snackColor: "",
    snackText: "",

    dialog: false,
    color: "primary",
    endpoint: "report_notification",
    e1: 1,
    menu2: false,
    preloader: false,
    loading: false,
    response: false,
    id: "",
    snackbar: false,
    to: "",
    cc: "",
    bcc: "",
    number: "",
    message: "",
    payload: {
      report_types: [],
      mediums: [],
      frequency: null,
      time: null,
      tos: [],
      ccs: [],
      bccs: [],
    },
    data: [],
    options: {},
    errors: [],
    data_history: [],
    options_history: {},
    days: [
      { id: 1, name: "Monday" },
      { id: 2, name: "Tuesday" },
      { id: 3, name: "Wednesday" },
      { id: 4, name: "Thursday" },
      { id: 5, name: "Friday" },
      { id: 6, name: "Saturday" },
      { id: 0, name: "Sunday" },
    ],
    headers_table_history: [
      {
        text: "#",
        align: "left",
        sortable: false,
        key: "title",
        value: "sno",
        filterable: false,
        filterSpecial: false,
      },
      {
        text: "Email",
        align: "left",
        sortable: false,
        key: "email",
        value: "email",
        filterable: false,
        filterSpecial: false,
      },
      {
        text: "Whatsapp Number",
        align: "left",
        sortable: false,
        key: "whatsapp_number",
        value: "whatsapp_number",
        filterable: false,
        filterSpecial: false,
      },
      {
        text: "Sent at ",
        align: "left",
        sortable: false,
        key: "created_at",
        value: "created_at",
        filterable: false,
        filterSpecial: false,
      },
    ],
    headers_table: [
      {
        text: "#",
        align: "left",
        sortable: false,
        key: "title",
        value: "sno",
        filterable: false,
        filterSpecial: false,
      },

      {
        text: "Subject",
        align: "left",
        sortable: true,
        key: "subject",
        value: "subject",
        filterable: true,
        filterSpecial: false,
      },
      {
        text: "Type",
        align: "left",
        sortable: true,
        key: "frequency",
        value: "frequency",
        filterable: true,
        filterSpecial: true,
      },
      {
        text: "Time",
        align: "left",
        sortable: true,
        key: "time",
        value: "time",
        filterable: true,
        filterSpecial: false,
      },
      {
        text: "Manager1",
        align: "left",
        sortable: true,
        key: "manager",
        value: "manager1",
        filterable: true,
        filterSpecial: false,
      },
      {
        text: "Manager2",
        align: "left",
        sortable: true,
        key: "manager",
        value: "manager2",
        filterable: true,
        filterSpecial: false,
      },
      {
        text: "Manager3",
        align: "left",
        sortable: true,
        key: "manager",
        value: "manager3",
        filterable: true,
        filterSpecial: false,
      },
      {
        text: "Media",
        align: "left",
        sortable: true,
        key: "medium",
        value: "medium",
        filterable: true,
        filterSpecial: true,
      },

      {
        text: "Last Sent",
        align: "left",
        sortable: true,
        key: "time",
        value: "last_sent",
        filterable: true,
        filterSpecial: false,
      },
      {
        text: "Options",
        align: "left",
        sortable: false,
        key: "time",
        value: "options",
        filterable: false,
        filterSpecial: false,
      },
    ],
    viewHistoryItem: null,
  }),
  watch: {
    options: {
      handler() {
        this.getDataFromApi();
      },
      deep: true,
    },
    // options_history: {
    //   handler() {
    //     this.getDataFromApi_history(this.viewHistoryItem);
    //   },
    //   deep: true,
    // },
  },
  created() {
    this.preloader = false;
    this.id = this.$auth?.user?.company?.id;
    this.getDataFromApi();
    if (!this.$auth.user.branch_id) {
      let branch_header = [
        {
          text: "Branch",
          align: "left",
          sortable: true,
          key: "branch_id", //sorting
          value: "branch.branch_name", //edit purpose

          filterable: true,
          filterSpecial: true,
        },
      ];
      this.headers_table.splice(1, 0, ...branch_header);
    }

    this.getbranchesList();
  },
  methods: {
    getDayName(id) {
      let day = this.days.filter((item) => item.id == id);
      if (day.length == 1) {
        return day[0].name;
      }
    },
    getbranchesList() {
      this.payloadOptions = {
        params: {
          company_id: this.$auth.user.company_id,
        },
      };

      this.$axios.get(`branches_list`, this.payloadOptions).then(({ data }) => {
        this.branchesList = data;
      });
    },
    openNewPage() {
      ++this.newDialogKey;
      this.editItemPayload = null;
      this.dialogNew = true;
    },
    gotoPage(url) {
      this.$router.push(url);
    },

    closePopup(data) {
      this.snackbar = data.status;
      this.response = data.message;

      this.dialogNew = false;
    },
    datatable_cancel() {
      this.datatable_search_textbox = "";
    },
    datatable_open() {
      this.datatable_search_textbox = "";
    },
    datatable_close() {
      this.loading = false;
    },
    send() {
      // https://ezwhat.com/api/send.php?number=923108559858&type=text&message=test%20message&instance_id=64466B01B7926&access_token=a27e1f9ca2347bb766f332b8863ebe9f
      this.$axios
        .get(
          `https://ezwhat.com/api/send.php?number=${this.number}&type=text&message=${this.message}&instance_id=64466B01B7926&access_token=a27e1f9ca2347bb766f332b8863ebe9f`
        )
        .then(({ data }) => console.log(data));
    },
    can(per) {
      return this.$pagePermission.can(per, this);
    },

    viewItem(item) {
      //++this.newDialogKey;

      this.dialogviewHisotry = true;
      this.viewHistoryItem = item;
      this.getDataFromApi_history(item);
    },
    getDataFromApi_history(item) {
      this.loading = true;
      this.data_history = [];
      let { sortBy, sortDesc, page, itemsPerPage } = this.options_history;

      let sortedBy = sortBy ? sortBy[0] : "";
      let sortedDesc = sortDesc ? sortDesc[0] : "";
      let options = {
        params: {
          page: page,
          sortBy: sortedBy,
          sortDesc: sortedDesc,
          per_page: itemsPerPage,
          company_id: this.$auth.user.company_id,
          notification_id: item.id,
        },
      };
      this.currentPage_history = page;
      this.perPage_history = itemsPerPage;

      this.$axios
        .get(`report_notification_logs?page=${page}`, options)
        .then(({ data }) => {
          this.data_history = data.data;
          this.totalRowsCount_history = data.total;
          this.total_history = data.total;
          this.loading = false;
        });
    },
    editItem(item) {
      //this.$router.push("/report_notifications/" + item.id);

      ++this.newDialogKey;
      this.dialogNew = true;
      this.editItemPayload = item;
    },

    deleteItem(item) {
      confirm(
        "Are you sure you wish to delete , to mitigate any inconvenience in future."
      ) &&
        this.$axios
          .delete(this.endpoint + "/" + item.id)
          .then(({ data }) => {
            if (!data.status) {
              this.errors = data.errors;
            } else {
              this.snackbar = data.status;
              this.response = data.message;
              this.getDataFromApi();
            }
          })
          .catch((err) => console.log(err));
    },

    add_to() {
      this.payload.tos.push(this.to);
      this.to = "";
    },
    add_cc() {
      this.payload.ccs.push(this.cc);
      this.cc = "";
    },
    add_bcc() {
      this.payload.bccs.push(this.bcc);
      this.bcc = "";
    },
    deleteTO(i) {
      this.payload.tos.splice(i, 1);
    },

    deleteCC(i) {
      this.payload.ccs.splice(i, 1);
    },

    deleteBCC(i) {
      this.payload.bccs.splice(i, 1);
    },
    applyFilters() {
      this.getDataFromApi();
    },
    toggleFilter() {
      // this.filters = {};
      this.isFilter = !this.isFilter;
    },
    clearFilters() {
      this.filters = {};

      this.isFilter = false;
      this.getDataFromApi();
    },
    getDataFromApi(url = this.endpoint, filter_column = "", filter_value = "") {
      if (
        (filter_column == "serach_medium" ||
          filter_column == "serach_email_recipients") &&
        filter_value != "" &&
        filter_value.length <= 5
      ) {
        this.snack = true;
        this.snackColor = "error";
        this.snackText = "Minimum 5 Characters to filter ";
        this.loading = false;
        return false;
      }
      this.loading = true;
      if (url == "") {
        url = this.endpoint;
      }
      let { sortBy, sortDesc, page, itemsPerPage } = this.options;

      let sortedBy = sortBy ? sortBy[0] : "";
      let sortedDesc = sortDesc ? sortDesc[0] : "";
      let options = {
        params: {
          page: page,
          sortBy: sortedBy,
          sortDesc: sortedDesc,
          per_page: itemsPerPage,
          company_id: this.$auth.user.company_id,
          role_type: "employee",
          ...this.filters,
        },
      };
      this.currentPage = page;
      this.perPage = itemsPerPage;
      if (filter_column != "") options.params[filter_column] = filter_value;

      this.$axios.get(`${url}?page=${page}`, options).then(({ data }) => {
        if (filter_column != "" && data.data.length == 0) {
          this.snack = true;
          this.snackColor = "error";
          this.snackText = "No Results Found";
          this.loading = false;
          return false;
        }
        this.data = data.data;
        this.totalRowsCount = data.total;
        this.total = data.total;
        this.loading = false;
      });
    },
  },
};
</script>
