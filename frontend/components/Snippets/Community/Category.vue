<template>
  <div v-if="can('employee_access')">
    <div class="text-center ma-2">
      <v-snackbar v-model="snackbar" small top="top" :color="color">
        {{ response }}
      </v-snackbar>
    </div>
    <div v-if="!loading">
      <v-dialog persistent v-model="DialogBox" width="500">
        <v-card>
          <v-toolbar class="popup_background" flat>
            {{ formAction }} Room Category

            <v-spacer></v-spacer>
            <span>
              <v-icon class="ml-2" @click="DialogBox = false" dark>
                mdi mdi-close-circle</v-icon
              >
            </span>
          </v-toolbar>
          <v-container>
            <v-row>
              <v-col cols="12">
                <v-text-field
                  label="Room Category"
                  :disabled="disabled"
                  v-model="payload.name"
                  dense
                  class="text-center"
                  outlined
                  :hide-details="!errors.name"
                  :error="errors.name"
                  :error-messages="errors && errors.name ? errors.name[0] : ''"
                ></v-text-field>
              </v-col>
            </v-row>
          </v-container>
          <v-divider></v-divider>
          <v-card-actions>
            <v-spacer></v-spacer>
            <div class="text-right">
              <v-btn small color="grey white--text" @click="DialogBox = false">
                Close
              </v-btn>

              <v-btn
                v-if="can('employee_create') && formAction == 'Create'"
                small
                :loading="loading"
                color="primary"
                @click="submit"
              >
                Submit
              </v-btn>
              <v-btn
                v-else-if="can('employee_create') && formAction == 'Edit'"
                small
                :loading="loading"
                color="primary"
                @click="update_data"
              >
                Update
              </v-btn>
            </div>
          </v-card-actions>
        </v-card>
      </v-dialog>
      <v-snackbar v-model="snack" :timeout="3000" :color="snackColor">
        {{ snackText }}

        <template v-slot:action="{ attrs }">
          <v-btn v-bind="attrs" text @click="snack = false"> Close </v-btn>
        </template>
      </v-snackbar>
      <div v-if="can(`employee_view`)">
        <v-card elevation="0">
          <v-toolbar class="mb-2" dense flat>
            <v-toolbar-title
              ><span>{{ Model }} </span></v-toolbar-title
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
                <v-icon class="ml-2" @click="clearFilters" dark
                  >mdi mdi-reload</v-icon
                >
              </v-btn>
            </span>
            <v-spacer></v-spacer>
            <span>
              <v-btn
                dense
                small
                class="primary"
                text
                title="Create Room Category"
                @click="addItem"
              >
                Create Room Category
                <v-icon right dark>mdi-plus-circle-outline</v-icon>
              </v-btn>
            </span>
          </v-toolbar>
          <v-data-table
            dense
            :headers="headers"
            :items="data"
            model-value="data.id"
            :loading="loadinglinear"
            :options.sync="options"
            :footer-props="{
              itemsPerPageOptions: [100, 500, 1000],
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
                      @click:clear="
                        filters[header.value] = '';
                        applyFilters();
                      "
                      :hide-details="true"
                      v-if="header.filterable && !header.filterSpecial"
                      v-model="filters[header.value]"
                      :id="header.value"
                      @input="applyFilters(header.key, $event)"
                      outlined
                      dense
                      autocomplete="off"
                    ></v-text-field>
                  </v-container>
                </td>
              </tr>
            </template>

            <!-- <template v-slot:item.email="{ item }">
                      {{ item?.employee?.user?.email }}
                    </template> -->

            <template v-slot:item.options="{ item }">
              <v-menu bottom left>
                <template v-slot:activator="{ on, attrs }">
                  <v-btn dark-2 icon v-bind="attrs" v-on="on">
                    <v-icon>mdi-dots-vertical</v-icon>
                  </v-btn>
                </template>
                <v-list width="120" dense>
                  <v-list-item @click="viewItem(item)">
                    <v-list-item-title style="cursor: pointer">
                      <v-icon color="secondary" small> mdi-eye </v-icon>
                      View
                    </v-list-item-title>
                  </v-list-item>
                  <v-list-item @click="editItem(item)">
                    <v-list-item-title style="cursor: pointer">
                      <v-icon color="secondary" small> mdi-pencil </v-icon>
                      Edit
                    </v-list-item-title>
                  </v-list-item>
                  <v-list-item @click="deleteItem(item)">
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
    </div>
    <Preloader v-else />
  </div>

  <NoAccess v-else />
</template>

<script>
import "cropperjs/dist/cropper.css";
import VueCropper from "vue-cropperjs";

export default {
  props: ["id"],
  components: {
    VueCropper,
  },

  data: () => ({
    originalURL: `https://mytime2cloud.com/register/visitor/`,
    fullCompanyLink: ``,
    encryptedID: "",
    fullLink: "",
    qrCodeDataURL: "",
    qrCompanyCodeDataURL: "",
    disabled: false,
    openTimePicker: false,
    closeTimePicker: false,

    menu: false,
    payload: {
      flat_number: "",
      room_category: "",
      manager_name: "",
      number: "",
      emergency_phone: "",
      email: "",
      open_time: "",
      close_time: "",
      zone_id: 1,
      weekend: 1,
      webaccess: 1,
    },

    tab: null,

    totalRowsCount: 0,
    filters: {},
    isFilter: false,
    sortBy: "id",
    sortDesc: false,
    snack: false,
    snackColor: "",
    snackText: "",
    loadinglinear: true,
    displayErrormsg: false,
    image: "",
    mime_type: "",
    cropedImage: "",
    cropper: "",
    autoCrop: false,
    dialogCropping: false,
    tabMenu: [],
    tab: "0",
    employeeId: 0,
    employeeObject: {},
    attrs: [],
    dialog: false,
    editDialog: false,
    viewDialog: false,
    selectedFile: "",
    DialogBox: false,
    m: false,
    expand: false,
    expand2: false,
    boilerplate: false,
    right: true,
    rightDrawer: false,
    drawer: true,
    tab: null,
    selectedItem: 1,
    on: "",
    files: "",
    search: "",
    loading: false,
    //total: 0,
    next_page_url: "",
    prev_page_url: "",
    current_page: 1,
    per_page: 1000,
    ListName: "",
    color: "background",
    response: "",
    snackbar: false,
    btnLoader: false,
    max_employee: 0,
    employee: {
      title: "Mr",
      display_name: "",
      employee_id: "",
      system_user_id: "",
    },
    upload: {
      name: "",
    },
    previewImage: null,

    pagination: {
      current: 1,
      total: 0,
      per_page: 10,
    },
    options: {},
    Model: "Room Category",
    endpoint: "room-category",
    search: "",
    snackbar: false,
    ids: [],
    loading: false,
    //total: 0,
    editedIndex: -1,
    response: "",
    data: [],
    errors: [],
    designations: [],
    roles: [],
    employees: [],
    department_filter_id: "",
    dialogVisible: false,
    payloadOptions: {},
    // "": "03:50:00",
    // "": "08:50:00",
    // "zone_id": 1,
    // "weekend": true,
    // "webaccess": true,
    headers: [
      {
        text: "Room Category",
        align: "left",
        sortable: true,
        key: "name",
        value: "name",
        filterable: true,
        filterSpecial: false,
      },
      {
        text: "Details",
        align: "left",
        sortable: false,
        key: "options",
        value: "options",
      },
    ],
    formAction: "Create",
  }),

  async created() {
    this.loading = false;
    this.boilerplate = true;

    this.payloadOptions = {
      params: {
        per_page: 10,
        company_id: this.id,
      },
    };

    this.getDataFromApi();
  },

  mounted() {},
  watch: {
    options: {
      handler() {
        this.getDataFromApi();
      },
      deep: true,
    },
  },
  methods: {
    encrypt() {
      this.encryptedID = this.$crypto.encrypt(id);
      // this.fullLink = this.originalURL + this.encryptedID;
    },
    closeViewDialog() {
      this.viewDialog = false;
    },
    caps(str) {
      if (str == "" || str == null) {
        return "---";
      } else {
        let res = str.toString();
        return res.replace(/\b\w/g, (c) => c.toUpperCase());
      }
    },
    closePopup() {
      //croppingimagestep5
      this.$refs.attachment_input.value = null;
      this.dialogCropping = false;
    },
    close() {
      this.dialog = false;
      this.errors = [];
      setTimeout(() => {}, 300);
    },
    can(per) {
      return this.$pagePermission.can(per, this);
    },

    onPageChange() {
      this.getDataFromApi();
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
    getDataFromApi(url = this.endpoint) {
      //this.loading = true;
      this.loadinglinear = true;

      let { sortBy, sortDesc, page, itemsPerPage } = this.options;

      let sortedBy = sortBy ? sortBy[0] : "";
      let sortedDesc = sortDesc ? sortDesc[0] : "";
      let options = {
        params: {
          page: page,
          sortBy: sortedBy,
          sortDesc: sortedDesc,
          per_page: itemsPerPage, //this.pagination.per_page,
          company_id: this.$auth.user.company_id,
          ...this.filters,
        },
      };

      this.$axios.get(this.endpoint, options).then(({ data }) => {
        this.data = data.data;
        //this.server_datatable_totalItems = data.total;
        this.pagination.current = data.current_page;
        this.pagination.total = data.last_page;

        this.totalRowsCount = data.total;

        this.data.length == 0
          ? (this.displayErrormsg = true)
          : (this.displayErrormsg = false);

        this.loadinglinear = false;
      });
    },
    addItem() {
      this.disabled = false;
      this.formAction = "Create";
      this.DialogBox = true;
      this.payload = {};
    },
    editItem(item) {
      this.disabled = false;
      this.formAction = "Edit";
      this.DialogBox = true;
      this.payload = item;
      this.previewImage = item.logo;
    },
    viewItem(item) {
      this.disabled = true;
      this.formAction = "View";
      this.DialogBox = true;
      this.payload = item;
      this.previewImage = item.logo;
      this.fullLink =
        this.originalURL + this.$auth.user.company_id + "-" + item.id;
      this.fullCompanyLink = this.originalURL + this.$auth.user.company_id;
      this.generateQRCode(this.fullLink);
      this.generateCompanyQRCode(this.fullCompanyLink);
    },
    deleteItem(item) {
      confirm(
        "Are you sure you wish to delete , to mitigate any inconvenience in future."
      ) &&
        this.$axios
          .delete(`${this.endpoint}/${item.id}`)
          .then(({ data }) => {
            this.getDataFromApi();
            this.snackbar = true;
            this.response = "Record deleted successfully";
          })
          .catch((err) => console.log(err));
    },
    close() {
      this.dialog = false;
      setTimeout(() => {
        this.editedItem = Object.assign({}, this.defaultItem);
        this.editedIndex = -1;
      }, 300);
    },

    submit() {
      this.payload.company_id = this.$auth.user.company_id;
      this.$axios
        .post(this.endpoint, this.payload)
        .then(({ data }) => {
          // this.encrypt(data.record.id);
          this.errors = [];
          this.snackbar = true;
          this.response = "Room Category inserted successfully";
          this.getDataFromApi();
          this.DialogBox = false;
          this.dialog = true;
        })
        .catch(({ response }) => {
          if (!response) {
            return false;
          }
          let { status, data, statusText } = response;

          if (status && status == 422) {
            this.errors = data.errors;
            return;
          }

          this.snackbar = true;
          this.response = statusText;
        });

      // }
    },

    update_data() {
      this.payload.company_id = this.$auth.user.company_id;
      this.$axios
        .put(this.endpoint + "/" + this.payload.id, this.payload)
        .then(({ data }) => {
          this.errors = [];
          this.snackbar = true;
          this.response = "Room Category updated successfully";
          this.getDataFromApi();
          this.DialogBox = false;
        })
        .catch(({ response }) => {
          if (!response) {
            return false;
          }
          let { status, data, statusText } = response;

          if (status && status == 422) {
            this.errors = data.errors;
            return;
          }

          this.snackbar = true;
          this.response = statusText;
        });
    },
  },
};
</script>
