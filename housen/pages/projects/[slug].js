import React, {useEffect,useState} from "react";
import Link from "next/link";
import API from "../../ReactCommon/utility/api";
import Constants from "../../constants/Global";
import ReactCarousel from './../../ReactCommon/Components/ReactCarousel';
import Map from "../../components/Map/GoogleMap";
import { withRouter, useRouter } from "next/router";
import { ToastContainer, toast } from 'react-toastify';
import Loader from '../../components/loader/loader';
import logo from '../../public/images/logo/logo.png';
import avatar1 from '../../public/images/agentInfo/img_avatar3.png';
import avatar2 from '../../public/images/agentInfo/img_avatar5.png';
import PopupForm from '../../components/Forms/PopupForm';
import PopupModel from '../../ReactCommon/Components/PopupModel'
import PreConRegister from '../../components/Forms/PreConRegister'
import Image from "next/image";
const PreConstructionDetil = (props) =>{
	const [porpertyDetail,setPropertyDetail] = useState(false);
	const [ApiKey,setApiKey] = useState("");
	const router = useRouter();
	const [builder,setbuilder] = useState([]);
	const [show,setShow] = useState(false);
	const [showFloor,setshowFloor] = useState(false);
	const [facebook, setFacebook] = useState('#');
	const [twitter, setTwitter] = useState('#');
	const [linkedin, setLinkedin] = useState('#');
	const [instagram, setInstagram] = useState('#');
	const [youtube, setYoutube] = useState('#');
	const [webEmail, setWebEmail] = useState('');
	const slug = router.query.slug;
  	const [selectedSingleImageCarousel,setselectedSingleImageCarousel] = useState(null);
  	const [showCard, setShowCard] = useState(4);
	console.log("slugs",slug);
	useEffect(() => {

		const body = {slug:slug}
        const getConstruction = async () => {
            const data = await API.jsonApiCall(Constants.base_url + "api/v1/services/global/preConstructionDetail",
                body, "POST", {}

            );
            if(data){
				setPropertyDetail(data.details);
				setbuilder(data.BuilderDetail);	
            }
          
            
        }
        if(slug!==undefined){

        	getConstruction();
        }
        
	},[slug]);
		
	let imageArray = [];
	if(porpertyDetail){
		let MediaImage = porpertyDetail.MediaImage;
		console.log("Attechments",MediaImage);
		let images = MediaImage.replace(/["\{\}\[\]]/gi, '');
		images = images.replaceAll("\\","");
	    imageArray = images.split(",");
	}
	let FloorPlans = [];
	if(porpertyDetail){
		let Attechments = porpertyDetail.Attechments;
		console.log("Attechments",Attechments);
		let images1 = Attechments.replace(/["\{\}\[\]]/gi, '');
		images1 = images1.replaceAll("\\","");
	    FloorPlans = images1.split(",");
	}
useEffect(() => {
  	
  	 if(localStorage.getItem('websetting')){
          let websetting = JSON.parse(localStorage.getItem('websetting'));
          if(websetting.GoogleMapApiKey!=null){
          	setApiKey(websetting.GoogleMapApiKey);
          }
          const parseData = websetting;
          setFacebook(parseData.FacebookUrl);
	      setTwitter(parseData.TwitterUrl);
	      setLinkedin(parseData.LinkedinUrl);
	      setInstagram(parseData.InstagramUrl);
	      setYoutube(parseData.YoutubeUrl);
	      setWebEmail(parseData.WebsiteEmail);
        }
   
  }, []);
function openForm(){
	document.getElementById('togglebtn').click();
}
var formatter = new Intl.NumberFormat("en-US", {
        style: "currency",
        currency: "USD",
        minimumFractionDigits: 0,
    });
// console.log("propslist",props);
	 
	  let banner = "";
	  if(imageArray.length>0)
	  {
	  	banner = imageArray[0];
	  }
	  function scrollToEl(id){
	  	 const element = document.getElementById(id);
  		 element.scrollIntoView(true);
	  }
	  if(props.isLogin){
	  	if(document.getElementById('LoginPopupModal'))
	  	{
	  		document.getElementById('LoginPopupModal').style.display= "none";
	  	}
	  }
	  function fbook() {
	    window.open(
	      `https://www.facebook.com/share.php?u=${window.location.href}`,
	      "Facebook",
	      "width=650,height=500"
	    );
	  }
	  function twitterShare() {
	    window.open(
	      `https://twitter.com/intent/tweet?text=${window.location.href}`,
	      "Twitter",
	      "width=650,height=500"
	    );
	  }
	  function pinterest() {
	    window.open(
	      `https://pinterest.com/pin/create/button/?url=${window.location.href}`,
	      "Pinterest",
	      "width=650,height=500"
	    );
	  }
	  function viewPopup(index)
	  {
	  	setselectedSingleImageCarousel(index);
	  	setShow(!show)
	  }
	  function viewPopup1(key){
	  	setselectedSingleImageCarousel(key);
	  	setshowFloor(!showFloor);
	  }

	 useEffect(() => {
		if (screen.width < 600) {
			setShowCard(1);
		}

		if (screen.width > 600 && screen.width < 1100) {
			setShowCard(2);
		}
		if (screen.width > 1100) {
			setShowCard(4);			
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
	  // console.log("props",props.isLogin);
	return(
			<>
				<div className="term_condition preconstruction pt-5" style={{ "background": "linear-gradient(var(--theme-opacity), var(--theme-opacity)), url(" + banner + ")" }}>
					
					<div className="container">
						<div className="row">
							<div className="col-md-7 col-lg-7">
								<img src={porpertyDetail && porpertyDetail.BuildingLogo?porpertyDetail.BuildingLogo:logo.src} className="preConLogo" />
								{/*<h3 className="precon_building_name">{porpertyDetail?porpertyDetail.BuildingName:''}</h3>*/}

							</div>
							<div className="col-md-5 col-lg-5">
								<div className="card form-card">
								  	<div className="card-body">
								    <h4 className="card-title">REGISTER <br/>For Platinum Access</h4>
								    <hr/>
								    <div className="row" id="preConPageForm">
								    	<PreConRegister/>
								    	{/*<PopupForm handleClose={props.togglePopUp}/>*/}
								    </div>
								  </div>
								</div>
							</div>
						</div>
					</div>
				</div>
				<div className="preCon_menu">
					<div className="container">
						<div className="row">
							<div className="col-md-3 col-lg-3">
								<button className="btn" onClick={()=>scrollToEl('Video')}><i className="fa fa-caret-square-o-left"></i><br/>
								Project Video</button>
							</div>
							<div className="col-md-3 col-lg-3">
								<button className="btn" onClick={()=>scrollToEl('Gallery')}><i className="fa fa-picture-o"></i><br/>
								GALLERY</button>
							</div>
							<div className="col-md-3 col-lg-3">
								<button className="btn" onClick={()=>scrollToEl('Heighlight')}><i className="fa fa-file-text-o"></i><br/>
								PROJECT DETAIL</button>
							</div>
							<div className="col-md-3 col-lg-3">
								<button className="btn" onClick={()=>scrollToEl('MapView')}><i className="fa fa-map-marker"></i><br/>
								VIEW ON MAP</button>
							</div>
						</div>
					</div>
				</div>
				<div className="container-fluid">
					<div className="row">
						<div className="col-md-12 col-lg-12">
							<div className="">
								<div className="row">
								
									<div className="col-md-6 PreConstructionDetilPage">
										<h4 className="pb-2">PROJECT SUMMARY</h4>
										<table className="projectDetail">
											<tr>
												<td><i className="fa fa-tags" aria-hidden="true"></i> Project Name:</td>
												<td>{porpertyDetail?porpertyDetail.BuildingName:''}</td>
											</tr>
											<tr>
												<td><i className="fa fa-map-marker" aria-hidden="true"></i>  Address:</td>
												<td>{porpertyDetail?porpertyDetail.Address:''}</td>
											</tr>
											<tr>
												<td><i className="fa fa-user" aria-hidden="true"></i> Developer:</td>
												<td>{builder?builder.BuilderName:''}</td>
											</tr>
											<tr>
												<td><i className="fa fa-home" aria-hidden="true"></i> Architects:</td>
												<td>{porpertyDetail?porpertyDetail.Architects:''}</td>
											</tr>
											<tr>
												<td><i className="fa fa-check" aria-hidden="true"></i> Project Status:</td>
												<td>{porpertyDetail?porpertyDetail.SaleStatus:''}</td>
											</tr>
											
											<tr>
												<td><i className="fa fa-building" aria-hidden="true"></i> City:</td>
												<td>{porpertyDetail?porpertyDetail.City:''}</td>
											</tr>
											
											<tr>
												<td><i className="fa fa-cog" aria-hidden="true"></i> Property Type:</td>
												<td>{porpertyDetail?porpertyDetail.BuildingType:''}</td>
											</tr>
											<tr>
												<td><i className="fa fa-underline" aria-hidden="true"></i> Units:</td>
												<td>{porpertyDetail?porpertyDetail.Suites:''}</td>
											</tr>
											<tr>
												<td><i className="fa fa-plus-circle" aria-hidden="true"></i> Storeys:</td>
												<td>{porpertyDetail?porpertyDetail.Storeys:''}</td>
											</tr>
											<tr>
												<td><i className="fa fa-cogs" aria-hidden="true"></i> Suit Sizes:</td>
												<td>{porpertyDetail?porpertyDetail.SizeRange:''}</td>
											</tr>
											
											<tr>
												<td><i className="fa fa-usd" aria-hidden="true"></i> Price Range:</td>
												{porpertyDetail?<td>{!props.isLogin?"Complete Registration to Reveal Pricing":porpertyDetail.PriceRange}</td>:''}
											</tr>
											<tr>
												<td><i className="fa fa-calendar-o" aria-hidden="true"></i> Completion Year:</td>
												<td>{porpertyDetail?porpertyDetail.Completion:''}</td>
											</tr>
										</table>
									</div>
									<div className="col-md-6 PreConstructionDetilPage" id="Heighlight">
										<h4 className="pb-2">PROJECT HIGHLIGHTS</h4>
										<div dangerouslySetInnerHTML={{__html: porpertyDetail?porpertyDetail.Content:''}}></div>
									</div>
									
									<div className="col-md-12">
										<h3 className="featuredListingHeading">THE BUILDER</h3>
										
										<div dangerouslySetInnerHTML={{__html: builder?builder.BuilderDescription:''}} className="text-center aboutBuilder"></div>
										
										<div className="row pt-3">
											
											<div className="col-md-12 builderLogo">
												<center>
													{builder && builder.Logo && 
													<img src={builder.Logo} className="preConLogo mb-4" />}
													{builder && builder.SecondLogo && 
													<img src={builder.SecondLogo} className="preConLogo mb-4" />}
													{builder && builder.ThirdLogo && 
													<img src={builder.ThirdLogo} className="preConLogo mb-4" />}
												</center>
											</div>
										

										</div>
										
									</div>

									<div className="col-md-12" id="MapView">
									<h3 className="featuredListingHeading mb-3">BUILDING LOCATION</h3>
										
										 <iframe
		                                    loading="lazy"
		                                    width="100%"
		                                    height="500"
		                                    className="iframMobile"
		                                    allowfullscreen
		                                    referrerpolicy="no-referrer-when-downgrade"
		                                    src={`https://www.google.com/maps?key=${ApiKey}&q=${porpertyDetail?porpertyDetail.Address:''}&output=embed`}>
		                                </iframe>
									</div>

									<div className="col-md-12 floorplan" id="FloorPlans">
									<h3 className="featuredListingHeading mb-3">FLOOR PLANS</h3>
									<ReactCarousel show={showCard}>
			                            {
			                                FloorPlans.map((item,key) => {
			                                	let login_re=<u onClick={props.togglePopUp} className={`join-signIn-toggle theme-color img-blur`}><svg viewBox="0 0 24 24" width="24" height="24" className="xs-ml1" aria-hidden="true"><path d="M18 8h-1V6A5 5 0 007 6v2H6a2 2 0 00-2 2v10c0 1.1.9 2 2 2h12a2 2 0 002-2V10a2 2 0 00-2-2zM9 6a3 3 0 116 0v2H9V6zm9 14H6V10h12v10zm-6-3c1.1 0 2-.9 2-2s-.9-2-2-2-2 .9-2 2 .9 2 2 2z"></path></svg> <u onClick={props.signInToggle} className={`join-signIn-toggle theme-color`}>
                                            Join</u> or <u onClick={props.signInToggle} className={`join-signIn-toggle red`}> Sign In</u> To Unlock All Floorplans</u>;
			                                    return (
			                                        <>
			                                        	{props.isLogin?<img src={item}
			                                                        
			                                            className={`preConsSlider floorPlanImg`} alt="" title="" loading="lazy" onClick={()=>viewPopup1(key)}/>:<><div className="position-relative floorPlanImage"><img src={item}
			                                                        
			                                            className={`preConsSlider floorPlanImg`} alt="" title="" loading="lazy" onClick={props.togglePopUp} className="filter"/>{login_re}</div>
                                            </>}
			                                           
			                                        </>)
			                                })
			                            }

			                        </ReactCarousel>	
										
                                        
									</div>

									<div className="col-md-12" id="Gallery">
										<h3 className="featuredListingHeading mb-3">GALLERY</h3>
										<ReactCarousel show={showCard}>
			                            {
			                                imageArray.map((item,key) => {
			                                    return (
			                                        <>
			                                        	{/*<Image
															src={item}
															layout={'responsive'}
															width={200}
															height={250}
															alt="blogs"
															objectFit={"cover"}
															placeholder="blur"
															blurDataURL={item}
															priority={true}
															quality='1'
															onClick={()=>viewPopup(key)}
														/> */}
			                                           <img src={item}
			                                                        
			                                            className={`preConsSlider precon_feature_image`} alt="" title="" loading="lazy" onClick={()=>viewPopup(key)}/>
			                                        </>)
			                                })
			                            }
			                        </ReactCarousel>
										 
									</div>
									<div className="col-md-12 videoPresentation" id="Video">
										<h3 className="featuredListingHeading mb-3">PROJECT PRESENTATION</h3>
										<div className="row">
											<div className="col-md-2"></div>
											<div className="col-md-8">
												<div dangerouslySetInnerHTML={{__html: porpertyDetail?porpertyDetail.VideoLink:''}} className="text-center aboutBuilder videoBox"></div>
											</div>
											<div className="col-md-2"></div>
										</div>

									</div>
									<div className="col-md-12 pt-2 staticSection">
										<div className="row">
											
											<div className="col-md-6 col-lg-6 col-12">
												<div className="withoutColorCard">
													<h3><strong>Platinum VIP</strong> Access & Incentives <strong>Program</strong></h3>
													<ul>
														
														<li><i className="fa fa-angle-right" aria-hidden="true"></i> Absolute First Access to Pricing & Floorplans</li>
														<li><i className="fa fa-angle-right" aria-hidden="true"></i> The Very Best Incentives & Promotions</li>
														<li><i className="fa fa-angle-right" aria-hidden="true"></i> First Access To Phase 1 Units For Best Selection </li>
														<li><i className="fa fa-angle-right" aria-hidden="true"></i> Lowest Phase 1 Pricing & Best Selection of Units</li>
														<li><i className="fa fa-angle-right" aria-hidden="true"></i> Extended Deposit Structure </li>
														<li><i className="fa fa-angle-right" aria-hidden="true"></i> Capped Development Levies</li>
														<li><i className="fa fa-angle-right" aria-hidden="true"></i> Right of Assignment</li>
														<li><i className="fa fa-angle-right" aria-hidden="true"></i> Free Lawyer Review of Your Purchase Agreement</li>
														<li><i className="fa fa-angle-right" aria-hidden="true"></i> Free Mortgage Agreements</li>

													</ul>
													<small>**Save Thousands $$$ Per Unit by Purchasing With First Access Phase 1 
													With Our Team of Platinum Brokers & Get The Best Selection of Units 
													Before The Public Release
													</small>
													<button type="submit" onClick={()=>scrollToEl('regForm')} class="common-btn search-btn btn w-100 mt-5">Register For More Info</button>
												</div>
											</div>
											<div className="col-md-6 col-lg-6 col-12 colorCard">
											
												<div className="text-center">
													<img src={porpertyDetail && porpertyDetail.BuildingLogo?porpertyDetail.BuildingLogo:logo.src} className="preConLogo" />
													<h4 className="pt-5 text-uppercase"><b>Sales Contact</b></h4>
													<h4 className="pt-4">Muhammad Ashiq</h4>
													<h5>647-500-777</h5>

													<h4 className="pt-4">Sajad Ahmed</h4>
													<h5>647-531-5555</h5>
													
													<h4 className="pt-4">Ashiq Hussain</h4>
													<h5>647-829-6336</h5>
													<h6 className="pt-4">precon@housen.ca</h6>
												</div>
											</div>

										</div>
									</div>

									<div className="preCon_menu menuBackgroundColor">
										<div className="container-fluid">
											<div className="row">
												<div className="col-lg-2 colmd-4 col-sm-6">
													<button className="btn" onClick={()=>scrollToEl('FloorPlans')}>
													FLOOR PLANS</button>
												</div>
												<div className="col-lg-2 colmd-4 col-sm-6">
													{porpertyDetail && porpertyDetail.Brochure!==null && porpertyDetail.Brochure!==""?<a className="btn" href={porpertyDetail.Brochure} target="_blank" download>
													BROCHURE</a>:<button className="btn" onClick={()=>scrollToEl('regForm')}>
													BROCHURE</button>}
												</div>
												<div className="col-lg-3 colmd-4 col-sm-6">
													{porpertyDetail && porpertyDetail.Feature_and_Finishes!==null && porpertyDetail.Feature_and_Finishes!==""?<a className="btn" target="_blank" href={porpertyDetail.Feature_and_Finishes} download>
													FEATURED & FINISHES</a>:<button className="btn" onClick={()=>scrollToEl('regForm')}>
													FEATURED & FINISHES</button>}
													
												</div>
												<div className="col-lg-3 colmd-4 col-sm-6" onClick={()=>scrollToEl('regForm')}>
													<button className="btn">
													VIP INCENTIVES</button>
												</div>
												<div className="col-lg-2 colmd-4 col-sm-6" onClick={()=>scrollToEl('regForm')}>
													<button className="btn">
													VIP PRICING</button>
												</div>
											</div>
										</div>
									</div>
								<div className="" id="regForm" style={{ "background": "url(" + banner + ")" }}>
								<div className="row">
									<div className="col-md-2"></div>
										<div className="col-md-8 col-lg-8 mb-4">
											<div className="card form-card">
											  	<div className="card-body">
											    <h4 className="card-title">REGISTER <br/>For Platinum Access</h4>
											    <hr/>
											    <div className="row" id="preConPageForm">
											    	<PreConRegister/>
											    	{/*<PopupForm handleClose={props.togglePopUp}/>*/}
											    </div>
											  </div>
											</div>
										</div>
									<div className="col-md-2"></div>

									</div>
									</div>
								</div>
								<div className="container mt-4">
										<div className="row">
											<div className="col-md-3 text-center">
												<img src={porpertyDetail && porpertyDetail.BuildingLogo?porpertyDetail.BuildingLogo:logo.src} className="preConLogo agentInfoLogo" />
											</div>
											<div className="col-md-3 text-center">
												<div className="card saleCard">
													<h4 className="card-title pt-2 pb-3"><small>Sales contact</small></h4>
												    <center><img className="card-img-top" src={avatar1.src} alt="Card image" /></center>
												    <div className="card-body">
												      <h4 className="card-title pt-2">Muhammad Ashiq </h4>
												      <p className="card-text">647-500-777</p>
												     
												    </div>
												</div>
											</div>
											<div className="col-md-3 text-center">
												<div className="card saleCard">
													<h4 className="card-title pt-2 pb-3"><small>Sales contact</small></h4>
												    <center><img className="card-img-top" src={avatar1.src} alt="Card image" /></center>
												    <div className="card-body">
												      <h4 className="card-title pt-2">Sajad Ahmed</h4>
												      <p className="card-text">647-531-5555</p>
												     
												    </div>
												</div>
											</div>
											<div className="col-md-3 text-center">
												<div className="card saleCard">
													<h4 className="card-title pt-2 pb-3"><small>Sales contact</small></h4>
												    <center><img className="card-img-top" src={avatar1.src} alt="Card image" /></center>
												    <div className="card-body">
												      <h4 className="card-title pt-2">Ashiq Hussain </h4>
												      <p className="card-text">647-829-6336</p>
												     
												    </div>
												</div>
											</div>
											<div className="col-md-12 text-center PreConShareOption pt-4">
												<a href='mailto:precon@housen.ca'><i className="fa fa-envelope"></i> precon@housen.ca</a>
												<ul>
												
													<li className="shareOption"><a href={facebook} className="footerSocialIcon" target="_blank"><i className="fa fa-facebook"></i></a></li>
											
													<li className="shareOption"><a href={twitter} className="footerSocialIcon" target="_blank"><i className="fa fa-twitter"></i></a></li>
													<li className="shareOption"><a href={instagram} className="footerSocialIcon" target="_blank"><i className="fa fa-instagram"></i></a></li>
													<li className="shareOption"><a href={youtube} className="footerSocialIcon" target="_blank"><i className="fa fa-youtube"></i></a></li>
													<li className="shareOption"><a href={linkedin} className="footerSocialIcon" target="_blank"><i className="fa fa-linkedin"></i></a></li>
												</ul>
											</div>
										</div>
										
								</div>
							</div>
						</div>
						<div className="bg-follow">
							<div className="col-md-12 text-center PreConShareOption">
												<ul>
													<li>Share this project :</li>
													<li onClick={fbook} className="shareOption"><i className="fa fa-facebook"></i></li>
													<li onClick={twitterShare} className="shareOption"><i className="fa fa-twitter"></i></li>
													<li onClick={pinterest} className="shareOption"><i className="fa fa-pinterest"></i></li>
												</ul>
											</div>
						</div>
						<div className="preCon_menu mb-2">
							<div className="container">
								<small className="text-center">*E.&O.E. Terms and Conditions Apply – 
								All information herein was gathered from sources both professional and
								 lay deemed to be reliable. <a href="/" className="text-white">Housen.ca</a>, RE/MAX Millennium Real Estate and
								  its representatives make no representation as to its accuracy and will
								   not be held responsible for any discrepancies. Prospective purchasers
								    are advised to verify all information herein. All renderings, pricing,
								     incentives and other information are subject to change by the builder &
								      developer without notice. We are independent Real Estate Brokers & We do 
								      not represent the builder & developer. 
								The content of this page is for your informational purposes only.</small>
							</div>
							
						</div>
					</div>
				</div>

				
	       <div>
		        
	       	<PopupModel show={show} selectedCarouselIndex = {selectedSingleImageCarousel} imageArray={imageArray} handleClose={viewPopup} className="imagesSlider"/>
	       	<PopupModel show={showFloor} selectedCarouselIndex = {selectedSingleImageCarousel} imageArray={FloorPlans} handleClose={viewPopup1}  className="floorSlider"/>

			</div>
			</>
		);
	

}
export default PreConstructionDetil;