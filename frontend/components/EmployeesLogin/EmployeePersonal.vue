<template>
  <v-card>
    <v-card-text v-if="employeeObject">
      <v-row class="pt-5">
        <v-col cols="12">
          <v-card>
            <v-tabs
              v-model="tab"
              background-color="transparent"
              color="violet"
              grow
              flat
            >
              <v-tab> Contact </v-tab>
              <v-tab> Passport & Emirates </v-tab>

              <v-tab> Visa & Bank </v-tab>

              <v-tab> Documents & Qualification</v-tab>

              <v-tab> Settings </v-tab>
              <v-tab> Payroll </v-tab>

              <v-tabs-slider color="violet"></v-tabs-slider>
              <v-tabs-items v-model="tab">
                <v-tab-item>
                  <v-card>
                    <v-card-text>
                      <v-row>
                        <v-col md="6" style="border-right: 1px solid #ddd">
                          <!-- <h5>Contact Details</h5> -->
                          <table class="view-profile-table-lineheight">
                            <tr>
                              <td>
                                <strong>Phone Number </strong> :
                                {{
                                  employeeObject.phone_number
                                    ? employeeObject.phone_number
                                    : "---"
                                }}
                              </td>
                            </tr>
                            <tr>
                              <td>
                                <strong>Whatsapp Number ( ex: 971XXXX)</strong>
                                :
                                {{
                                  employeeObject.whatsapp_number
                                    ? employeeObject.whatsapp_number
                                    : "---"
                                }}
                              </td>
                            </tr>
                            <tr>
                              <td>
                                <strong>Alternate Email</strong> :
                                {{
                                  employeeObject.local_email
                                    ? employeeObject.local_email
                                    : "---"
                                }}
                              </td>
                            </tr>
                            <tr>
                              <td>
                                <strong>Phone Relative Number </strong> :
                                {{
                                  employeeObject.phone_relative_number
                                    ? employeeObject.phone_relative_number
                                    : "---"
                                }}
                              </td>
                            </tr>
                            <tr>
                              <td>
                                <strong>Relation</strong> :
                                {{
                                  employeeObject.relation
                                    ? employeeObject.relation
                                    : "---"
                                }}
                              </td>
                            </tr>
                            <tr>
                              <td>
                                <strong>Local Address</strong> :
                                {{
                                  employeeObject.local_address
                                    ? employeeObject.local_address
                                    : "---"
                                }}
                              </td>
                            </tr>
                            <tr>
                              <td>
                                <strong>Local City</strong> :
                                {{
                                  employeeObject.local_city
                                    ? employeeObject.local_city
                                    : "---"
                                }}
                              </td>
                            </tr>

                            <tr>
                              <td>
                                <strong>Local Country</strong> :
                                {{
                                  employeeObject.local_country
                                    ? employeeObject.local_country
                                    : "---"
                                }}
                              </td>
                            </tr>
                          </table>
                        </v-col>
                        <v-col md="6">
                          <!-- <h5>Home Country - Details</h5> -->
                          <table class="view-profile-table-lineheight">
                            <tr>
                              <td>
                                <strong>Address</strong> :
                                {{
                                  employeeObject.home_address
                                    ? employeeObject.home_address
                                    : "---"
                                }}
                              </td>
                            </tr>
                            <tr>
                              <td>
                                <strong>Tel</strong> :
                                {{
                                  employeeObject.home_tel
                                    ? employeeObject.home_tel
                                    : "---"
                                }}
                              </td>
                            </tr>
                            <tr>
                              <td>
                                <strong>Mobile</strong> :
                                {{
                                  employeeObject.home_mobile
                                    ? employeeObject.home_mobile
                                    : "---"
                                }}
                              </td>
                            </tr>
                            <tr>
                              <td>
                                <strong>Fax</strong> :
                                {{
                                  employeeObject.home_fax
                                    ? employeeObject.home_fax
                                    : "---"
                                }}
                              </td>
                            </tr>
                            <tr>
                              <td>
                                <strong>City</strong> :
                                {{
                                  employeeObject.home_city
                                    ? employeeObject.home_city
                                    : "---"
                                }}
                              </td>
                            </tr>
                            <tr>
                              <td>
                                <strong>State</strong> :
                                {{
                                  employeeObject.home_state
                                    ? employeeObject.home_state
                                    : "---"
                                }}
                              </td>
                            </tr>
                            <tr>
                              <td>
                                <strong>Country</strong> :
                                {{
                                  employeeObject.home_country
                                    ? employeeObject.home_country
                                    : "---"
                                }}
                              </td>
                            </tr>
                            <tr>
                              <td>
                                <strong>Personal Email</strong> :
                                {{
                                  employeeObject.home_email
                                    ? employeeObject.home_email
                                    : "---"
                                }}
                              </td>
                            </tr>
                          </table>
                        </v-col>
                      </v-row>
                    </v-card-text>
                  </v-card>
                </v-tab-item>
                <v-tab-item>
                  <v-card elevation="4">
                    <v-card-text>
                      <v-row>
                        <v-col md="6" style="border-right: 1px solid #ddd">
                          <!-- <h5>Passport Details</h5> -->
                          <table class="view-profile-table-lineheight">
                            <tr>
                              <td>
                                <strong>Passport No </strong> :
                                {{
                                  employeeObject.passport
                                    ? employeeObject.passport.passport_no
                                    : "---"
                                }}
                              </td>
                            </tr>
                            <tr>
                              <td>
                                <strong>Place Of Issue </strong> :
                                {{
                                  employeeObject.passport
                                    ? employeeObject.passport.place_of_issues
                                    : "---"
                                }}
                              </td>
                            </tr>
                            <tr>
                              <td>
                                <strong>Issue Date</strong> :
                                {{
                                  employeeObject.passport
                                    ? employeeObject.passport.issue_date
                                    : "---"
                                }}
                              </td>
                            </tr>
                            <tr>
                              <td>
                                <strong>Expiry Date </strong> :
                                {{
                                  employeeObject.passport
                                    ? employeeObject.passport.expiry_date
                                    : "---"
                                }}
                              </td>
                            </tr>
                            <tr>
                              <td>
                                <strong>Country</strong> :
                                {{
                                  employeeObject.passport
                                    ? employeeObject.passport.country
                                    : "---"
                                }}
                              </td>
                            </tr>
                            <tr>
                              <td>
                                <strong>Note</strong> :
                                {{
                                  employeeObject.passport
                                    ? employeeObject.passport.note
                                    : "---"
                                }}
                              </td>
                            </tr>
                          </table>
                        </v-col>
                        <v-col md="6">
                          <!-- <h5>Emirates Details</h5> -->
                          <table class="view-profile-table-lineheight">
                            <tr>
                              <td>
                                <strong>Emirate Id </strong> :
                                {{
                                  employeeObject.emirate
                                    ? employeeObject.emirate.emirate_id
                                    : "---"
                                }}
                              </td>
                            </tr>
                            <tr>
                              <td>
                                <strong>Nationality </strong> :
                                {{
                                  employeeObject.emirate
                                    ? employeeObject.emirate.nationality
                                    : "---"
                                }}
                              </td>
                            </tr>
                            <tr>
                              <td>
                                <strong>Issue Date</strong> :
                                {{
                                  employeeObject.emirate
                                    ? employeeObject.emirate.issue
                                    : "---"
                                }}
                              </td>
                            </tr>
                            <tr>
                              <td>
                                <strong>Expiry Date </strong> :
                                {{
                                  employeeObject.emirate
                                    ? employeeObject.emirate.expiry
                                    : "---"
                                }}
                              </td>
                            </tr>
                            <tr>
                              <td>
                                <strong>Date of Birth</strong> :
                                {{
                                  employeeObject.emirate
                                    ? employeeObject.emirate.date_of_birth
                                    : "---"
                                }}
                              </td>
                            </tr>
                          </table>
                        </v-col>
                      </v-row>
                    </v-card-text>
                  </v-card>
                </v-tab-item>
                <v-tab-item>
                  <v-card elevation="2">
                    <v-card-text>
                      <v-row>
                        <v-col md="6" style="border-right: 1px solid #ddd">
                          <!-- <h5>Visa Details</h5> -->
                          <table class="view-profile-table-lineheight">
                            <tr>
                              <td>
                                <strong>Visa Number </strong> :
                                {{
                                  employeeObject.visa
                                    ? employeeObject.visa.visa_no
                                    : "---"
                                }}
                              </td>
                            </tr>
                            <tr>
                              <td>
                                <strong>Place Of Issue </strong> :
                                {{
                                  employeeObject.visa
                                    ? employeeObject.visa.place_of_issues
                                    : "---"
                                }}
                              </td>
                            </tr>
                            <tr>
                              <td>
                                <strong>Issue Date</strong> :
                                {{
                                  employeeObject.visa
                                    ? employeeObject.visa.issue_date
                                    : "---"
                                }}
                              </td>
                            </tr>
                            <tr>
                              <td>
                                <strong>Expiry Date </strong> :
                                {{
                                  employeeObject.visa
                                    ? employeeObject.visa.expiry_date
                                    : "---"
                                }}
                              </td>
                            </tr>
                            <tr>
                              <td>
                                <strong>Country</strong> :
                                {{
                                  employeeObject.visa
                                    ? employeeObject.visa.country
                                    : "---"
                                }}
                              </td>
                            </tr>
                            <tr>
                              <td>
                                <strong>Labour No</strong> :
                                {{
                                  employeeObject.visa
                                    ? employeeObject.visa.note
                                    : "---"
                                }}
                              </td>
                            </tr>
                            <tr>
                              <td>
                                <strong>Note</strong> :
                                {{
                                  employeeObject.visa
                                    ? employeeObject.visa.note
                                    : "---"
                                }}
                              </td>
                            </tr>
                          </table>
                        </v-col>
                        <v-col md="6">
                          <!-- <h5>Bank Details</h5> -->
                          <table class="view-profile-table-lineheight">
                            <tr>
                              <td>
                                <strong>Bank Name</strong> :
                                {{
                                  employeeObject.bank
                                    ? employeeObject.bank.bank_name
                                    : "---"
                                }}
                              </td>
                            </tr>
                            <tr>
                              <td>
                                <strong>Bank Address </strong> :
                                {{
                                  employeeObject.bank
                                    ? employeeObject.bank.address
                                    : "---"
                                }}
                              </td>
                            </tr>
                            <tr>
                              <td>
                                <strong>Account No</strong> :
                                {{
                                  employeeObject.bank
                                    ? employeeObject.bank.account_no
                                    : "---"
                                }}
                              </td>
                            </tr>
                            <tr>
                              <td>
                                <strong>Account Name</strong> :
                                {{
                                  employeeObject.bank
                                    ? employeeObject.bank.account_title
                                    : "---"
                                }}
                              </td>
                            </tr>
                            <tr>
                              <td>
                                <strong>IBAN</strong> :
                                {{
                                  employeeObject.bank
                                    ? employeeObject.bank.iban
                                    : "---"
                                }}
                              </td>
                            </tr>
                            <tr>
                              <td>
                                <strong>Other Text</strong> :
                                {{
                                  employeeObject.bank
                                    ? employeeObject.bank.other_text
                                    : "---"
                                }}
                              </td>
                            </tr>
                            <tr>
                              <td>
                                <strong>Other Value</strong> :
                                {{
                                  employeeObject.bank
                                    ? employeeObject.bank.other_value
                                    : "---"
                                }}
                              </td>
                            </tr>
                          </table>
                        </v-col>
                      </v-row>
                    </v-card-text>
                  </v-card>
                </v-tab-item>
                <v-tab-item>
                  <v-card elevation="2">
                    <v-card-text>
                      <h5>Documents({{ document_list.length }})</h5>
                      <table
                        style="
                          width: 100%;
                          border-collapse: collapse;
                          margin: 5px;
                        "
                      >
                        <thead>
                          <tr>
                            <th style="padding: 8px; text-align: left">
                              Title
                            </th>
                            <th style="padding: 8px; text-align: left">
                              Document
                            </th>
                          </tr>
                        </thead>
                        <tbody>
                          <tr
                            v-for="(document, index) in document_list"
                            :key="index"
                          >
                            <td
                              style="
                                padding: 8px;
                                text-align: left;
                                border-top: 1px solid #ddd;
                              "
                            >
                              {{ document.title }}
                            </td>
                            <td
                              style="
                                padding: 8px;
                                text-align: left;
                                border-top: 1px solid #ddd;
                              "
                            >
                              <a
                                :href="document.attachment"
                                download
                                target="_blank"
                              >
                                <v-icon color="primary"> mdi-download </v-icon>
                              </a>
                            </td>
                          </tr>
                          <!-- Add more rows as needed -->
                        </tbody>
                      </table>

                      <v-row>
                        <v-col md="6" style="border-right: 0px solid #ddd">
                          <h5>Qualification Details</h5>
                          <table class="view-profile-table-lineheight">
                            <tr>
                              <td>
                                <strong>Certificate </strong> :
                                {{
                                  employeeObject.qualification.certificate
                                    ? employeeObject.qualification.certificate
                                    : "---"
                                }}
                              </td>
                            </tr>
                            <tr>
                              <td>
                                <strong>College </strong> :
                                {{
                                  employeeObject.qualification.collage
                                    ? employeeObject.qualification.collage
                                    : "---"
                                }}
                              </td>
                            </tr>
                            <tr>
                              <td>
                                <strong>Start Date</strong> :
                                {{
                                  employeeObject.qualification.start
                                    ? employeeObject.qualification.start
                                    : "---"
                                }}
                              </td>
                            </tr>
                            <tr>
                              <td>
                                <strong>End Date</strong> :
                                {{
                                  employeeObject.qualification.end
                                    ? employeeObject.qualification.end
                                    : "---"
                                }}
                              </td>
                            </tr>
                            <tr>
                              <td>
                                <strong>Type</strong> :
                                {{
                                  employeeObject.bank.type
                                    ? employeeObject.bank.type
                                    : "---"
                                }}
                              </td>
                            </tr>
                          </table>
                        </v-col>
                      </v-row>
                    </v-card-text>
                  </v-card>
                </v-tab-item>
                <v-tab-item>
                  <v-card elevation="2">
                    <v-card-text>
                      <v-row>
                        <v-col md="6" style="border-right: 1px solid #ddd">
                          <!-- <h5>Settings</h5> -->

                          <table style="width: 70%">
                            <tr>
                              <td><strong>Employee Status</strong></td>
                              <td>
                                <v-switch
                                  disabled
                                  color="success"
                                  class="mt-0 ml-2"
                                  v-model="employeeObject.status"
                                ></v-switch>
                              </td>
                            </tr>
                            <tr>
                              <td><strong>Web Login Access</strong></td>
                              <td>
                                <v-switch
                                  disabled
                                  color="success"
                                  class="mt-0 ml-2"
                                  v-model="employeeObject.user.web_login_access"
                                ></v-switch>
                              </td>
                            </tr>
                            <tr>
                              <td><strong>Mobile App Login Access</strong></td>
                              <td>
                                <v-switch
                                  disabled
                                  color="success"
                                  class="mt-0 ml-2"
                                  v-model="
                                    employeeObject.user.mobile_app_login_access
                                  "
                                ></v-switch>
                              </td>
                            </tr>

                            <tr>
                              <td><strong>Over Time</strong></td>
                              <td>
                                <div class="text-overline mb-1">
                                  <v-switch
                                    disabled
                                    color="success"
                                    class="mt-0 ml-2"
                                    v-model="employeeObject.overtime"
                                  ></v-switch>
                                </div>
                              </td>
                            </tr>
                            <tr>
                              <td><strong>Enable Whatsapp OTP</strong></td>
                              <td>
                                <div class="text-overline mb-1">
                                  <v-switch
                                    disabled
                                    color="success"
                                    class="mt-0 ml-2"
                                    v-model="
                                      employeeObject.user.enable_whatsapp_otp
                                    "
                                  ></v-switch>
                                </div>
                              </td>
                            </tr>
                            <tr>
                              <td><strong>Live Tracking Location</strong></td>
                              <td>
                                <div class="text-overline mb-1">
                                  <v-switch
                                    disabled
                                    color="success"
                                    class="mt-0 ml-2"
                                    v-model="
                                      employeeObject.user.tracking_status
                                    "
                                  ></v-switch>
                                </div>
                              </td>
                            </tr>
                          </table>
                          <table class="view-profile-table-lineheight">
                            <tr>
                              <td>
                                <strong>Leave Group </strong> :
                                {{
                                  employeeObject.leave_group
                                    ? employeeObject.leave_group.name
                                    : "---"
                                }}
                              </td>
                            </tr>
                            <tr>
                              <td>
                                <strong>Leave Manager/Reporting Manger </strong>
                                :
                                {{
                                  employeeObject.reporting_manager
                                    ? employeeObject.reporting_manager
                                        .first_name +
                                      " " +
                                      employeeObject.reporting_manager.last_name
                                    : "---"
                                }}
                              </td>
                            </tr>
                          </table>
                        </v-col>
                      </v-row>
                    </v-card-text>
                  </v-card>
                </v-tab-item>
                <v-tab-item>
                  <v-card elevation="2">
                    <v-card-text>
                      <!-- <h5>Salary Information</h5> -->
                      <v-row>
                        <v-col md="6" style="border-right: 1px solid #ddd">
                          <table
                            v-if="employeeObject.payroll"
                            class="employee-table view-profile-table-lineheight"
                            style="width: 100%"
                          >
                            <tr style="border: 0px solid #ddd">
                              <th>Effective Date</th>
                              <td class="text-left">
                                :
                                {{
                                  employeeObject.payroll &&
                                  employeeObject.payroll
                                    .effective_date_formatted
                                    ? employeeObject.payroll
                                        .effective_date_formatted
                                    : "---"
                                }}
                              </td>
                            </tr>
                            <tr style="border: 0px solid #ddd">
                              <th>Basic Salary</th>

                              <td class="text-left">
                                :
                                {{
                                  employeeObject.payroll &&
                                  employeeObject.payroll.basic_salary
                                    ? employeeObject.payroll.basic_salary
                                    : "---"
                                }}
                              </td>
                            </tr>

                            <tr
                              style="border: 0px solid #ddd"
                              v-for="(item, index) in employeeObject.payroll
                                .earnings"
                              :key="index"
                            >
                              <th>{{ item.label }}</th>
                              <td class="text-left">: {{ item.value }}</td>
                            </tr>
                          </table>
                        </v-col>
                      </v-row>
                    </v-card-text>
                  </v-card>
                </v-tab-item>
              </v-tabs-items>
            </v-tabs>
          </v-card>
        </v-col>
      </v-row>
    </v-card-text>
  </v-card>
</template>
<script>
export default {
  props: ["employeeObject"],
  data: () => ({
    switchValue: true,
    image: "",
    mime_type: "",
    cropedImage: "",
    cropper: "",
    autoCrop: false,
    selectedFile: "",
    upload_edit: {
      name: "",
    },
    drawer: false,
    group: null,
    attrs: [],
    dialog: false,
    editDialog: false,
    tab: null,
    m: false,
    expand: false,
    expand2: false,
    boilerplate: false,
    right: true,
    rightDrawer: false,
    tab: null,
    selectedItem: 1,

    on: "",
    color: "background",
    files: "",
    Model: "Employee",
    endpoint: "employee",
    search: "",
    loading: false,
    total: 0,
    next_page_url: "",
    prev_page_url: "",
    current_page: 1,
    per_page: 8,
    response: "",
    ListName: "",
    snackbar: false,
    btnLoader: false,
    max_employee: 0,
    employee: {
      title: "",
      display_name: "",
      employee_id: "",
      system_user_id: "",
      profile_picture: "",
      employee_role_id: "",
    },
    upload: {
      name: "",
    },
    previewImage: null,
    snackbar: false,
    ids: [],
    loading: false,
    total: 0,
    titleItems: ["Mr", "Mrs", "Miss", "Ms", "Dr"],
    editedIndex: -1,
    response: "",
    departments: [],
    sub_departments: [],
    designations: [],
    roles: [],
    data: [],
    errors: [],
    departments: [],
    department_id: "",
    payloadOptions: {},
    last_login: {},
    document_list: [],
  }),

  created() {},
  mounted() {},
  watch: {},
  computed: {},
  methods: {},
};
</script>
