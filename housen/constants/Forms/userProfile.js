import Input from "../../ReactCommon/Components/Input";
import CheckBox from "../../ReactCommon/Components/CheckBox";
import Button from "../../ReactCommon/Components/Button1";

let Forms = {
  updateProfile:[
	{
		component: "h4",
		propAttr: {
		  className: "head-login form_title",
		},
		children:'Basic Details'
	  },
	  {
		component: Input,
		propAttr: {
		  props: {
			type: "text",
			name: "email",
			placeholder: "",
			className: "form-control custom-input readonlyInput",
			autoComplete: "off",
			id: "login_email",
			readOnly: true
		  },
		  extraProps: {
			errorMsg: "",
			validation: "",
			parentcls: "col-lg-6 col-md-6 mt-4 font-normal",
			label: "Email address"
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
			name: "fullName",
			placeholder: "Enter full name",
			className: "form-control custom-input ",
			autoComplete: "off",
			id: "firstName",
		  },
		  extraProps: {
			errorMsg: "Full name is required",
			validation: "required",
			parentcls: "col-lg-6 col-md-6 mt-4 font-normal",
			label: "Full name",

		  },
		  settings: [
			{
			  apiKey: "fullName",
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
		component: Input,
		propAttr: {
		  props: {
			type: "text",
			name: "mobile",
			placeholder: "Enter Mobile",
			className: "form-control custom-input",
			autoComplete: "off",
			id: "mobile",
			maxLength:"15"
		  },
		  extraProps: {
			errorMsg: "Mobile number should be 10 digits",
			validation: "required",
			parentcls: "col-lg-6 col-md-6 mt-4 font-normal",
			label: "Mobile number"
		  },
		  settings: [
			{
			  apiKey: "mobile",
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
			type: "submit",
			"data-action": "submit",
			btnclass:"submitButton mt-3 pt-1 pb-2",
		  },
		  settings: [
			{
			  prop: "showBtn",
			  apiKey: "showBtn",
			},
			{
				prop:'cb',
				funcName:'updateProfile'
			}
		  ],
		  
		  extraProps: {
			label: "Update",
			style: {'width':'150px'},
			btnDivCls: "mt-2 col-md-6 col-lg-6 pt-4",
		  },
		},
		component: Button,

	  },
	  {
	  	component:"hr",
	  	propAttr:{
	  		className:"mt-3"
	  	}
	  }

  ],
  updatePassword:[
  	{
		component: "h4",
		propAttr: {
		  className: "head-login form_title mb-4",
		},
		children:'Update Password'
	  },
	  {
		component: Input,
		propAttr: {
		  props: {
			type: "password",
			name: "password",
			placeholder: "",
			className: "form-control custom-input",
			autoComplete: "off",
			id: "password",
		  },
		  extraProps: {
			errorMsg: "Password is required",
			validation: "required",
			parentcls: "col-lg-6 col-md-6 font-normal",
			label: "New password"
		  },
		  settings: [
			{
			  apiKey: "password",
			  prop: "",
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
		component: Input,
		propAttr: {
		  props: {
			type: "password",
			name: "confirmationPassword",
			placeholder: "",
			className: "form-control custom-input",
			autoComplete: "off",
			id: "confirmationPassword",
		  },
		  extraProps: {
			errorMsg: "",
			validation: "required,confirm",
			parentcls: "col-lg-6 col-md-6 font-normal",
			label: "Confirm password",
			msgId:"ConfrimMessage"
		  },
		  settings: [
			{
			  apiKey: "confirmationPassword",
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
		propAttr: {
		  props: {
			name: "submitBtn1",
			type: "submit",
			"data-action": "submit",
			btnclass:"submitButton mt-3 pt-1 pb-2 width-50",
		  },
		  settings: [
			{
			  prop: "showBtn",
			  apiKey: "showBtn",
			},
			{
				prop:'cb',
				funcName:'updateProfile'
			}
		  ],
		  
		  extraProps: {
			label: "Update Password",
			btnDivCls: "mt-2 col-md-6 col-lg-6",
		  },
		},
		component: Button,

	  
	}
	],

  validateFields: {
		updateProfile:['fullName','mobile'],
		updatePassword:['password','confirmationPassword']
  },
};
export default Forms;
