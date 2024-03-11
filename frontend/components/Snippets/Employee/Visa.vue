<template>
  <div>
    <div class="text-center ma-2">
      <v-snackbar v-model="snackbar" top="top" color="secondary" elevation="24">
        {{ response }}
      </v-snackbar>
    </div>
    <v-container>
      <v-row>
        <v-col cols="6">
          <div class="form-group">
            <label class="col-form-label">{{ caps("Visa no") }}</label>
            <v-text-field
              dense
              small
              outlined
              v-model="visaItem.visa_no"
              type="text"
              class="form-control"
            ></v-text-field>
            <span v-if="errors && errors.visa_no" class="text-danger mt-2">{{
              errors.visa_no[0]
            }}</span>
          </div>
        </v-col>
        <v-col cols="6">
          <div class="form-group">
            <label class="col-form-label">{{ caps("note") }}</label>
            <v-text-field
              dense
              small
              outlined
              v-model="visaItem.note"
              type="text"
              class="form-control"
            ></v-text-field>
            <span v-if="errors && errors.note" class="text-danger mt-2">{{
              errors.note[0]
            }}</span>
          </div>
        </v-col>
        <v-col cols="6">
          <div class="form-group">
            <label class="col-form-label">{{ caps("place of issues") }}</label>
            <v-text-field
              dense
              small
              outlined
              v-model="visaItem.place_of_issues"
              type="text"
              class="form-control"
            ></v-text-field>
            <span
              v-if="errors && errors.place_of_issues"
              class="text-danger mt-2"
              >{{ errors.place_of_issues[0] }}</span
            >
          </div>
        </v-col>

        <v-col cols="6">
          <div class="form-group">
            <label class="col-form-label">{{ caps("country") }}</label>
            <v-text-field
              dense
              small
              outlined
              v-model="visaItem.country"
              class="form-control"
              type="text"
            ></v-text-field>
            <span v-if="errors && errors.country" class="text-danger mt-2">{{
              errors.country[0]
            }}</span>
          </div>
        </v-col>

        <v-col cols="6">
          <div class="form-group">
            <label class="col-form-label">{{ caps("issue date") }}</label>
            <v-text-field
              dense
              small
              outlined
              v-model="visaItem.issue_date"
              type="date"
              class="form-control"
            ></v-text-field>
            <span v-if="errors && errors.issue_date" class="text-danger mt-2">{{
              errors.issue_date[0]
            }}</span>
          </div>
        </v-col>

        <v-col cols="6">
          <div class="form-group">
            <label class="col-form-label">{{ caps("expiry date") }}</label>
            <v-text-field
              dense
              small
              outlined
              v-model="visaItem.expiry_date"
              type="date"
              class="form-control"
            ></v-text-field>
            <span
              v-if="errors && errors.expiry_date"
              class="text-danger mt-2"
              >{{ errors.expiry_date[0] }}</span
            >
          </div>
        </v-col>

        <v-col cols="6">
          <div class="form-group">
            <label class="col-form-label">{{ caps("labour no") }}</label>
            <v-text-field
              dense
              small
              outlined
              v-model="visaItem.labour_no"
              type="text"
              class="form-control"
            ></v-text-field>
            <span v-if="errors && errors.labour_no" class="text-danger mt-2">{{
              errors.labour_no[0]
            }}</span>
          </div>
        </v-col>
        <v-col cols="6">
          <div class="form-group">
            <label class="col-form-label">{{ caps("expiry date") }}</label>
            <v-text-field
              dense
              small
              outlined
              v-model="visaItem.personal_no"
              type="text"
              class="form-control"
            ></v-text-field>
            <span
              v-if="errors && errors.personal_no"
              class="text-danger mt-2"
              >{{ errors.personal_no[0] }}</span
            >
          </div>
        </v-col>
        <v-col cols="6">
          <div class="form-group">
            <label class="col-form-label">{{
              caps("labour issue date")
            }}</label>
            <v-text-field
              dense
              small
              outlined
              v-model="visaItem.labour_issue_date"
              type="date"
              class="form-control"
            ></v-text-field>
            <span
              v-if="errors && errors.labour_issue_date"
              class="text-danger mt-2"
              >{{ errors.labour_issue_date[0] }}</span
            >
          </div>
        </v-col>
        <v-col cols="6">
          <div class="form-group">
            <label class="col-form-label">{{
              caps("labour expiry date")
            }}</label>
            <v-text-field
              dense
              small
              outlined
              v-model="visaItem.labour_expiry_date"
              type="date"
              class="form-control"
            ></v-text-field>
            <span
              v-if="errors && errors.labour_expiry_date"
              class="text-danger mt-2"
              >{{ errors.labour_expiry_date[0] }}</span
            >
          </div>
        </v-col>
        <span v-if="errors && errors.length" class="error--text">{{
          errors
        }}</span>
      </v-row>
      <v-row>
        <v-col cols="12" class="text-right">
          <v-btn class="primary" small @click="save_visa_info">Save</v-btn>
        </v-col>
      </v-row>
    </v-container>
  </div>
</template>

<script>
export default {
  props: ["employeeId"],
  data() {
    return {
      visa_info: false,
      snackbar: false,
      response: "",
      errors: [],
      visaItem: {},
    };
  },
  created() {
    this.getInfo(this.employeeId);
  },
  methods: {
    getInfo(id) {
      this.$axios.get(`visa/${id}`).then(({ data }) => {
        this.visaItem = {
          ...data,
          employee_id: id,
        };
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

    can(item) {
      return true;
    },

    close_visa_info() {
      this.visa_info = false;
      this.errors = [];
      setTimeout(() => {
        this.$emit("close-popup");
      }, 1000);
    },

    save_visa_info() {
      let payload = {
        ...this.visaItem,
        company_id: this.$auth?.user?.company?.id,
        employee_id: this.visaItem.employee_id,
      };

      this.$axios
        .post(`visa`, payload)
        .then(({ data }) => {
          this.loading = false;

          if (!data.status) {
            this.errors = data.errors;
          } else {
            this.errors = [];
            this.snackbar = true;
            this.response = data.message;
            this.close_visa_info();
          }
        })
        .catch((e) => console.log(e));
    },
  },
};
</script>

<style scoped>
table {
  font-family: arial, sans-serif;
  border-collapse: collapse;
  width: 100%;
}

td,
th {
  text-align: left;
  padding: 8px;
}

tr:nth-child(even) {
  background-color: #fbfdff;
}
</style>
