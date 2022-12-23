import React,{useState,useEffect,Component} from "react";
import CommonForms from "../../constants/Forms/CommonForms";
import utilityGlobal from "../../ReactCommon/utility/utilityGlobal";
import Autocomplete from "../../ReactCommon/Components/AutoSuggestion";
import check from "../../public/images/icon/check.png";
class ConfirmEvalueForm extends Component{
	constructor(props) {
    	super(props);
    	this.handleChange = this.handleChange.bind(this);
    	this.handleSubmit = this.handleSubmit.bind(this);
    	this.handleCheck = this.handleCheck.bind(this);
    	this.selected = this.selected.bind(this);
    	this.removeTags = this.removeTags.bind(this);
	    this.state = {
		    formConfig: CommonForms.homeValueFormConfirm, 
		    validateField: CommonForms.validateFields.homeValueFormConfirm,
	      	showBtn: false,
	      	dataFlag:false,
	      	select_user_type:[
  			{ text: "Undecided Home Seller", value: "Undecided Home Seller" },
  			{ text: "Decided Home Seller", value: "Decided Home Seller" },
  			{ text: "Future Home Seller", value: "Future Home Seller" },
  			{ text: "Commercial Buyer", value: "Commercial Buyer" },
  		],
	      	errormsg:"",
	      	errorMsg1:"",
	      	chips:[]
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
		this.setState({
			errormsg:"",
	      	nameMsg:"",
	      	emailMsg:'',
	      	phoneMsg:''
		})
		if(event.target.fullname.value==""){
			this.setState({
				errorMsg1:'Fill required Fields *'
			})
			return 0;
		} 
		if(event.target.emailId.value==""){
			this.setState({
				errorMsg1:'Fill required Fields *'
			})
			return 0;
		} 
		if(event.target.mobile.value==""){
			this.setState({
				errorMsg1:'Fill required Fields *'
			})
			return 0;
			
		}
		if(event.target.mobile.value.length<14){
				this.setState({
				errorMsg1:'Phone number should be 10 digit'
				})
				return 0;
		}
		let prop = document.getElementsByClassName('propertyType');
		let user_category = "";
		for (var i = 0; i < prop.length; i++) {

			if(prop[i].checked){
				user_category+=prop[i].value;
			}	
		}
		const extProp = {
			'fullname':event.target.fullname.value,
			'email':event.target.emailId.value,
			'mobile':event.target.mobile.value,
			'user_category':user_category,
			'user_type':this.state.chips.toString(),
		};
		this.props.handleSubmitData(extProp);
		
	}
	handleCheck(event){
		let prop = document.getElementsByClassName('propertyType');
		const user_type =[
  			{ text: "First Time Home Buyer", value: "First Time Home Buyer" },
  			{ text: "Foreclosure Home Buyer", value: "Foreclosure Home Buyer" },
  			{ text: "Affordable Home Buyer", value: "Affordable Home Buyer" },
  			{ text: "Pre-Development Home Buyer", value: "Pre-Development Home Buyer" },
  			{ text: "Pre-Construction Cond", value: "Pre-Construction Cond" },
  			{ text: "Apartment/Condo Buyer", value: "Apartment/Condo Buyer" },
  			{ text: "Luxury Home Buyer", value: "Luxury Home Buyer" },
  			{ text: "Commercial Buyer", value: "Commercial Buyer" },
  		]
  		const user_type1 =[
  			{ text: "Undecided Home Seller", value: "Undecided Home Seller" },
  			{ text: "Decided Home Seller", value: "Decided Home Seller" },
  			{ text: "Future Home Seller", value: "Future Home Seller" },
  			{ text: "Commercial Buyer", value: "Commercial Buyer" },
  		]
  		const user_type2 =[
  			{ text: "6 Months Lease", value: "6 Months Lease" },
  			{ text: "1 Year Lease", value: "1 Year Lease" },
  			{ text: "1+ Year Lease", value: "1+ Year Lease" },
  			{ text: "No-contract Lease", value: "No-contract Lease" },
  		]
  		const user_type3 =[
  			{ text: "New Agent", value: "New Agent" },
  			{ text: "Realtor", value: "Realtor" },
  			{ text: "Service Provider", value: "Service Provider" },
  			{ text: "Interested in Franchise", value: "Interested in Franchise" },
  			{ text: "Associate", value: "Associate" },
  			{ text: "Past Client", value: "Past Client" },
  			
  		]
  		let final=[];
  		let count = 0;
  		this.setState({
						errormsg:""
				})
		for (var i = 0; i < prop.length; i++) {

			if(prop[i].checked){

				if(count<2){
					if(prop[i].value=="BUYER"){
					final  = final.concat(user_type);
					}
					if(prop[i].value=="SELLER"){
						final  = final.concat(user_type1);
					}
					if(prop[i].value=="RENTER"){
						final  = final.concat(user_type2);
					}
					if(prop[i].value=="OTHER"){
						final  = final.concat(user_type3);
					}	
				}
				else
				{
					event.target.checked=false
					this.setState({
						errormsg:"Please pick a maximum of 2 categories"
					})
				}
				count++
			}	
		}
		this.setState({
				select_user_type:final
			})
	}
	
	selected(event){
		let val = event.value;
		let prev = this.state.chips;
		if (prev.includes(val)) {
			let index = prev.indexOf(val);
			prev.splice(index, 1);
		} else {
			prev.push(val)
		}

		this.setState({
			chips: prev
		});

	}
	removeTags(event){
		let val = event.target.id;
		let prev = this.state.chips;
		if (prev.includes(val)) {
			let index = prev.indexOf(val);
			prev.splice(index, 1);
		}
		this.setState({
			chips: prev
		});
	}

  	render() {
  		
  		return (
		<>
			<form className="agentSearchForm" onSubmit={this.handleSubmit}>
				<div className="mb-3">
					<h3>Comparative Market Analysis of Your Home</h3>
				    <p>Our Team will prepare a CMA report for your property and get in touch with you once it is ready.</p>
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
	            Who Do I Send The Evaluation To?
					{utilityGlobal.renderConfig(this.state.formConfig, this.state, this)}
					<span className="err-inp-msg">{this.state.errorMsg1}</span>
				<p className="pt-3 mb-0">I am a:<span className='text-danger'>*</span> (select up to 2 categories)</p>
					<div className="checkbox-label checkbox-custom-label">
						<input type="checkbox" name="user_type[]" onClick={this.handleCheck} className="checkboxState propertyType" value={'BUYER'} />
						<span className="checkbox-text">{'BUYER'}</span>
					</div>
					<div className="checkbox-label checkbox-custom-label">
						<input type="checkbox" name="user_type[]" defaultChecked onClick={this.handleCheck} className="checkboxState propertyType" value={'SELLER'} />
						<span className="checkbox-text">{'SELLER'}</span>
					</div>
					<div className="checkbox-label checkbox-custom-label">
						<input type="checkbox" name="user_type[]" onClick={this.handleCheck} className="checkboxState propertyType" value={'RENTER'} />
						<span className="checkbox-text">{'RENTER'}</span>
					</div>
					<div className="checkbox-label checkbox-custom-label">
						<input type="checkbox" name="user_type[]" onClick={this.handleCheck} className="checkboxState propertyType" value={'OTHER'} />
						<span className="checkbox-text">{'OTHER'}</span>
					</div>
					<p>{this.state.errormsg}</p>
				</div>
				<Autocomplete
	              inputProps={{
	                id: "autoSuggestion",
	                name: "user_type",
	                className: "auto form-control auto-suggestion-inp",
	                placeholder: "Select all applicable options",
	                title: "user type",
	                readOnly: true,
	                required:true
	              }}
	              allList={this.state.select_user_type}
	              autoCompleteCb={''}
	              cb={this.selected}
	              selectedText={''}
	              extraProps={{}}
	            />  
	            <div>
	            {this.state.chips.map((item)=>{
	            	return(
						<>
							<span className="tags_input">{item} <span type="button" className="remove" id={item} onClick={this.removeTags}>x</span></span>
						</>
					)
	            })
	   
	            }
	            </div>
	            <p className="notice">By submitting this form, you are providing express
	             consent to receive commercial electronic messages 
	             from www.housen.ca. You may unsubscribe at any time.</p>
	            <button type="submit" className="submitButton">See my report!</button>
			</form>
	        {this.state.dataFlag &&
	        	<Loader />
	        } 
		</>
		);
  	}
}
export default ConfirmEvalueForm;