<template>
  <div class="mt-5">
    <div class="text-center ma-2">
      <v-snackbar v-model="snackbar" small top="top" color="background">
        {{ response }}
      </v-snackbar>
    </div>

    <v-row>
      <v-progress-linear
        :active="loading"
        color="primary"
        indeterminate
      ></v-progress-linear>

      <v-col cols="6" xs="6" sm="12" md="6" lg="6" xl="6">
        <v-col md="12" sm="12" cols="12">
          <label class="col-form-label">
            <strong>Leave Group Name</strong></label
          >
          <v-autocomplete
            :items="leave_groups"
            item-text="group_name"
            item-value="id"
            placeholder="Select"
            v-model="setting.leave_group_id"
            :hide-details="!errors.leave_group_id"
            :error="errors.leave_group_id"
            :error-messages="
              errors && errors.leave_group_id ? errors.leave_group_id[0] : ''
            "
            dense
            outlined
          ></v-autocomplete>
        </v-col>
        <v-col md="12" sm="12" sx="12" xl="12" cols="12" outlined>
          <label class="col-form-label"
            ><strong>Leave Manager/Reporting Manger</strong>
          </label>
          <v-autocomplete
            :items="leave_managers"
            :item-text="getEmployeeName"
            item-value="id"
            placeholder="Select"
            v-model="setting.reporting_manager_id"
            :hide-details="!errors.reporting_manager_id"
            :error="errors.reporting_manager_id"
            :error-messages="
              errors && errors.reporting_manager_id
                ? errors.reporting_manager_id[0]
                : ''
            "
            dense
            outlined
          ></v-autocomplete>
        </v-col>
      </v-col>

      <v-col
        cols="6"
        xs="12"
        sm="12"
        md="6"
        lg="6"
        xl="6"
        outlined
        elevation="2"
      >
        <table style="width: 70%">
          <tr>
            <td><strong>Employee Status</strong></td>
            <td>
              <v-switch
                color="success"
                class="mt-0 ml-2"
                v-model="setting.status"
              ></v-switch>
            </td>
          </tr>
          <tr>
            <td><strong>Web Login Access</strong></td>
            <td>
              <v-switch
                color="success"
                class="mt-0 ml-2"
                v-model="setting.user.web_login_access"
              ></v-switch>
            </td>
          </tr>
          <tr>
            <td><strong>Mobile App Login Access</strong></td>
            <td>
              <v-switch
                color="success"
                class="mt-0 ml-2"
                v-model="setting.user.mobile_app_login_access"
              ></v-switch>
            </td>
          </tr>

          <tr>
            <td><strong>Over Time</strong></td>
            <td>
              <div class="text-overline mb-1">
                <v-switch
                  color="success"
                  class="mt-0 ml-2"
                  v-model="setting.overtime"
                ></v-switch>
              </div>
            </td>
          </tr>
          <tr>
            <td><strong> Whatsapp OTP</strong></td>
            <td>
              <div class="text-overline mb-1">
                <v-switch
                  color="success"
                  class="mt-0 ml-2"
                  v-model="setting.user.enable_whatsapp_otp"
                ></v-switch>
              </div>
            </td>
          </tr>
          <tr>
            <td><strong>Location Tracking </strong></td>
            <td>
              <div class="text-overline mb-1">
                <v-switch
                  color="success"
                  class="mt-0 ml-2"
                  v-model="setting.user.tracking_status"
                ></v-switch>
              </div>
            </td>
          </tr>
        </table>
      </v-col>
      <v-col col="12" class="text-right">
        <div class="text-right">
          <v-btn small class="primary mt-1" @click="update_setting">Save</v-btn>
        </div>
      </v-col>
    </v-row>
  </div>
</template>

<script>
export default {
  props: ["employeeId"],
  data() {
    return {
      response: "",
      snackbar: false,
      setting: {
        user: {},
      },
      leave_managers: [],
      leave_groups: [],
      errors: [],
      loading: false,
    };
  },
  created() {
    this.payloadOptions = {
      params: {
        per_page: 10,
        company_id: this.$auth.user.company_id,
      },
    };

    this.getInfo(this.employeeId);

    this.getLeaveGroups();
    this.getLeaveManagers();
  },
  methods: {
    getLeaveGroups() {
      this.payloadOptions.params.company_id = this.$auth.user.company_id;
      this.loading = true;
      this.$axios.get(`leave_groups`, this.payloadOptions).then(({ data }) => {
        this.leave_groups = data.data;
        this.loading = false;
      });
    },
    getLeaveManagers() {
      this.loading = true;
      this.payloadOptions.params.company_id = this.$auth.user.company_id;

      this.$axios.get(`employeesList`, this.payloadOptions).then(({ data }) => {
        this.leave_managers = data.data;
        this.loading = false;
      });
    },
    getEmployeeName(item) {
      return item.first_name ? item.first_name + " " + item.last_name : "---";
    },
    getInfo(id) {
      this.loading = true;
      this.$axios.get(`employee/${id}`).then(({ data }) => {
        this.employeeId = data.id;
        this.setting = {
          ...data,
        };
        this.loading = false;
      });
    },
    caps(str) {
      if (str == "" || str == null) {
        return "---";
      } else {
        let res = str.toString();
        return res.replace(/\b\w/g, (c) => c.toUpperCase());
      }
    },
    update_setting() {
      let payload = {
        company_id: this.$auth?.user?.company?.id,
        employee_id: this.setting.employee_id,
        status: this.setting.status,
        overtime: this.setting.overtime,

        leave_group_id: this.setting.leave_group_id,
        reporting_manager_id: this.setting.reporting_manager_id,
        user_id: this.setting.user_id,
        mobile_app_login_access: this.setting.user.mobile_app_login_access,
        web_login_access: this.setting.user.web_login_access,
        enable_whatsapp_otp: this.setting.user.enable_whatsapp_otp,
        tracking_status: this.setting.user.tracking_status,
      };

      // return;
      this.$axios
        .post(`employee/update/setting`, payload)
        .then(({ data }) => {
          this.loading = false;

          if (!data.status) {
            this.errors = data.errors;
          } else {
            this.errors = [];
            this.snackbar = true;
            this.response = "Settings has been successfully updated";
            this.$emit("close-popup");
          }
        })
        .catch((e) => console.log(e));
    },
  },
};
</script>
