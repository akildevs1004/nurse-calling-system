// holds your root state
export const state = () => ({
  first_login: 1,
  color: "primary",
  employee_id: "",
  main_report_type: "",
  loginType: "manager",
  shift_type_id: "",
  shift_id: "",
  shift_name: "",
  branch_id: "",

  devices: null,
  employees: null,
  employees_with_document_expiry: null,
  access_control_report: null,
  employeeList: null,
  deviceList: null,

  shifts: null,
  department_list: null,
  timezone_list: null,
  branchList: null,
  roles: null,
  designation_list: null,
  leave_groups: null,
  leave_managers: null,

  login_token: "",
  email: "",
  password: "",
});

// contains your mutations
export const mutations = {
  RESET_STATE(state) {
    // Object.keys(state).forEach((key) => {
    //   state[key] = null;
    // });
    state = () => ({
      first_login: 1,
      color: "primary",
      employee_id: "",
      main_report_type: "",
      loginType: "manager",
      shift_type_id: "",
      shift_id: "",
      shift_name: "",
      branch_id: "",
      devices: null,
      employees: null,
      employees_with_document_expiry: null,
      access_control_report: null,
      employeeList: null,
      deviceList: null,
      shifts: null,
      department_list: null,
      timezone_list: null,
      branchList: null,
      hostList: null,
      purposeList: null,
      roles: null,
      designation_list: null,
      leave_groups: null,
      leave_managers: null,

      login_token: "",
      email: "",
      password: "",
    });

    //console.log("all mutations cleared", state);
  },
  login_token(state, value) {
    state.login_token = value;
  },
  email(state, value) {
    state.email = value;
  },
  password(state, value) {
    state.password = value;
  },
  first_login(state, value) {
    state.first_login = value;
  },
  devices(state, value) {
    state.devices = value;
  },
  employees(state, value) {
    state.employees = value;
  },
  employees_with_document_expiry(state, value) {
    state.employees_with_document_expiry = value;
  },
  access_control_report(state, value) {
    state.access_control_report = value;
  },
  employeeList(state, value) {
    state.employeeList = value;
  },
  deviceList(state, value) {
    state.deviceList = value;
  },
  shifts(state, value) {
    state.shifts = value;
  },
  department_list(state, value) {
    state.department_list = value;
  },

  timezone_list(state, value) {
    state.timezone_list = value;
  },
  devices_list(state, value) {
    state.devices_list = value;
  },
  designation_list(state, value) {
    state.designation_list = value;
  },
  roles(state, value) {
    state.roles = value;
  },
  leave_groups(state, value) {
    state.leave_groups = value;
  },
  leave_managers(state, value) {
    state.leave_managers = value;
  },
  loginType(state, value) {
    state.loginType = value;
  },
  branch_id(state, value) {
    state.branch_id = value;
  },
  change_color(state, value) {
    state.color = value;
  },
  employee_id(state, value) {
    state.employee_id = value;
  },
  main_report_type(state, value) {
    state.main_report_type = value;
  },
  shift_type_id(state, value) {
    state.shift_type_id = value;
  },
  shift_id(state, value) {
    state.shift_id = value;
  },
  shift_name(state, value) {
    state.shift_name = value;
  },

  branchList(state, value) {
    state.branchList = value;
  },
  purposeList(state, value) {
    state.purposeList = value;
  },
  hostList(state, value) {
    state.hostList = value;
  },
};

export const actions = {
  resetState({ commit }) {
    commit("RESET_STATE");
  },

  async fetchData(
    { commit, state },
    { key, refresh, endpoint, options, filters }
  ) {
    try {
      // if (state[key] && !options.refresh) {
      //   return state[key];
      // }

      let configs = {
        params: {
          company_id: this.$auth.user.company_id,
        },
      };

      if (options) {
        const { page, sortBy, sortDesc, itemsPerPage } = options;
        configs.params.page = page;
        configs.params.per_page = itemsPerPage;
        configs.params.sortBy = sortBy ? sortBy[0] : "";
        configs.params.sortDesc = sortDesc ? sortDesc[0] : "";
      }

      if (filters) {
        configs.params = { ...configs.params, ...filters };
      }

      const { data } = await this.$axios.get(endpoint, configs);
      //console.log(`Fetching ${key}:`, data);

      commit(key, data);
      return data;
    } catch (error) {
      console.error(`Error fetching ${key}:`, error);
      throw new Error(`Failed to fetch ${key}: ${error.message}`);
    }
  },

  async fetchDropDowns({ commit }, { key, endpoint }) {
    try {
      const { data } = await this.$axios.get(endpoint, {
        params: {
          order_by: "name",
          company_id: this.$auth.user.company_id,
        },
      });
      commit(key, data);
      return data;
    } catch (error) {
      console.error(`Error fetching ${key}:`, error);
      throw new Error(`Failed to fetch ${key}: ${error.message}`);
    }
  },

  async roles_list({ commit, state }) {
    if (state.roles) return state.roles;

    try {
      const { data } = await this.$axios.get("role-list", {
        params: {
          order_by: "name",
          company_id: this.$auth.user.company_id,
        },
      });
      commit("roles", data);
      return data;
    } catch (error) {
      return error;
    }
  },

  async leave_groups_list({ commit, state }) {
    if (state.leave_groups) return state.leave_groups;

    try {
      const { data } = await this.$axios.get("leave-group-list", {
        params: {
          order_by: "name",
          company_id: this.$auth.user.company_id,
        },
      });
      commit("leave_groups", data);
      return data;
    } catch (error) {
      return error;
    }
  },

  async leave_managers_list({ commit, state }) {
    if (state.leave_managers) return state.leave_managers;

    try {
      const { data } = await this.$axios.get("employee-list", {
        params: {
          order_by: "name",
          company_id: this.$auth.user.company_id,
        },
      });
      commit("leave_managers", data);
      return data;
    } catch (error) {
      return error;
    }
  },
  async devices_list({ commit, state }) {
    if (state.devices_list) return state.devices_list;

    try {
      const { data } = await this.$axios.get("device-list", {
        params: {
          order_by: "name",
          company_id: this.$auth.user.company_id,
        },
      });
      commit("devices_list", data);
      return data;
    } catch (error) {
      return error;
    }
  },
  async department_list({ commit, state }, options) {
    try {
      if (state.department_list && options.isFilter == false)
        return state.department_list;
      const { data } = await this.$axios.get(options.endpoint, options);
      commit("department_list", data);
      return data;
    } catch (error) {
      return error;
    }
  },

  async timezone_list({ commit, state }, options) {
    try {
      if (state.timezone_list && options.isFilter == false)
        return state.timezone_list;
      const { data } = await this.$axios.get(options.endpoint, options);
      commit("timezone_list", data);
      return data;
    } catch (error) {
      return error;
    }
  },

  async designation_list({ commit, state }, options) {
    try {
      if (state.designation_list && options.isFilter == false)
        return state.designation_list;
      const { data } = await this.$axios.get(options.endpoint, options);
      commit("designation_list", data);
      return data;
    } catch (error) {
      return error;
    }
  },
};
