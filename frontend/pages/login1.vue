<template>
  <v-container fluid>
    <v-row justify="center" align="center" style="margin-top: 10%">
      <v-col cols="12" sm="8" md="8" lg="6">
        <v-card class="login-card" elevation="12">
          <v-row>
            <v-col cols="4">
              <v-form
                class="pa-2"
                ref="form"
                method="post"
                v-model="valid"
                lazy-validation
              >
                <div class="text-center">
                  <img width="100%" :lazy-src="logo" :src="logo" alt="logo" />
                </div>
                <v-text-field
                  v-model="email"
                  :rules="emailRules"
                  :hide-details="true"
                  placeholder="Email"
                  required
                  dense
                  outlined
                  type="email"
                ></v-text-field>
                <br />
                <v-text-field
                  :hide-details="true"
                  placeholder="Password"
                  dense
                  outlined
                  :rules="passwordRules"
                  :append-icon="show_password ? 'mdi-eye' : 'mdi-eye-off'"
                  :type="show_password ? 'text' : 'password'"
                  v-model="password"
                  @click:append="show_password = !show_password"
                ></v-text-field>
                <br />
                <v-select
                  v-model="loginUserType"
                  :items="[`Manager`, `Employee`]"
                  dense
                  outlined
                  :hide-details="true"
                  placeholder="Type"
                ></v-select>
                <div v-if="msg" class="text-center pt-1 mb-5 pb-1">
                  <span class="error--text">
                    {{ msg }}
                  </span>
                </div>
                <br />

                <v-btn
                  :loading="loading"
                  color="background"
                  small
                  dark
                  @click="login"
                  >Login</v-btn
                >
              </v-form>
            </v-col>
            <v-col cols="8">
              <v-img
                lazy-src="/bg.png"
                src="/bg.png"
                class="login-image"
              ></v-img>
            </v-col>
          </v-row>
        </v-card>
      </v-col>
    </v-row>
  </v-container>
</template>

<script>
export default {
  // components: { VueRecaptcha },

  layout: "login",
  data: () => ({
    // sitekey: "6Lf1wYwhAAAAAOMJYvI73SgjCSrS_OSS2kDJbVvs", // i am not robot
    // reCaptcha: null,
    // showGRC: false,
    logo: "/ideaHRMS-final-blue.svg",
    loginUserType: "Manager",
    valid: true,
    loading: false,
    snackbar: false,
    email: "",
    password: "",
    show_password: false,
    msg: "",
    emailRules: [
      (v) => !!v || "E-mail is required",
      (v) => /.+@.+\..+/.test(v) || "E-mail must be valid",
    ],

    passwordRules: [(v) => !!v || "Password is required"],
  }),
  created() {},
  methods: {
    // mxVerify(res) {
    //   this.reCaptcha = res;
    //   this.showGRC = this.reCaptcha ? false : true;
    // },

    login() {
      // this.showGRC = this.reCaptcha ? false : true;

      // if (this.$refs.form.validate() && this.reCaptcha) {
      if (this.$refs.form.validate()) {
        this.msg = "";
        this.loading = true;
        // const token = await this.$recaptcha.getResponse();
        let credentials = {
          email: this.email,
          password: this.password,
        };
        this.$auth
          .loginWith("local", { data: credentials })
          .then(({ data }) => {})
          .catch(({ response }) => {
            setTimeout(() => (this.loading = false), 2000);

            if (!response) {
              this.msg = "No response found";
              return;
            }
            let { status, data, statusText } = response;
            if (!status) {
              this.msg = "Server Down";
              return;
            }
            this.msg = status == 422 ? data.message : statusText;
          });
      }
    },
  },
};
</script>
<style scoped>
.login-card {
  display: flex;
}
.login-image {
  height: 100%;
}
</style>
