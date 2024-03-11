<template>
  <div class="mt-15 nopadding">
    <div class="text-center ma-2">
      <v-snackbar v-model="snackbar" small top="top" color="background">
        {{ response }}
      </v-snackbar>
    </div>
    <div style="text-align: right" class="pr-5">
      <v-icon
        @click="editItem"
        small
        class="red"
        style="border-radius: 50%; padding: 5px"
        color="secondary"
        >mdi-pencil</v-icon
      >
    </div>

    <!-- <KeyValueTable :data="table_data" :hideEditBtn="true" /> -->

    <div class="card mb-3">
      <div class="card-body" style="padding: 9px">
        <div class="row">
          <div class="col-sm-3 padding5">Role</div>
          <div class="col-sm-9 padding5">
            {{ table_data.Role }}
          </div>
        </div>
        <hr />
        <div class="row alternatebackground">
          <div class="col-sm-3 padding5">EID</div>
          <div class="col-sm-9 padding5">
            {{ table_data.EID }}
          </div>
        </div>
        <hr />
        <div class="row">
          <div class="col-sm-3 padding5">Name</div>
          <div class="col-sm-9 padding5">
            {{ table_data.Name }}
          </div>
        </div>
        <hr />
        <div class="row alternatebackground">
          <div class="col-sm-3 padding5">Department</div>
          <div class="col-sm-9 padding5">
            {{ table_data.Department }}
          </div>
        </div>
        <hr />
        <div class="row">
          <div class="col-sm-3 padding5">Sub Department</div>
          <div class="col-sm-9 padding5">
            {{ table_data.SubDepartment }}
          </div>
        </div>
        <hr />
        <div class="row alternatebackground">
          <div class="col-sm-3 padding5">Email</div>
          <div class="col-sm-9 padding5">
            {{ table_data.Email }}
          </div>
        </div>
        <hr />
        <div class="row">
          <div class="col-sm-3 padding5">Whatsapp Number</div>
          <div class="col-sm-9 padding5">
            {{ table_data.Whatsapp_Number }}
          </div>
        </div>
        <hr />
        <div class="row alternatebackground">
          <div class="col-sm-3 padding5">Joining Date</div>
          <div class="col-sm-9 padding5">
            {{ table_data.Joining_Date }}
          </div>
        </div>
        <!-- -->
        <hr />
      </div>
    </div>
  </div>
</template>

<script>
import KeyValueTable from "./KeyValueTable.vue";

export default {
  components: { KeyValueTable },
  props: ["employeeId"],
  data() {
    return {
      response: "",
      snackbar: false,
      work: {},
      table_data: {},
      table_data2: {},
    };
  },
  created() {
    this.getInfo();
  },
  methods: {
    getInfo() {
      this.$axios.get(`employee/${this.employeeId}`).then(async ({ data }) => {
        this.table_data2 = data;
        this.table_data = {
          Role: await data.role.name,
          EID: data.employee_id,
          Name: data.display_name,
          Department: await data.department.name,
          "Sub Department": (await data.sub_department)
            ? data.sub_department.name
            : "---",
          SubDepartment: (await data.sub_department)
            ? data.sub_department.name
            : "---",
          Email: await data.user.email,
          "Whatsapp Number": data.whatsapp_number,
          Whatsapp_Number: data.whatsapp_number,
          "Joining Date": data.joining_date,
          Joining_Date: data.joining_date,
        };

        this.work = data;
      });
    },
    editItem() {
      this.$router.push(`/employees/${this.employeeId}`);
    },
    caps(str) {
      if (str == "" || str == null) {
        return "---";
      } else {
        let res = str.toString();
        return res.replace(/\b\w/g, (c) => c.toUpperCase());
      }
    },
    update_setting() {
      let payload = {
        company_id: this.$auth?.user?.company?.id,
        employee_id: this.setting.employee_id,
        status: this.setting.status,
        overtime: this.setting.overtime,
        mobile_application: this.setting.mobile_application,
      };

      // return;
      this.$axios
        .post(`employee/update/setting`, payload)
        .then(({ data }) => {
          this.loading = false;

          if (!data.status) {
            this.errors = data.errors;
          } else {
            this.errors = [];
            this.snackbar = true;
            this.response = "Setting has been successfully updated";
            this.$emit("close-popup");
          }
        })
        .catch((e) => console.log(e));
    },
  },
};
</script>

<style scoped>
.padding5 {
  padding: 1px;
  padding-left: 13px;
  font-size: 11px;
}

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

.nopadding {
  margin-top: 0px !important;
}

.alternatebackground {
  background: #9acdc5;
  color: #fff;
}
</style>
