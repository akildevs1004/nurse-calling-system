<template>
  <div>
    <div class="text-center ma-2">
      <v-snackbar v-model="snackbar" top="top" color="secondary" elevation="24">
        {{ response }}
      </v-snackbar>
    </div>
    <v-row>
      <v-col cols="6">
        <v-row>
          <v-col cols="6">
            <div class="form-group">
              <!-- <label class="col-form-label">{{ caps("passport no") }}</label> -->

              <v-text-field
                label="Passport No"
                :hide-details="!errors.passport_no"
                :error-messages="errors.passport_no && errors.passport_no[0]"
                dense
                small
                outlined
                v-model="passport_list.passport_no"
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
                v-model="passport_list.note"
                type="text"
                class="form-control"
              ></v-text-field>
            </div>
          </v-col>
          <v-col cols="6">
            <div class="form-group">
              <!-- <label class="col-form-label">{{ caps("place of issues") }}</label> -->
              <v-text-field
                label="Place of Issue"
                :hide-details="!errors.place_of_issues"
                :error-messages="
                  errors.place_of_issues && errors.place_of_issues[0]
                "
                dense
                small
                outlined
                v-model="passport_list.place_of_issues"
                type="text"
                class="form-control"
              ></v-text-field>
            </div>
          </v-col>

          <v-col cols="6">
            <div class="form-group">
              <!-- <label class="col-form-label">{{ caps("country") }}</label> -->
              <v-text-field
                label="Issue Date"
                :hide-details="!errors.country"
                :error-messages="errors.country && errors.country[0]"
                dense
                small
                outlined
                v-model="passport_list.country"
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
                v-model="passport_list.issue_date"
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
                v-model="passport_list.expiry_date"
                type="date"
                class="form-control"
              ></v-text-field>
            </div>
          </v-col>
        </v-row>

        <v-row>
          <v-col cols="12" class="text-right">
            <v-btn class="primary" small @click="save_passport_info"
              >Save</v-btn
            >
          </v-col>
        </v-row>
      </v-col>
    </v-row>
  </div>
</template>

<script>
export default {
  props: ["employeeId"],
  data() {
    return {
      passport_info: false,
      errors: [],
      snackbar: false,
      response: "",
      passport_list: {},
    };
  },
  created() {
    this.getInfo(this.employeeId);
  },
  methods: {
    getInfo(id) {
      this.$axios.get(`passport/${id}`).then(({ data }) => {
        this.passport_list = {
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

    close_passport_info() {
      this.passport_info = false;
      this.errors = [];
      setTimeout(() => {
        this.$emit("close-popup");
      }, 1000);
    },

    save_passport_info() {
      let payload = {
        ...this.passport_list,
        company_id: this.$auth?.user?.company?.id,
        employee_id: this.passport_list.employee_id,
      };

      this.$axios
        .post(`passport`, payload)
        .then(({ data }) => {
          this.loading = false;

          if (!data.status) {
            this.errors = data.errors;
          } else {
            this.$emit("eventFromchild");
            this.errors = [];
            this.snackbar = true;
            this.response = data.message;
            this.close_passport_info();
          }
        })
        .catch((e) => console.log(e));
    },
  },
};
</script>

<!-- <style scoped>
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
</style> -->
