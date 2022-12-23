import React, { Component } from "react";
import "react-toastify/dist/ReactToastify.css";
import { ToastContainer, toast } from "react-toastify";
import LeadFormJson from "./../../../constants/Forms/LeadsFormConstant";
import ScheduleForm from "./../../../constants/Forms/ScheduleFormConstant";
import utilityGlobal from "./../../utility/utilityGlobal";
import { agentInfo, leadFormApi } from "./../../../constants/GlobalConstants";
import API from "./../../utility/api";
import Constants from "../../../constants/GlobalConstants";
import Schedule from "./../ScheduleShowing";
import Loader from "../../../components/loader/loader";
import AgentProfile from "./../../../public/images/agentInfo/profile.png";
class RightSection extends Component {
  constructor(props) {
    super(props);
    this.handleChange = this.handleChange.bind(this);
    this.submitForm = this.submitForm.bind(this);

    this.bookAShowing = this.bookAShowing.bind(this);
    this.forceToLogin = this.forceToLogin.bind(this);

    this.state = {
      detail: props,
      formConfig: LeadFormJson.leadForm,
      validateField: LeadFormJson.validateFields.leadForm,
      showBtn: false,
      bookAShowing: false,
      userDetails: props.userDetails ? props.userDetails : {},
      dataFlag: false,
      agentInfo: Constants.agentInfo,
    };
  }
  handleChange(e) {
    let data = {};
    let inpval = e.target.value;
    if (e.target.name === "phone") {
      var x = e.target.value
        .replace(/\D/g, "")
        .match(/(\d{0,3})(\d{0,3})(\d{0,4})/);
      inpval = !x[2]
        ? x[1]
        : "(" + x[1] + ") " + x[2] + (x[3] ? "-" + x[3] : "");
    }
    data[e.target.name] = inpval;
    e.target.value = inpval;
    this.setState(data, () => {
      console.log(
        "in forms",
        utilityGlobal.validateData(this.state.validateField, this).status
      );
      this.setState({
        showBtn: utilityGlobal.validateData(this.state.validateField, this)
          .status,
      });
    });
  }
  submitForm(e) {
    e.preventDefault();
    console.log("in submit");
    this.setState({
      dataFlag: true,
    });
    this.setState({
      showBtn: false,
    });

    const { validateField } = this.state;
    const data = {};
    for (let i = 0; i < validateField.length; i++) {
      data[validateField[i]] = this.state[validateField[i]];
    }
    data.property_url = window.location.href;
    data.property_id = this.props.Ml_num;
    data.user_id = this.state.userDetails.login_user_id
      ? this.state.userDetails.login_user_id
      : 1;
    data.page_from = "propertydetails";
    data.agent_id = Constants.agentId;
    data.propertyaddress = this.props.Addr;
    API.jsonApiCall(leadFormApi, data, "POST", null, {
      "Content-Type": "application/json",
    })
      .then((res) => {
        toast.success("Submit Successfully");
        this.setState({
          showBtn: false,
          dataFlag: false,
        });
        e.target.name.value = "";
        e.target.email.value = "";
        e.target.phone.value = "";
        e.target.message.value = "";
      })
      .catch((e) => {
        // console.log("error", e);
        toast.error("Something went wrong try later!");
        this.setState({
          dataFlag: false,
        });
      });
  }
  forceToLogin() {
    this.props.togglePopUp();
  }

  bookAShowing(click) {
    this.setState({
      bookAShowing: !this.state.bookAShowing,
    });
  }
  render() {
    return (
      <>
        <div id="sidebarcc">
          <Schedule props={this.state} bookshow={this.bookAShowing} />
          <div className="schedule_section" id="sidebarSection">
            <div className="">
              <div className="my-form">
                <div className="head-form" hidden>
                  <img
                    {...AgentProfile}
                    className="img-fluid agent-profile"
                    alt="agent-profile"
                  />
                  <div className="head-text">
                    <h6 className="h4">{this.state.agentInfo.name}</h6>
                    <span>{this.state.agentInfo.title}</span>
                    <span>{this.state.agentInfo.officeName}</span>
                    <span>{this.state.agentInfo.type}</span>
                    <span>{this.state.agentInfo.OfficeAddress}</span>
                    <span>
                      {this.state.agentInfo.city},{this.state.agentInfo.state}
                    </span>
                    <p>
                      <a
                        href={"tel:" + this.state.agentInfo.brokerageNumber}
                        className="text-secondary"
                      >
                        {this.state.agentInfo.brokerageNumber}
                      </a>
                    </p>
                  </div>
                </div>
                <h6 className="formHeading h3">Get more information</h6>
                <div className="mt-2">
                  {this.props.isLogin ? (
                    <>
                      <button
                        name="showSchedule"
                        type="button"
                        btnclass="btn showSchedule"
                        onClick={this.bookAShowing}
                        className="btn showSchedule"
                        id="schedule"
                      >
                        Schedule a showing
                      </button>
                    </>
                  ) : (
                    <>
                      <button
                        name="showSchedule"
                        type="button"
                        btnclass="btn showSchedule"
                        onClick={this.forceToLogin}
                        className="btn showSchedule"
                        id="schedule"
                      >
                        Schedule a showing
                      </button>
                    </>
                  )}
                </div>
              </div>
            </div>
          </div>
          <div className="schedule_section mt-4">
            <div className="my-form ">
              <h6 className="h6"> Request Info </h6>
              <form
                id="contactForm"
                action=""
                className="sidebar_propform"
                onSubmit={this.submitForm}
              >
                {utilityGlobal.renderConfig(
                  this.state.formConfig,
                  this.state,
                  this
                )}
              </form>
            </div>
          </div>
          {this.state.dataFlag && <Loader />}
        </div>
      </>
    );
  }
}
export default RightSection;
