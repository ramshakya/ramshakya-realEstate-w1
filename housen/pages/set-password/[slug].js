import React, { Component, useState, useEffect, useRef } from "react";
import API from "../../ReactCommon/utility/api";
import Constants from "../../constants/Global";
const extra_url = Constants.extra_url;
import { ToastContainer, toast } from 'react-toastify';
import 'react-toastify/dist/ReactToastify.css';
import { useRouter } from 'next/router';
// import Layout from "../components/Layout/Layout";
import Loader from '../../components/loader/loader';
const SetPassword = ()=>{
	const [confirm,setConfirm] = useState(false);
	const [errorMsg,setErrorMsg] = useState(false);
	const [check,setCheck] = useState(false);
	const [tokenExist,setTokenExist] = useState(null);
	const [preLoader,setPreloader] = useState(false);
	const router = useRouter();
	const token = router.query.slug;
		useEffect(()=>{
			// console.log(token);
			const geTokenCheck=()=>{
					API.jsonApiCall((extra_url+'verifyToken'), {checkToken:token}, "POST", null, {
		            "Content-Type": "application/json",
			        }).then((res) => {
			        
			         	setTokenExist(res);
			         	console.log("gettign data",res);
			         	setPreloader(true);
			         	// console.log(res);
			       });
			    }
			if(token !== undefined){	
				geTokenCheck();   
	    	}
	  },[token])  
	
	
	// console.log("------token",preLoader);
	const handleSubmit=(event)=>{
		event.preventDefault();
		setPreloader(false);
		 const Password = event.target.password.value;
		  const confirmPassword = event.target.confirmPassword.value;
		  handleInput();
		  if(check)
		  {
		  	let body = {Password:Password,ConfirmPassword:confirmPassword,Token:token};
		  	API.jsonApiCall((extra_url+'verifyToken'), body, "POST", null, {
            "Content-Type": "application/json",
	        }).then((res) => {
	        
	          if(res.success)
	          {
	            toast.success(res.success);
	            window.location.href = "/";
	          }
	          else
	          {
	            toast.error(res.error);

	          }
	          setPreloader(true);
	       });
		  }
	}
	const handleInput = (event)=>{
		let psd = document.getElementById('password').value;
		let cnf = document.getElementById('cnfPassword').value;
		if(psd=="")
		{
			setErrorMsg('Password is required')
			setCheck(false);
		}
		else
		{
			setErrorMsg(false);
			if(psd==cnf){
				  	setConfirm(false);
				  	setCheck(true);
				  }
				   else
				  {
				  	setConfirm('Confirm password mismatch');
				  	setCheck(false);
				  }
		}
		
	}
	return(
			<div>
				<div className="container section-padding custom-padding">
					<div className="row pt-5">
						<div className="col-md-4 col-lg-4"></div>
						<div className="col-md-4 col-lg-4">
						{(tokenExist==='success') &&
							<form className="set-password-box" onSubmit={handleSubmit}>
								<h4>Set password</h4>
								<div className="form-group mb-2">
									<label className="font-normal">New password</label>
									<input type="password" className="form-control forgot-psd" id="password" name="password" onKeyUp={handleInput}/>
									<span className="err-inp-msg" id="msg1">{errorMsg}</span>
								</div>
								<div className="form-group pb-3">
									<label className="font-normal">Confirm password</label>
									<input type="password" className="form-control forgot-psd" id="cnfPassword" name="confirmPassword" onKeyUp={handleInput}/>
									<span className="err-inp-msg" id="msg2">{confirm}</span>
								</div>
								<button type="submit" className="submitButton" style={{"width":"100%"}}>Submit</button>
							</form>
						}
						{(tokenExist==='Session Expired') &&
							<div className="page-expired text-center"><h3>Session Expired!<br/><small style={{fontSize:'16px'}}> Request forgot password again </small></h3></div>
						}
						{(tokenExist==='Link expired') &&
							<div className="page-expired text-center"><h3>Link Expired!</h3></div>
						}
						{(tokenExist===null) &&
							<div className="page-expired text-center"><h3>---</h3></div>
						}
						</div>
						<div className="col-md-4 col-lg-4"></div>
					</div>
				</div>
				{!preLoader &&
					<Loader/>
				}
			</div>
		)
}

export default SetPassword;