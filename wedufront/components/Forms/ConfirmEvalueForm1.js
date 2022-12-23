import React,{useState,useEffect,Component} from "react";
import CommonForms from "../../constants/Forms/CommonForms";
import utilityGlobal from "../../ReactCommon/utility/utilityGlobal";
import Autocomplete from "../../ReactCommon/Components/AutoSuggestion";
import check from "../../public/images/icons/check.png";
class ConfirmEvalueForm extends Component{
	constructor(props) {
    	super(props);
    	this.handleChange = this.handleChange.bind(this);
    	this.handleSubmit = this.handleSubmit.bind(this);
	    this.state = {
		    formConfig: CommonForms.homeValueFormConfirm, 
		    validateField: CommonForms.validateFields.homeValueFormConfirm,
	      	showBtn: false,
	      	dataFlag:false
	    };
  	}
  	handleChange(e) {
	    let data = {};
		let inpval=e.target.value;
	    if(e.target.name==="mobile"){
	    	const { value, maxLength } = e.target;
			var x = e.target.value.replace(/\D/g, '').match(/(\d{0,3})(\d{0,3})(\d{0,4})/);
			inpval = !x[2] ? x[1] : '(' + x[1] + ') ' + x[2] + (x[3] ? '-' + x[3] : '');
			// if (String(value).length >= maxLength) {
			//       e.preventDefault();
			//       return;
			// }
	    }
	    data[e.target.name] = inpval;
	    this.setState(data, () => {
	      this.setState({
	        showBtn: utilityGlobal.validateData(this.state.validateField, this).status,
	      });
	    });
	}
	handleSubmit(event) {
		event.preventDefault();
		const extProp = {
			'fullname':event.target.fullname.value,
			'email':event.target.emailId.value,
			'mobile':event.target.mobile.value,
		};
		this.props.handleSubmitData(extProp);
		
	}

  	render() {
  
  		return (
		<>
			<form className="agentSearchForm" onSubmit={this.handleSubmit}>
				<div className="mb-3">
					<h3>Let's keep in touch</h3>
				    <p>We’ll email you monthly market information so that
				     you can see how things are changing.</p>
				     <ul className="steps">
						<li className="check_step">	
		                    <img src={check.src} width={40}/>
		                </li>
		                <li>	
		                    2
		                </li>
					</ul>
				</div>	       			
				
	            <div className="mb-3">
					{utilityGlobal.renderConfig(this.state.formConfig, this.state, this)}
				</div>
			</form>
	        {this.state.dataFlag &&
	        	<Loader />
	        } 
		</>
		);
  	}
}
export default ConfirmEvalueForm;