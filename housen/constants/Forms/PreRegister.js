import Input from "../../ReactCommon/Components/Input";
import Button from "../../ReactCommon/Components/Button";
let PreRegister = {
	Register: [
		{
			component: Input,
			propAttr: {
				props: {
					type: "text",
					name: "fname",
					placeholder: "",
					className: "form-control",
					autoComplete: "off",
					id: "fname",
				},
				extraProps: {
					errorMsg: "Name is required",
					validation: "required",
					label: "",
					parentcls: "col-md-6 mt-2 font-normal inputs-areas",
					label: "First name"
				},
				settings: [
					{
						apiKey: "name",
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
					name: "lname",
					placeholder: "",
					className: "form-control",
					autoComplete: "off",
					id: "lname",
				},
				extraProps: {
					errorMsg: "",
					validation: "",
					label: "",
					parentcls: "col-md-6 mt-2 font-normal inputs-areas",
					label: "Last name"
				},
				settings: [
					{
						apiKey: "name",
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
					name: "email",
					placeholder: "",
					className: "form-control",
					autoComplete: "off",
					id: "email",
				},
				extraProps: {
					errorMsg: "Input valid email id",
					validation: "required,email",
					label: "",
					parentcls: "col-md-6 mt-2 font-normal inputs-areas",
					label: "Email"

				},
				settings: [
					{
						apiKey: "email",
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
					name: "phone",
					placeholder: "",
					className: "form-control",
					autoComplete: "off",
					id: "phone",
				
				},
				extraProps: {
					errorMsg: "Mobile Number is required",
					validation: "required",
					label: "",
					parentcls: "col-md-6 mt-2 font-normal inputs-areas",
					label: "Phone"

				},
				settings: [
					{
						apiKey: "phone",
						prop: "",
					},
					{
						funcName: "handleChange",
						prop: "cb",
					},
				],
			},
		}
	],
	validateFields: {
		Register: ["fname", "email", "phone"]
	},
}
export default PreRegister;