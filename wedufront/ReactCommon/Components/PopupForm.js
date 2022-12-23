import React, { Component } from "react";
import Popup from "../../ReactCommon/Components/Popup";
import Forms from "../../Constants/FormConstant";
import utilityGlobal from "../../ReactCommon/utility/utilityGlobal";
import { requestToAPI } from "../../pages/api/api";
import { ToastContainer, toast } from 'react-toastify';
import 'react-toastify/dist/ReactToastify.css';
import baseUrl from "../../Constants/baseurl";
import SocialLogin from "./facebook";
class PopupForm extends Component {
  constructor(props) {
    super(props);
    this.handleChange = this.handleChange.bind(this);
    this.toggleForm = this.toggleForm.bind(this);
    this.handleEmail = this.handleEmail.bind(this);
    this.handleSubmit = this.handleSubmit.bind(this);
    this.handleLogin = this.handleLogin.bind(this);
    this.handleForgot = this.handleForgot.bind(this);
    this.state = {
      formConfig: Forms.loginForm,
      validateField: Forms.validateFields.loginForm,
      showBtn: false,
    };
  }
  handleChange(e) {
    let data = {};
    data[e.target.name] = e.target.value;
    this.setState(data, () => {
      this.setState({
        showBtn: utilityGlobal.validateData(this.state.validateField, this).status,
      });
    });
  }
  toggleForm(e) {
    let data = {};
    data = {
      formConfig: Forms[e.target.name],
      validateField: Forms.validateFields.forgetPassword,
    };
    this.setState(data, () => {
      this.setState({
        showBtn: utilityGlobal.validateData(this.state.validateField, this)
          .status,
      });
    });
  }
  async handleEmail() {
    let data = {};
    const { validateField } = this.state;
    let email = this.state[validateField[0]];
    let body = JSON.stringify({ email: email });
    let res = await requestToAPI(body, "frontend/duplicate_email/", "POST");

    if (res.email) {
      toast.error(res.email[0]);
    }
    else {
      data = {
        formConfig: Forms.emailSuccess,
        validateField: Forms.validateFields.emailSuccess,
      };
      // toast.success("");
    }
    this.setState(data, () => {
      this.setState({
        showBtn: utilityGlobal.validateData(this.state.validateField, this)
          .status,
      });
    });
  }
  async handleSubmit() {
    const { validateField } = this.state;
    const data = {};
    for (let i = 0; i < validateField.length; i++) {
      data[validateField[i]] = this.state[validateField[i]]
    }
    let body = JSON.stringify({
      Email: data['email'], Password: data['password'],
      Firstname: data['firstName'], Lastname: data['lastName'], Phone: data['mobile']
    });
    let res = await requestToAPI(body, "frontend/register/", "POST");
    if (res.token) {
      toast.success("Signup Successful");
      document.getElementById('closeBtn').click();
    }
    else {
      toast.error("Something went wrong try later!");
    }
  }
  async handleLogin() {
    const { validateField } = this.state;
    const data = {};
    for (let i = 0; i < validateField.length; i++) {
      data[validateField[i]] = this.state[validateField[i]]
    }

    let body = JSON.stringify({ Email: data['email'], Password: data['password'] });
    let res = await requestToAPI(body, "frontend/login/", "POST");

    if (res.errors) {
      toast.error(res.errors);
    }
    else {
      toast.success("Login Successful");
      localStorage.setItem('login_token', res.token);
      document.getElementById('closeBtn').click();
    }
  }
  async handleForgot() {
    const { validateField } = this.state;
    const data = {};
    for (let i = 0; i < validateField.length; i++) {
      data[validateField[i]] = this.state[validateField[i]]
    }

    let body = JSON.stringify({ Email: data['email'], Url: baseUrl['Url'] });
    let res = await requestToAPI(body, "frontend/forgot_password/", "POST");
    if (res.message) {
      toast.success(res.message);
    }
    if (res.error) {
      toast.error(res.error);
    }
  }
  render() {
    return (
      <>
        <Popup handleClose={this.props.handleClose}>
          {utilityGlobal.renderConfig(this.state.formConfig, this.state, this)}
        </Popup>
          
      </>
    );
  }
}

export default PopupForm;
