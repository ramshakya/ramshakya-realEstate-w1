import Autocomplete from '../../ReactCommon/Components/AutoSuggestion'
import React, {useEffect,useState} from "react";
import Link from "next/link";
import API from "../../ReactCommon/utility/api";

// import Card from "../components/Cards/PreConstrcutionCard"
import Constants from "../../constants/Global";
import ReactCarousel from './../../ReactCommon/Components/ReactCarousel';
import Map from "../../components/Map/GoogleMap";
import { withRouter, useRouter } from "next/router";
import { ToastContainer, toast } from 'react-toastify';
import Loader from '../../components/loader/loader';
const  PreConRegister =(prosp)=> {
	const [dataFlag, setDataFlag] = useState(false);
	  const [phone,setPhone] = useState('');
	  const [msg,setmsg] = useState(false);
	  const handleSubmit = (event) => {
	      setDataFlag(true);
	      event.preventDefault();
	      const name = event.target.firstName;
	      const last = event.target.lastname;
	      const email = event.target.email;
	      const mobile = event.target.mobile;
	      const message = event.target.message;
	      const current_url = window.location.href;
	      const current_page = window.location.pathname;
	      const beds = event.target.Beds;
	      const realtor = event.target.Realtor;
	      const fullmsg = message.value+'<br /> Beds: '+beds.value+' <br/> Realtor : '+realtor.value;
	      const fullname = name.value+' '+last.value;
	      const body = JSON.stringify({Name:fullname,Email:email.value,Message:fullmsg,Phone:mobile.value,Url:current_url,Page:current_page,AgentId:Constants.agentId});
	    
	      API.jsonApiCall((Constants.base_url+'api/v1/services/global/ContactEnquiry'), body, "POST", null, {
	            "Content-Type": "application/json",
	        }).then((res) => {
	          setDataFlag(false);
	          if(res.success)
	          {
	            toast.success("Register Successfully");
	            setmsg(true);
	            document.getElementsByClassName('PreRegisterForm')[0].reset();
	            document.getElementsByClassName('PreRegisterForm')[1].reset();
	            document.getElementById('phone').value="";
	            setPhone(" ");
	          }
	          else
	          {
	            // toast.error(res.errors);
	          }
	       });
	    }
	   function checkMobile(e){
	   	// console.log(e.target.value.length);
	   		if(e.target.value.length>10){

	   			return false;
	   		} 
	   		else
	   		{
	   			setPhone(e.target.value);
	   		}
	   }
	   function onChange(e) {
	   	if(e.target.value.length>14){
	   		return false;
	   	}
	   	let inpval =e.target.value;
	   	var x = e.target.value.replace(/\D/g, '').match(/(\d{0,3})(\d{0,3})(\d{0,4})/);
			inpval = !x[2] ? x[1] : '(' + x[1] + ') ' + x[2] + (x[3] ? '-' + x[3] : '');
	    setPhone(inpval);
	  }
	const Realtor = [
		  { text: "Yes", value: "Yes" },
		  { value: "No", text: "No" },
		 ]
		 const beds = [
			  { value: "--", text: "--" },
			  { value: "Studio", text: "Studio" },
			  { value: "1 Bedroom", text: "1 Bedroom" },
			  { value: "1 Bedroom + Den", text: "1 Bedroom + Den" },
			  { value: "2 Bedroom", text: "2 Bedroom" },
			  { value: "2 Bedroom + Den", text: "2 Bedroom + Den" },
			  { value: "3 Bedroom", text: "3 Bedroom" },
			  { value: "Multiple Units", text: "Multiple Units" },
			];
	
		return (
			<>
				<form onSubmit={handleSubmit} id="PreRegisterForm" className="row PreRegisterForm">
						{/*utilityGlobal.renderConfig(this.state.formConfig, this.state, this)*/}
											<div className="col-md-6 pb-3">
												<small>First Name</small>
										      <input type="text" className="form-control" id="firstName" required="true" placeholder="" name="firstName" />
										    </div>
										    <div className="col-md-6 pb-3">
												<small>Last Name</small>
										      <input type="text" className="form-control" placeholder="" name="lastname" />

										    </div>
										    <div className="col-md-6 pb-3">
												<small>Email</small>

										      <input type="email" className="form-control" id="email" placeholder="" required="true" name="email" />
										    </div>
										    <div className="col-md-6 pb-3">
												<small>Phone</small>
										      <input type="text" className="form-control" placeholder="" required="true" id="phone" name="mobile" onChange={onChange} value={phone} />

										    </div>
						<div className="col-md-6 pt-2">
							<small>Are you a Realtor?</small>
							<Autocomplete
									inputProps={{
										name: "Realtor",
										className: "form-control",
										placeholder: "--",
										title: "Realtor",
										readOnly: true,
										id: "Realtor"
										}
									}
									
									allList={Realtor}
									
									cb={''}
									selectedText={''}
									extraProps={{}}
								/>
						</div>
						<div className="col-md-6 pt-2">
							<small>Number Of Bedrooms</small>
							<Autocomplete
									inputProps={{
										name: "Beds",
										className: "form-control",
										placeholder: "--",
										title: "Beds",
										readOnly: true,
										id: "Beds"
										}
									}
									
									allList={beds}
									
									cb={''}
									selectedText={''}
									extraProps={{}}
								/>
						</div>
						<div className="col-md-12 pt-2">
						<label>Message</label>
						<textarea name="message" className="form-control"></textarea>
						{msg &&<div className="registerMsg">
							Thank you for registering, Please check your mailbox and spam email or promotions tab on Gmail. Please add us to your safe sender/contact list. This will ensure that you will receive all the latest and future new development projects in the Platinum/VIP 1st phase along with all of our best Special Promotions.
						</div>}
						<button type="submit" className="btn submitButton">Register</button>
						
						</div>
					</form>
				{dataFlag &&
					<Loader />
				}

			</>
		)
}
export default PreConRegister;