<template>
  <div style="width: 100% !important">
    <v-row>
      <v-col cols="12">
        <v-row class="mt-5 mb-5">
          <v-col cols="4">
            <h3>Employees Devices</h3>
            <div>Dashboard</div>
          </v-col>
        </v-row>
      </v-col>
    </v-row>

    <v-row>
      <v-col cols="5">
        <v-toolbar color="background" dense flat dark>
          <span>All Employees List</span>
        </v-toolbar>
        <div>
          <select multiple v-model="leftSelectedUsers" @dblclick="moveRight" class="form-control" size="13">
            <option v-for="(user, index) in leftEmployees" :key="index" :value="user.id">
              {{ user.first_name }}
            </option>
          </select>
        </div>
      </v-col>

      <v-col cols="2">
        <div style="text-align: -webkit-center">
          <button type="button" id="undo_redo_undo" class="btn btn-primary btn-block">
            Options
          </button>

          <button @click="moveRight" type="button" id="undo_redo_rightSelected" class="btn btn-default btn-block">
            <i aria-hidden="true" class="v-icon notranslate mdi mdi-chevron-right theme--red"></i>
          </button>

          <button @click="allmoveRight" type="button" id="undo_redo_rightAll" class="btn btn-default btn-block">
            <i aria-hidden="true" class="v-icon notranslate mdi mdi-chevron-double-right theme--red"></i>
          </button>
          <button @click="moveLeft" type="button" id="undo_redo_leftSelected" class="btn btn-default btn-block">
            <i aria-hidden="true" class="v-icon notranslate mdi mdi-chevron-left theme--red"></i>
          </button>
          <button @click="allmoveLeft" type="button" id="undo_redo_leftAll" class="btn btn-default btn-block">
            <i aria-hidden="true" class="v-icon notranslate mdi mdi-chevron-double-left theme--red"></i>
          </button>
        </div>
      </v-col>

      <v-col cols="5">
        <v-toolbar color="background" dense flat dark>
          <span>Selected Employees List</span>
        </v-toolbar>
        <div>
          <select multiple v-model="rightSelectedUsers" @dblclick="moveLeft" class="form-control" size="13">
            <option v-for="(user, index) in rightEmployees" :key="index" :value="user.id">
              {{ user.first_name }}
            </option>
          </select>
        </div>
      </v-col>
    </v-row>
  </div>
</template>

<script>
export default {
  data() {
    return {
      endpointEmployee: "employee",
      endpointDevise: "device",
      leftSelectedUsers: [],
      leftEmployees: [],
      rightSelectedUsers: [],
      rightEmployees: [],
    };
  },
  created() {
    //this.getDevisesDataFromApi();
    this.getEmployeesDataFromApi();
  },
  methods: {
    onSubmit() {



      //this.$emit("todo-added", this.employeeOptions);
      let options = {
        params: {
          // deviseOptionsselected: this.rightEmployees,
          employeeOptionsselected: this.rightEmployees,
        },
      };


      // this.$axios.get(`${url}?page=${page}`, options).then(({ data }) => {
      //   //this.deviseslist = data.data;
      // });
    },
    // getDevisesDataFromApi(url = this.endpointDevise) {
    //   //this.loading = true;
    //   // let page = this.pagination.current;
    //   let options = {
    //     params: {
    //       per_page: 1000, //this.pagination.per_page,
    //       company_id: this.$auth.user.company_id,
    //     },
    //   };
    //   let page = 1;
    //   this.$axios.get(`${url}?page=${page}`, options).then(({ data }) => {
    //     this.deviseslist = data.data;
    //   });
    // },
    getEmployeesDataFromApi(url = this.endpointEmployee) {
      //this.loading = true;
      // let page = this.pagination.current;
      let options = {
        params: {
          per_page: 1000, //this.pagination.per_page,
          company_id: this.$auth.user.company_id,
        },
      };
      let page = 1;
      this.$axios.get(`${url}?page=${page}`, options).then(({ data }) => {
        this.leftEmployees = data.data;
      });
    },
    sortObject: (o) =>
      o.sort(function compareByName(a, b) {
        let nameA = a.first_name.toUpperCase(); // Convert names to uppercase for case-insensitive sorting
        let nameB = b.first_name.toUpperCase();

        if (nameA < nameB) {
          return -1;
        } else if (nameA > nameB) {
          return 1;
        } else {
          return 0;
        }
      }),

    allmoveLeft() {
      this.leftEmployees = this.leftEmployees.concat(this.rightEmployees);
      this.rightEmployees = [];
      this.leftEmployees = this.sortObject(this.leftEmployees);
    },
    allmoveRight() {
      this.rightEmployees = this.rightEmployees.concat(this.leftEmployees);
      this.leftEmployees = [];
      this.rightEmployees = this.sortObject(this.rightEmployees);
    },
    moveLeft() {

      if (!this.rightSelectedUsers.length) return;


      //for (let i = this.leftSelectedUsers.length; i > 0; i--) {
      let _rightSelectedUsers_length = this.rightSelectedUsers.length;
      for (let i = 0; i < _rightSelectedUsers_length; i++) {
        if (this.rightSelectedUsers) {
          let selectedindex = this.rightEmployees.findIndex(
            (e) => e.id == this.rightSelectedUsers[i]
          );

          let selectedobject = this.rightEmployees.find(
            (e) => e.id == this.rightSelectedUsers[i]
          );

          this.leftEmployees.push(selectedobject);

          this.rightEmployees.splice(selectedindex, 1);
        }
      }
      this.leftEmployees = this.sortObject(this.leftEmployees);

    },
    moveRight() {
      if (!this.leftSelectedUsers.length) return;

      let _leftSelectedUsers_length = this.leftSelectedUsers.length;
      for (let i = 0; i < _leftSelectedUsers_length; i++) {
        if (this.leftSelectedUsers) {
          let selectedindex = this.leftEmployees.findIndex(
            (e) => e.id == this.leftSelectedUsers[i]
          );

          let selectedobject = this.leftEmployees.find(
            (e) => e.id == this.leftSelectedUsers[i]
          );

          this.rightEmployees.push(selectedobject);

          this.leftEmployees.splice(selectedindex, 1);
        }
      }
      this.rightEmployees = this.sortObject(this.rightEmployees);

    },
  },
};
</script>

<style scoped>
/* @import "../../node_modules/@syncfusion/ej2-base/styles/material.css";
@import "../../node_modules/@syncfusion/ej2-inputs/styles/material.css";
@import "../../node_modules/@syncfusion/ej2-vue-dropdowns/styles/material.css"; */
/* @import "https://netdna.bootstrapcdn.com/bootstrap/3.3.4/css/bootstrap.min.css"; */
/* @media only screen and ((min-width: 1200px)) {
  .container {
    width: 90% !important;
  }
}
@media (min-width: 1200px) {
  .container {
    width: 90% !important;
  }
}
.container {
  width: 90% !important;
} */
/* .container {
  display: grid;
  grid-template-columns: 30% 10% 30%;
  align-items: center;
} */

.container select {
  height: 200px;
  width: 100%;
}

.container .middle {
  text-align: center;
}

.container button {
  width: 80%;
  margin-bottom: 5px;
}
</style>
