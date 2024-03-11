<template>
  <v-card>
    <v-toolbar class="popup_background" dense
      >Profile Details
      <v-spacer></v-spacer>
      <!-- <v-btn @click="openDocumentDrawer" small class="primary"
        >Documents&nbsp;<v-icon small> mdi-file </v-icon></v-btn
      >
      &nbsp; -->
      <v-icon @click="closeParentDialog" outlined dark>
        mdi mdi-close-circle
      </v-icon>
    </v-toolbar>

    <v-card-text style="background-color: #ddd">
      <v-row class="pt-5">
        <v-col cols="3">
          <v-card elevation="2">
            <v-row>
              <v-col cols="12">
                <div class="mt-5" style="margin: 0 auto; width: 200px">
                  <v-img
                    style="
                      width: 100%;
                      height: 200px;
                      border: 1px solid #5fafa3;
                      border-radius: 50%;
                      margin: 0 auto;
                    "
                    :src="
                      employeeObject.profile_picture || '/no-profile-image.jpg'
                    "
                  ></v-img>
                  <br />
                  <div class="text-center">
                    <strong>{{ employeeObject.full_name }}</strong>
                  </div>
                  <div class="text-center text-center">
                    <strong
                      ><v-icon color="violet">mdi-account-tie</v-icon>Employee
                      ID: {{ employeeObject.employee_id }}</strong
                    >
                  </div>
                  <div class="text-center">
                    <span v-html="formatJoiningDate"></span>
                  </div>
                </div>
                <hr />
              </v-col>
              <v-col cols="12" class="pl-5">
                <table class="view-profile-table-lineheight">
                  <!-- <tr>
                    <td>
                      <strong>Display Name </strong> :
                      {{ employeeObject.display_name }}
                    </td>
                  </tr> -->
                  <tr>
                    <td>
                      <strong>Department </strong> :
                      {{ employeeObject.department.name }}
                    </td>
                  </tr>
                  <tr>
                    <td>
                      <strong>Sub Department </strong> :
                      {{ employeeObject.sub_department.name }}
                    </td>
                  </tr>
                  <tr>
                    <td>
                      <strong>Designation </strong> :
                      {{ employeeObject.designation.name }}
                    </td>
                  </tr>
                  <tr>
                    <td>
                      <strong>Role </strong> :
                      {{ employeeObject?.user?.role?.name || "---" }}
                    </td>
                  </tr>
                  <!-- <tr>
                    <td colspan="2"><v-divider></v-divider></td>
                  </tr> -->
                  <tr>
                    <td>
                      <v-icon color="violet">mdi-cellphone-settings</v-icon>
                      <strong>Contact</strong> :
                      {{ employeeObject.phone_number || "---" }}
                    </td>
                  </tr>
                  <tr>
                    <td>
                      <v-icon color="violet">mdi-email-outline</v-icon>
                      <strong>Email</strong> :

                      {{ employeeObject.local_email || "" }}
                    </td>
                  </tr>

                  <!-- <tr>
                    <td>
                      <strong>Address</strong> :
                      {{ employeeObject.local_address || "---" }}
                      <br />
                      &nbsp; {{ employeeObject.local_city || "" }}
                      <span v-if="employeeObject.local_city">,</span>
                      &nbsp; {{ employeeObject.local_country || "" }}
                    </td>
                  </tr>
                  <tr>
                    <td>
                      <strong>Qualification</strong> :
                      {{ employeeObject.qualification.certificate }}
                    </td>
                  </tr>
                  <tr>
                    <td>
                      <strong>Bank</strong>
                      :
                      {{ employeeObject.bank.bank_name }}
                      <br />
                      &nbsp;Address: {{ employeeObject.bank.address }}
                      <br />
                      &nbsp;Acc.No: {{ employeeObject.bank.account_no }}
                    </td>
                  </tr>
                  <tr>
                    <td>
                      <strong>Timezone</strong> :
                      {{ employeeObject.timezone.timezone_name }}
                    </td>
                  </tr>
                  <tr>
                    <hr colspan="2"></hr></td>
                  </tr>
                  <tr>
                    <td>
                      <strong
                        >Passport : {{ employeeObject.passport.country }}
                      </strong>
                      : {{ employeeObject.passport.passport_no
                      }}<br />&nbsp;Expire on
                      <span v-if="employeeObject.passport.expiry_date">
                        {{
                          formatDate(employeeObject.passport.expiry_date)
                        }}</span
                      >
                      <span v-else> --- </span>
                    </td>
                  </tr>
                  <tr>
                    <td>
                      <strong>Emirates ID </strong> :
                      {{ employeeObject.emirate.emirate_id }}<br />&nbsp;Expire
                      on
                      <span v-if="employeeObject.emirate.expiry">
                        {{ formatDate(employeeObject.emirate.expiry) }}</span
                      >
                      <span v-else> --- </span>
                    </td>
                  </tr> -->
                  <tr>
                    <td>
                      <v-row>
                        <v-col md="8">
                          <v-icon color="violet">mdi-laptop</v-icon
                          ><strong> Web Login</strong></v-col
                        >
                        <v-col md="4">
                          <v-switch
                            style="margin-top: 0px"
                            disabled
                            v-model="employeeObject.status"
                          ></v-switch>
                        </v-col>
                      </v-row>
                    </td>
                  </tr>
                  <tr>
                    <td>
                      <v-icon color="violet">mdi-login-variant</v-icon
                      ><strong> Last Login </strong> :
                      {{ last_login ? last_login : "---" }}
                    </td>
                  </tr>
                  <tr>
                    <td>
                      <v-row>
                        <v-col md="8">
                          <v-icon color="violet">mdi-cellphone-settings</v-icon>
                          <strong>Mobile Login</strong>
                        </v-col>
                        <v-col md="4">
                          <v-switch
                            style="margin-top: 0px"
                            disabled
                            v-model="employeeObject.mobile_application"
                          ></v-switch>
                        </v-col>
                      </v-row>
                    </td>
                  </tr>
                </table>
              </v-col>
            </v-row>
          </v-card>
        </v-col>
        <v-col cols="9">
          <v-card>
            <v-tabs
              v-model="tab"
              background-color="transparent"
              color="whitebackground"
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
                          <h5>Contact Details</h5>
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
                          <h5>Home Country - Details</h5>
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
                          <h5>Passport Details</h5>
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
                          <h5>Emirates Details</h5>
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
                          <h5>Visa Details</h5>
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
                          <h5>Bank Details</h5>
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
                      <!-- <v-divider></v-divider> -->
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
                                  employeeObject.qualification.type
                                    ? employeeObject.qualification.type
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
                          <h5>Settings</h5>

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
                      <h5>Salary Information</h5>
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
            <v-divider></v-divider>
            <DashboardRealTimeLogTableview
              v-if="employeeObject.system_user_id > 0"
              :system_user_id="employeeObject.system_user_id"
              :key="employeeObject.system_user_id"
            />
          </v-card>
        </v-col>
        <v-col cols="2">
          <table></table>
        </v-col>
        <v-col cols="3" style="max-width: 20%">
          <table></table>
        </v-col>
        <v-col cols="2">
          <table></table>
        </v-col>
      </v-row>
      <v-navigation-drawer v-model="drawer" bottom temporary right fixed>
        <v-toolbar class="background" dense dark
          >Documents
          <v-spacer></v-spacer>

          <v-icon @click="drawer = false" outlined dark color="white">
            mdi mdi-close-circle
          </v-icon>
        </v-toolbar>
        <table style="width: 100%; border-collapse: collapse; margin: 5px">
          <thead>
            <tr>
              <th
                style="
                  padding: 8px;
                  text-align: left;
                  border-bottom: 1px solid #ddd;
                "
              >
                Title
              </th>
              <th
                style="
                  padding: 8px;
                  text-align: left;
                  border-bottom: 1px solid #ddd;
                "
              >
                Document
              </th>
            </tr>
          </thead>
          <tbody>
            <tr v-for="(document, index) in document_list" :key="index">
              <td
                style="
                  padding: 8px;
                  text-align: left;
                  border-bottom: 1px solid #ddd;
                "
              >
                {{ document.title }}
              </td>
              <td
                style="
                  padding: 8px;
                  text-align: left;
                  border-bottom: 1px solid #ddd;
                "
              >
                <a :href="document.attachment" download target="_blank">
                  <v-icon color="primary"> mdi-download </v-icon>
                </a>
              </td>
            </tr>
            <!-- Add more rows as needed -->
          </tbody>
        </table>
      </v-navigation-drawer>
    </v-card-text>
  </v-card>
</template>
<script>
import "cropperjs/dist/cropper.css";
import VueCropper from "vue-cropperjs";
export default {
  components: {
    VueCropper,
  },
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

  created() {
    this.payloadOptions = {
      params: {
        per_page: 1000,
        company_id: this.$auth.user.company_id,
      },
    };

    this.getDepartments();
    this.getSubDepartments();
    this.getDesignations();
    this.getRoles();
    this.getLastLogin();

    try {
      let employee_id = this.$route.params.id;
      if (employee_id) {
        this.editItemId(employee_id);
      }
    } catch (error) {}

    this.openDocumentDrawer();
  },
  mounted() {
    //this.getDataFromApi();
  },
  watch: {
    dialog(val) {
      val || this.close();
    },
    options: {
      handler() {
        this.getDataFromApi();
      },
      deep: true,
    },
    group() {
      this.drawer = false;
    },
  },
  computed: {
    formatJoiningDate() {
      let dateObj = new Date();

      let { joining_date } = this.employeeObject;

      if (joining_date) {
        dateObj = new Date(joining_date);
      }

      let day = dateObj.getDate();
      let month = dateObj.toLocaleString("default", { month: "long" });
      let year = dateObj.getFullYear();
      let daySuffix = this.setDaySuffix(day);
      return `<p>DOJ: ${day}<sup>${daySuffix}</sup> ${month} ${year}</p>`;
    },
  },
  methods: {
    getLastLogin() {
      //
      if (this.employeeObject)
        this.$axios
          .get(
            `activity/${this.employeeObject.user_id}?action=Login&company_id=${this.$auth.user.company_id}`
          )
          .then(({ data }) => {
            this.last_login = data.date_time;
          });
    },
    closeParentDialog() {
      this.$emit("close-parent-dialog");
    },
    openDocumentDrawer() {
      //this.drawer = true;
      this.$axios
        .get(`documentinfo/${this.employeeObject.id}`)
        .then(({ data }) => {
          this.document_list = data;
        });
    },
    formatDate(date) {
      let dateObj = new Date();

      if (date) {
        dateObj = new Date(date);
      }

      let day = dateObj.getDate();
      let month = dateObj.getMonth() + 1;
      let year = dateObj.getFullYear();
      return `${day}/${month}/${year}`;
    },
    setDaySuffix(day) {
      switch (day) {
        case 1:
        case 21:
        case 31:
          return "st";
          break;
        case 2:
        case 22:
          return "nd";
          break;
        case 3:
        case 23:
          return "rd";
          break;
        default:
          return "th";
          break;
      }
    },
    getDepartments() {
      this.$axios.get(`departments`, this.payloadOptions).then(({ data }) => {
        this.departments = data.data;
      });
    },
    getSubDepartments() {
      this.$axios
        .get(`sub-departments`, this.payloadOptions)
        .then(({ data }) => {
          this.sub_departments = data.data;
        });
    },
    getDesignations() {
      this.$axios.get(`designation`, this.payloadOptions).then(({ data }) => {
        this.designations = data.data;
      });
    },
    getRoles() {
      this.payloadOptions.params.role_type = "employee";

      this.$axios.get(`role`, this.payloadOptions).then(({ data }) => {
        this.roles = data.data;
      });
    },
    can() {
      return true;
    },
    close() {
      this.dialog = false;
    },
  },
};
</script>

<style scoped>
.v-slide-group__content {
  background-color: #ddd;
}
</style>
