import Input from "../../ReactCommon/Components/Input";
import Button from "../../ReactCommon/Components/Button";
let LeadsFormConstant = {
	ScheduleForm: [
		{
			component: Input,
			propAttr: {
				props: {
					type: "text",
					name: "user_name",
					placeholder: "",
					className: "form-control",
					autoComplete: "off",
					id: "user_name",
				},
				extraProps: {
					errorMsg: "Name name is required",
					validation: "required",
					label: "Full Name",
					parentcls: "mt-2 font-normal inputs-areas"
				},
				settings: [
					{
						apiKey: "user_name",
						prop: "selectedValue",
					},
					{
						funcName: "handleChangeForSchdule",
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
					placeholder: "",
					className: "form-control",
					autoComplete: "off",
					id: "user_email",
				},
				extraProps: {
					errorMsg: "Input valid email id",
					validation: "required,email",
					label: "Email Address",
					parentcls: " font-normal inputs-areas"
				},
				settings: [
					{
						apiKey: "user_email",
						prop: "selectedValue",
					},
					{
						funcName: "handleChangeForSchdule",
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
					placeholder: "",
					className: "form-control",
					autoComplete: "off",
					id: "user_phone",
					maxLength:'15'
				},
				extraProps: {
					errorMsg: "Mobile Number is required",
					validation: "required",
					label: "Mobile Number",
					parentcls: " font-normal inputs-areas"
				},
				settings: [
					{
						apiKey: "user_phone",
						prop: "selectedValue",
					},
					{
						funcName: "handleChangeForSchdule",
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
					name: "user_message",
					placeholder: "",
					className: "form-control",
					
					autoComplete: "off",
					id: "user_message",
				},
				extraProps: {
					errorMsg: "",
					validation: "",
					label: "Additional Notes (optional)",
					parentcls: " font-normal inputs-areas"
				},
				settings: [
					{
						apiKey: "user_message",
						prop: "selectedValue",
					},
					{
						funcName: "handleChangeForSchdule",
						prop: "cb",
					},
				],
			},
		},
		{
			component: Button,
			propAttr: {
				props: {
					name: "send",
					type: "button",
					 id:"scheduleBook",
					className: "btn submit-btn btn",
				},
				settings: [
					{
						prop: "showBtn",
						apiKey: "showBtn",
					},
					{
						prop: 'cb',
						funcName: 'submitFormDate'
					}
				],
				btnDivCls: "mt-2",
				extraProps: {
					label: "Submit Request",
					btnDivCls: "col-md-6 sendBtnSection ",
				},
			},
			
		}
	],
	validateFields: {
		ScheduleForm: ["user_name", "user_email", "user_phone","user_message"]
	},
}
export default LeadsFormConstant;