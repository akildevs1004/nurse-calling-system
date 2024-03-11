<template>
  <div class="mt-8">
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
                <!-- <label class="col-form-label">Bank Name</label> -->
                <v-text-field
                  label="Bank Name"
                  dense
                  small
                  outlined
                  type="text"
                  v-model="data.bank_name"
                  class="form-control"
                ></v-text-field>
                <span
                  v-if="errors && errors.bank_name"
                  class="text-danger mt-2"
                  >{{ errors.bank_name[0] }}</span
                >
              </div>
            </v-col>
            <v-col cols="6">
              <div class="form-group">
                <!-- <label class="col-form-label">Bank Address</label> -->
                <v-text-field
                  label="Bank Address"
                  dense
                  small
                  outlined
                  type="text"
                  v-model="data.address"
                  class="form-control"
                ></v-text-field>
                <span
                  v-if="errors && errors.address"
                  class="text-danger mt-2"
                  >{{ errors.address[0] }}</span
                >
              </div>
            </v-col>
            <v-col cols="6">
              <div class="form-group">
                <!-- <label class="col-form-label">Account No</label> -->
                <v-text-field
                  label="Account Number"
                  dense
                  small
                  outlined
                  v-model="data.account_no"
                  class="form-control"
                ></v-text-field>
                <span
                  type="text"
                  v-if="errors && errors.account_no"
                  class="text-danger mt-2"
                  >{{ errors.account_no[0] }}</span
                >
              </div>
            </v-col>

            <v-col cols="6">
              <div class="form-group">
                <!-- <label class="col-form-label">Account Name</label> -->
                <v-text-field
                  label="Account Name"
                  dense
                  small
                  outlined
                  type="text"
                  v-model="data.account_title"
                  class="form-control"
                ></v-text-field>
                <span
                  v-if="errors && errors.account_title"
                  class="text-danger mt-2"
                  >{{ errors.account_title[0] }}</span
                >
              </div>
            </v-col>

            <v-col cols="6">
              <div class="form-group">
                <!-- <label> IBAN</label> -->
                <v-text-field
                  label="IBAN"
                  dense
                  small
                  outlined
                  type="text"
                  v-model="data.iban"
                  class="form-control"
                ></v-text-field>
                <span v-if="errors && errors.iban" class="text-danger mt-2">{{
                  errors.iban[0]
                }}</span>
              </div>
            </v-col>

            <!-- <v-col cols="12">
              <a href="#" @click="add_other_bank_info = !add_other_bank_info">{{
                `${add_other_bank_info ? "hide" : "show"} Other Field`
              }}</a>
            </v-col> -->
            <v-col cols="6"> </v-col>
            <v-col cols="6">
              <div class="form-group">
                <!-- <label class="col-form-label">Other Text</label> -->
                <v-text-field
                  label="Extra Text"
                  dense
                  small
                  outlined
                  type="text"
                  v-model="data.other_text"
                  class="form-control"
                ></v-text-field>
                <span v-if="errors && errors.other_text" class="text-danger">{{
                  errors.other_text[0]
                }}</span>
              </div>
            </v-col>

            <v-col cols="6">
              <div class="form-group">
                <!-- <label class="col-form-label">Other Value</label> -->
                <v-text-field
                  label="Extra Value"
                  dense
                  small
                  outlined
                  type="text"
                  v-model="data.other_value"
                  class="form-control"
                ></v-text-field>
                <span
                  v-if="errors && errors.other_value"
                  class="text-danger mt-2"
                  >{{ errors.other_value[0] }}</span
                >
              </div>
            </v-col>

            <span v-if="errors && errors.length" class="error--text">{{
              errors
            }}</span>
          </v-row>
          <v-row>
            <v-col cols="12" class="text-right">
              <v-btn class="primary" small @click="save_bank_info">Save</v-btn>
            </v-col>
          </v-row>
        </v-col>
      </v-row>
    </v-container>
  </div>
</template>

<script>
export default {
  props: ["employeeId", "hideEditBtn"],
  data() {
    return {
      endpoint: "bankinfo",
      add_other_bank_info: false,
      popup: false,
      snackbar: false,
      response: "",
      errors: [],
      //data: {},
      data: {
        bank_name: "",
        account_no: "",
        account_title: "",
        address: "",
        other_text: "",
        other_value: "",
        iban: "",
      },
    };
  },
  created() {
    this.getInfo();
  },
  methods: {
    getInfo() {
      this.$axios
        .get(`${this.endpoint}/${this.employeeId}`)
        .then(({ data }) => {
          //this.data = data;

          this.data = {
            bank_name: data.bank_name,
            address: data.address,
            account_no: data.account_no,
            account_title: data.account_title,
            iban: data.iban,
            other_text: data.other_text,
            other_value: data.other_value,
          };
        })
        .catch((err) => {
          console.log(err);
        });
    },
    can(item) {
      return true;
    },
    save_bank_info() {
      let payload = {
        ...this.data,
        company_id: this.$auth?.user?.company?.id,
        employee_id: this.employeeId,
      };
      this.$axios
        .post(`bankinfo`, payload)
        .then(({ data }) => {
          this.loading = false;

          if (!data.status) {
            this.errors = data.errors;
          } else {
            this.$emit("eventFromchild");
            this.$emit("close-popup");
            this.errors = [];
            this.snackbar = true;
            this.response = data.message;
            this.getInfo();
            this.close_bank_info();
          }
        })
        .catch((e) => console.log(e));
    },
    close_bank_info() {
      this.popup = false;
      this.errors = [];
      setTimeout(() => {}, 300);
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
