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
          <v-row>
            <v-col cols="6">
              <div class="form-group">
                <!-- <label class="col-form-label">{{ caps("Visa no") }}</label> -->
                <v-text-field
                  label="Visa no"
                  :hide-details="!errors.visa_no"
                  :error-messages="errors.visa_no && errors.visa_no[0]"
                  dense
                  small
                  outlined
                  v-model="visaItem.visa_no"
                  type="text"
                  class="form-control"
                ></v-text-field>
              </div>
            </v-col>
            <v-col cols="6">
              <div class="form-group">
                <!-- <label class="col-form-label">{{ caps("note") }}</label> -->
                <v-text-field
                  label="Note"
                  :hide-details="!errors.note"
                  :error-messages="errors.note && errors.note[0]"
                  dense
                  small
                  outlined
                  v-model="visaItem.note"
                  type="text"
                  class="form-control"
                ></v-text-field>
              </div>
            </v-col>
            <v-col cols="6">
              <div class="form-group">
                <!-- <label class="col-form-label">{{
                  caps("place of issues")
                }}</label> -->
                <v-text-field
                  label="Place of issues"
                  :hide-details="!errors.place_of_issues"
                  :error-messages="
                    errors.place_of_issues && errors.place_of_issues[0]
                  "
                  dense
                  small
                  outlined
                  v-model="visaItem.place_of_issues"
                  type="text"
                  class="form-control"
                ></v-text-field>
              </div>
            </v-col>

            <v-col cols="6">
              <div class="form-group">
                <!-- <label class="col-form-label">{{ caps("country") }}</label> -->
                <v-text-field
                  label="Country"
                  :hide-details="!errors.country"
                  :error-messages="errors.country && errors.country[0]"
                  dense
                  small
                  outlined
                  v-model="visaItem.country"
                  class="form-control"
                  type="text"
                ></v-text-field>
              </div>
            </v-col>

            <v-col cols="6">
              <div class="form-group">
                <!-- <label class="col-form-label">{{ caps("issue date") }}</label> -->
                <v-text-field
                  label="Issue Date"
                  :hide-details="!errors.issue_date"
                  :error-messages="errors.issue_date && errors.issue_date[0]"
                  dense
                  small
                  outlined
                  v-model="visaItem.issue_date"
                  type="date"
                  class="form-control"
                ></v-text-field>
              </div>
            </v-col>

            <v-col cols="6">
              <div class="form-group">
                <!-- <label class="col-form-label">{{ caps("expiry date") }}</label> -->
                <v-text-field
                  label="Expiry Date"
                  :hide-details="!errors.expiry_date"
                  :error-messages="errors.expiry_date && errors.expiry_date[0]"
                  dense
                  small
                  outlined
                  v-model="visaItem.expiry_date"
                  type="date"
                  class="form-control"
                ></v-text-field>
              </div>
            </v-col>

            <v-col cols="6">
              <div class="form-group">
                <!-- <label class="col-form-label">{{ caps("labour no") }}</label> -->
                <v-text-field
                  label="Labour No"
                  :hide-details="!errors.labour_no"
                  :error-messages="errors.labour_no && errors.labour_no[0]"
                  dense
                  small
                  outlined
                  v-model="visaItem.labour_no"
                  type="text"
                  class="form-control"
                ></v-text-field>
              </div>
            </v-col>
            <v-col cols="6">
              <div class="form-group">
                <!-- <label class="col-form-label">{{
                  caps("labour issue date")
                }}</label> -->
                <v-text-field
                  label="Labour Issue Date"
                  :hide-details="!errors.labour_issue_date"
                  :error-messages="
                    errors.labour_issue_date && errors.labour_issue_date[0]
                  "
                  dense
                  small
                  outlined
                  v-model="visaItem.labour_issue_date"
                  type="date"
                  class="form-control"
                ></v-text-field>
              </div>
            </v-col>
            <v-col cols="6">
              <div class="form-group">
                <!-- <label class="col-form-label">{{
                  caps("labour expiry date")
                }}</label> -->
                <v-text-field
                  label="Labour Expiry Date"
                  :hide-details="!errors.labour_expiry_date"
                  :error-messages="
                    errors.labour_expiry_date && errors.labour_expiry_date[0]
                  "
                  dense
                  small
                  outlined
                  v-model="visaItem.labour_expiry_date"
                  type="date"
                  class="form-control"
                ></v-text-field>
              </div>
            </v-col>
          </v-row>
          <v-row>
            <v-col cols="12" class="text-right">
              <v-btn class="primary" small @click="save_visa_info">Save</v-btn>
            </v-col>
          </v-row>
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
