import React, {useEffect,useState} from "react";
import Link from "next/link";
import API from "../ReactCommon/utility/api";

import Card from "../components/Cards/PreConstrcutionCard"
import Constants from "../constants/Global";
import Pagination from "../ReactCommon/Components/Pagination";
const Construction = (props) =>{
	const [LatestProperty,setLatestProperty] = useState([]);
	const [CommingSoonProperty,setCommingSoonProperty] = useState([]);
	const [currentPage,setcurrentPage] = useState(1);
	useEffect(() => {
		const body = { AgentId: Constants.agentId,currentPage:currentPage,limit:12,cityname:"",LatestOrComminSoon:1}
        const latest = async () => {
            const data = await API.jsonApiCall(Constants.base_url + "api/v1/services/global/preConstruction",
                body, "POST", {}

            );
            if(data){
            	 
		        setLatestProperty(data.result);   
            }
                       
        }
        const commingSoon = async () => {
			const body1 = { AgentId: Constants.agentId,currentPage:currentPage,limit:12,cityname:"",LatestOrComminSoon:0}

            const data1 = await API.jsonApiCall(Constants.base_url + "api/v1/services/global/preConstruction",
                body1, "POST", {}

            );
            if(data1){
            	 
		            setCommingSoonProperty(data1.result);         
            }
                       
        }
        latest();
        commingSoon();
	},[currentPage]);

	return(
			<>
				<div className="term_condition preconHeader">
					<h3 className="text-center">LOOKING FOR<br/> PRE-CONSTRUCTION <br/>HOME & CONDO INVESTMENTS?
					</h3>
					<p className="text-center text-white">Find The Hottest & Latest Pre-Construction Home & Condo Developments!</p>
				</div>
				<div className="container mb-5 preconContent">
					<div className="row">
						<div className="col-md-12 col-lg-12 mb-3">
							<h3 className="text-center text-bold">LATEST PROJECTS</h3>
						</div>
						
							
							{LatestProperty.map((item)=>{
								return(
									<>
									<div className="col-md-6 col-lg-3 col-sm-6 col-12">
										<Card 
											item={item}
											showIsFav={true}
					                        openUserPopup={true}
					                        openLoginCb={props.togglePopUp}
					                        isLogin={props.isLogin}
					                        LoginRequired={props.LoginRequired}
										/>
										</div>
									</>
								
							)
							})}
						
						<div className="col-md-12 col-lg-12 mb-3 mt-5 text-center">
							<Link href="/projects"><a className="common-btn search-btn btn ">See All Projects</a></Link>
						</div>
					</div>
					<div className="row">
						<div className="col-md-12 col-lg-12 mb-3 mt-5">
							<h3 className="text-center text-bold">COMING SOON</h3>
						</div>
						
							
							{CommingSoonProperty.map((item)=>{
								return(
									<>
									<div className="col-md-6 col-lg-3 col-sm-6 col-12">
										<Card 
											item={item}
											showIsFav={true}
					                        openUserPopup={true}
					                        openLoginCb={props.togglePopUp}
					                        isLogin={props.isLogin}
					                        LoginRequired={props.LoginRequired}
										/>
										</div>
									</>
								
							)
							})}
						
						<div className="col-md-12 col-lg-12 mb-3 mt-5 text-center">
							<Link href="/projects"><a className="common-btn search-btn btn ">See All Projects</a></Link>
						</div>
					</div>
				</div>
			</>
		);
	

}
export default Construction;