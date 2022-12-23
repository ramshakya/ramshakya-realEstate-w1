import Input from "../../ReactCommon/Components/Input";
import Button from "../../ReactCommon/Components/Button";
let LeadsFormConstant = {
	leadForm: [
		{
			component: Input,
			propAttr: {
				props: {
					type: "text",
					name: "name",
					placeholder: "Full Name",
					className: "form-control  mt-4",
					autoComplete: "off",
					id: "name",
				},
				extraProps: {
					errorMsg: "Name is required",
					validation: "required",
					label: "",
					parentcls: "mt-1 font-normal inputs-areas"
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
					placeholder: "Email Address",
					className: "form-control mt-4",
					autoComplete: "off",
					id: "email",
				},
				extraProps: {
					errorMsg: "Input valid email id",
					validation: "required,email",
					label: "",
					parentcls: "mt-1 font-normal inputs-areas"
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
					placeholder: "Mobile Number",
					className: "form-control  mt-4",
					autoComplete: "off",
					id: "phone",
				
				},
				extraProps: {
					errorMsg: "Mobile Number is required",
					validation: "required",
					label: "",
					parentcls: "mt-1 font-normal inputs-areas"
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
		},
		// {
		// 	component: Input,
		// 	propAttr: {
		// 		props: {
		// 			type: "text",
		// 			name: "message",
		// 			placeholder: "",
		// 			className: "form-control",
					
		// 			autoComplete: "off",
		// 			id: "message",

		// 		},
		// 		extraProps: {
		// 			errorMsg: "Message is required",
		// 			validation: "required",
		// 			label: "Message",
		// 			parentcls: "mt-1 font-normal inputs-areas"
		// 		},
		// 		settings: [
		// 			{
		// 				apiKey: "message",
		// 				prop: "",
		// 			},
		// 			{
		// 				funcName: "handleChange",
		// 				prop: "cb",
		// 			},
		// 		],
		// 	},
		// },
		// {
		// 	component: Button,
		// 	propAttr: {
		// 		props: {
		// 			name: "send",
		// 			type: "submit",
		// 			"data-action": "next",
		// 			btnclass: "btn sendBtn",
		// 			className: "btn sendBtn sendClass submit-btn btn",
		// 		},
		// 		settings: [
		// 			{
		// 				prop: "showBtn",
		// 				apiKey: "showBtn",
		// 			},
		// 			{
		// 				prop: 'cb',
		// 				funcName: ''
		// 			}
		// 		],
		// 		btnDivCls: "mt-2",
		// 		extraProps: {
		// 			label: "Contact Agent",
		// 			btnDivCls: "col-md-6  sendBtnSection",
		// 		},
		// 	},
			
		// }
	],
	validateFields: {
		leadForm: ["name", "email", "phone"]
	},
}
export default LeadsFormConstant;