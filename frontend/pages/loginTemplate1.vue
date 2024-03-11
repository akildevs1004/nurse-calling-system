<template>
  <v-app>
    <v-dialog persistent v-model="dialogWhatsapp" width="600px">
      <v-card>
        <v-card-title
          dense
          class="white--text"
          style="background-color: #6946dd; color: #fff !important"
        >
          Whatsapp Verification
          <v-spacer></v-spacer>
          <v-icon @click="dialogWhatsapp = false" outlined dark color="white">
            mdi mdi-close-circle
          </v-icon>
        </v-card-title>
        <v-card-text>
          <div class="row g-0">
            <div class="col-lg-12">
              <div class="card-body p-md-5 mx-md-4">
                <v-row class="pb-5">
                  <v-col md="12" cols="12" class="text-center">
                    <h2>MyTime2Cloud</h2>
                  </v-col>
                </v-row>

                <h5>
                  Two Step Whatsapp OTP Verification
                  <v-icon dark color="green" fill>mdi-whatsapp</v-icon>
                </h5>
                <p>
                  We sent a verification code to your mobile number. Enter the
                  Code from the mobile in the filed below
                </p>
                <h2 style="font-size: 30px">
                  {{ maskMobileNumber }}
                </h2>
                <br />
                <!-- <v-form ref="form" method="post" v-model="whatsappFormValid" lazy-validation> -->
                <label
                  for=""
                  class="pb-5"
                  style="font-weight: bold; font-size: 20px"
                  >Type your 6 Digit Security Code</label
                >
                <div class="form-outline mb-4">
                  <v-otp-input
                    v-model="otp"
                    length="6"
                    :rules="requiredRules"
                  ></v-otp-input>
                </div>

                <div class="text-center pt-1 mb-5 pb-1">
                  <span v-if="msg" class="error--text">
                    {{ msg }}
                  </span>
                  <v-btn
                    :loading="loading"
                    @click="checkOTP(otp)"
                    class="btn btn-block fa-lg mt-1 mb-3"
                    style="background-color: #6946dd; color: #fff"
                  >
                    Verify OTP
                  </v-btn>
                  <!-- <v-btn :loading="loading" @click="checkOTP(otp)"
                      class="btn btn-primary btn-block text-white fa-lg primary mt-1 mb-3 btntext">
                      Verify OTP
                    </v-btn> -->
                </div>

                <div
                  class="d-flex align-items-center justify-content-center pb-4"
                ></div>
                <!-- </v-form> -->
              </div>
            </div>
          </div>
        </v-card-text>
      </v-card>
    </v-dialog>
    <section class="h-100 gradient-form" style="background-color: #eee">
      <div class="container py-5 h-100">
        <div class="row d-flex justify-content-center align-items-center h-100">
          <div
            class="col-xl-8"
            style="margin: 25px; width: 100%; height: 200px; position: relative"
          >
            <div style="width: 100%; position: absolute; top: 66%; left: 24%">
              <div class="card1 rounded-3 text-black">
                <div class="row g-0">
                  <div
                    class="col-lg-6 col-md-12 col-sm-12 col-lg-6"
                    style="background-color: #6946dd"
                  >
                    <div class="text-center" style="height: 100px"></div>
                    <div
                      class="card-body p-md-5 mx-md-4"
                      style="color: #fff; padding: 3rem !important"
                    >
                      <h2 class="pb-7" style="font-size: 2em">
                        Welcome To Xtreme Guard
                      </h2>

                      <v-form
                        ref="form"
                        method="post"
                        v-model="valid"
                        lazy-validation
                        autocomplete="off"
                      >
                        <div class="form-outline">
                          <v-text-field
                            dark
                            color="white--text"
                            rounded
                            v-model="credentials.email"
                            :rules="emailRules"
                            :hide-details="false"
                            id="form2Example11"
                            placeholder="username"
                            autofill="false"
                            required
                            dense
                            outlined
                            type="email"
                            prepend-inner-icon="mdi-account"
                            autocomplete="false"
                            aria-autocomplete="none"
                          ></v-text-field>
                        </div>

                        <div class="form-outline">
                          <v-text-field
                            dark
                            color="white--text"
                            rounded
                            dense
                            outlined
                            :rules="passwordRules"
                            autocomplete="off"
                            placeholder="Password"
                            prepend-inner-icon="mdi-lock  "
                            :append-icon="
                              show_password ? 'mdi-eye' : 'mdi-eye-off'
                            "
                            :type="show_password ? 'text' : 'password'"
                            v-model="credentials.password"
                            class="input-group--focused text-white"
                            @click:append="show_password = !show_password"
                          ></v-text-field>
                        </div>

                        <v-row>
                          <v-col md="6">
                            <v-checkbox color="white--text" value="red" dark>
                              <template v-slot:label>
                                <label style="color: #fff"
                                  >Remember Password</label
                                >
                              </template>
                            </v-checkbox>
                          </v-col>
                          <v-col md="6" class="text-right pt-8">
                            <nuxt-link class="text-white" to="/reset-password"
                              >Forgot password?</nuxt-link
                            >
                          </v-col>
                        </v-row>

                        <div class="text-center pt-1 mb-5 pb-1">
                          <span
                            v-if="msg"
                            class="error--text111"
                            style="color: #ff9f87"
                          >
                            {{ msg }}
                          </span>
                          <v-btn
                            :loading="loading"
                            @click="loginWithOTP()"
                            class="btn btn-black btn-block white mt-1 mb-3 p-4 btntext"
                            style="width: 100%; height: 48px"
                          >
                            Login
                          </v-btn>
                        </div>
                      </v-form>
                      <div class="text-center white--text text-white">
                        Don't Have an Account? Contact Admin
                      </div>
                    </div>
                  </div>
                  <div
                    class="col-md-12 col-sm-12 col-lg-6 d-flex align-items-center bgimage"
                  ></div>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </section>
  </v-app>
</template>

<script>
export default {
  layout: "login",
  data: () => ({
    maskMobileNumber: "",
    whatsappFormValid: true,
    logo: "/ideaHRMS-final-blue.svg",
    valid: true,
    loading: false,
    snackbar: false,

    show_password: false,
    msg: "",
    requiredRules: [(v) => !!v || "Required"],
    emailRules: [
      (v) => !!v || "E-mail is required",
      (v) => /.+@.+\..+/.test(v) || "E-mail must be valid",
    ],

    passwordRules: [(v) => !!v || "Password is required"],

    dialogWhatsapp: false,
    otp: "",
    userId: "",
    credentials: {
      email: "demo@gmail.com",
      password: "demo",
    },
  }),
  created() {
    // this.$store.commit("dashboard/resetState", null);
    this.$store.dispatch("dashboard/resetState");
    this.$store.dispatch("resetState");

    this.verifyToken();
  },
  mounted() {
    // setTimeout(() => {
    //   window.location.reload();
    // }, 1000 * 60 * 15); //15 minutes
  },
  methods: {
    verifyToken() {
      if (this.$route.query.email && this.$route.query.password) {
        this.email = this.$route.query.email;
        this.password = this.$route.query.password;

        this.loginWithOTP();
      }
    },
    hideMobileNumber(inputString) {
      // Check if the input is a valid string
      if (typeof inputString !== "string" || inputString.length < 4) {
        return inputString; // Return input as is if it's not a valid string
      }

      // Use a regular expression to match all but the last 3 digits
      var regex = /^(.*)(\d{5})$/;
      var matches = inputString.match(regex);

      if (matches) {
        var prefix = matches[1]; // Text before the last 3 digits
        var lastDigits = matches[2]; // Last 3 digits
        var maskedPrefix = "*".repeat(prefix.length); // Create a string of asterisks of the same length as the prefix
        return maskedPrefix + lastDigits;
      } else {
        return inputString; // Return input as is if there are fewer than 3 digits
      }
    },

    handleInputChange() {},
    // mxVerify(res) {
    //   this.reCaptcha = res;
    //   this.showGRC = this.reCaptcha ? false : true;
    // },
    checkOTP(otp) {
      if (otp == "") {
        alert("Enter OTP");
        return;
      }
      let payload = {
        userId: this.userId,
      };
      this.$axios
        .post(`check_otp/${otp}`, payload)
        .then(({ data }) => {
          if (!data.status) {
            alert("Invalid OTP. Please try again");
          } else {
            this.login();
          }
        })
        .catch((err) => console.log(err));
    },

    loginWithOTP() {
      this.loading = true;
      this.$store.commit("email", this.credentials.email);
      this.$store.commit("password", this.credentials.password);

      this.$axios
        .post("loginwith_otp", this.credentials)
        .then(({ data }) => {
          if (!data.status) {
            //alert("OTP Verification: " + data.message);
            alert("Invalid Login Deails");
          } else if (data.user_id) {
            if (data.enable_whatsapp_otp == 1) {
              this.dialogWhatsapp = true;
              this.userId = data.user_id;
              if (data.mobile_number) {
                this.maskMobileNumber = this.hideMobileNumber(
                  data.mobile_number
                );
              }

              this.loading = false;
            } else {
              this.login();
            }
          } else {
            alert("Invalid Login Deails");
          }
        })
        .catch((err) => console.log(err));

      this.loading = false;
    },
    login() {
      if (this.$refs.form.validate()) {
        this.$store.commit("email", this.credentials.email);
        this.$store.commit("password", this.credentials.password);

        this.msg = "";
        this.loading = true;
        this.$auth
          .loginWith("local", { data: this.credentials })
          .then(({ data }) => {
            if (
              this.$auth.user.role_id == 0 &&
              this.$auth.user.user_type == "employee"
            ) {
              window.location.href = process.env.EMPLOYEE_APP_URL;
              return "";
            }

            setTimeout(() => (this.loading = false), 2000);
          })
          .catch(({ response }) => {
            if (!response) {
              return false;
            }
            let { status, data, statusText } = response;
            this.msg = status == 422 ? data.message : statusText;
            setTimeout(() => (this.loading = false), 2000);
          });
      }
    },
  },
};
</script>
<style scoped>
.v-text-field--outlined >>> fieldset {
  border-color: #fff;
}

.v-list__group__header__prepend-icon .v-icon {
  color: red;
}

.v-input__icon {
  color: #fff !important;
}

.hidden-sm-and-down .v-icon {
  color: white !important;
}

.v-text-field--rounded {
  border-radius: 10px;
}

.text-white {
  color: #fff;
}

.v-label {
  color: #fff !important;
}

.bgimage {
  /* background-image: url(../static/login2.jpg) no-repeat center center fixed;
  ; */

  background-image: url("../static/login2.jpg");
  background-size: cover;

  min-height: 600px;
}

.v-btn {
  text-transform: inherit !important;
}

.v-input__control .v-label {
  color: red;
}

.btntext {
  color: #6946dd;
  font-weight: bold;
  font-size: 22px;
}

/* fieldset {
  border-radius: 10px;
  ;
} */
/*
i {
  color: #FFF;
} */

@media (min-width: 768px) {
  .gradient-form {
    height: 100vh !important;
  }
}

@media (min-width: 769px) {
  .primary {
    background: #5fafa3 !important;
    /* #5fafa3 */
    border-top-right-radius: 0.3rem;
    border-bottom-right-radius: 0.3rem;
  }
}
input:-webkit-autofill::webkit-input-placeholder {
  background-color: red;
}
input:-webkit-autofill {
  -webkit-text-fill-color: red !important;
  background-color: red !important;
}
/*
input:-webkit-autofill,
input:-webkit-autofill:hover,
input:-webkit-autofill:focus {
  -webkit-text-fill-color: #6946dd;
  background-color: #6946dd;
}

input:-internal-autofill-selected {
  appearance: menulist-button;
  background-image: none !important;
  background-color: -internal-light-dark(#6946dd, #6946dd) !important;
  color: fieldtext !important;
}
input:-webkit-autofill,
input:-webkit-autofill:focus {
  transition: background-color 0s 600000s, color 0s 600000s;
}
input.my-input {
  background-color: #6946dd !important;
  background-image: none !important;
  color: rgb(0, 0, 0) !important;
}

input.my-input:-internal-autofill-selected {
  background-color: #6946dd !important;
  background-image: none !important;
  color: rgb(0, 0, 0) !important;
} */
</style>
