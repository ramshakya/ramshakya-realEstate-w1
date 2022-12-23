import Card from "../Cards/PropertyCard"
import React, { useEffect, useState } from "react";
import ReactCarousel from './../../ReactCommon/Components/ReactCarousel';
import detect from "../../ReactCommon/utility/detect";
import { useRouter } from 'next/router';
import Constants from '../../constants/Global';

const Listings = (props) => {
	let dfCard=props.isDetails?3:4;
	  dfCard=props.isWatch?2:dfCard;
	const [showCard, setShowCard] = useState(dfCard);
	const [userDetails, setUserDetails] = useState(props.userDetails);
	const [emailIsVerified, setEmailVerified] = useState(props.emailIsVerified);

	const { heading } = props;
	const router = useRouter()
	const propertyData = props.propertyData;
	const firstRow = propertyData.slice(0, 6);
	const secondRow = propertyData.slice(7, 12);
	const thirdRow = propertyData.slice(13, 18);
	const imageData = props.imageData;
	useEffect(() => {
		if (props.emailIsVerified) {
			setEmailVerified(props.emailIsVerified)
		}
	}, [props.emailIsVerified]);
	useEffect(() => {
		if(props.isWatch){
			setShowCard(2);
		}
		if (screen.width < 600) {
			setShowCard(1);
		}

		if (screen.width > 600 && screen.width < 1100) {
			setShowCard(2);
		}
		if (screen.width > 1100) {
			if(props.isDetails){
				setShowCard(2);
			}
			else
			{
				if(!props.isWatch){
					setShowCard(4);
				}
			}
			
		}

	}, []);
	if (typeof window !== "undefined") {
		window.addEventListener('resize', function (event) {
			if (screen.width < 600) {
				setShowCard(1);
			}
			if (screen.width > 600 && screen.width < 1100) {
				setShowCard(2);
			}
			if (screen.width > 1100) {
				if(props.isDetails){
				setShowCard(2);
				}
				else
				{
					setShowCard(3);
				}

			}
		}, true);
	}
	function verifyEmail() {
		localStorage.setItem('verifyemail', true)
		router.push('/profile');
	}
	
	return (
		<>
			<div className={` ${ props.isDetails?"px-5":" p-4 "}  featuredListing`}>
				{
					 heading   &&
					<div className="row">
						<div className={`col-md-12 col-lg-12 pb-3 ${detect.isMobile() ? "mt-5" : detect.isMobile()} `}>
							<h5 className="featuredListingHeading">{heading}</h5>
						</div>
					</div>
				}
				{propertyData ?
					<div className={` ${ props.isDetails?"":""} row propertyCardSlider`}>
						<ReactCarousel show={showCard} homeSlider={props.isDetails?'':'homeSlider'}>
							{firstRow.map((item) => {
								return (
									<>
										<div className={` ${ props.isDetails?"px-3":"   "}  p-1 position-relative`}>
											<Card
												item={item}
												imageData={imageData}
												showIsFav={true}
												showTrash={props.isTrash}
												removeCb={props.removeCb}
												openUserPopup={true}
												openLoginCb={props.togglePopUp}
												isLogin={props.isLogin}
												LoginRequired={props.LoginRequired}
												isSold={props.isSold?props.isSold:false}
												emailIsVerified={emailIsVerified}
												signInToggle={props.signInToggle}
												verifyEmail={verifyEmail}
											/>
										</div>
									</>
								)
							})}
						</ReactCarousel>
						<ReactCarousel show={showCard} homeSlider={props.isDetails?'':'homeSlider'}>
							{secondRow.map((item,k) => {
								return (
										<div key={k} className={` ${ props.isDetails?"px-3":"   "} p-1 position-relative`}>
											<Card
												item={item}
												imageData={imageData}
												showIsFav={true}
												showTrash={props.isTrash}
												openUserPopup={true}
												openLoginCb={props.togglePopUp}
												isLogin={props.isLogin}
												isSold={props.isSold?props.isSold:false}
												signInToggle={props.signInToggle}
												LoginRequired={props.LoginRequired}
											/>
										</div>
									 
								)
							})}
						</ReactCarousel>
						<ReactCarousel show={showCard} homeSlider={props.isDetails?'':'homeSlider'}>
							{thirdRow.map((item) => {
								return (
									<>
										<div className="p-1 position-relative">
											<Card
												item={item}
												imageData={imageData}
												showIsFav={true}
												openUserPopup={true}
												openLoginCb={props.togglePopUp}
												isSold={props.isSold?props.isSold:false}
												isLogin={props.isLogin}
												showTrash={props.isTrash}
												signInToggle={props.signInToggle}
												LoginRequired={props.LoginRequired}
											/>
										</div>
									</>
								)
							})}
						</ReactCarousel>
					</div>
					:
					""
				}
			</div>
		</>
	);
}
export default Listings