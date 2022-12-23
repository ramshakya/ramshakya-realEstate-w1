import { Tab, Row, Col, Nav, Form, Button } from "react-bootstrap";
import React, { Component } from "react";
import Forms from "../../constants/Forms/userProfile";
import utilityGlobal from "../../ReactCommon/utility/utilityGlobal";
import { requestToAPI } from "../../pages/api/api";
import { ToastContainer, toast } from 'react-toastify';
import Loader from './../loader/loader';

class AccountSetting extends Component {
	constructor(props) {
		super(props);
		this.handleChange = this.handleChange.bind(this);
		this.updateProfile = this.updateProfile.bind(this);

		this.state = {
			formConfig: Forms.updateProfile,
			validateField: "",
			showBtn: false,
			email: "",
			fullName: "",
			mobile: "",
			dataFlag: false
		};
	}
	handleChange(e) {

		let data = {};
		let inpval=e.target.value;
		if (e.target.name === "mobile") {
            const { value, maxLength } = e.target;
			if (String(value).length >= maxLength) {
				e.preventDefault();
				return;
			}
			var x = e.target.value.replace(/\D/g, '').match(/(\d{0,3})(\d{0,3})(\d{0,4})/);
			inpval = !x[2] ? x[1] : '(' + x[1] + ') ' + x[2] + (x[3] ? '-' + x[3] : '');
			 
		}
		data[e.target.name] = inpval;

		if (e.target.name === "password" || e.target.name === "confirmationPassword") {
			console.log("e.target.name", e.target.name);
			let psd = document.getElementById('password').value;
			let cnf = document.getElementById('confirmationPassword').value;
			if (psd == cnf) {

				document.getElementById('ConfrimMessage').innerHTML = "";
			}
			else {
				document.getElementById('ConfrimMessage').innerHTML = "Confirm password must be same as password";
			}
			data['validateField'] = Forms.validateFields.updatePassword;
		}
		else {
			data['validateField'] = Forms.validateFields.updateProfile;
		}

		this.setState(data, () => {
			this.setState({
				showBtn: utilityGlobal.validateData(this.state.validateField, this).status,
			});
		});

		//console.log(this.state);
	}

	async updateProfile(e) {
		e.preventDefault();
		this.setState({
			// dataFlag: true,
			showBtn: false,
		});
		const { validateField } = this.state;
		 
		const data = {};
		for (let i = 0; i < validateField.length; i++) {
			data[validateField[i]] = this.state[validateField[i]]
		}
		let localStorageData = localStorage.getItem('userDetail');
		// console.log(localStorageData);
		localStorageData = JSON.parse(localStorageData);
		let id = localStorageData.login_user_id;
		let token = localStorage.getItem('login_token');
		let body = "";
		 
		if (e.target.innerHTML== "Update Password" || e.target.innerText== "Update Password") {
			console.log("validateField", this.state);
			if(!data['password'] && !data['confirmationPassword']){
				this.setState({
					showBtn: false
				});
				return;
			}
			let obj =  { 
				password: data['password'], 
			    cnfpassword: data['confirmationPassword'], 
				id: id 
			}
			body = JSON.stringify(obj);

		}
		else {
			body = JSON.stringify({
				fullname: data['fullName'], mobile: data['mobile'], id: id
			});
		}

		let res = await requestToAPI(body, "api/v1/services/updateUserDetail", "POST", token);

		if (res.success) {
			let localStorageData = JSON.parse(localStorage.getItem('userDetail'));
			localStorageData.login_name=data['fullName'];
			localStorageData.login_mobile=data['mobile'];

			// console.log(,"localStorageData");
			localStorage.setItem('userDetail', JSON.stringify(localStorageData));
			toast.success(res.success);
		}
		else {
			toast.error(res.error);
		}
		this.setState({
			dataFlag: false
		});

	}

	componentDidMount() {

		let BearerToken = localStorage.getItem('login_token');
		let localStorageData = localStorage.getItem('userDetail');
		localStorageData = JSON.parse(localStorageData);
		this.setState({
			email: localStorageData.login_email,
			mobile: localStorageData.login_mobile,
			fullName: localStorageData.login_name,
			isVerified: localStorageData.EmailIsVerified,
		})
	}
	render() {
		return (
			<>
				<div className="accountsetting">
					<form className="row" onSubmit={this.updateProfile}>
						{utilityGlobal.renderConfig(Forms.updateProfile, this.state, this)}
					</form>
				</div>
				{this.state.dataFlag &&
					<Loader />
				}
			</>
		);
	}
}
export default AccountSetting;