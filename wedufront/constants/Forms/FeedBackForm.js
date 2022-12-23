import Input from "./../../ReactCommon/Components/Input";
import Button from "./../../ReactCommon/Components/Button";
let FeedBackForm = {
	feedBackform: [
		{
			component: Input,
			propAttr: {
				props: {
					type: "text",
					name: "name",
					placeholder: "Your Name",
					className: "form-control",
					autoComplete: "off",
					id: "name",
				},
				extraProps: {
					errorMsg: "Name is required",
					validation: "required",
					label: "",
					parentcls: "mt-2 font-normal contact_inputs"
				},
				settings: [
					{
						apiKey: "name",
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
					type: "text",
					name: "email",
					placeholder: "Email Address*",
					className: "form-control",
					autoComplete: "off",
					id: "email",
				},
				extraProps: {
					errorMsg: "Input valid email",
					validation: "required,email",
					label: "",
					parentcls: " font-normal contact_inputs"
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
					type: "text",
					name: "phone",
					placeholder: "Phone Number*",
					className: "form-control",
					autoComplete: "off",
					id: "phone",
					maxLength:'15'
				},
				extraProps: {
					errorMsg: "Mobile Number is required",
					validation: "required",
					label: "",
					parentcls: " font-normal contact_inputs"
				},
				settings: [
					{
						apiKey: "phone",
						prop: "selectedValue",
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
		feedbackform: ["name", "email", "phone"]
	},
}
export default FeedBackForm;