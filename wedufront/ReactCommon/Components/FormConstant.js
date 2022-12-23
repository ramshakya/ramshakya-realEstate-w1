import Input from "../ReactCommon/Components/Input";
import CheckBox from "../ReactCommon/Components/CheckBox";
import Button from "../ReactCommon/Components/Button";
let Forms = {
  loginForm: [
    {
      component: "h4",
      propAttr: {
        className: "head-login",
      },
	  children:'User Login'
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
      component: CheckBox,
      propAttr: {
        checkBoxProps: {
          className: "form-check-input",
          type: "checkbox",
          id: "materialChecked2",
        },
        extraProps: {
          parentcls: "form-check",
          checkFor: "materialChecked2",
          labelClassName: "form-check-label",
          label: "Remember me",
        },
      },
    },
    {
      component: "a",
      propAttr: {
        href: "javascript:void(0)",
        className: "forgot-password-link",
        name: "forgetPassword",
        settings: [{ prop: "onClick", funcName: "toggleForm" }],
      },
      children: "Forget Password?",
    },
    {
      propAttr: {
        props: {
          name: "submitBtn",
          type: "button",
          "data-action": "next",
        },
        settings: [
          {
            prop: "showBtn",
            apiKey: "showBtn",
          },
          {
						prop:'cb',
						funcName:'handleLogin'
					}
        ],
        btnDivCls: "mt-2",
        extraProps: {
          label: "Login <i class='icon-next'></i>",
        },
      },
      component: Button,
    },
	{
		component: "a",
		propAttr: {
		  href: "javascript:void(0)",
		  className: "signup-link",
		  name: "signUpForm",
		  settings: [{ prop: "onClick", funcName: "toggleForm" }],
		},
		children: "Sign Up",
	},
  ],

  signUpForm:[
	{
		component: "h4",
		propAttr: {
		  className: "head-login",
		},
		children:'Sign Up'
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
	  {
		propAttr: {
		  props: {
			name: "submitBtn",
			type: "button",
			"data-action": "next",
		  },
		  settings: [
			{
			  prop: "showBtn",
			  apiKey: "showBtn",
			},
			{
				prop:'cb',
				funcName:'handleEmail'
			}
		  ],
		  btnDivCls: "mt-2",
		  extraProps: {
			label: "Next <i class='icon-next'></i>",
		  },
		},
		component: Button,
	  },
  ],

  emailSuccess:[
	{
		component: "h4",
		propAttr: {
		  className: "head-login",
		},
		children:'You are almost done'
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
			readOnly: true
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
			type: "text",
			name: "firstName",
			placeholder: "Enter first name",
			className: "form-control my-0",
			autocomplete: "off",
			id: "firstName",
		  },
		  extraProps: {
			errorMsg: "First name is required",
			validation: "required,alpha",
		  },
		  settings: [
			{
			  apiKey: "firstName",
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
			name: "lastName",
			placeholder: "Enter last name",
			className: "form-control my-0",
			autocomplete: "off",
			id: "lastName",
		  },
		  extraProps: {
			errorMsg: "last name  is required",
			validation: "required,alpha",
		  },
		  settings: [
			{
			  apiKey: "lastName",
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
			className: "form-control my-0",
			autocomplete: "off",
			id: "mobile",
		  },
		  extraProps: {
			errorMsg: "mobile  is required",
			validation: "required,number",
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
		component: Input,
		propAttr: {
		  props: {
			type: "password",
			name: "confirmationPassword",
			placeholder: "Enter confirm Password",
			className: "form-control my-0",
			autocomplete: "off",
			id: "confirmationPassword",
		  },
		  extraProps: {
			errorMsg: "Confirm password is required",
			validation: "required",
		  },
		  settings: [
			{
			  apiKey: "confirmationPassword",
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
		  },
		  settings: [
			{
			  prop: "showBtn",
			  apiKey: "showBtn",
			},
			{
				prop:'cb',
				funcName:'handleSubmit'
			}
		  ],
		  btnDivCls: "mt-2",
		  extraProps: {
			label: "Next <i class='icon-next'></i>",
		  },
		},
		component: Button,
	  },
  ],
  forgetPassword: [
	{
		component: "h4",
		propAttr: {
		  className: "head-login",
		},
		children:'Forget Password'
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
    {
      propAttr: {
        props: {
          name: "submitBtn",
          type: "button",
          "data-action": "next",
        },
        settings: [
          {
            prop: "showBtn",
            apiKey: "showBtn",
          },
         {
						prop:'cb',
						funcName:'handleForgot'
					}
        ],
        btnDivCls: "my-0",
        extraProps: {
          label: "Submit <i class='icon-next'></i>",
        },
      },
      component: Button,
    },
  ],
  updateProfile:[
	{
		component: "h4",
		propAttr: {
		  className: "head-login",
		},
		children:'You are almost done'
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
			readOnly: true
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
			type: "text",
			name: "firstName",
			placeholder: "Enter first name",
			className: "form-control my-0",
			autocomplete: "off",
			id: "firstName",
		  },
		  extraProps: {
			errorMsg: "First name is required",
			validation: "required,alpha",
		  },
		  settings: [
			{
			  apiKey: "firstName",
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
			name: "lastName",
			placeholder: "Enter last name",
			className: "form-control my-0",
			autocomplete: "off",
			id: "lastName",
		  },
		  extraProps: {
			errorMsg: "last name  is required",
			validation: "required,alpha",
		  },
		  settings: [
			{
			  apiKey: "lastName",
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
			className: "form-control my-0",
			autocomplete: "off",
			id: "mobile",
		  },
		  extraProps: {
			errorMsg: "mobile  is required",
			validation: "required,number",
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
		component: Input,
		propAttr: {
		  props: {
			type: "password",
			name: "confirmationPassword",
			placeholder: "Enter confirm Password",
			className: "form-control my-0",
			autocomplete: "off",
			id: "confirmationPassword",
		  },
		  extraProps: {
			errorMsg: "Confirm password is required",
			validation: "required",
		  },
		  settings: [
			{
			  apiKey: "confirmationPassword",
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
		  },
		  settings: [
			{
			  prop: "showBtn",
			  apiKey: "showBtn",
			},
			{
				prop:'cb',
				funcName:'handleSubmit'
			}
		  ],
		  btnDivCls: "mt-2",
		  extraProps: {
			label: "Next <i class='icon-next'></i>",
		  },
		},
		component: Button,
	  },
  ],
  validateFields: {
    loginForm: ["email", "password"],
    forgetPassword: ["email"],
		signUpForm:['email'],
		emailSuccess:['email','firstName','lastName','mobile','password','confirmationPassword']
  },
};
export default Forms;
