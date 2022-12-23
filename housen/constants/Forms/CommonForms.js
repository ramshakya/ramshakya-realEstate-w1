import Input from "../../ReactCommon/Components/Input";
import CheckBox from "../../ReactCommon/Components/CheckBox";
import Button from "../../ReactCommon/Components/Button1";
let CommonForms = {
	Staff: [
		{
	    	component: Input,
	      	propAttr: {
	        	props: {
	          		type: "text",
	          		name: "agentName",
	          		placeholder: "",
	          		className: "form-control",
	          		autoComplete: "off",
	          		id: "agentName",
	        	},
	        	extraProps: {
	          		errorMsg: "Input field is required",
	          		validation: "required",
	          		label: "Find An Agent",
	          		parentcls:"mt-3 font-normal"
	        	},
	        	settings: [
		          	{
		            	apiKey: "agentName",
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
	    	component: Button,
		    propAttr: {
			    props: {
			          name: "staff",
			          type: "button",
			          "data-action": "next",
			          btnclass:"submitButton",
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
		          label: "Search",
		        },
		    },
		      
	    },
	],
	homeValueForm:[
		{
	    	component: Input,
	      	propAttr: {
	        	props: {
	          		type: "number",
	          		name: "buildyear",
	          		placeholder: "",
	          		className: "form-control",
	          		autoComplete: "off",
	          		id: "buildyear",
	          		required:true
	        	},
	        	extraProps: {
	          		errorMsg: "Field is required",
	          		validation: "",
	          		label: "Year Build",
	          		parentcls:"mt-3 font-normal"
	        	},
	        	settings: [
		          	{
		            	apiKey: "buildyear",
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
	          		type: "number",
	          		name: "sqft",
	          		placeholder: "",
	          		className: "form-control",
	          		autoComplete: "off",
	          		id: "sqft",
	          		required:true
	        	},
	        	extraProps: {
	          		errorMsg: "Field is required",
	          		validation: "",
	          		label: "Square Feet",
	          		parentcls:"mt-3 font-normal"
	        	},
	        	settings: [
		          	{
		            	apiKey: "sqft",
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
	    	component: Button,
		    propAttr: {
			    props: {
			          name: "loginBtn",
			          type: "submit",
			          "data-action": "next",
			          btnclass:"submitButton",
			    },
		        settings: [
			        {
			            prop: "showBtn",
			            apiKey: "showBtn",
			        },
		          	{
						prop:'cb',
						funcName:''
					}
		        ],
		        btnDivCls: "mt-2",
		        extraProps: {
		          label: "Confirm!",
		        },
		    },
		      
	    },

	    
	],
	homeValueFormConfirm:[
		{
	    	component: Input,
	      	propAttr: {
	        	props: {
	          		type: "text",
	          		name: "fullname",
	          		placeholder: "",
	          		className: "form-control",
	          		autoComplete: "off",
	          		id: "fullname",
	        	},
	        	extraProps: {
	          		errorMsg: "Field is required",
	          		validation: "required",
	          		label: "Full name <span class='text-danger'>*</span>",
	          		parentcls:"mt-3 font-normal"
	        	},
	        	settings: [
		          	{
		            	apiKey: "fullname",
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
	          		type: "email",
	          		name: "emailId",
	          		placeholder: "",
	          		className: "form-control",
	          		autoComplete: "off",
	          		id: "emailId",
	        	},
	        	extraProps: {
	          		errorMsg: "Enter valid email id",
	          		validation: "required,email",
	          		label: "Email Address <span class='text-danger'>*</span>",
	          		parentcls:"mt-3 font-normal"
	        	},
	        	settings: [
		          	{
		            	apiKey: "emailId",
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
	          		name: "mobile",
	          		placeholder: "",
	          		className: "form-control",
	          		autoComplete: "off",
	          		id: "mobile",
	          		
	        	},
	        	extraProps: {
	          		errorMsg: "Phone will be 10-12 digit",
	          		validation: "required",
	          		label: "Phone number <span class='text-danger'>*</span>",
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
	    // {
	    // 	component: Button,
		   //  propAttr: {
			  //   props: {
			  //         name: "loginBtn",
			  //         type: "submit",
			  //         "data-action": "next",
			  //         btnclass:"submitButton",
			  //   },
		   //      settings: [
			  //       {
			  //           prop: "showBtn",
			  //           apiKey: "showBtn",
			  //       },
		   //        	{
					// 	prop:'cb',
					// 	funcName:''
					// }
		   //      ],
		   //      btnDivCls: "mt-2",
		   //      extraProps: {
		   //        label: "See my report!",
		   //      },
		   //  },
		      
	    // },
	],
	validateFields: {
	    Staff:['agentName'],
	    homeValueForm:['buildyear','sqft'],
	    homeValueFormConfirm:['fullname','emailId','mobile']
	  },
}
export default CommonForms;