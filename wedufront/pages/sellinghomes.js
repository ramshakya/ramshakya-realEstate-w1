import Link from "next/link";
import Image from 'next/image'
import image from "../public/images/check-mark-100x100.png";
import image1 from "../public/images/selling.jpg";
import global from "../constants/GlobalConstants";
import {useEffect,useState} from "react";
import API from "../ReactCommon/utility/api";
import PropertiesList from "./../components/HomeListSection/PropertiesList";
import Card from "../ReactCommon/Components/MapCard";
import Pagination from "../ReactCommon/Components/Pagination";
const defaultImage = global.defaultImage
const SellingHomes =(props)=>{
	const [flag,setFlag] = useState(true);
	const [soldListing,setSoldListing] = useState([]);
  	const [curr_page, setCurr_page] = useState(1);
  	const [total,settotal] = useState('');
  	props.setMetaInfo(global.pageMeta.newHome);
	useEffect(() => {
		// window.scrollTo({ behavior: 'smooth', top: '500px' });
	    if (localStorage.getItem('websetting')) {
	      let websetting = JSON.parse(localStorage.getItem('websetting'));
	     	let OfficeName = websetting.OfficeName;
	     	const getTest=async()=>{
	     		
	    		const body = { limit: 12, OfficeName: OfficeName,currentPage:curr_page };
		          const soldData = await API.jsonApiCall(global.base_url + "api/v1/services/global/getSoldByAgent",
		            body, "POST", null, {"Content-Type": "application/json"}
		          );
		          if(soldData.totalRecord){
		          	 setSoldListing(soldData.result);
		          	 settotal(soldData.totalRecord);
		          }		         
		      	}
	     	getTest();
	     	setFlag(false);
	    }
	    		
	  }, [flag]);

	function handleChange(str) {

	    if (str !== 0) {
	      setCurr_page(str);
	      // setLoader(true);
	      setFlag(true);
	      document.getElementById("sellHome").focus();
	    }
	  };
	return (
		<>
			<section className="section-padding custom-paddings">
                <div className="container">
                    <div className="row justify-content-center align-items-center">
                        <div className="col-lg-12 text-center">
                          	<h1 className="heading-text text-center">
                          		Sell Your Home with a {global.APP_NAME}
                          	</h1>
                          	<center><span className="underline-text"></span></center>
                          	<p className="pt-4">
                        		Advertising your home in the local newspaper are times of the past.
                        		Today, everyone looks to Google for their next home. Sell 
                        		your home using Google and {global.APP_NAME}; The only brokerage 
                        		in the world to be a trusted Google Partner!
                        	</p>
                        </div>

                         <div className="col-lg-12 text-center buttonSection">
                         		<div className="row">
                         			<div className="col-md-12 mt-4 col-sm-6 col-lg-2"></div>
                         			<div className="col-md-2 mt-4 col-sm-2 col-lg-2">
                         				{/* <Link href="/listingmenu">
                         					<a className="common-btn custom-right-arrow">
                         					View Listings Packages</a>
                         				</Link> */}
                         			</div>
                         			<div className="col-md-6 mt-4 col-sm-6 col-lg-4">
                         				<Link href="/homevalue">
                         					<a className="common-btn custom-right-arrow">
                         						Free Home Evaluations
                         					</a>
                         				</Link>
                         			</div>
									 <div className="col-md-4 mt-4 col-sm-4 col-lg-4"></div>

                         			<div className="col-md-12 mt-4 col-sm-6 col-lg-2"></div>
                         		</div>
                         </div>
                         <div className="col-lg-12">
                         	<h3 className="heading-text text-center">
                         	   Our 3 Step Selling Process:</h3>
                          	<center><span className="underline-text"></span></center>
                          	<div className="row pt-5">
                          		<div className="col-md-6 col-lg-6 mt-3">
                          			<div className="row mb-4">
                          				<div className="col-md-1 col-lg-1">
                          					<div className="circle-text">1</div>
                          				</div>
                          				<div className="col-md-11 col-lg-11">
                          					<h4 className="heading-text">Price It Right</h4>
											<p>
												Show agents and potential buyers you’re serious about selling.
												 Price your home competitively to attract more buyers.
											</p>
                          				</div>
                          			</div>
                          			<div className="row mb-4 pt-3">
                          				<div className="col-md-1 col-lg-1">
                          					<div className="circle-text">2</div>
                          				</div>
                          				<div className="col-md-11 col-lg-11">
                          					<h4 className="heading-text">Prep For Market</h4>
											<p>
												First impressions matter when selling your home. 
												Small details such as light fixtures and professional photos go a long way.
											</p>
                          				</div>
                          			</div>
                          			<div className="row mb-4 pt-3">
                          				<div className="col-md-1 col-lg-1">
                          					<div className="circle-text">3</div>
                          				</div>
                          				<div className="col-md-11 col-lg-11">
                          					<h4 className="heading-text">Marketing Mania</h4>
											<p>
												97% of buyers start their search online. Our web presence is second to none.
												 Utilizing the latest tools & tech to ensure maximum exposure.
											</p>
                          				</div>
                          			</div>
                          			
                          			<div className="row mb-4">
                          			<div className="col-md-2 col-sm-6 col-lg-3">
                          			</div>
                          			<div className="col-md-10 col-sm-6 col-lg-6 pt-5">
                          			{/*<a className="common-btn custom-right-arrow" href="/sellinghomes#">
                          				Learn More </a>*/}
                          			</div>
                          			<div className="col-md-12 col-sm-6 col-lg-4">
                          			</div>
                          				
                          			</div>

                          		</div>
                          		<div className="col-md-6 col-lg-6">
                          			<Image className="buyImage" 
                          			width={'100%'} 
                    				height={'100%'}
                          			layout="responsive"
                          			src={image1.src}
                          			priority={false}
                          			alt={'selling-process'}
                          			/>
                          			
                          		</div>
                          	</div>
                        </div>
                         <div className="col-lg-12 mt-5">
                         	<h3 className="heading-text text-center">
                         	   Hire a {global.APP_NAME} </h3>
                          	<center><span className="underline-text"></span></center>
                          	<div className="row pt-5 secondSectionBuy">
                          		<div className="col-md-4 col-lg-4 mb-5">
                          			<div className="row">
                          				<div className="col-md-2 col-lg-2 text-center">
                          					<i className="fa fa-umbrella icon-color icon-size"></i>
                          				</div>
                          				<div className="col-md-10 col-lg-10">
                          					<h5 className="heading-text">Real Estate Portals & Classifieds</h5>
                          					<p>All Brokerage Websites & over 5000 affiliated real estate agent websites and classifieds.</p>
                          				</div>
                          			</div>
                          		</div>
                          		<div className="col-md-4 col-lg-4 mb-5">
                          			<div className="row">
                          				<div className="col-md-2 col-lg-2 text-center">
                          					<i className="fa fa-search icon-color icon-size"></i>
                          				</div>
                          				<div className="col-md-10 col-lg-10">
                          					<h5 className="heading-text">Search Engines</h5>
                          					<p>Our network of websites rank high for the target keywords across all search engines.</p>
                          				</div>
                          			</div>
                          		</div>
                          		<div className="col-md-4 col-lg-4 mb-5">
                          			<div className="row">
                          				<div className="col-md-2 col-lg-2 text-center">
                          					<i className="fa fa-comments icon-color icon-size"></i>
                          				</div>
                          				<div className="col-md-10 col-lg-10">
                          					<h5 className="heading-text">Social Media</h5>
                          					<p>Our brand is very popular, with thousands of followers across top social networks (Top 10% on Linkedin).</p>
                          				</div>
                          			</div>
                          		</div>
                          		<div className="col-md-4 col-lg-4 mb-5">
                          			<div className="row">
                          				<div className="col-md-2 col-lg-2 text-center">
                          					<i className="fa fa-map-marker icon-color icon-size"></i>
                          				</div>
                          				<div className="col-md-10 col-lg-10">
                          					<h5 className="heading-text">Target Local Buyers</h5>
                          					<p>Reach the local audience, targeted to your postal code, who are “in-the-market” for real estate.</p>
                          				</div>
                          			</div>
                          		</div>
                          		<div className="col-md-4 col-lg-4 mb-5">
                          			<div className="row">
                          				<div className="col-md-2 col-lg-2 text-center">
                          					<i className="fa fa-plus icon-color icon-size"></i>
                          				</div>
                          				<div className="col-md-10 col-lg-10">
                          					<h5 className="heading-text">Google Digital Ads</h5>
                          					<p>Search and Display ads with features such as: Keyword tuning, Remarketing, Mobile & Video ads.</p>
                          				</div>
                          			</div>
                          		</div>
                          		<div className="col-md-4 col-lg-4 mb-5">
                          			<div className="row">
                          				<div className="col-md-2 col-lg-2 text-center">
                          					<i className="fa fa-video-camera icon-color icon-size"></i>
                          				</div>
                          				<div className="col-md-10 col-lg-10">
                          					<h5 className="heading-text">Proud Sponsor</h5>
                          					<p>We donate a proceed of every sale to support children’s health!</p>
                          				</div>
                          			</div>
                          		</div>
                          	</div>
                         </div>
	                    </div>
	                </div>
	            <div className="property-section">
	            <div className="container-fluid">
                    <div className="row justify-content-center align-items-center">
                        <div className="col-lg-12">
                         	<h3 className="heading-text text-center">
                         	  We Sell A Lot Of Homes</h3>
                         	  <a href=""  id="sellHome"></a>

                          	<center><span className="underline-text"></span></center>
                          	<div className="row sellingHome">
                          	 {soldListing.map((item,key)=>{
                          	 	return(
                          	 	<div className="col-md-3 col-lg-3 mt-3">
                          	 		<Card
                                        item={item}
                                        defaultImage={defaultImage}
                                        showIsFav={props.showIsFav}
                                        openUserPopup={props.openUserPopup}
                                        openLoginCb={props.openLoginCb}
                                        isLogin={props.isLogin}
                                    />
                                 </div>
                          	 	)
                          	 })}
                          	 <div className="col-md-12 col-lg-12 d-flex justify-content-center pt-3">

					            <Pagination
					              itemsCount={total}
					              itemsPerPage={12}
					              currentPage={curr_page}
					              setCurrentPage={handleChange}
					              alwaysShown={false}
					            />
					          </div>
                          	 </div>
                          	 {/*<PropertiesList
				              showIsFav={true}
				              openUserPopup={true}
				              data={soldListing}
				              openLoginCb={props.togglePopUp}
				              headerText={""}
				              isLogin={props.isLogin}
				              isLoading={false}
				            />*/}
                        </div>
                    </div>
                </div>
                </div>
                <div className="container">
                    <div className="row justify-content-center align-items-center">
                        <div className="col-lg-12 mt-5">
                         	<h3 className="heading-text text-center">
                         	  Results Matter</h3>
                          	<center><span className="underline-text"></span></center>
                          	<div className="row pt-5 secondSectionBuy">
                          		<div className="col-md-6 col-lg-6 mb-5">
                          			<div className="row">
                          				<div className="col-md-2 col-lg-2 text-center">
                          				                          					<img src={image.src} style={{'width':'40px'}} alt='check-box'/>

                          					
                          				</div>
                          				<div className="col-md-10 col-lg-10 pt-1">
                          					<h4 className="heading-text">Top 1% in Listing by Units, Canada wide.</h4>
                          				</div>
                          			</div>
                          		</div>
                          		<div className="col-md-6 col-lg-6 mb-5">
                          			<div className="row">
                          				<div className="col-md-2 col-lg-2 text-center">
                          					<img src={image.src} style={{'width':'40px'}} alt='check-box'/>
                          				</div>
                          				<div className="col-md-10 col-lg-10">
                          					<h4 className="heading-text">23% of our listings SOLD at 100% of asking or over. The remaining 77% sold at 99% of asking price. IMS</h4>
                          				</div>
                          			</div>
                          		</div>
                          		<div className="col-md-6 col-lg-6 mb-5">
                          			<div className="row">
                          				<div className="col-md-2 col-lg-2 text-center">
                          					                          					<img src={image.src} style={{'width':'40px'}} alt='check-box'/>

                          				</div>
                          				<div className="col-md-10 col-lg-10 pt-1">
                          					<h4 className="heading-text">Our homes SELL 19 days faster than the average agent of 37.1 DOM. IMS</h4>
                          				</div>
                          			</div>
                          		</div>
                          		<div className="col-md-6 col-lg-6 mb-5">
                          			<div className="row">
                          				<div className="col-md-2 col-lg-2 text-center">
                          					                          					<img src={image.src} style={{'width':'40px'}} alt='check-box'/>

                          				</div>
                          				<div className="col-md-10 col-lg-10">
                          					<h4 className="heading-text">Our homes SELL 2.12% higher than the average agent. (+$8,480 on $400K IMS)</h4>
                          				</div>
                          			</div>
                          		</div>
                          	</div>
                        </div>
                        
                        
                    </div>
                </div>
                
            </section>
            <section className="buysection">
            	<div className="container">
                	<div className="col-lg-12 text-center">
                        	<h3 className="heading-text mb-5 pb-4">Begin with a Free Home Evaluation.</h3>
                        	<Link href="/homevalue">
                         		<a className="common-btn buyFooterButton custom-right-arrow">
                         		TRY NOW </a>
                         	</Link>
                        </div>
                </div>
            </section>
		</>
		)
}
export default SellingHomes;