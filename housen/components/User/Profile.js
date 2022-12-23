import React, { useEffect, useState } from "react";
import { Tab, Row, Col, Nav, Form, Button, Tabs, Modal } from "react-bootstrap";
import AccountSetting from "./AccountSetting";
import Fovourite from "./Fovourite";
import { ToastContainer, toast } from 'react-toastify';
import LoginInfo from "./LoginInfo";
import SavedSearch from "./SavedSearch";
import { requestToAPI } from "../../pages/api/api";
import EmailHistory from "./EmailHistory"
import UpdatePassword from "./UpdatePassword";
import WatchListActive from './WatchListActive';
import WatchListSold from './WatchListSold';
import WatchListCommunity from './WatchListCommunity';
import { withRouter, useRouter } from "next/router";
const Profile = (props) => {
	const router = useRouter();
	const slug = router.query.slug;
	const [flag, setFlag] = useState(false);
	const [emailSent, setEmailSent] = useState(false);
	const [warning, setWarning] = useState(false);
	const [codeSent, setCodeSent] = useState(false);
	const [code, setCode] = useState('');
	const [emailId, setEmailid] = useState(props.userDetails.login_email);
	const [userDetails, setUserDetails] = useState(props.userDetails);
	const [modalShow, setModalShow] = React.useState(false);
	const [errorShow, setError] = React.useState("");
	const [flag1,setFlag1] = useState(true);
	let prevValue;
	// if(!checkLoginToken){
	// 	window.location.href = "/";
	// }
	// confirmCode
	//Email
	//Code

	useEffect(() => {
		if (!modalShow) {
			setEmailSent(false);
			setCodeSent(false);
			setCode('');
		}
	}, [modalShow]);
	useEffect(() => {
		setTimeout(() => {
			try {
				// if (localStorage.getItem('SavedSearches')) {
				// 	let dom = document.getElementById('SavedSearches');
				// 	dom.firstChild.click();
				// }
				// if (localStorage.getItem('SavedHomes')) {
				// 	let dom = document.getElementById('SavedHomes');
				// 	dom.firstChild.click();
				// }
				// if (localStorage.getItem('WatchList')) {
				// 	let dom = document.getElementById('watchedList');
				// 	dom.firstChild.click();
				// }
				
				if (localStorage.getItem('verifyemail')) {
					setModalShow(true);;
				}
				// localStorage.removeItem('verifyemail')
				// localStorage.removeItem('SavedHomes')
				// localStorage.removeItem('SavedSearches')
				// localStorage.removeItem('WatchList')
			} catch (error) {

			}
			window.scrollTo({
				top: 0,
				behavior: "smooth",
			});
		}, 150);
	}, []);
	// 
	function openModel() {
		setModalShow(true);
	}
	const verifyEmail = async () => {
		try {
			setCodeSent(true);
			setEmailSent(true);
			let email = props.userDetails.login_email;
			let body = JSON.stringify({
				email: email
			});
			let res = await requestToAPI(body, "api/v1/services/resendOtp", "POST", {});
			if (res.success) {
				toast.success("Code sent successfully.");
				setEmailSent(false);
			}
			else {
				toast.error("Something went wrong please try again !");
			}
		} catch (e) {
			console.log("error", e);
		}
	}
	const handleVerify = async () => {
		try {
			if (!prevValue) {
				setError("Please Enter code .");
				return false;
			}
			// setModalShow(true);
			let email = props.userDetails.login_email;
			let body = JSON.stringify({
				email: email,
				code: code ? code : prevValue
			});
			let json = await requestToAPI(body, "api/v1/services/confirmCode", "POST", {});
			if (json.confirmcode) {
				toast.success(json.message);
				if (localStorage.getItem('userDetail')) {
					let userDetail = localStorage.getItem('userDetail');
					try {
						userDetail = JSON.parse(userDetail);
						userDetail.EmailIsVerified = true;
						setWarning();
						userDetails.EmailIsVerified = true
						localStorage.setItem('userDetail', JSON.stringify(userDetail));
						setModalShow(false);
					} catch (e) {
						console.log("error", e);
					}
				}
			}
			if (json.error) {
				toast.error(json.message);
			}

		} catch (e) {
			console.log("error", e);
		}
	}
	function validateInp(e) {
		if (isNaN(e.target.value)) {
			e.target.value = "";
			return false;
		}
		prevValue = e.target.value;
		// setCode(e.target.value)
	}

	useEffect(()=>{
		setTimeout(() => {
			try {
				if(slug=="user"){
					let dom = document.getElementById('user');
					dom.firstChild.click();
				}
				if (slug=="SavedSearches") {
					let dom = document.getElementById('SavedSearches');
					dom.firstChild.click();
				}
				if (slug=="SavedHomes") {
					let dom = document.getElementById('SavedHomes');
					dom.firstChild.click();
				}
				if (slug=="WatchList") {
					let dom = document.getElementById('watchedList');
					dom.firstChild.click();
				}
				if (slug=="LoginInfo") {
					let dom = document.getElementById('LoginInfo');
					dom.firstChild.click();
				}
			} catch (error) {

			}
			window.scrollTo({
				top: 0,
				behavior: "smooth",
			});
		}, 150);
	},[slug])
	function MyVerticallyCenteredModal(props) {
		return (
			<Modal
				{...props}
				size="md"
				aria-labelledby="contained-modal-title-vcenter"
				centered
				className={'email-verify-models'}
			>
				<Modal.Header closeButton>
					<Modal.Title id="contained-modal-title-vcenter">
						<h5>Confirm Your Email</h5>
					</Modal.Title>
				</Modal.Header>
				<Modal.Body>
					{
						codeSent ? <>
							{
								emailSent ? <> <p>Code is sending on your email address ......</p></> : <>
									<span className="confirm-code">Code is sent to your email address.</span><br />
									<input type="text" name="confirmCode" maxLength={'10'} onChange={validateInp} placeholder="Enter code here" className="form-control mt-3" autoComplete="off" id="confirm_code" />
									{
										!prevValue &&
										<span className="err-inp-msg">{errorShow}</span>
									}
								</>
							}
						</> : <>
							<input type="text" readOnly name="confirmCode1" value={emailId} placeholder="Email" className="form-control mt-3" autoComplete="off" id="confirm_coded" />
						</>
					}

				</Modal.Body>
				<Modal.Footer>
					{
						codeSent ? <>
							<Button className={'Verify-email-submit'} disabled={emailSent ? true : false} onClick={handleVerify}>

								{
									emailSent ? 'Sending....' : 'Verify'
								}
							</Button>
						</> : <>
							<Button className={'Verify-email-submit'} onClick={verifyEmail}>
								Send Code
							</Button>
						</>
					}


				</Modal.Footer>
			</Modal>
		);
	}
	function setUrl(str){
		router.push('/profile/'+str);
	}
	return (
		<div className="UserProfile">
			<h4 className="profile-title">My Profile</h4>
			{
				!userDetails.EmailIsVerified && <>
					<MyVerticallyCenteredModal
						show={modalShow}
						onHide={() => setModalShow(false)}
					/>
					<p className="profile-status"><i>Your Email is not verified !  <Button className="verify-now" onClick={openModel} > verify now</Button></i></p>
				</>
			}
			<Tab.Container id="left-tabs-example" defaultActiveKey="first">
				<Row>
					<Col sm={3}>
						<Nav variant="pills" className="flex-column">
							<Nav.Item id="user">
								<Nav.Link eventKey="first" onClick={()=>setUrl('user')}>Profile</Nav.Link>
							</Nav.Item>
							<Nav.Item id="SavedHomes">
								<Nav.Link eventKey="second" onClick={()=>setUrl('SavedHomes')}>Favourites</Nav.Link>
							</Nav.Item>
							<Nav.Item id="SavedSearches">
								<Nav.Link eventKey="third" onClick={()=>setUrl('SavedSearches')}>Saved Alert</Nav.Link>
							</Nav.Item>
							<Nav.Item id="watchedList">
								<Nav.Link eventKey="fifth" onClick={()=>setUrl('watchedList')}>Watch Listings</Nav.Link>
							</Nav.Item>
							<Nav.Item id="LoginInfo">
								<Nav.Link eventKey="fourth" onClick={()=>setUrl('LoginInfo')}>Login Info</Nav.Link>
							</Nav.Item>
						</Nav>
					</Col>
					<Col sm={9}>
						<Tab.Content>
							<Tab.Pane eventKey="first">
								<div className="row">
									<div className="col-md-12 col-lg-12 profileItem">
										<Tabs defaultActiveKey="BasicDetail" id="uncontrolled-tab-example" className="mb-3">
											<Tab eventKey="BasicDetail" title="Basic Details">
												<AccountSetting />

											</Tab>
											<Tab eventKey="UpdatePassword" title="Update Password">
												<UpdatePassword />
											</Tab>

										</Tabs>

									</div>

								</div>
							</Tab.Pane>
							<Tab.Pane eventKey="second">
								<div className="row">
									<div className="col-md-12 col-lg-12 prorfileAccount">
										<div className="profile-heading"><h5>Favourites Properties</h5></div>
										<Fovourite />
									</div>
								</div>
							</Tab.Pane>
							<Tab.Pane eventKey="third">
								<div className="row">
									<div className="col-md-12 col-lg-12 profileItem">
										<Tabs defaultActiveKey="home" id="uncontrolled-tab-example" className="mb-3">
											<Tab eventKey="home" title="Saved search">
												<div className="profile-heading"><h5>Saved Searches</h5></div>
												<SavedSearch />
											</Tab>
											<Tab eventKey="profile" title="Email history">
												<div className="profile-heading"><h5>Email history</h5></div>
												<EmailHistory />
											</Tab>
										</Tabs>
									</div>
								</div>
							</Tab.Pane>
							<Tab.Pane eventKey="fourth">
								<div className="row">
									<div className="col-md-12 col-lg-12 prorfileAccount">
										<div className="profile-heading"><h5>Login Info</h5></div>
										<LoginInfo />
									</div>
								</div>
							</Tab.Pane>
							<Tab.Pane eventKey="fifth">
								<div className="row">
									<div className="col-md-12 col-lg-12 prorfileAccount">
										<div className="profile-heading mb-4"><h5>Watch Listings</h5></div>
										<Tabs defaultActiveKey="active" id="uncontrolled-tab-example" className="mb-3">
											<Tab eventKey="active" title="Active">
												<WatchListActive isActive={true} />
											</Tab>
											<Tab eventKey="sold" title="Sold">
												{/* <div className="profile-heading"><h5>Watch Listings</h5></div> */}
												<WatchListSold {...props} isActive={false} />
											</Tab>
											<Tab eventKey="Community" title="Watch Community">
												{/* <div className="profile-heading"><h5>Watch Listings</h5></div> */}
												<WatchListCommunity {...props} isActive={false} />
											</Tab>
										</Tabs>
									</div>
								</div>
							</Tab.Pane>
						</Tab.Content>
					</Col>
				</Row>
			</Tab.Container>

		</div>
	);
}
export default Profile;