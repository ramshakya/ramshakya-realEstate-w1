import Input from "./../Components/Input";
import Button from "../Components/Button";
let Forms = {
	loginForm: [
		{
			component: Input,
			propAttr: {
				props: {
					type: "text",
					name: "email",
					placeholder: "Enter email",
					className: "form-control",
					autocomplete: "off",
					id: "login_email",
				},
				extraProps: {
					errorMsg: "email is required",
					validation: "required,email",
				},
				settings: [
					{
						apiKey: "email",
						prop: "selectedValue",
					},
					{
						funcName: "handleChange",
						prop: "cb",
					},
				],
			},
		},
		{
			component: Input,
			propAttr: {
				props: {
					type: "password",
					name: "password",
					placeholder: "Enter password",
					className: "form-control my-0",
					autocomplete: "off",
					id: "login_password",
				},
				extraProps: {
					errorMsg: "password is required",
					validation: "required",
				},
				settings: [
					{
						apiKey: "password",
						prop: "selectedValue",
					},
					{
						funcName: "handleChange",
						prop: "cb",
					},
				],
			},

			// cb:"handleChange"
		},
		{
			propAttr: {
				props: {
					name: "submitBtn",
					type: "button",
					"data-action": "next",
					className: "sendClass submit-btn btn ",
				},
				settings: [
					{
						prop: "showBtn",
						apiKey: "showBtn",
					},
					{
						prop: 'cb',
						funcName: 'handleLogin'//fun name 
					}
				],
				btnDivCls: "sendClass submit-btn btn ",
				extraProps: {
					label: "Send ",
				},
			},
			component: Button,
		},
	],

	signUpForm: [
		{
			component: "h4",
			propAttr: {
				className: "head-login",
			},
			children: 'Sign Up'
		},
		{
			component: Input,
			propAttr: {
				props: {
					type: "text",
					name: "email",
					placeholder: "Enter email",
					className: "form-control",
					autocomplete: "off",
					id: "login_email",
				},
				extraProps: {
					errorMsg: "email is required",
					validation: "required,email",
				},
				settings: [
					{
						apiKey: "email",
						prop: "selectedValue",
					},
					{
						funcName: "handleChange",
						prop: "cb",
					},
				],
			},
		},
		 
	],
	 
	validateFields: {
		loginForm: ["email", "password"],
		forgetPassword: ["email"],
		signUpForm: ['email'],
		emailSuccess: ['email', 'firstName', 'lastName', 'mobile', 'password', 'confirmationPassword']
	},
};
export default Forms;
