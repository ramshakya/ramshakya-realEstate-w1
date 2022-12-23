import React,{useState,useEffect,Component} from "react";
import CommonForms from "../../constants/Forms/CommonForms";
import utilityGlobal from "../../ReactCommon/utility/utilityGlobal";
import Autocomplete from "../../ReactCommon/Components/AutoSuggestion";
import Constants from "../../constants/Global";
class ConfirmEvalueForm extends Component{
	constructor(props) {
    	super(props);
    	this.handleChange = this.handleChange.bind(this);
    	this.handleSubmit = this.handleSubmit.bind(this);
	    this.state = {
		    formConfig: CommonForms.homeValueForm, 
		    validateField: CommonForms.validateFields.homeValueForm,
	      	showBtn: true,
	      	errormsg:""
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
		this.setState({
				errormsg:""
			})

		
		const extProp = {
			'home_type':event.target.home_type.value,
			'beds':event.target.beds.value,
			'baths':event.target.baths.value,
			'basement':event.target.basement.value,
			'listing_status':"",
			'parking_type':event.target.parking_type.value,
		};
		if(event.target.home_type.value=="" || event.target.beds.value=="" || event.target.baths.value==""){
			this.setState({
				errormsg:'Please select required Fields *'
			})
		}
		else
		{
			// console.log("extProp",extProp);
			this.props.handleSubmitData(extProp);
		}
		
		
	}

  	render() {
  	// const purpose =[
  	// 	{ text: "Just curious", value: "Just curious" },
  	// 	{ text: "Need to sell ASAP", value: "Need to sell ASAP" },
  	// 	{ text: "Thinking about selling", value: "Thinking about selling" },
  	// 	{ text: "Thinking about buying", value: "Thinking about buying" },
  	// 	{ text: "Need to refinance", value: "Need to refinance" },
  	// ];
  	// const looking_sale =[
  	// 	{ text: "Within 1 month", value: "Within 1 month" },
  	// 	{ text: "Next 3 months", value: "Next 3 months" },
  	// 	{ text: "Next 6 months", value: "Next 6 months" },
  	// 	{ text: "Next 12 months", value: "Next 12 months" },
  	// 	{ text: "1 year or more", value: "Just curious" },
  	// 	{ text: "Just curious", value: "Just curious" },
  	// ]
  	const filterBeds = [
		  { value: "1", text: "1" },
		  { value: "2", text: "2" },
		  { value: "3", text: "3" },
		  { value: "4", text: "4" },
		  { value: "5+", text: "5+" },
		];
	const filterBaths = [
		  { value: "1", text: "1" },
		  { value: "2", text: "2" },
		  { value: "3", text: "3" },
		  { value: "4", text: "4" },
		  { value: "5+", text: "5+" },
		];

  	const home_type=[
  		{ text: "Detached", value: "Detached" },
  		{ text: "Semi-Detached", value: "Semi-Detached" },
  		{ text: "Townhouse", value: "Townhouse" },
  		{ text: "Condo Apartment", value: "Condo Apartment" },
  		{ text: "Farm", value: "Farm" },
  		{ text: "Cottage", value: "Cottage" },
  		{ text: "Commercial", value: "Commercial" },
  		{ text: "Land", value: "Land" },
  		{ text: "Duplex", value: "Duplex" },
  		{ text: "Triplex", value: "Triplex" },
  		{ text: "Fourplex", value: "Fourplex" },
  		{ text: "Multiplex", value: "Multiplex" },
  		{ text: "Other", value: "Other" },
  	]
  	const basement_type=[
  		{ text: "Finished", value: "Finished" },
  		{ text: "Finished w/Walkout", value: "Finished w/Walkout" },
  		{ text: "Partly Finished", value: "Partly Finished" },
  		{ text: "Unfinished", value: "Unfinished" },
  		{ text: "None", value: "None" },
  		{ text: "Crawl Space", value: "Crawl Space" },
  		{ text: "Unfinished w/Walkout", value: "Unfinished w/Walkout" },
  		{ text: "Other", value: "Other" },
  	]
  	// const lsting_status=[
  	// 	{ text: "Not Currently Liste", value: "Not Currently Liste" },
  	// 	{ text: "For Sale by Owner", value: "For Sale by Owner" },
  	// 	{ text: "Listed with Agent", value: "Listed with Agent" },
  	// ]

  	const parking_type = [
  		{ text: "1 Car Garage", value: "1 Car Garage" },
  		{ text: "1.5 Car Garage", value: "1.5 Car Garage" },
  		{ text: "2 Car Garage", value: "2 Car Garage" },
  		{ text: "3+ Car Garage", value: "3+ Car Garage" },
  		{ text: "Street Parking", value: "Street Parking" },
  		{ text: "Underground", value: "Underground" },
  		{ text: "Carport", value: "Carport" },
  		{ text: "Other", value: "Other" },
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
				<label>Type of Home <span className="text-danger">*</span> </label>
				<Autocomplete
	              inputProps={{
	                id: "autoSuggestion",
	                name: "home_type",
	                className: "auto form-control auto-suggestion-inp",
	                placeholder: "Select",
	                title: "Home type",
	                readOnly: true,
	                required:true
	              }}
	              allList={home_type}
	              autoCompleteCb={''}
	              cb={''}
	              selectedText={''}
	              
	              extraProps={{}}
	            />   
	            <label className="pt-2">Number of Bedrooms <span className="text-danger">*</span> </label>    			
				<Autocomplete
	              inputProps={{
	                id: "autoSuggestion",
	                name: "beds",
	                className: "auto form-control auto-suggestion-inp",
	                placeholder: "Select",
	                title: "Purpose for request?",
	                readOnly: true,
	                required:true
	              }}
	              allList={filterBeds}
	              autoCompleteCb={''}
	              cb={''}
	              selectedText={''}
	              
	              extraProps={{}}
	            />
	            <label className="pt-2">Number of Bathrooms <span className="text-danger">*</span> </label>    			
				<Autocomplete
	              inputProps={{
	                id: "autoSuggestion",
	                name: "baths",
	                className: "auto form-control auto-suggestion-inp",
	                placeholder: "Select",
	                title: "",
	                readOnly: true,
	                required:true
	              }}
	              allList={filterBaths}
	              autoCompleteCb={''}
	              cb={''}
	              selectedText={''}
	              
	              extraProps={{}}
	            />
	            {this.state.errormsg && <p><span className="err-inp-msg">{this.state.errormsg}</span></p>}
	            
	            <label className="pt-2">Type of Basement</label>    			
				<Autocomplete
	              inputProps={{
	                id: "autoSuggestion",
	                name: "basement",
	                className: "auto form-control auto-suggestion-inp",
	                placeholder: "Select",
	                title: "",
	                readOnly: true,
	                required:true
	              }}
	              allList={basement_type}
	              autoCompleteCb={''}
	              cb={''}
	              selectedText={''}
	              
	              extraProps={{}}
	            />
	            {/*<label className="pt-2">Listing Status</label>    			
				<Autocomplete
	              inputProps={{
	                id: "autoSuggestion",
	                name: "listing_status",
	                className: "auto form-control auto-suggestion-inp",
	                placeholder: "Select",
	                title: "",
	                readOnly: true,
	                required:true
	              }}
	              allList={lsting_status}
	              autoCompleteCb={''}
	              cb={''}
	              selectedText={''}
	              
	              extraProps={{}}
	            />*/}
	            <label className="pt-2">Type of Parking</label>    			

	            <Autocomplete
	              inputProps={{
	                id: "autoSuggestion",
	                name: "parking_type",
	                className: "auto form-control auto-suggestion-inp",
	                placeholder: "Select",
	                title: "",
	                readOnly: true,
	                required:true
	              }}
	              allList={parking_type}
	              autoCompleteCb={''}
	              cb={''}
	              selectedText={''}
	              
	              extraProps={{}}
	            />
	            <div className="mb-3">
	            	<button className="submitButton">Confirm!</button>
					{/*utilityGlobal.renderConfig(this.state.formConfig, this.state, this)*/}
				</div>
			</form>
	        
		</>
		);
  	}
}
export default ConfirmEvalueForm;