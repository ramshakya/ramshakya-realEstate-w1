import React,{useState,useEffect,Component} from "react";
import CommonForms from "../../constants/Forms/CommonForms";
import utilityGlobal from "../../ReactCommon/utility/utilityGlobal";

class SearchStaff extends Component{
	constructor(props) {
    	super(props);
    	this.handleChange = this.handleChange.bind(this);
    	this.handleSubmit = this.handleSubmit.bind(this);
	    this.state = {
		    formConfig: CommonForms.Staff, 
		    validateField: CommonForms.validateFields.Staff,
	      	showBtn: false,
	      	dataFlag:false
	    };
  	}
  	handleChange(e) {
	    let data = {};
	    
	    data[e.target.name] = e.target.value;
	    this.setState(data, () => {
	      this.setState({
	        showBtn: utilityGlobal.validateData(this.state.validateField, this).status,
	      });
	    });
	}
	handleSubmit() {
		let {validateField} = this.state;
		this.props.handleSubmitData(this.state[validateField[0]])
	}

  	render() {

  		return (
		<>
			<div className="agentSearchForm">
				<div className="mb-3">
					
					{utilityGlobal.renderConfig(this.state.formConfig, this.state, this)}
				</div>
					       			
				
			</div>
	        {this.state.dataFlag &&
	        	<Loader />
	        } 
		</>
		);
  	}
}
export default SearchStaff;