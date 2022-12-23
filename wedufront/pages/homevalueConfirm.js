import React, {useEffect,useState} from "react";
import Layout from '../components/Layout/Layout';
import Link from "next/link";
import Map from "../components/Map/GoogleMap";
import Form from "../components/Forms/ConfirmEvalueForm";
import Form1 from "../components/Forms/ConfirmEvalueForm1";
import Constants from '../constants/GlobalConstants';
import API from "../ReactCommon/utility/api";
import { ToastContainer, toast } from 'react-toastify';
import 'react-toastify/dist/ReactToastify.css';
const extra_url = Constants.extra_url;
import Loader from '../components/loader/loader';
const homevalueConfirm = (props) =>{
	const [searchQuery, setSearchQuery] = useState(''); 
	const [buildyear, setBuildyear] = useState(''); 
	const [sqft, setSqft] = useState(''); 
	const [purpose, setPurpose] = useState(''); 
	const [sellTime, setSellTime] = useState(''); 
	const [flag, setFlag] = useState(true);
	const [dataFlag, setDataFlag] = useState(false);
	useEffect(() => {
		if(localStorage.getItem('googleSearch'))
		{
			setSearchQuery(localStorage.getItem('googleSearch'));
		}
	});
	const handleSubmit=(event)=>{

		setBuildyear(event.buildyear);
		setSqft(event.sqft);
		setPurpose(event.purpose);
		setSellTime(event.sellTime);
		setFlag(false);
	}
	const handleSubmitData = (event)=>{
		console.log(event);
		let fullname = event.fullname
		let email = event.email
		let phone = event.mobile
		 setDataFlag(true);
		let body = JSON.stringify({
			buildyear:buildyear,
			sqft:sqft,
			purpose:purpose,
			time:sellTime,
			name:fullname,
			email:email,
			phone:phone,
			agent_id:Constants.agentId,
			searchQuery:searchQuery
		});
		API.jsonApiCall((extra_url+'SubmitHomeValue'), body, "POST", null, {
            "Content-Type": "application/json",
        }).then((res) => {
          if(res.success)
          {
            toast.success(res.success);
            window.location.href = "/homevaluereport";
          }
          else
          {
            toast.error(res.errors);

          }
           setDataFlag(false);
       });
	}

	return(
			<>
				<section className="error-404 section-padding">
				    <div className="container-fluid custom-container">
				        <div className="row">
				         <div className="col-lg-7 col-lg-7">
				            	
				            	<Map searchQuery = {searchQuery}/>
				         </div>
				        <div className="col-md-5 col-lg-5">
				         
				        {flag &&
				            <Form handleSubmitData = {handleSubmit}/>
				        }
				        {!flag &&
				            <Form1 handleSubmitData = {handleSubmitData}/>
				         }

				         </div>
				           
				        </div>
				    </div>
				</section>

					{dataFlag &&
	          <Loader />
	        } 
	       		
			</>
		);
	

}
export default homevalueConfirm;