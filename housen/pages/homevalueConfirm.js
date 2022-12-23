import React, {useEffect,useState} from "react";
import Link from "next/link";
import Map from "../components/Map/GoogleMap";
import Form from "../components/Forms/ConfirmEvalueForm";
import Form1 from "../components/Forms/ConfirmEvalueForm1";
import Constants from '../constants/Global';
import API from "../ReactCommon/utility/api";
import { ToastContainer, toast } from 'react-toastify';
import 'react-toastify/dist/ReactToastify.css';
const extra_url = Constants.extra_url;
import Loader from '../components/loader/loader';
import { useRouter } from 'next/router'
const homevalueConfirm = (props) =>{
	const router = useRouter()
	const [searchQuery, setSearchQuery] = useState(''); 

	const [home_type, setHome_type] = useState(''); 
	const [beds, setBeds] = useState(''); 
	const [baths, setBaths] = useState(''); 
	const [basement, setBasement] = useState(''); 
	const [listing_status, setListing_status] = useState(''); 
	const [parking_type, setParking_type] = useState(''); 
	
	const [flag, setFlag] = useState(true);
	const [dataFlag, setDataFlag] = useState(false);
	useEffect(() => {
		if(localStorage.getItem('googleSearch'))
		{
			setSearchQuery(localStorage.getItem('googleSearch'));
		}
	});
	const handleSubmit=(event)=>{
		setHome_type(event.home_type)
		setBeds(event.beds)
		setBaths(event.baths)
		setBasement(event.basement)
		setListing_status(event.listing_status)
		setParking_type(event.parking_type)
		setFlag(false);
	}
	const handleSubmitData = (event)=>{
		console.log(event);
		let fullname = event.fullname
		let email = event.email
		let phone = event.mobile
		let user_category = event.user_category
		let user_type = event.user_type
		 setDataFlag(true);
		let body = JSON.stringify({
			home_type:home_type,
			beds:beds,
			baths:baths,
			basement:basement,
			listing_status:listing_status,
			parking_type:parking_type,
			name:fullname,
			email:email,
			phone:phone,
			agent_id:Constants.agentId,
			searchQuery:searchQuery,
			user_category:user_category,
			user_type:user_type
		});
		console.log("body",body);
		API.jsonApiCall((extra_url+'SubmitHomeValue'), body, "POST", null, {
            "Content-Type": "application/json",
        }).then((res) => {
          if(res.success)
          {
            toast.success(res.success);
            window.location.href = "/map";
            // router.push('/map');
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
				         <div className="col-lg-7 col-lg-7 mobileMap">
				            	
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