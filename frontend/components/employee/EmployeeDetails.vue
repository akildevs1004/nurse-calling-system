<template>
  <v-card>
    <v-toolbar class="primary white--text background" dense dark
      >Profile Details
      <v-spacer></v-spacer>
      <v-btn @click="openDocumentDrawer" small class="primary"
        >Documents&nbsp;<v-icon small>
          mdi-file
          <!-- mdi-open-in-new -->
        </v-icon></v-btn
      >
      &nbsp;
      <v-icon @click="closeParentDialog" outlined dark color="white">
        mdi mdi-close-circle
      </v-icon>
    </v-toolbar>

    <v-card-text>
      <v-row class="pt-5">
        <v-col cols="3">
          <div class="mt-5" style="margin: 0 auto; width: 200px">
            <v-img
              style="
                width: 100%;
                height: 200px;
                border: 1px solid #5fafa3;
                border-radius: 50%;
                margin: 0 auto;
              "
              :src="employeeObject.profile_picture || '/no-profile-image.jpg'"
            ></v-img>
            <br />
            <div class="text-center">
              <strong>{{ employeeObject.full_name }}</strong>
            </div>
            <div class="text-center">
              <strong>Employee ID: {{ employeeObject.employee_id }}</strong>
            </div>
            <div class="text-center">
              <span v-html="formatJoiningDate"></span>
            </div>
          </div>
        </v-col>
        <v-col cols="2">
          <table>
            <tr>
              <td>
                <strong>Display Name </strong><br />{{
                  employeeObject.display_name
                }}
              </td>
            </tr>
            <tr>
              <td>
                <strong>Department </strong><br />{{
                  employeeObject.department.name
                }}
              </td>
            </tr>
            <tr>
              <td>
                <strong>Sub Department </strong><br />{{
                  employeeObject.sub_department.name
                }}
              </td>
            </tr>
            <tr>
              <td>
                <strong>Designation </strong><br />{{
                  employeeObject.designation.name
                }}
              </td>
            </tr>
            <tr>
              <td>
                <strong>Role </strong><br />{{
                  employeeObject?.user?.role?.name || "---"
                }}
              </td>
            </tr>
          </table>
        </v-col>
        <v-col cols="2">
          <table>
            <tr>
              <td>
                <strong>Contact</strong><br />
                {{ employeeObject.phone_number || "---" }}
                <br />
                {{ employeeObject.local_email || "---" }}
              </td>
            </tr>
            <tr>
              <td>
                <strong>Address</strong><br />{{
                  employeeObject.local_address || "---"
                }}
                <br />
                {{ employeeObject.local_city || "---" }}
                <span v-if="employeeObject.local_city">,</span>
                {{ employeeObject.local_country || "---" }}
              </td>
            </tr>
            <tr>
              <td>
                <strong>Qualification</strong><br />{{
                  employeeObject.qualification.certificate
                }}
              </td>
            </tr>
            <tr>
              <td>
                <strong>Bank</strong>
                <br />
                {{ employeeObject.bank.bank_name }}
                <br />
                {{ employeeObject.bank.address }}
                <br />
                {{ employeeObject.bank.account_no }}
              </td>
            </tr>
          </table>
        </v-col>
        <v-col cols="3" style="max-width: 20%">
          <table>
            <tr>
              <td>
                <strong>Timezone</strong><br />{{
                  employeeObject.timezone.timezone_name
                }}
              </td>
            </tr>
            <!-- <tr>
              <td>
                <strong>Shift</strong><br />{{
                  employeeObject.schedule.shift.name
                }}
                <br />
                {{ employeeObject.schedule.shift.working_hours }}
              </td>
            </tr> -->
            <tr>
              <td>
                <strong
                  >Passport : {{ employeeObject.passport.country }} </strong
                ><br />
                {{ employeeObject.passport.passport_no }}<br />Expired on
                <span v-if="employeeObject.passport.expiry_date">
                  {{ formatDate(employeeObject.passport.expiry_date) }}</span
                >
                <span v-else> --- </span>
              </td>
            </tr>
            <tr>
              <td>
                <strong>Emirates ID </strong><br />{{
                  employeeObject.emirate.emirate_id
                }}<br />Expired on
                <span v-if="employeeObject.emirate.expiry">
                  {{ formatDate(employeeObject.emirate.expiry) }}</span
                >
                <span v-else> --- </span>
              </td>
            </tr>
          </table>
        </v-col>
        <v-col cols="2">
          <table>
            <tr>
              <td>
                <div style="display: flex; align-items: center; height: 30px">
                  <strong style="width: 70px">Web Login</strong>
                  <v-switch disabled v-model="employeeObject.status"></v-switch>
                </div>
                Last Login :
                {{ last_login }}
                <!-- 08 June 2023 -->
              </td>
            </tr>
            <tr>
              <td>
                <div style="display: flex; align-items: center; height: 30px">
                  <strong style="width: 90px">Mobile Login</strong>
                  <v-switch
                    disabled
                    v-model="employeeObject.mobile_application"
                  ></v-switch>
                </div>
                <!-- Last Login <br />08 June 2023 -->
              </td>
            </tr>
            <!-- <tr>
              <td>
                <div style="display: flex; align-items: center; height: 30px">
                  <strong style="width: 70px">Overtime</strong>
                  <v-switch disabled v-model="switchValue"></v-switch>
                </div>
              </td>
            </tr> -->
          </table>
        </v-col>
      </v-row>
      <v-navigation-drawer v-model="drawer" bottom temporary right fixed>
        <v-toolbar class="background" dense dark
          >Documents
          <v-spacer></v-spacer>

          <v-icon @click="drawer = false" outlined dark color="white">
            mdi mdi-close-circle
          </v-icon>
        </v-toolbar>
        <table style="width: 100%; border-collapse: collapse; margin: 5px">
          <thead>
            <tr>
              <th
                style="
                  padding: 8px;
                  text-align: left;
                  border-bottom: 1px solid #ddd;
                "
              >
                Title
              </th>
              <th
                style="
                  padding: 8px;
                  text-align: left;
                  border-bottom: 1px solid #ddd;
                "
              >
                Document
              </th>
            </tr>
          </thead>
          <tbody>
            <tr v-for="(document, index) in document_list" :key="index">
              <td
                style="
                  padding: 8px;
                  text-align: left;
                  border-bottom: 1px solid #ddd;
                "
              >
                {{ document.title }}
              </td>
              <td
                style="
                  padding: 8px;
                  text-align: left;
                  border-bottom: 1px solid #ddd;
                "
              >
                <a :href="document.attachment" download target="_blank">
                  <v-icon color="primary"> mdi-download </v-icon>
                </a>
              </td>
            </tr>
            <!-- Add more rows as needed -->
          </tbody>
        </table>
      </v-navigation-drawer>
    </v-card-text>
  </v-card>
</template>
<script>
import "cropperjs/dist/cropper.css";
import VueCropper from "vue-cropperjs";
export default {
  components: {
    VueCropper,
  },
  props: ["employeeObject"],
  data: () => ({
    switchValue: true,
    image: "",
    mime_type: "",
    cropedImage: "",
    cropper: "",
    autoCrop: false,
    selectedFile: "",
    upload_edit: {
      name: "",
    },
    drawer: false,
    group: null,
    attrs: [],
    dialog: false,
    editDialog: false,
    tab: null,
    m: false,
    expand: false,
    expand2: false,
    boilerplate: false,
    right: true,
    rightDrawer: false,
    tab: null,
    selectedItem: 1,

    on: "",
    color: "background",
    files: "",
    Model: "Employee",
    endpoint: "employee",
    search: "",
    loading: false,
    total: 0,
    next_page_url: "",
    prev_page_url: "",
    current_page: 1,
    per_page: 8,
    response: "",
    ListName: "",
    snackbar: false,
    btnLoader: false,
    max_employee: 0,
    employee: {
      title: "",
      display_name: "",
      employee_id: "",
      system_user_id: "",
      profile_picture: "",
      employee_role_id: "",
    },
    upload: {
      name: "",
    },
    previewImage: null,
    snackbar: false,
    ids: [],
    loading: false,
    total: 0,
    titleItems: ["Mr", "Mrs", "Miss", "Ms", "Dr"],
    editedIndex: -1,
    response: "",
    departments: [],
    sub_departments: [],
    designations: [],
    roles: [],
    data: [],
    errors: [],
    departments: [],
    department_id: "",
    payloadOptions: {},
    last_login: {},
    document_list: [],
  }),

  created() {
    this.payloadOptions = {
      params: {
        per_page: 1000,
        company_id: this.$auth.user.company_id,
      },
    };

    this.getDepartments();
    this.getSubDepartments();
    this.getDesignations();
    this.getRoles();
    this.getLastLogin();

    try {
      let employee_id = this.$route.params.id;
      if (employee_id) {
        this.editItemId(employee_id);
      }
    } catch (error) {}
  },
  mounted() {
    //this.getDataFromApi();
  },
  watch: {
    dialog(val) {
      val || this.close();
    },
    options: {
      handler() {
        this.getDataFromApi();
      },
      deep: true,
    },
    group() {
      this.drawer = false;
    },
  },
  computed: {
    formatJoiningDate() {
      let dateObj = new Date();

      let { joining_date } = this.employeeObject;

      if (joining_date) {
        dateObj = new Date(joining_date);
      }

      let day = dateObj.getDate();
      let month = dateObj.toLocaleString("default", { month: "long" });
      let year = dateObj.getFullYear();
      let daySuffix = this.setDaySuffix(day);
      return `<p>${day}<sup>${daySuffix}</sup> ${month} ${year}</p>`;
    },
  },
  methods: {
    getLastLogin() {
      //
      if (this.employeeObject)
        this.$axios
          .get(
            `activity/${this.employeeObject.user_id}?action=Login&company_id=${this.$auth.user.company_id}`
          )
          .then(({ data }) => {
            this.last_login = data.date_time;
          });
    },
    closeParentDialog() {
      this.$emit("close-parent-dialog");
    },
    openDocumentDrawer() {
      this.drawer = true;
      this.$axios
        .get(`documentinfo/${this.employeeObject.id}`)
        .then(({ data }) => {
          this.document_list = data;
        });
    },
    formatDate(date) {
      let dateObj = new Date();

      if (date) {
        dateObj = new Date(date);
      }

      let day = dateObj.getDate();
      let month = dateObj.getMonth() + 1;
      let year = dateObj.getFullYear();
      return `${day}/${month}/${year}`;
    },
    setDaySuffix(day) {
      switch (day) {
        case 1:
        case 21:
        case 31:
          return "st";
          break;
        case 2:
        case 22:
          return "nd";
          break;
        case 3:
        case 23:
          return "rd";
          break;
        default:
          return "th";
          break;
      }
    },
    getDepartments() {
      this.$axios.get(`departments`, this.payloadOptions).then(({ data }) => {
        this.departments = data.data;
      });
    },
    getSubDepartments() {
      this.$axios
        .get(`sub-departments`, this.payloadOptions)
        .then(({ data }) => {
          this.sub_departments = data.data;
        });
    },
    getDesignations() {
      this.$axios.get(`designation`, this.payloadOptions).then(({ data }) => {
        this.designations = data.data;
      });
    },
    getRoles() {
      this.payloadOptions.params.role_type = "employee";

      this.$axios.get(`role`, this.payloadOptions).then(({ data }) => {
        this.roles = data.data;
      });
    },
    can() {
      return true;
    },
    close() {
      this.dialog = false;
    },
  },
};
</script>
