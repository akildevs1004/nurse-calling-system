<template>
  <div v-if="can(`user_access`)">
    <div class="text-center ma-2">
      <v-snackbar v-model="snackbar" top="top" color="secondary" elevation="24">
        {{ response }}
      </v-snackbar>
    </div>
    <v-row class="mt-5 mb-10">
      <v-col cols="10">
        <h3>User</h3>
        <div>Dashboard / User / Create</div>
      </v-col>
    </v-row>
    <v-card>
      <v-card flat>
        <v-card-text>
          <div class="row">
            <div class="col-sm-6">
              <div class="form-group">
                <label class="col-form-label"></label>
                Name <span class="text-danger">*</span>
                <input v-model="payload.name" class="form-control" type="" />
              </div>
              <span v-if="errors && errors.name" class="text-danger mt-2">{{
                errors.name[0]
              }}</span>
            </div>
            <div class="col-sm-6">
              <div class="form-group">
                <label class="col-form-label"></label>
                Email <span class="text-danger">*</span>
                <input
                  v-model="payload.email"
                  class="form-control"
                  type="email"
                />
              </div>
              <span v-if="errors && errors.email" class="text-danger mt-2">{{
                errors.email[0]
              }}</span>
            </div>
          </div>
          <div class="row">
            <div class="col-sm-6">
              <div class="form-group">
                <label class="col-form-label"
                  >Password <span class="text-danger">*</span></label
                >
                <input
                  v-model="payload.password"
                  class="form-control"
                  type="password"
                />
                <span
                  v-if="errors && errors.password"
                  class="text-danger mt-2"
                  >{{ errors.password[0] }}</span
                >
              </div>
            </div>
            <div class="col-sm-6">
              <div class="form-group">
                <label class="col-form-label"
                  >Confirm Password <span class="text-danger">*</span></label
                >
                <input
                  v-model="payload.password_confirmation"
                  class="form-control"
                  type="password"
                />
                <span
                  v-if="errors && errors.password_confirmation"
                  class="text-danger mt-2"
                  >{{ errors.password_confirmation[0] }}</span
                >
              </div>
            </div>
          </div>
          <div class="row">
            <div class="col-sm-6">
              <div class="form-group">
                <label class="col-form-label">Role </label>
                <span class="text-danger">*</span>
                <select
                  v-model="payload.role_id"
                  class="form-select"
                  aria-label="Default select"
                >
                  <option value="">Select Role</option>
                  <option
                    v-for="(item, idx) in roles"
                    :key="idx"
                    :value="item.id"
                  >
                    {{ item.name }}
                  </option>
                </select>
                <span
                  v-if="errors && errors.role_id"
                  class="text-danger mt-2"
                  >{{ errors.role_id[0] }}</span
                >
              </div>
            </div>
          </div>

          <v-row>
            <v-col cols="12">
              <div class="text-right">
                <v-btn
                  v-if="can(`user_create`)"
                  small
                  :loading="loading"
                  color="primary"
                  @click="store_device"
                >
                  Submit
                </v-btn>
              </div>
            </v-col>
          </v-row>
        </v-card-text>
      </v-card>
    </v-card>
  </div>
  <NoAccess v-else />
</template>

<script>
export default {
  data: () => ({
    loading: false,

    payload: {
      name: "",
      email: "",
      password: "",
      password_confirmation: "",
      role_id: "",
    },

    errors: [],
    roles: [],
    response: "",
    snackbar: false,
  }),
  async created() {
    this.getRoles();
  },
  methods: {
    can(per) {
      return this.$pagePermission.can(per, this);
    },
    
    getRoles() {
      let options = {
        params: {
          company_id: this.$auth.user.company_id,
        },
      };
      this.$axios.get(`role`, options).then(({ data }) => {
        this.roles = data.data;
      });
    },
    store_device() {
      let payload = this.payload;
      payload.company_id = this.$auth.user.company_id;

      this.loading = true;

      this.$axios
        .post(`/users`, payload)
        .then(({ data }) => {
          this.loading = false;

          if (!data.status) {
            this.errors = data.errors;
          } else {
            this.snackbar = true;
            this.response = "User added successfully";
            setTimeout(() => this.$router.push(`/user`), 2000);
          }
        })
        .catch((e) => console.log(e));
    },
  },
};
</script>
