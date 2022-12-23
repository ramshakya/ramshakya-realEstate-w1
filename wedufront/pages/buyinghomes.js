import Link from "next/link";
import global from "../constants/GlobalConstants";
import { getPropertiesList, agentId, base_url, front_url } from "../constants/GlobalConstants";
import API from "../ReactCommon/utility/api";
import { useEffect, useState } from "react";
const BuyingHomes = (props) => {
	const [propertyCount, setPropertyCount] = useState(false);
	const [flag, setFlag] = useState(true);
	useEffect(() => {
		props.setMetaInfo(global.pageMeta.newHome);
		if (flag) {
			getAllData();
		}

	}, [flag]);
	const getAllData = async () => {
		const result = await API.jsonApiCall(
			getPropertiesList,
			{},
			"GET",
			null,
			{
				"Content-Type": "application/json",
			},
			{ propertyList: 0, featuredListing: 0, recentListing: 0, agentId }
		);
		console.log("");

		setPropertyCount(result.resiCount + result.condosCount)
		setFlag(false);
	}

	console.log("propertyCount", propertyCount);
	return (
		<>
			<section className="section-padding mt-1">
				<div className="container">
					<div className="row justify-content-center align-items-center">
						<div className="col-lg-12 text-center">
							<h1 className="heading-text text-center">Buy a Home with a {global.APP_NAME}</h1>
							<center><span className="underline-text"></span></center>
							<p className="pt-4">
								It is a proven fact; Finding a great real estate
								agent will save you thousands and make you thousands.
								All {global.APP_NAME} agents are trained on money-saving
								tactics and investment fundamentals to serve the needs
								of today’s Savvy Home Buyers.
							</p>
						</div>
						<div className="col-lg-12 text-center buttonSection ">
							<div className="buttonsSection">
								<div className="row text-center">
									<div className="col-md-6 col-sm-12 mt-4">
										<Link href="/buyingrealestate">

											<a className="common-btn custom-right-arrow">
												Send Me Listings </a>
										</Link>
									</div>
									<div className="col-md-6 col-sm-12 mt-4">
										<Link href="/map">
											<a className="common-btn custom-right-arrow">Browse {propertyCount}+ MLS® Listings

											</a>
										</Link>
									</div>
								</div>
							</div>
						</div>
						<div className="col-lg-12">
							<h3 className="heading-text text-center">
								Hire a {global.APP_NAME} Agent</h3>
							<center><span className="underline-text"></span></center>
							<div className="row pt-5 secondSectionBuy">
								<div className="col-md-4 col-lg-4 mb-5">
									<div className="row">
										<div className="col-md-2 col-lg-2 text-center">
											<i className="fa fa-umbrella icon-color icon-size"></i>
										</div>
										<div className="col-md-10 col-lg-10">
											<h5 className="heading-text">No Cost, Obligation or Risk</h5>
											<p>Seller pays for services rendered. Ask about our guarantee</p>
										</div>
									</div>
								</div>
								<div className="col-md-4 col-lg-4 mb-5">
									<div className="row">
										<div className="col-md-2 col-lg-2 text-center">
											<i className="fa fa-wrench icon-color icon-size"></i>
										</div>
										<div className="col-md-10 col-lg-10">
											<h5 className="heading-text">Access to Buyer Registry Service</h5>
											<p>Never lose out & avoid over-bidding, listings sent per minute.</p>
										</div>
									</div>
								</div>
								<div className="col-md-4 col-lg-4 mb-5">
									<div className="row">
										<div className="col-md-2 col-lg-2 text-center">
											<i className="fa fa-shield icon-color icon-size"></i>
										</div>
										<div className="col-md-10 col-lg-10">
											<h5 className="heading-text">Access to 100+ Banks/Lenders</h5>
											<p>Lowest rates & better deals than your bank.</p>
										</div>
									</div>
								</div>
								<div className="col-md-4 col-lg-4 mb-5">
									<div className="row">
										<div className="col-md-2 col-lg-2 text-center">
											<i className="fa fa-file icon-color icon-size"></i>
										</div>
										<div className="col-md-10 col-lg-10">
											<h5 className="heading-text">Hand-Picked & Door Knocked</h5>
											<p>When inventory is low or by request.</p>
										</div>
									</div>
								</div>
								<div className="col-md-4 col-lg-4 mb-5">
									<div className="row">
										<div className="col-md-2 col-lg-2 text-center">
											<i className="fa fa-user icon-color icon-size"></i>
										</div>
										<div className="col-md-10 col-lg-10">
											<h5 className="heading-text">SR Trained Professionals</h5>
											<p>Negotiate and look after your best interests.</p>
										</div>
									</div>
								</div>
								<div className="col-md-4 col-lg-4 mb-5">
									<div className="row">
										<div className="col-md-2 col-lg-2 text-center">
											<i className="fa fa-user icon-color icon-size"></i>
										</div>
										<div className="col-md-10 col-lg-10">
											<h5 className="heading-text">SR Expert Advice</h5>
											<p>Every step of the way, even after you move in.</p>
										</div>
									</div>
								</div>
							</div>
						</div>
						<div className="col-lg-12">
							<h3 className="heading-text text-center">
								Smart Home Buyer Program</h3>
							<center><span className="underline-text"></span></center>
							<div className="row pt-5">
								<div className="col-md-6 col-lg-6">
									<div className="row mb-4">
										<div className="col-md-1 col-lg-1">
											<div className="circle-text">S</div>
										</div>
										<div className="col-md-11 col-lg-11">
											<h4 className="heading-text">Specific: Two Homes For
												The Price Of One</h4>
											<p>
												Identify your short and long term real estate goals.
											</p>
										</div>
									</div>
									<div className="row mb-4">
										<div className="col-md-1 col-lg-1">
											<div className="circle-text">A</div>
										</div>
										<div className="col-md-11 col-lg-11">
											<h4 className="heading-text">Attainable: Via SMART Mortgage Financing</h4>
											<p>
												Access to over 100 banks and lenders means more attainable payment options.
											</p>
										</div>
									</div>
									<div className="row mb-4">
										<div className="col-md-1 col-lg-1">
											<div className="circle-text">R</div>
										</div>
										<div className="col-md-11 col-lg-11">
											<h4 className="heading-text">Relevant: Align With Your Goals</h4>
											<p>
												We correlate your short and long-term goals accordingly with your reasons to buy.
											</p>
										</div>
									</div>
									<div className="row mb-4">
										<div className="col-md-1 col-lg-1">
											<div className="circle-text">S</div>
										</div>
										<div className="col-md-11 col-lg-11">
											<h4 className="heading-text">Specific: Two Homes For
												The Price Of One</h4>
											<p>
												Identify your short and long term real estate goals.
											</p>
										</div>
									</div>
									<div className="row mb-4">
										<div className="col-md-1 col-lg-1">
											<div className="circle-text">T</div>
										</div>
										<div className="col-md-11 col-lg-11">
											<h4 className="heading-text">Timeline: 3-5 Year Cycle</h4>
											<p>
												We make sure your goals are met while keeping a close eye on your timelines.
											</p>
										</div>
									</div>

								</div>
								<div className="col-md-6 col-lg-6">
									<img className="buyImage" src="https://searchrealty.ca/wp-content/uploads/2019/10/savvy-buyers-image2.jpg" alt="savvy-buyers"/>
								</div>
							</div>
						</div>
						<div className="col-lg-12 mt-5">
							<h3 className="heading-text text-center">
								Our Guarantees</h3>
							<center><span className="underline-text"></span></center>
							<div className="row pt-4">
								<div className="col-md-6 col-lg-6 text-center">
									<img src="https://searchrealty.ca/wp-content/uploads/2019/08/guarantee-appreciation.png" alt="guarantee-appreciation"/>
									<h4 className="heading-text">Appreciation Guaranteed</h4>
									<p>If the value of your home does not increase after 3-5 years, we will sell your home at 0% commission!</p>
								</div>
								<div className="col-md-6 col-lg-6 text-center">
									<img src="https://searchrealty.ca/wp-content/uploads/2019/08/guarantee-satisfaction.png" alt="guarantee-satisfaction"/>
									<h4 className="heading-text">Satisfaction Guaranteed</h4>
									<p>If for any reason you’re not happy with my services, we will rip up any agreements we have in place!</p>
								</div>
							</div>
						</div>


					</div>
				</div>

			</section>
			<section className="buysection">
				<div className="container">
					<div className="col-lg-12 text-center">
						<h3 className="heading-text mb-5 pb-4">Need Help? Use our SR Concierge.</h3>
						<Link href="/homevalue">
							<a className="common-btn buyFooterButton">
								TRY NOW &nbsp;&nbsp;&nbsp;<i className="fa fa-chevron-right"></i></a>
						</Link>
					</div>
				</div>
			</section>
		</>
	)
}
export default BuyingHomes;