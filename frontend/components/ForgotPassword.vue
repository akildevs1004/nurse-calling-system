<template>
  <div>
    <v-snackbar v-model="snackbar" top="top" color="secondary" elevation="24">
      {{ response }}
    </v-snackbar>
    <section class=" ">
      <div class="container py-5">
        <div class="row d-flex justify-content-center align-items-center">
          <div class="col-xl-12">
            <v-form v-if="emailView" ref="form" method="post">
              <div class="form-outline">
                <v-text-field
                  label="Email"
                  v-model="email"
                  placeholder="Email"
                  autofill="false"
                  required
                  dense
                  outlined
                  type="email"
                  prepend-inner-icon="mdi-email"
                  autocomplete="false"
                  aria-autocomplete="none"
                ></v-text-field>
              </div>
              <!-- <div class="form-outline mb-4">
                          <input v-model="email" style="border: 1px solid" type="email" id="form2Example11"
                            class="form-control" placeholder="master@erp.com" />
                        </div> -->

              <div class="text-center pt-1 mb-5 pb-1">
                <span v-if="errors && errors.email" class="error--text"
                  >{{ errors.email[0] }}
                </span>
                <div v-if="emailMsg" class="error--text">{{ emailMsg }}</div>

                <v-btn
                  small
                  :loading="resetLoading"
                  @click="reset_password"
                  class="primary mt-1 mb-3 p-4"
                >
                  Reset the Password
                </v-btn>
                <!-- <v-btn :loading="loading" @click="reset_password" class="
                              btn btn-primary btn-block
                              text-white
                              fa-lg
                              primary
                              mt-1
                              mb-3
                            ">
                            Submit
                          </v-btn> -->
              </div>

              <div
                class="d-flex align-items-center justify-content-center pb-4"
              ></div>
            </v-form>

            <v-form v-if="codeView" ref="form" method="post">
              <!-- <p>Please enter your code</p> -->
              <span
                ><v-text-field
                  label="Verification Code"
                  dense
                  outlined
                  v-model="code"
                  type="number"
                  placeholder=""
                ></v-text-field
              ></span>

              <span class="text-center">
                <v-btn small dense @click="reset_password" class="primary">
                  Resend code
                </v-btn>

                <v-btn
                  dense
                  small
                  :loading="loading"
                  @click="check_code"
                  class="primary"
                  style="float: right"
                >
                  Verify Code
                </v-btn>
              </span>
              <!-- <v-otp-input length="6"></v-otp-input> -->

              <div class="text-center pt-1 mb-5 pb-1">
                <span v-if="errors && errors.code" class="error--text"
                  >{{ errors.code[0] }}
                </span>
                <span v-if="msg" class="error--text">{{ msg }} </span>
              </div>
            </v-form>

            <v-form v-if="newPasswordView" ref="form" method="post">
              <span
                >Enter new Password Details<br />
                (Uppercase and minimim 6 letters)</span
              >
              <div class="form-outline mb-4 pt-5">
                <v-text-field
                  label="New Password"
                  dense
                  outlined
                  :append-icon="show_password ? 'mdi-eye' : 'mdi-eye-off'"
                  :type="show_password ? 'text' : 'password'"
                  v-model="password"
                  class="input-group--focused"
                  @click:append="show_password = !show_password"
                  :error="errors.password"
                  :error-messages="
                    errors && errors.password ? errors.password[0] : ''
                  "
                ></v-text-field>

                <v-text-field
                  label="Confirm Password"
                  dense
                  outlined
                  :append-icon="
                    show_password_confirm ? 'mdi-eye' : 'mdi-eye-off'
                  "
                  :type="show_password_confirm ? 'text' : 'password'"
                  v-model="password_confirmation"
                  class="input-group--focused"
                  @click:append="show_password_confirm = !show_password_confirm"
                  :error="errors.show_password_confirm"
                  :error-messages="
                    errors && errors.show_password_confirm
                      ? errors.show_password_confirm[0]
                      : ''
                  "
                ></v-text-field>
              </div>

              <div class="text-center pt-1 mb-5 pb-1">
                <span v-if="errors && errors.code" class="error--text"
                  >{{ errors.code[0] }}
                </span>
                <span v-if="msg" class="error--text">{{ msg }} </span>
                <v-btn
                  dense
                  :loading="loading"
                  @click="change_new_password"
                  class="btn btn-primary btn-block text-white fa-lg primary mt-1 mb-3"
                >
                  Update New Password
                </v-btn>
              </div>

              <div
                class="d-flex align-items-center justify-content-center pb-4"
              ></div>
            </v-form>

            <div
              class="text--green"
              style="color: green; min-height: 200px"
              v-model="successView"
            >
              {{ successMessage }}
            </div>
          </div>
        </div>
      </div>
    </section>
  </div>
</template>

<script>
export default {
  layout: "login",
  auth: false,
  data: () => ({
    successView: true,
    successMessage: "",
    loading: false,
    snackbar: false,
    response: "",
    show_password: false,
    show_password_confirm: false,
    emailView: true,
    emailMsg: "",
    codeView: false,
    newPasswordView: false,
    go_login_msg: false,

    email: "",
    code: "",
    password: "",
    password_confirmation: "",

    msg: "",
    errors: [],
    resetLoading: false,
  }),
  methods: {
    reset_password() {
      let payload = {
        email: this.email,
      };
      this.resetLoading = true;
      this.emailMsg = "";
      this.$axios
        .post("/reset-password", payload)
        .then(({ data }) => {
          this.resetLoading = false;
          if (!data.status) {
            this.errors = data.errors;
            this.emailMsg = data.message;
          } else {
            this.errors = [];
            this.snackbar = true;
            this.response = data.message;
            this.emailView = true;
            this.codeView = true;
          }
        })
        .catch((e) => console.log(e));
    },

    check_code() {
      let payload = {
        code: this.code,
        email: this.email,
      };
      this.loading = true;
      this.emailMsg = "";
      this.$axios
        .post("/check-code", payload)
        .then(({ data }) => {
          this.loading = false;
          if (!data.status) {
            this.errors = data.errors;
            this.msg = data.message;
          } else {
            this.errors = [];
            this.msg = "";
            this.newPasswordView = true;
            // this.emailView = false;
            // this.codeView = false;
          }
        })
        .catch((e) => console.log(e));
    },

    change_new_password() {
      let payload = {
        email: this.email,
        password: this.password,
        password_confirmation: this.password_confirmation,
      };
      this.loading = true;
      this.emailMsg = "";
      this.$axios
        .post("/new-password", payload)
        .then(({ data }) => {
          this.loading = false;
          if (!data.status) {
            this.errors = data.errors;
            this.emailMsg = data.message;
          } else {
            this.errors = [];
            this.snackbar = true;
            this.response = data.message;
            this.$router.push(`/login`);

            this.newPasswordView = false;
            this.emailView = false;
            this.codeView = false;
            this.success = true;
            this.successMessage = "New Password is successfully Updated";
          }
        })
        .catch((e) => console.log(e));
    },
  },
};
</script>
