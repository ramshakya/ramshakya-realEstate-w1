import Input from "./../../ReactCommon/Components/Input";
import Button from "./../../ReactCommon/Components/Button";
let ContactUs = {
	contactform: [
		{
			component: Input,
			propAttr: {
				props: {
					type: "text",
					name: "user_name",
					placeholder: "Your Name",
					className: "form-control",
					autoComplete: "off",
					id: "user_name",
				},
				extraProps: {
					errorMsg: "Name is required",
					validation: "required,alpha",
					label: "",
					parentcls: "mt-2 font-normal contact_inputs"
				},
				settings: [
					{
						apiKey: "user_name",
						prop: "",
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
					type: "text",
					name: "user_email",
					placeholder: "Your Email",
					className: "form-control",
					autoComplete: "off",
					id: "user_email",
				},
				extraProps: {
					errorMsg: "Input valid email",
					validation: "required,email",
					label: "",
					parentcls: " font-normal contact_inputs"
				},
				settings: [
					{
						apiKey: "user_email",
						prop: "",
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
					type: "text",
					name: "user_phone",
					placeholder: "Phone Number",
					className: "form-control",
					autoComplete: "off",
					id: "user_phone",
					maxLength:'15'
				},
				extraProps: {
					errorMsg: "Input valid mobile number",
					validation: "required",
					label: "",
					parentcls: " font-normal contact_inputs"
				},
				settings: [
					{
						apiKey: "user_phone",
						prop: "",
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
					type: "text",
					name: "user_location",
					placeholder: "City or Location",
					className: "form-control",
					autoComplete: "off",
					id: "user_location",
					 
				},
				extraProps: {
					errorMsg: "City or Location is required",
					validation: "required",
					label: "",
					parentcls: " font-normal contact_inputs"
				},
				settings: [
					{
						apiKey: "user_location",
						prop: "",
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
		contactform: ["user_name", "user_email", "user_phone","user_location"]
	},
}
export default ContactUs;