import React, { useState, useEffect, useRef } from 'react';
import PopularSearches from "./PopularSearches";
import PopularCities from "./PopularCities";
import Link from "next/link";
import { domToReact } from 'html-react-parser';
import { useRouter } from 'next/router';
import detect from '../../ReactCommon/utility/detect';
const Footer = (props) => {
	const router = useRouter();
	const [flag, setFlag] = useState(true);
	const { webEmail, phoneNo, websiteAddress, facebook, twitter, linkedin, instagram, youtube, websiteName, logo, logoAlt } = props;

	useEffect(() => {
		function addScript() {
			const script = document.createElement("script");
			script.src = "//widget.manychat.com/107973050889440.js";
			script.defer = 'defer';
			document.head.appendChild(script);
			const script2 = document.createElement("script");
			script2.src = "https://mccdn.me/assets/js/widget.js";
			script2.defer = 'defer';
			document.head.appendChild(script2);
			setFlag(false);
		}
		if (flag) {
			//   addScript();
		}
	}, [flag]);
	function bookAShowing() {
		var myElement = document.getElementById('scheduleTime-v2');
		var topPos = myElement.offsetTop;
		console.log('topPos',topPos);
		window.scrollTo({
			top: topPos+110,
			behavior: "smooth",
		});
	}
	function contactAgent() {
		var myElement = document.getElementById('contactForm-v2');
		var topPos = myElement.offsetTop;
		window.scrollTo({
			top: topPos+230,
			behavior: "smooth",
			
		});
	}
	function redirectToBlog(str,active)
	{
		props.popularSearch(true);
		localStorage.setItem("category", str);
		localStorage.setItem("activeCls", active);
		router.push('/blogs');
	}
	let blogsCat = [
			{
				text: "Home",
				value: "HOME"
			},
			{
				text: "Market News",
				value: "MARKET NEWS"
			},
			{
				text: "For Buyers",
				value: "FOR BUYERS"
			},
			{
				text: "For Sellers",
				value: "FOR SELLERS"
			},
			{
				text: "For Renters",
				value: "FOR RENTERS"
			},
			{
				text: "Pre-Construction",
				value: "PRE CONSTRUCTION"
			},
			{
				text: "Free Guides",
				value: "FREE GUIDES"
			}
		];
	return (
		<>
			{
				props.showFooterBtn &&
				<div className="container footer-btns " >
					<div className="row p-2 pb-3 btns-bottom" >
						<div className="col-6  ">
							<button className='btn showSchedule' onClick={bookAShowing}>Book A Showing</button>
						</div>
						<div className="col-6 ">
							<button className='btn showSchedule' onClick={contactAgent}>Contact Agent</button>
						</div>
					</div>
				</div>
			}
			<footer className="footer" id='footers' >
				<div className="container-fluid p-5 pt-0">

					<span id="end-section-div"></span>
					<div className="row footer-list ">
						<div className="col-md-2">
							<Link href="/"><a><img src={logo} alt={logoAlt} className="footer_logo" /></a></Link>
						</div>
						<div className="col-md-3 col-6 popularSearch text-center">
							<PopularSearches popularSearch={props.popularSearch} checkCity={props.checkCity} checkCityChange={props.checkCityChange} />

						</div>
						<div className="col-md-3 col-6 popularSearch text-center">
							<PopularCities popularSearch={props.popularSearch} />
						</div>
						<div className="col-md-2 col-6 text-center">
							<p>BLOG</p>
							<ul className="footer-link">
							{blogsCat.map((item,key)=>{
								return(
									<li key={key}><Link href={`/blog/${item.value}`}><a>{item.text}</a></Link></li>
									)
							})}
								{/*<li><a href="javascript:void(0)" onClick={()=>redirectToBlog('',0)}>Home</a></li>
								<li><a href="javascript:void(0)" onClick={()=>redirectToBlog('MARKET NEWS',1)}>Market News</a></li>
								
								<li><a href="javascript:void(0)" onClick={()=>redirectToBlog('FOR BUYERS',2)}>For Buyers</a></li>
								<li><a href="javascript:void(0)" onClick={()=>redirectToBlog('FOR SELLERS',3)}>For Sellers</a></li>
								<li><a href="javascript:void(0)" onClick={()=>redirectToBlog('FOR RENTERS',4)}>For Renters</a></li>
								<li><a href="javascript:void(0)" onClick={()=>redirectToBlog('PRE-CONSTRUCTION',5)}>Pre-Construction</a></li>
								<li><a href="javascript:void(0)" onClick={()=>redirectToBlog('FREE GUIDES',6)}>Free Guides</a></li>
								*/}
							</ul>
						</div>
						<div className="col-md-2 col-6 text-center">
							<p>HOUSEN.CA</p>
							<ul className="footer-link text-center">
								{/* <li><a href="javascript:void(0)">Join Us</a></li> */}
								<li><Link href="/aboutUs"> About Us </Link></li>
								<li><Link href="/ContactUs">Contact Us</Link></li>
								<li><a href={'tel:' + phoneNo}>{phoneNo}</a><br />
									{websiteAddress}
								</li>
								<li className="pt-3">
									<a href={facebook} className="footerSocialIcon" target="_blank"><i className="fa fa-facebook"></i></a>
									<a href={twitter} className="footerSocialIcon" target="_blank"><i className="fa fa-twitter"></i></a>
									<a href={instagram} className="footerSocialIcon" target="_blank"><i className="fa fa-instagram"></i></a>
									<a href={linkedin} className="footerSocialIcon" target="_blank"><i className="fa fa-linkedin"></i></a>

								</li>

							</ul>
						</div>
					</div>
					<div className="row">
						<div className="col-md-2"></div>
						<div className="col-md-8 text-center">
							<ul className="footer-credits">
								<li><Link href="/terms-and-conditions"><a>Terms and Conditions</a></Link></li>
								<li><Link href="/privacy-policy"><a>Privacy Policy</a></Link></li>
								<li><Link href="/cookies-policy"><a>Cookies Policy</a></Link></li>

								<li><a href="javascript:void(0)">Copyright © 2022 {websiteName}. All rights reserved</a></li>
							</ul>
						</div>
						<div className="col-md-2"></div>
					</div>
				</div>
			</footer>
		</>
	)
}
export default Footer;
{/* <script 
src="//widget.manychat.com/107973050889440.js" defer="defer"></script>
<script src="https://mccdn.me/assets/js/widget.js" defer="defer"></script> */}