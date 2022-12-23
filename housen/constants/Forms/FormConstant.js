import Input from "../../ReactCommon/Components/Input";
import CheckBox from "../../ReactCommon/Components/CheckBox";
import Button from "../../ReactCommon/Components/Button1";
let Forms = {
	loginForm: [
		
		{
	    	component: Input,
	      	propAttr: {
	        	props: {
	          		type: "text",
	          		name: "email",
	          		placeholder: "",
	          		className: "form-control",
	          		autoComplete: "off",
	          		id: "login_email",
	        	},
	        	extraProps: {
	          		errorMsg: "Input valid email id",
	          		validation: "required,email",
	          		label: "Email",
	          		parentcls:"mt-3 font-normal"
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
	          		name: "login_password",
	          		placeholder: "",
	          		className: "form-control",
	          		autoComplete: "off",
	          		id: "login_password",
	        	},
	        	extraProps: {
	          		errorMsg: "Password is required",
	          		validation: "required",
	          		label: "Password",
	          		parentcls:"mt-2 font-normal"
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
	    },
	  
	    {
		    component: "a",
		    propAttr: {
		      		href: "javascript:void(0)",
		      		className: "forgot-password-link font-normal",
		      		name: "forgetPassword",
		      		settings: [{ prop: "onClick", funcName: "toggleForm" }],
		    },
		    children: "Forget Password?",
	    },
	    {
		    propAttr: {
			    props: {
			          name: "loginBtn",
			          type: "submit",
			          "data-action": "next",
			          btnclass:"submitButton signInBtn",
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
		          label: "Sign-in",
		        },
		    },
		      component: Button,
	    },
	    
	    
	   
		// {
		// 	component: "p",
		// 	propAttr: {
		// 	  className: "text-center mt-1 font-normal inline-block mt-4 signupBackground",
		// 	  name: "signUpForm"
		// 	},
		// 	children: "",
		// },
	    {
		    component: "a",
		    propAttr: {
		      		href: "javascript:void(0)",
		      		className:"signup-link signupBackground linkForSingup",
		      		name: "SignUp",
					id:"newAccount",
		      		settings: [{ prop: "onClick", funcName: "toggleForm" }],
		    },
		    children: "New to Housen? Create an account",
	    },
	    
	],
	SignUp: [
		
		{
	    	component: Input,
	      	propAttr: {
	        	props: {
	          		type: "text",
	          		name: "signupEmail",
	          		placeholder: "",
	          		className: "form-control",
	          		autoComplete: "off",
	          		id: "signup_email",
	        	},
	        	extraProps: {
	          		errorMsg: "Enter valid email id",
	          		validation: "required,email",
	          		label: "Email address",
	          		parentcls:"mt-3 font-normal"
	        	},
	        	settings: [
		          	{
		            	apiKey: "signupEmail",
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
			          name: "nextBtn",
			          type: "submit",
			          "data-action": "next",
			          btnclass:"submitButton signInBtn"
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
		          label: "Next",
		        },
		    },
		      component: Button,
	    },
	 //    {
		// 	component: "p",
		// 	propAttr: {
		// 	  className: "text-center mt-3 inline-block mt-0 font-normal",
		// 	},
		// 	children: "Already registered? ",
		// },
		{
			component: "a",
			propAttr: {
			  href: "javascript:void(0)",
			  className: "linkForSingup",
			  name: "loginForm",
			  settings: [{ prop: "onClick", funcName: "toggleForm" }],
			},
			children: "Already registered? Please sign-in",
		},
	    {
			component: "p",
			propAttr: {
			  className: "text-center mt-3 terms-use",
			},
			// children: "By registering, you agree to our terms of use and that real estate professionals and lenders may call/text you about your inquiry, which may involve use of automated means and prerecorded/artificial voices. You don’t need to consent as a condition of buying any property, goods or services. Messages/data rates may apply.",
			children: "",
		},
		
		
	],
	emailSuccess:[
		{
			component: "h5",
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
	          		name: "signupEmail",
	          		placeholder: "",
	          		className: "form-control",
	          		autoComplete: "off",
	          		id: "signupEmail",
	          		readOnly: true
	        	},
	        	extraProps: {
	          		errorMsg: "Email is required",
	          		validation: "required",
	          		label: "Email address",
	          		parentcls:"mt-3 font-normal"
	        	},
	        	settings: [
		          	{
		            	apiKey: "signupEmail",
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
	          		placeholder: "",
	          		className: "form-control",
	          		autoComplete: "off",
	          		id: "fullName",
	        	},
	        	extraProps: {
	          		errorMsg: "Name is required",
	          		validation: "required,alpha",
	          		label: "Full Name",
	          		parentcls:"mt-3 font-normal"
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
	    },
	    // {
	    // 	component: Input,
	    //   	propAttr: {
	    //     	props: {
	    //       		type: "text",
	    //       		name: "lastName",
	    //       		placeholder: "",
	    //       		className: "form-control",
	    //       		autoComplete: "off",
	    //       		id: "lastName",
	    //     	},
	    //     	extraProps: {
	    //       		errorMsg: "Last name is required",
	    //       		validation: "alpha",
	    //       		label: "Last Name",
	    //       		parentcls:"mt-3 font-normal"
	    //     	},
	    //     	settings: [
		//           	{
		//             	apiKey: "lastName",
		//             	prop: "selectedValue",
		//           	},
		//           	{
		//             	funcName: "handleChange",
		//             	prop: "cb",
		//           	},
	    //     	],
	    //   	},
	    // },
	    {
	    	component: Input,
	      	propAttr: {
	        	props: {
	          		type: "text",
	          		name: "mobile",
	          		placeholder: "",
	          		className: "form-control",
	          		autoComplete: "off",
	          		id: "mobile",
	          		// maxLength:"13"
	        	},
	        	extraProps: {
	          		errorMsg: "",
	          		validation: "",
	          		label: "Mobile number",
	          		parentcls:"mt-3 font-normal"
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
	    },
	    {
	    	component: Input,
	      	propAttr: {
	        	props: {
	          		type: "password",
	          		name: "password",
	          		placeholder: "",
	          		className: "form-control",
	          		autoComplete: "off",
	          		id: "password",
	        	},
	        	extraProps: {
	          		errorMsg: "Password is required",
	          		validation: "required",
	          		label: "Password",
	          		parentcls:"mt-3 font-normal"
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
	    },
	    {
	    	component: Input,
	      	propAttr: {
	        	props: {
	          		type: "password",
	          		name: "confirmationPassword",
	          		placeholder: "",
	          		className: "form-control",
	          		autoComplete: "off",
	          		id: "confirmationPassword",
	        	},
	        	extraProps: {
	          		errorMsg: "",
	          		validation: "required,confirm",
	          		label: "Confirm password",
	          		parentcls:"mt-3 font-normal",
	          		msgId:"ConfrimMessage"
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
	    },
	    {
		    propAttr: {
			    props: {
			          name: "signupBtn",
			          type: "submit",
			          "data-action": "next",
			          btnclass:"submitButton theme-bg-color"
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
		          label: "Register",
		        },
		    },
		      component: Button,
	    },
	 //    {
		// 	component: "p",
		// 	propAttr: {
		// 	  className: "text-center mt-3 inline-block mt-0",
		// 	  name: "signUpForm"
		// 	},
		// 	children: " ",
		// },
		{
			component: "a",
			propAttr: {
			  href: "javascript:void(0)",
			  className: "linkForSingup signup-link",
			  name: "loginForm",
			  settings: [{ prop: "onClick", funcName: "toggleForm" }],
			},
			children: "Already a member? Login here",
		},
	],
	forgetPassword: [
		{
			component: "h5",
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
	          name: "forgot_email",
	          placeholder: "",
	          className: "form-control",
	          autoComplete: "off",
	          id: "forgot_email",
	        },
	        extraProps: {
	          errorMsg: "email is required",
	          validation: "required,email",
	          label: "Email",
	          parentcls:"mt-3 font-normal"
	        },
	        settings: [
	          {
	            apiKey: "forgot_email",
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
	          name: "forgotBtn",
	          type: "submit",
	          "data-action": "next",
	           btnclass:"submitButton"
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
	        btnDivCls: "my-0",
	        extraProps: {
	          label: "Submit",
	        },
	      },
	      component: Button,
	    },
	 //    {
		// 	component: "p",
		// 	propAttr: {
		// 	  className: "text-center mt-3 inline-block1 font-normal",
		// 	},
		// 	children: "Back to ",
		// },
    	{
			component: "a",
			propAttr: {
			  href: "javascript:void(0)",
			  className: "linkForSingup signup-link",
			  name: "loginForm",
			  settings: [{ prop: "onClick", funcName: "toggleForm" }],
			},
			children: "Back to Sign-in",
		},
  	],

	  confirmMail: [
		{
			component:"h5",
			propAttr:{
				className:""
			},
			children:"Confirm Your Email"
		},
		{
			component:"span",
			propAttr:{
				className:"confirm-code"
			},
			children:"Code is sent to your email address."
		},
		{
	    	component: Input,
	      	propAttr: {
	        	props: {
	          		type: "number",
	          		name: "confirmCode",
	          		placeholder: "Enter code here",
	          		className: "form-control",
	          		autoComplete: "off",
	          		id: "confirm_code",
	        	},
	        	extraProps: {
	          		errorMsg: "Enter code",
	          		validation: "required,number",
	          		label: "Code",
	          		parentcls:"mt-3 font-normal"
	        	},
	        	settings: [
		          	{
		            	apiKey: "confirmCode",
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
			          name: "submitCode",
			          type: "submit",
			          "data-action": "next",
			          btnclass:"submitButton"
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
		          label: "Next",
		        },
		    },
		      component: Button,
	    },
	    {
			component: "p",
			propAttr: {
			  className: "text-center mt-3 inline-block mt-0 font-normal",
			},
			children: "Already registered? ",
		},
		{
			component: "a",
			propAttr: {
			  href: "javascript:void(0)",
			  className: "signup-link",
			  name: "loginForm",
			  settings: [{ prop: "onClick", funcName: "toggleForm" }],
			},
			children: "Please sign-in",
		},
	    {
			component: "p",
			propAttr: {
			  className: "text-center mt-3 terms-use",
			},
			// children: "By registering, you agree to our terms of use and that real estate professionals and lenders may call/text you about your inquiry, which may involve use of automated means and prerecorded/artificial voices. You don’t need to consent as a condition of buying any property, goods or services. Messages/data rates may apply.",
			children: "",
		},
	],

	validateFields: {
	    loginForm: ["email", "login_password"],
	    SignUp: ["signupEmail"],
	    confirmMail: ["confirmCode"],
	    emailSuccess: ['signupEmail','fullName','password','confirmationPassword','mobile'],
	    forgetPassword: ["forgot_email"],
	  },
}
export default Forms;