<template>
  <div class="mt-5">
    <div class="text-center ma-2">
      <v-snackbar v-model="snackbar" top="top" color="secondary" elevation="24">
        {{ response }}
      </v-snackbar>
    </div>
    <v-container>
      <v-row>
        <v-col cols="6">
          <div class="form-group">
            <label class="col-form-label">{{ caps("certificate") }}</label>
            <!-- <input v-model="qualification_list.certificate" type="text" class="form-control" /> -->
            <v-text-field
              outlined
              dense
              small
              v-model="qualification_list.certificate"
            ></v-text-field>
            <span
              v-if="errors && errors.certificate"
              class="text-danger mt-2"
              >{{ errors.certificate[0] }}</span
            >
          </div>
        </v-col>
        <v-col cols="6">
          <div class="form-group">
            <label class="col-form-label">{{ caps("college ") }}</label>
            <!-- <input v-model="qualification_list.collage" type="text" class="form-control" /> -->
            <v-text-field
              outlined
              dense
              small
              v-model="qualification_list.collage"
            ></v-text-field>
            <span v-if="errors && errors.collage" class="text-danger mt-2">{{
              errors.collage[0]
            }}</span>
          </div>
        </v-col>
        <v-col cols="6">
          <div class="form-group">
            <label class="col-form-label">{{ caps("start date") }}</label>
            <!-- <input v-model="qualification_list.start" class="form-control" type="date" /> -->

            <v-menu
              v-model="menu_start"
              :close-on-content-click="false"
              transition="scale-transition"
              offset-y
              max-width="290px"
              min-width="auto"
            >
              <template v-slot:activator="{ on, attrs }">
                <v-text-field
                  :hide-details="!qualification_list.start"
                  :error-messages="errors.issue && errors.issue[0]"
                  v-model="qualification_list.start"
                  persistent-hint
                  append-icon="mdi-calendar"
                  readonly
                  outlined
                  dense
                  v-bind="attrs"
                  v-on="on"
                ></v-text-field>
              </template>
              <v-date-picker
                v-model="qualification_list.start"
                no-title
                @input="menu_start = false"
              ></v-date-picker>
            </v-menu>
            <span v-if="errors && errors.start" class="text-danger mt-2">{{
              errors.start[0]
            }}</span>
          </div>
        </v-col>
        <v-col cols="6">
          <div class="form-group">
            <label class="col-form-label">{{ caps("end date") }}</label>

            <v-menu
              v-model="menu_end"
              :close-on-content-click="false"
              transition="scale-transition"
              offset-y
              max-width="290px"
              min-width="auto"
            >
              <template v-slot:activator="{ on, attrs }">
                <v-text-field
                  :hide-details="!qualification_list.end"
                  :error-messages="errors.issue && errors.issue[0]"
                  v-model="qualification_list.end"
                  persistent-hint
                  append-icon="mdi-calendar"
                  readonly
                  outlined
                  dense
                  v-bind="attrs"
                  v-on="on"
                ></v-text-field>
              </template>
              <v-date-picker
                v-model="qualification_list.end"
                no-title
                @input="menu_end = false"
              ></v-date-picker>
            </v-menu>
            <span v-if="errors && errors.end" class="text-danger mt-2">{{
              errors.end[0]
            }}</span>
          </div>
        </v-col>
        <v-col cols="6">
          <div class="form-group">
            <label class="col-form-label">{{ caps("type") }}</label>
            <!-- <input v-model="qualification_list.type" type="text" class="form-control" /> -->
            <v-text-field
              outlined
              dense
              small
              v-model="qualification_list.type"
            ></v-text-field>
            <span v-if="errors && errors.type" class="text-danger mt-2">{{
              errors.type[0]
            }}</span>
          </div>
        </v-col>
        <span v-if="errors && errors.length" class="error--text">{{
          errors
        }}</span>
      </v-row>
      <v-row>
        <v-col cols="12" class="text-right">
          <v-btn class="primary" small @click="save_qualification_info"
            >Save</v-btn
          >
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
      qualification_info: false,
      snackbar: false,
      response: "",
      errors: [],
      menu: false,
      qualification_list: {},
      menu_start: false,
      menu_end: false,
    };
  },
  created() {
    this.getQualificationInfo(this.employeeId);
  },
  methods: {
    getQualificationInfo(id) {
      this.$axios.get(`qualification/${id}`).then(({ data }) => {
        this.qualification_list = {
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

    save_qualification_info() {
      let payload = {
        ...this.qualification_list,
        company_id: this.$auth?.user?.company?.id,
        employee_id: this.qualification_list.employee_id,
      };

      this.$axios
        .post(`qualification`, payload)
        .then(({ data }) => {
          this.loading = false;

          if (!data.status) {
            this.errors = data.errors;
          } else {
            this.$emit("eventFromchild");
            this.errors = [];
            this.snackbar = true;
            this.response = data.message;
            this.close_qualification_info();
          }
        })
        .catch((e) => console.log(e));
    },

    close_qualification_info() {
      this.qualification_info = false;
      this.errors = [];
      setTimeout(() => {
        this.$emit("close-popup");
      }, 1000);
    },
  },
};
</script>
