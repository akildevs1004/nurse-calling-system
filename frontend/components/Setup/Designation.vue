<template>
  <div v-if="can(`designation_access`)">
    <div class="text-center ma-2">
      <v-snackbar v-model="snackbar" top="top" color="secondary" elevation="24">
        {{ response }}
      </v-snackbar>
    </div>
    <!-- <v-row class="mt-5 mb-5">
        <v-col cols="12">
          <div class="text-right">
  
            <v-btn small class="primary" to="/department">
              <v-icon small>mdi-arrow-left</v-icon>&nbsp;Back
            </v-btn>
  
          </div>
        </v-col>
      </v-row> -->

    <v-row>
      <v-dialog
        persistent
        v-model="dialogForm"
        :fullscreen="false"
        width="500px"
      >
        <v-card>
          <v-card-title dense class="popup_background">
            <span>{{ formTitle }} {{ Model }}</span>
            <v-spacer></v-spacer>
            <v-icon @click="dialogForm = false" outlined dark>
              mdi mdi-close-circle
            </v-icon>
          </v-card-title>
          <v-card-text>
            <v-container>
              <v-row class="mt-2">
                <v-col cols="12">
                  <v-text-field
                    v-model="editedItem.name"
                    placeholder="Designation"
                    outlined
                    dense
                  ></v-text-field>
                  <span v-if="errors && errors.name" class="error--text">{{
                    errors.name[0]
                  }}</span>
                </v-col>
                <v-col cols="12">
                  <v-autocomplete
                    v-model="editedItem.department_id"
                    :items="departments"
                    item-text="name"
                    item-value="id"
                    placeholder="Select Departments"
                    outlined
                    dense
                  >
                  </v-autocomplete>
                  <span
                    v-if="errors && errors.department_id"
                    class="error--text"
                    >{{ errors.department_id[0] }}</span
                  >
                </v-col>
                <v-col md="12" lg="12" class="text-right" style="padding: 0px">
                  <v-btn class="primary" @click="save">Save</v-btn>
                </v-col>
              </v-row>
            </v-container>
          </v-card-text>
        </v-card>
      </v-dialog>
      <v-col md="12">
        <!-- <Back color="primary" /> -->

        <v-card class="mb-5 mt-2" elevation="0">
          <v-toolbar class="rounded-md" dense flat>
            <v-toolbar-title
              ><span> {{ Model }}s List</span></v-toolbar-title
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
                <v-icon class="ml-2" @click="clearFilters()" dark
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
                <v-icon dark white>mdi-filter</v-icon>
              </v-btn>
            </span>
            <v-spacer></v-spacer>
            <v-btn
              v-if="can(`designation_create`)"
              @click="newItem"
              small
              class="primary"
            >
              Designation +
            </v-btn>
          </v-toolbar>

          <!-- <v-text-field
              class=" form-control py-0 ma-1 mb-0 w-25 float-start custom-text-box floating shadow-none"
                  placeholder="Search..." solo flat @input="searchIt" v-model="search"
                  :hide-details="true"></v-text-field> -->

          <v-snackbar v-model="snack" :timeout="3000" :color="snackColor">
            {{ snackText }}

            <template v-slot:action="{ attrs }">
              <v-btn v-bind="attrs" text @click="snack = false"> Close </v-btn>
            </template>
          </v-snackbar>
          <v-data-table
            dense
            :headers="headers_table"
            :items="data"
            model-value="data.id"
            :loading="loading"
            :footer-props="{
              itemsPerPageOptions: [10, 50, 100, 500, 1000],
            }"
            class="elevation-1"
            :options.sync="options"
            :server-items-length="totalRowsCount"
          >
            <template v-slot:header="{ props: { headers } }">
              <tr v-if="isFilter">
                <td v-for="header in headers" :key="header.text">
                  <v-text-field
                    clearable
                    :hide-details="true"
                    v-if="header.filterable && !header.filterSpecial"
                    v-model="filters[header.key]"
                    :id="header.value"
                    @input="applyFilters(header.key, $event)"
                    outlined
                    dense
                    autocomplete="off"
                  ></v-text-field>
                </td>
              </tr>
            </template>
            <template v-slot:item.sno="{ item, index }">
              {{ ++index }}
            </template>
            <template v-slot:item.name="{ item }">
              {{ caps(item.name) }}
            </template>
            <template v-slot:item.department.name="{ item }">
              {{ caps(item.department && item.department.name) }}
            </template>
            <template v-slot:item.action="{ item }">
              <v-menu bottom left>
                <template v-slot:activator="{ on, attrs }">
                  <v-btn dark-2 icon v-bind="attrs" v-on="on">
                    <v-icon>mdi-dots-vertical</v-icon>
                  </v-btn>
                </template>
                <v-list width="120" dense>
                  <v-list-item
                    v-if="can(`designation_edit`)"
                    @click="editItem(item)"
                  >
                    <v-list-item-title style="cursor: pointer">
                      <v-icon color="secondary" small> mdi-pencil </v-icon>
                      Edit
                    </v-list-item-title>
                  </v-list-item>
                  <v-list-item
                    v-if="can(`designation_delete`)"
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
      </v-col>
    </v-row>
  </div>
  <NoAccess v-else />
</template>
<script>
export default {
  data: () => ({
    totalRowsCount: 0,

    showFilters: false,
    filters: {},
    isFilter: false,

    datatable_search_textbox: "",
    filter_employeeid: "",
    snack: false,
    snackColor: "",
    snackText: "",
    dialogForm: false,
    pagination: {
      current: 1,
      total: 0,
      per_page: 10,
    },
    Model: "Designation",
    options: {},
    endpoint: "designation",
    search: "",
    snackbar: false,
    dialog: false,
    ids: [],
    loading: false,
    total: 0,
    headers: [
      { text: "Designation", align: "left", sortable: false, value: "name" },
      {
        text: "Department",
        align: "left",
        sortable: false,
        value: "department.name",
      },
      { text: "Actions", align: "center", value: "action", sortable: false },
    ],
    headers_table: [
      {
        text: "#",
        align: "left",
        sortable: true,
        value: "sno",
      },
      {
        text: "Designation",
        align: "left",
        sortable: true,
        value: "name",
        filterable: true,
        key: "designation_name",
      },
      {
        text: "Department Name ",
        align: "left",
        sortable: true,
        value: "department.name",
        filterable: true,
        key: "department_name",
      },
      { text: "Actions", align: "center", value: "action", sortable: false },
    ],
    editedIndex: -1,
    editedItem: { name: "", department_id: "" },
    defaultItem: { name: "", department_id: "" },
    response: "",
    data: [],
    departments: [],
    errors: [],
  }),

  computed: {
    formTitle() {
      return this.editedIndex === -1 ? "New" : "Edit";
    },
  },

  watch: {
    options: {
      handler() {
        this.getDataFromApi();
      },
      deep: true,
    },

    dialog(val) {
      val || this.close();
      this.errors = [];
      this.search = "";
    },
  },
  created() {
    this.getDepartments();
    this.getDataFromApi();
  },

  methods: {
    datatable_save() {},
    datatable_cancel() {
      this.datatable_search_textbox = "";
    },
    datatable_open() {
      this.datatable_search_textbox = "";
    },
    datatable_close() {
      this.loading = false;
    },
    newItem() {
      this.dialogForm = true;
    },
    onPageChange() {
      this.getDataFromApi();
    },
    caps(str) {
      if (str == "" || str == null) {
        return "---";
      } else {
        let res = str.toString();
        return res.replace(/\b\w/g, (c) => c.toUpperCase());
      }
    },
    getDepartments() {
      let options = {
        params: {
          per_page: 100,
          company_id: this.$auth.user.company_id,
        },
      };
      this.$axios.get(`departments`, options).then(({ data }) => {
        this.departments = data.data;
      });
    },
    can(per) {
      return this.$pagePermission.can(per, this);
    },

    applyFilters() {
      this.getDataFromApi();
      this.from_menu_filter = false;
      this.to_menu_filter = false;
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
      if (url == "") url = this.endpoint;
      this.loading = true;

      const { sortBy, sortDesc, page, itemsPerPage } = this.options;

      let sortedBy = sortBy ? sortBy[0] : "";
      let sortedDesc = sortDesc ? sortDesc[0] : "";

      this.payloadOptions = {
        params: {
          page: page,
          sortBy: sortedBy,
          sortDesc: sortedDesc,
          per_page: itemsPerPage,
          sortBy: sortedBy,
          sortDesc: sortedDesc,
          per_page: itemsPerPage,
          ...this.filters,
          company_id: this.$auth.user.company_id,
        },
      };
      if (filter_column != "") {
        this.payloadOptions.params[filter_column] = filter_value;
      }
      this.$axios
        .get(`${url}?page=${page}`, this.payloadOptions)
        .then(({ data }) => {
          if (filter_column != "" && data.data.length == 0) {
            this.snack = true;
            this.snackColor = "error";
            this.snackText = "No Results Found";
            this.loading = false;
            return false;
          }
          this.totalRowsCount = data.total;
          this.data = data.data;

          this.pagination.current = data.current_page;
          this.pagination.total = data.last_page;
          this.loading = false;
        });
    },

    searchIt(e) {
      if (e.length == 0) {
        this.getDataFromApi();
      } else if (e.length > 2) {
        this.getDataFromApi(`${this.endpoint}/search/${e}`);
      }
    },

    editItem(item) {
      this.editedIndex = this.data.indexOf(item);
      this.editedItem = Object.assign({}, item);
      this.dialog = true;
      this.dialogForm = true;
    },

    delteteSelectedRecords() {
      let just_ids = this.ids.map((e) => e.id);
      confirm(
        "Are you sure you wish to delete selected records , to mitigate any inconvenience in future."
      ) &&
        this.$axios
          .post(`${this.endpoint}/delete/selected`, {
            ids: just_ids,
          })
          .then((res) => {
            if (!res.data.status) {
              this.errors = res.data.errors;
            } else {
              this.getDataFromApi();
              this.snackbar = res.data.status;
              this.ids = [];
              this.response = "Selected records has been deleted";
            }
          })
          .catch((err) => console.log(err));
    },

    deleteItem(item) {
      confirm(
        "Are you sure you wish to delete , to mitigate any inconvenience in future."
      ) &&
        this.$axios
          .delete(this.endpoint + "/" + item.id)
          .then(({ data }) => {
            const index = this.data.indexOf(item);
            this.data.splice(index, 1);
            this.snackbar = data.status;
            this.response = data.message;
          })
          .catch((err) => console.log(err));
    },

    close() {
      this.dialog = false;
      this.dialogForm = false;
      setTimeout(() => {
        this.editedItem = Object.assign({}, this.defaultItem);
        this.editedIndex = -1;
      }, 300);
    },

    save() {
      let payload = {
        name: this.editedItem.name,
        department_id: this.editedItem.department_id,
        company_id: this.$auth.user.company_id,
      };
      if (this.editedIndex > -1) {
        this.$axios
          .put(this.endpoint + "/" + this.editedItem.id, payload)
          .then(({ data }) => {
            if (!data.status) {
              this.errors = data.errors;
            } else {
              const index = this.data.findIndex(
                (item) => item.id == this.editedItem.id
              );
              this.getDataFromApi();
              this.snackbar = data.status;
              this.response = data.message;
              this.dialogForm = false;
              this.close();
            }
          })
          .catch((err) => console.log(err));
      } else {
        this.$axios
          .post(this.endpoint, payload)
          .then(({ data }) => {
            if (!data.status) {
              this.errors = data.errors;
            } else {
              this.getDataFromApi();
              this.snackbar = data.status;
              this.response = data.message;
              this.dialogForm = false;
              this.close();
              this.errors = [];
              this.search = "";
            }
          })
          .catch((res) => console.log(res));
      }
    },
  },
};
</script>
