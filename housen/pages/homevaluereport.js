import React, {useEffect,useState} from "react";
import Layout from '../components/Layout/Layout';
import Link from "next/link";
const HomeValueResult = (props) =>{
	const [searchQuery, setSearchQuery] = useState('');
	useEffect(() => {
		if(localStorage.getItem('googleSearch'))
		{
			setSearchQuery(localStorage.getItem('googleSearch'));
		}
	});
	return(
			<>
				<section className="homeValueResult">
				    <div className="container">
				        <div className="row justify-content-center align-items-center">
				            <div className="col-lg-12 homevauehead">
				               	<h1 className="text-center">HOME VALUATION REPORT</h1>
				               	<h5 className="text-center">{searchQuery}</h5>
				            </div>
				        </div>
				    </div>
				</section>
				<section className="">
				    <div className="container">
				        <div className="row justify-content-center align-items-center">
				        <div className="col-lg-3"></div>
				            <div className="col-lg-6">
				                <p className="homeValueMessage text-center">We currently have 717 buyers that
				                 are looking for homes like yours on this site.
				                 Talk to one of our local experts to find out how things
				                  are changing in your market.</p>
				            </div>
				            <div className="col-lg-3">
				                
				            </div>
				        </div>

				    </div>
				</section>
				<section className="homevauehead pt-5">
				    <div className="container">
				        <div className="row justify-content-center align-items-center">
				        <div className="col-lg-3"></div>
				            <div className="col-lg-6">
				                <h2 className="text-center"> We are working on your estimate for
				                 this address, and will send it to you shortly. Thank you.</h2>
				            </div>
				            <div className="col-lg-3">
				                
				            </div>
				        </div>
				        
				    </div>
				</section>
			</>
		);
	

}
export default HomeValueResult;