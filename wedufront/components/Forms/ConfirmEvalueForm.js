import React,{useState,useEffect,Component} from "react";
import CommonForms from "../../constants/Forms/CommonForms";
import utilityGlobal from "../../ReactCommon/utility/utilityGlobal";
import Autocomplete from "../../ReactCommon/Components/AutoSuggestion";

class ConfirmEvalueForm extends Component{
	constructor(props) {
    	super(props);
    	this.handleChange = this.handleChange.bind(this);
    	this.handleSubmit = this.handleSubmit.bind(this);
	    this.state = {
		    formConfig: CommonForms.homeValueForm, 
		    validateField: CommonForms.validateFields.homeValueForm,
	      	showBtn: true
	    };
  	}
  	handleChange(e) {
	    let data = {};
	    
	    // data[e.target.name] = e.target.value;
	    // this.setState(data, () => {
	    //   this.setState({
	    //     showBtn: utilityGlobal.validateData(this.state.validateField, this).status,
	    //   });
	    // });
	}
	handleSubmit(event) {
		event.preventDefault();
		const extProp = {
			'buildyear':event.target.buildyear.value,
			'sqft':event.target.sqft.value,
			'purpose':event.target.purpose.value,
			'sellTime':event.target.sellTime.value
		};
		
		this.props.handleSubmitData(extProp);
		
	}

  	render() {
  	const purpose =[
  		{ text: "Just curious", value: "Just curious" },
  		{ text: "Need to sell ASAP", value: "Need to sell ASAP" },
  		{ text: "Thinking about selling", value: "Thinking about selling" },
  		{ text: "Thinking about buying", value: "Thinking about buying" },
  		{ text: "Need to refinance", value: "Need to refinance" },
  	];
  	const looking_sale =[
  		{ text: "Within 1 month", value: "Within 1 month" },
  		{ text: "Next 3 months", value: "Next 3 months" },
  		{ text: "Next 6 months", value: "Next 6 months" },
  		{ text: "Next 12 months", value: "Next 12 months" },
  		{ text: "1 year or more", value: "Just curious" },
  		{ text: "Just curious", value: "Just curious" },
  	]
  		return (
		<>
			<form className="agentSearchForm" onSubmit={this.handleSubmit}>
				<div className="mb-3">
					<h3>Please confirm our information is correct</h3>
				            	<p>This will ensure we're using the correct comparable data.</p>
					<ul className="steps">
						<li>	
		                    1
		                </li>
		                <li>	
		                    2
		                </li>
					</ul>
				</div>	       			
				<Autocomplete
	              inputProps={{
	                id: "autoSuggestion",
	                name: "purpose",
	                className: "auto form-control auto-suggestion-inp",
	                placeholder: "Purpose for request?",
	                title: "Purpose for request?",
	                readOnly: true,
	                required:true
	              }}
	              allList={purpose}
	              autoCompleteCb={''}
	              cb={''}
	              selectedText={''}
	              
	              extraProps={{}}
	            />
	            <Autocomplete
	              inputProps={{
	                id: "autoSuggestion",
	                name: "sellTime",
	                className: "auto form-control auto-suggestion-inp mt-4",
	                placeholder: "Are you looking to sell? ",
	                title: "Are you looking to sell?",
	                readOnly: true,
	                required:true
	              }}
	              allList={looking_sale}
	              autoCompleteCb={''}
	              cb={''}
	              selectedText={''}
	              
	              extraProps={{}}
	            />
	            <div className="mb-3">
					{utilityGlobal.renderConfig(this.state.formConfig, this.state, this)}
				</div>
			</form>
	        
		</>
		);
  	}
}
export default ConfirmEvalueForm;