import React, { useState, useEffect, Component } from "react";
import Popup from "../../ReactCommon/Components/Popup";
import utilityGlobal from "../../ReactCommon/utility/utilityGlobal";
import Forms from "../../constants/Forms/FormConstant";
import Constants from "../../constants/Global";

import { ToastContainer, toast } from "react-toastify";
import "react-toastify/dist/ReactToastify.css";
const extra_url = Constants.extra_url;
const front_url = Constants.front_url;
import Loader from "./../loader/loader";
import Link from "next/link";
class PopupForm extends Component {
  constructor(props) {
    super(props);
    this.handleChange = this.handleChange.bind(this);
    this.handleSubmit = this.handleSubmit.bind(this);
    this.toggleForm = this.toggleForm.bind(this);
    this.state = {
      formConfig: Forms.loginForm,
      validateField: Forms.validateFields.loginForm,
      showBtn: false,
      dataFlag: false,
      email: "",
      signText: "Sign in",
    };
  }

  componentDidMount() {
    if (localStorage.getItem("isjoin")) {
      document.getElementById("newAccount").click();
      localStorage.removeItem("isjoin");
    }
  }
  handleChange(e) {
    let data = {};
    let inpval = e.target.value;
    if (e.target.name === "mobile") {
      var x = e.target.value
        .replace(/\D/g, "")
        .match(/(\d{0,3})(\d{0,3})(\d{0,4})/);
      inpval = !x[2]
        ? x[1]
        : "(" + x[1] + ") " + x[2] + (x[3] ? "-" + x[3] : "");
    }
    if (
      e.target.name === "password" ||
      e.target.name === "confirmationPassword"
    ) {
      let psd = document.getElementById("password").value;
      let cnf = document.getElementById("confirmationPassword").value;
      if (psd == cnf) {
        document.getElementById("ConfrimMessage").innerHTML = "";
      } else {
        document.getElementById("ConfrimMessage").innerHTML =
          "Confirm password must be same as password";
      }
    }
    data[e.target.name] = inpval;
    this.setState(data, () => {
      this.setState({
        showBtn: utilityGlobal.validateData(this.state.validateField, this)
          .status,
      });
    });
  }
  handleSubmit(e) {
    e.preventDefault();
    this.setState({
      dataFlag: true,
    });

    let { validateField } = this.state;

    let data = {};
    for (let i = 0; i < validateField.length; i++) {
      data[validateField[i]] = this.state[validateField[i]];
    }
    let btnClick = e.target.name;
    let body = "";
    let page = "";
    if (btnClick === "loginBtn") {
      body = JSON.stringify({
        Email: data["email"],
        Password: data["login_password"],
        AgentId: Constants.agentId,
      });
      page = "login";
    }
    if (btnClick === "nextBtn") {
      body = JSON.stringify({
        email: data["signupEmail"],
        AgentId: Constants.agentId,
      });
      page = "duplicateEmail"; // email send
    }
    if (btnClick === "submitCode") {
      body = JSON.stringify({
        code: data["confirmCode"],
        email: this.state.email,
      });
      page = "confirmCode";
    }
    if (btnClick === "signupBtn") {
      this.setState({
        email: data["signupEmail"],
      });
      body = JSON.stringify({
        Email: data["signupEmail"],
        Firstname: data["fullName"],
        Phone: data["mobile"],
        Password: data["password"],
        AgentId: Constants.agentId,
      });
      page = "register";
    }
    if (btnClick === "forgotBtn") {
      let reset_url = front_url + "set-password/";
      body = JSON.stringify({ Email: data["forgot_email"], Url: reset_url });
      page = "forgotPassword";
    }
    const requestOptions = {
      method: "POST",
      headers: { "Content-Type": "application/json" },
      body: body,
    };
    let urls = extra_url + page;
    fetch(urls, requestOptions)
      .then((response) => response.text())
      .then((res) => JSON.parse(res))
      .then((json) => {
        if (page === "confirmCode") {
          return;
          if (json.confirmcode) {
            toast.success(json.message);
            if (localStorage.getItem("userDetail")) {
              let userDetail = localStorage.getItem("userDetail");
              try {
                userDetail = JSON.parse(userDetail);
                userDetail.EmailIsVerified = true;
                localStorage.setItem("userDetail", JSON.stringify(userDetail));
                this.props.handleClose();
              } catch (e) {
                console.log("error", e);
              }
            }
          }
          if (json.error) {
            toast.error(json.message);
          }
        }

        if (btnClick === "loginBtn") {
          if (json.error) {
            toast.error(json.error);
          } else if (json.errors) {
            Object.entries(res.errors).map((item) => {
              toast.error(item[1][0]);
            });
          } else {
            toast.success("Login Successfully");
            localStorage.setItem("login_token", json.token);
            localStorage.setItem(
              "userDetail",
              JSON.stringify(json.user_detail)
            );
            localStorage.setItem(
              "estimatedTokenTime",
              JSON.stringify(json.estimated_token_time)
            );
            this.props.handleClose();
            // window.location.href = "/profile";
          }
        }

        if (btnClick === "nextBtn") {
          if (json.email) {
            toast.error(json.email[0]);
          } else {
            data = {
              formConfig: Forms.emailSuccess,
              validateField: Forms.validateFields.emailSuccess,
            };
          }
          this.setState(data, () => {
            this.setState({
              showBtn: utilityGlobal.validateData(
                this.state.validateField,
                this
              ).status,
            });
          });
        }
        if (btnClick === "signupBtn") {
          if (json.error) {
            toast.error("Something went wrong try later!");
          } else if (json.errors) {
            Object.entries(json.errors).map((item) => {
              toast.error(item[1][0]);
            });
          } else {
            toast.success(json.success);
            localStorage.setItem("login_token", json.LoginDetail.token);
            localStorage.setItem(
              "userDetail",
              JSON.stringify(json.LoginDetail.user_detail)
            );
            localStorage.setItem(
              "estimatedTokenTime",
              JSON.stringify(json.LoginDetail.estimated_token_time)
            );
            if (json.email) {
              toast.error(json.email[0]);
            } else {
              // data = {
              // 	formConfig: Forms.confirmMail,
              // 	validateField: Forms.validateFields.confirmMail,
              // };
            }
            this.setState(data, () => {
              this.setState({
                showBtn: utilityGlobal.validateData(
                  this.state.validateField,
                  this
                ).status,
              });
            });
            this.props.handleClose();
          }

          // if(json.success){}
          // else{
          // 	toast.error("Something went wrong try later!");
          // }
        }

        if (btnClick === "forgotBtn") {
          if (json.message) {
            toast.success(json.message);
          }
          if (json.error) {
            toast.error(json.error);
            console.log(json.error);
          }
        }
        this.setState({
          dataFlag: false,
        });
      })
      .catch((err) => console.log({ err }));
  }
  toggleForm(e) {
    if (e.target.name == "SignUp" || e.target.name == "loginForm") {
      this.setState({
        signText: e.target.name == "SignUp" ? "Create account" : "Sign in",
      });
    } else {
      this.setState({
        signText: false,
      });
    }

    document.getElementById("LoginPopupform").reset();
    let data = {};

    data = {
      formConfig: Forms[e.target.name],
      validateField: Forms.validateFields[e.target.name],
    };
    this.setState(data, () => {
      this.setState({
        showBtn: utilityGlobal.validateData(this.state.validateField, this)
          .status,
      });
    });
  }

  render() {
    return (
      <>
        <Popup handleClose={this.props.handleClose} {...this.props}>
          <form onSubmit={this.handleSubmit} id="LoginPopupform">
            {this.state.signText && (
              // <div>

              // 	<ul className="LoginBenefit">
              // 		<li>Instant access to all photos, virtual tours, features!</li>
              // 		<li>Full property details & sold history</li>
              // 		<li>Full access to sold price data of homes & sold history</li>
              // 		<li>Favourite homes, new listing alerts to your inbox, save searches</li>
              // 		<li>New listings updated multiple times daily!</li>
              // 		<li>Your local real estate market trends & statistics</li>
              // 	</ul>
              // </div>
              <div>
                <p className="theme-color h5">{this.state.signText}</p>
                <p className="">
                  <b>Your free account includes:</b>
                </p>
                {/* <p className="sliderLoginBox-heading mt-1 h5"> <svg viewBox="0 0 24 24" width="24" height="24" className="xs-ml1" aria-hidden="true"><path d="M18 8h-1V6A5 5 0 007 6v2H6a2 2 0 00-2 2v10c0 1.1.9 2 2 2h12a2 2 0 002-2V10a2 2 0 00-2-2zM9 6a3 3 0 116 0v2H9V6zm9 14H6V10h12v10zm-6-3c1.1 0 2-.9 2-2s-.9-2-2-2-2 .9-2 2 .9 2 2 2z"></path></svg>
									Login - Free Account Required
								</p> */}
                {/* <p className="sliderLoginBox">Join Thousands of Canadians Searching For Homes on Housen.ca Every Month!</p> */}
                <ul className="LoginBenefit sliderLoginBox-list">
                  <li>Instant Access to All Photos, Virtual Tours, & More!</li>
                  <li>
                    {" "}
                    Full Access to{" "}
                    <span style={{ color: "red" }}>
                      Listing Sold History{" "}
                    </span>{" "}
                    (GTA) & Details
                  </li>
                  <li>
                    Save Homes & Searches, Add Listing/Community Watch Alerts
                  </li>
                </ul>
                {/* <div className=" text-white">
									<h6 className="join-and-signIn-head"><button onClick={this.props.signInToggle} className={`  join-signIn-toggle btn  primary-btn-cls`}>
										View Full Listing & Photos &nbsp;
										<svg viewBox="0 0 24 24" height="24" width="24" className="fill-current xs-mr1"><circle cx="12" cy="12" r="3"></circle><path d="M20 4h-3.17l-1.24-1.35A2 2 0 0014.12 2H9.88c-.56 0-1.1.24-1.48.65L7.17 4H4a2 2 0 00-2 2v12c0 1.1.9 2 2 2h16a2 2 0 002-2V6a2 2 0 00-2-2zm-8 13a5 5 0 110-10 5 5 0 010 10z"></path></svg>
									</button> {' '} {' '}</h6>
								</div> */}
              </div>
            )}
            {utilityGlobal.renderConfig(
              this.state.formConfig,
              this.state,
              this
            )}
          </form>

          <p className="text-center">
            I accept
            <a href="/Term&conditions">
              <span className="signup-link"> Terms of Use </span>
            </a>{" "}
            and
            <a href="/privacy_policy" className="signup-link">
              <span className="signup-link"> Privacy Policy </span>
            </a>
          </p>
          <p className="text-center">Or continue with</p>
        </Popup>
        {this.state.dataFlag && <Loader />}
      </>
    );
  }
}
export default PopupForm;
