import React, { useState, useEffect, Component } from "react";
import Popup from "../../ReactCommon/Components/Popup";
import utilityGlobal from "../../ReactCommon/utility/utilityGlobal";
import Forms from "../../constants/Forms/FormConstant";
import Constants from '../../constants/GlobalConstants';

import { ToastContainer, toast } from 'react-toastify';
import 'react-toastify/dist/ReactToastify.css';
const extra_url = Constants.extra_url;
const front_url = Constants.front_url;
import Loader from './../loader/loader';
import Link from "next/link";
class PopupForm extends Component {

	constructor(props) {
		super(props);
		this.handleChange = this.handleChange.bind(this);
		this.handleSubmit = this.handleSubmit.bind(this);
		this.toggleForm = this.toggleForm.bind(this);
		this.finished_rendering = this.finished_rendering.bind(this);
		this.state = {
			formConfig: Forms.loginForm,
			validateField: Forms.validateFields.loginForm,
			showBtn: false,
			dataFlag: false
		};
	}

	componentDidMount() {

	}
	finished_rendering() {
		console.log("finished rendering plugins");
		var spinner = document.getElementById("spinner");
		spinner.removeAttribute("style");
		spinner.removeChild(spinner.childNodes[0]);
	}
	handleChange(e) {
		let data = {};
		let inpval = e.target.value;
		if (e.target.name === "mobile") {
			var x = e.target.value.replace(/\D/g, '').match(/(\d{0,3})(\d{0,3})(\d{0,4})/);
			inpval = !x[2] ? x[1] : '(' + x[1] + ') ' + x[2] + (x[3] ? '-' + x[3] : '');
			document.getElementById('mobileValid').style.display = "none";
		}
		if (e.target.name === "password" || e.target.name === "confirmationPassword") {
			let psd = document.getElementById('password').value;
			let cnf = document.getElementById('confirmationPassword').value;
			if (psd == cnf) {

				document.getElementById('ConfrimMessage').innerHTML = "";
			}
			else {
				document.getElementById('ConfrimMessage').innerHTML = "Confirm password must be same as password";
			}
		}
		// console.log(e.target.name);
		data[e.target.name] = inpval;
		this.setState(data, () => {
			this.setState({
				showBtn: utilityGlobal.validateData(this.state.validateField, this).status,
			});
		});
	}
	handleSubmit(e) {
		e.preventDefault();
		this.setState({
			dataFlag: true
		});

		let { validateField } = this.state;

		let data = {};
		for (let i = 0; i < validateField.length; i++) {
			data[validateField[i]] = this.state[validateField[i]]
		}
		let btnClick = e.target.name;
		let body = "";
		let page = "";
		if (btnClick === "loginBtn") {
			body = JSON.stringify({ Email: data['email'], Password: data['login_password'], AgentId: Constants.agentId });
			page = "login";
		}
		if (btnClick === "nextBtn") {
			body = JSON.stringify({ email: data['signupEmail'] });
			page = "duplicateEmail";

		}
		if (btnClick === "signupBtn") {
			if (data['mobile'].length < 14) {
				document.getElementById('mobileValid').style.display = "block";
				this.setState({
					dataFlag: false
				});
				return;
			}
			body = JSON.stringify({
				Email: data['signupEmail'], Firstname: data['firstName'], Lastname: data['lastName'],
				Phone: data['mobile'], Password: data['password'], AgentId: Constants.agentId
			});
			page = "register";

		}
		if (btnClick === "forgotBtn") {
			let reset_url = front_url + '/set-password/';
			body = JSON.stringify({ Email: data['forgot_email'], Url: reset_url });
			page = "forgotPassword";

		}

		const requestOptions = {
			method: 'POST',
			headers: { 'Content-Type': 'application/json' },
			body: body
		};
		let urls = extra_url + page;
		fetch(urls, requestOptions).then((response) =>
			response.text()).then((res) => JSON.parse(res))
			.then((json) => {
				if (btnClick === "loginBtn") {
					if (json.error) {
						toast.error(json.error);
					} else if (json.errors) {
						Object.entries(res.errors).map(item => {
							toast.error(item[1][0]);
						})
					} else {
						if (window.location.href.includes("propertydetails")) {
							let propertiesSection = document.getElementById('propertiesSection').classList.remove("filter");
							// propertiesSection
						}
						toast.success("Login Successfully");
						localStorage.setItem('login_token', json.token);
						localStorage.setItem("userLoggedIn", true);
						localStorage.setItem('userDetail', JSON.stringify(json.user_detail));
						localStorage.setItem('estimatedTokenTime', JSON.stringify(json.estimated_token_time))
						this.props.handleClose();
						// window.location.href = "/profile";
					}
				}
				if (btnClick === "nextBtn") {
					if (json.email) { toast.error(json.email[0]); }
					else {
						data = {
							formConfig: Forms.emailSuccess,
							validateField: Forms.validateFields.emailSuccess,
						};
					}
					this.setState(data, () => {
						this.setState({
							showBtn: utilityGlobal.validateData(this.state.validateField, this)
								.status,
						});
					});
				}
				if (btnClick === "signupBtn") {
					if (json.error) {
						toast.error("Something went wrong try later!");
					} else if (json.errors) {
						Object.entries(json.errors).map(item => {
							toast.error(item[1][0]);
						})
					} else {
						toast.success(json.success);
						localStorage.setItem('login_token', json.LoginDetail.token);
						localStorage.setItem('userDetail', JSON.stringify(json.LoginDetail.user_detail));
						localStorage.setItem('estimatedTokenTime', JSON.stringify(json.LoginDetail.estimated_token_time))
						this.props.handleClose();
					}

					// if(json.success){}
					// else{
					// 	toast.error("Something went wrong try later!");
					// }
				}
				if (btnClick === "forgotBtn") {
					if (json.message) { toast.success(json.message); }
					if (json.error) { toast.error(json.error); console.log(json.error) }
				}
				this.setState({
					dataFlag: false
				});
			}).catch((err) => console.log({ err }));

	}
	toggleForm(e) {
		document.getElementById("LoginPopupform").reset();
		let data = {};

		data = {
			formConfig: Forms[e.target.name],
			validateField: Forms.validateFields[e.target.name],
		};

		// console.log(this.state);
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
						{utilityGlobal.renderConfig(this.state.formConfig, this.state, this)}
					</form>

					<div className="links-cls">
						<p className="text-center">I accept
							<a href="/Term&conditions"><span className="signup-link"> Terms of Use </span></a>
							and
							<a href="/privacy_policy"><span className="signup-link"> Privacy Policy </span></a>

						</p>
						<p className="text-center">Or continue with</p>
					</div>
				</Popup>

				{this.state.dataFlag &&
					<Loader />
				}

			</>
		);
	}
}
export default PopupForm;