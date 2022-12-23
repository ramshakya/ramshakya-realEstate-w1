import React, {useEffect,useState} from "react";
import Link from "next/link";
import Constants from "../../constants/Global";
import API from "../../ReactCommon/utility/api";
import MapCard from "../Cards/PropertyCard";
import Pagination from "../../ReactCommon/Components/Pagination";
const TorontoSearches = (props) =>{
	const [search, setSearch] = useState('');
	const [flag,setFlag] = useState(true);
	const [listing,setListing] = useState([]);
	const [total,settotal] = useState("");
	const [currentPage,setCurrentPage] = useState(1);
	const [heading,setheading] = useState('');
	const [notFound,setnotFound] = useState('');
	useEffect(() => {
		if(localStorage.getItem('popularSearch'))
		{
			setSearch(localStorage.getItem('popularSearch'));
		}
		
	});
	useEffect(()=>{
		if(search){
			let searchParam = JSON.parse(search)
			setheading(searchParam.heading);
			let body = {
				'status':searchParam.status,
				'currentPage':currentPage,
				'limit':12,
				'city':searchParam.city,
				'propertytype':searchParam.propertytype,
				'subtype':searchParam.subtype,
			};
			
			API.jsonApiCall(Constants.base_url + "api/v1/services/PopularSearch",
		      body, "POST", null, { "Content-Type": "application/json" }
		    ).then((res) => {
		      	if(res.totalRecord){
		      		
		          	 setListing(res.result);
		          	 settotal(res.totalRecord);
		          	 setnotFound("");
		          	
		          	 
		          }	
		         else
		          	{

		          		setListing([]);	
			        	setnotFound("No Listing Found");
		          	}
		        
		    }).catch((e) => {

		    });
		}
		
	},[search,currentPage])
	function pageChange(e) {
		setCurrentPage(e);
	}
	return(
			<>
				<section className="error-404 section-padding">
				    <div className="container-fluid">
				        <div className="row">
				            <div className="col-md-12 col-lg-12">
				            	<h3 className="p-3">{heading}</h3>
				            </div>
				        </div>
				        <div className="row">
				        	<div className="col-lg-12">
				        		<p className="text-center">{notFound}</p>
				        	</div>
				        	{listing.map((res,index)=>{
				        		return(
				        		<>
				        	<div className="col-md-6 col-sm-6 col-12 col-lg-3 mb-4" key={index}>
				        		<MapCard item={res} key={index}
									showIsFav={true}
									openUserPopup={true}
									openLoginCb={props.togglePopUp}
									isLogin={props.isLogin}
									
								/>
							</div>
				        		</>
				        		)
				        	})}
				        <div className="col-md-12 col-lg-12">
				          <div className="d-flex justify-content-center ">
							{total > 1 &&
								<Pagination
									itemsCount={total}
									itemsPerPage={12}
									currentPage={currentPage}
									setCurrentPage={pageChange}
									alwaysShown={false}
								/>
							}
							</div>
						</div>
				        </div>
				    </div>
				</section>
			</>
		);
	

}
export default TorontoSearches;