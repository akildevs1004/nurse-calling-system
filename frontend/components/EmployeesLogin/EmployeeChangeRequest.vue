<template>
  <v-container>
    <v-row>
      <v-col cols="12">
        <v-select
          dense
          outlined
          hide-details
          v-model="editedItem.request_type"
          :items="request_types"
          item-text="text"
          item-value="value"
          label="Request Type"
        ></v-select>
      </v-col>

      <v-col cols="12">
        <v-menu
          v-model="menu1"
          :close-on-content-click="false"
          :nudge-right="40"
          transition="scale-transition"
          offset-y
          min-width="auto"
        >
          <template v-slot:activator="{ on, attrs }">
            <v-text-field
              dense
              outlined
              hide-details
              v-model="editedItem.from_date"
              label="From"
              readonly
              v-bind="attrs"
              v-on="on"
            ></v-text-field>
          </template>
          <v-date-picker
            v-model="editedItem.from_date"
            @input="menu1 = false"
          ></v-date-picker>
        </v-menu>
      </v-col>

      <v-col cols="12">
        <v-menu
          v-model="menu2"
          :close-on-content-click="false"
          :nudge-right="40"
          transition="scale-transition"
          offset-y
          min-width="auto"
        >
          <template v-slot:activator="{ on, attrs }">
            <v-text-field
              dense
              outlined
              hide-details
              v-model="editedItem.to_date"
              label="To"
              readonly
              v-bind="attrs"
              v-on="on"
            ></v-text-field>
          </template>
          <v-date-picker
            v-model="editedItem.to_date"
            @input="menu2 = false"
          ></v-date-picker>
        </v-menu>
      </v-col>

      <v-col cols="12">
        <v-textarea
          rows="2"
          dense
          outlined
          hide-details
          v-model="editedItem.remarks"
          label="Remarks"
        ></v-textarea>
      </v-col>

      <v-col cols="12">
        <p :class="`${responseStatusColor}--text`" v-if="response">
          {{ response }}
        </p>
        <v-btn class="indigo" block dark @click="submit">Submit</v-btn>
      </v-col>
    </v-row>
  </v-container>
</template>

<script>
export default {
  data() {
    return {
      responseStatusColor: "",
      menu1: false,
      menu2: false,
      request_types: [],
      response: null,
      endpoint: `change_request`,
      editedItem: {
        company_id: null,
        branch_id: 0,
        employee_device_id: null,
        request_type: "Attendance",
        from_date: new Date(Date.now() - new Date().getTimezoneOffset() * 60000)
          .toISOString()
          .substr(0, 10),
        to_date: new Date(Date.now() - new Date().getTimezoneOffset() * 60000)
          .toISOString()
          .substr(0, 10),
        remarks: "",
        status: "P",
      },
    };
  },
  async created() {
    this.remarks = "";
    this.response = "";
    this.request_types = [
      {
        text: "Attendance",
        value: "Attendance",
      },
      {
        text: "Overtime",
        value: "Overtime",
      },
    ];

    let { company_id, employee } = this.$auth.user;

    this.editedItem.company_id = company_id;
    this.editedItem.branch_id = employee.branch_id;
    this.editedItem.employee_device_id = employee.system_user_id;

    // this.$axios.get("https://backend.eztime.online/api/company").then(({ data }) => {
    //   this.request_types = data.data;
    // });
  },
  methods: {
    submit() {
      this.editedItem.requested_at = this.getCurrentDateTime();

      this.$axios
        .post(this.endpoint, this.editedItem)
        .then(({ data }) => {
          this.responseStatusColor = "green";
          this.response = "Request has been submitted succussfully.";
          setTimeout(() => {
            this.$emit("reload");
            this.$emit("close-change-request-form");
            this.response = "";
            this.remarks = "";
            this.responseStatusColor = "";
          }, 2000);
        })
        .catch(({ response }) => {
          if (!response) {
            return false;
          }
          let { status, data, statusText } = response;
          this.response = status == 422 ? data.message : statusText;
          this.responseStatusColor = "red";
        });
    },
    getCurrentDateTime() {
      const now = new Date();

      const year = now.getFullYear();
      const month = String(now.getMonth() + 1).padStart(2, "0"); // Months are zero-based
      const day = String(now.getDate()).padStart(2, "0");
      const hours = String(now.getHours()).padStart(2, "0");
      const minutes = String(now.getMinutes()).padStart(2, "0");
      const seconds = String(now.getSeconds()).padStart(2, "0");

      return `${year}-${month}-${day} ${hours}:${minutes}:${seconds}`;
    },
  },
};
</script>
