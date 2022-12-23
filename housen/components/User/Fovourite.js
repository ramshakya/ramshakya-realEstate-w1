// import FavCard from "../Card/FavCard";
import React, {useEffect,useState} from "react";
import Constants from '../../constants/Global';
// import Card from "../../ReactCommon/Components/Card";
import MapCard from "../Cards/PropertyCard"
const extra_url = Constants.extra_url;
const base_url = Constants.base_url;
// import {requestToAPI} from "../../pages/api/api";

const Favourite =(props)=>{
	// const [token, setToken] = useState(false);
	const [favouriteData, setFavouriteData] = useState([]);
	const [userDetails,setUserDetails] = useState([]);
	const [flag,setFlag] = useState(false);
    useEffect(() => {   

           const fetch_favourite = async () => {
           	       let localStorageData=localStorage.getItem('userDetail');
			localStorageData = JSON.parse(localStorageData);
			let id = localStorageData.login_user_id;
			let token = localStorage.getItem('login_token');

           	 	const body= JSON.stringify({LeadId: id,AgentId:Constants.agentId});
           		const requestOptions = {
	            		method: 'POST',
	            		headers: { 'Content-Type': 'application/json','Authorization': `Bearer ${token}` },
	            		body: body
	        	};
	        	let page = "global/GetFavouriteProperty";
	        	let urls = extra_url+page;
	        	fetch(urls, requestOptions).then((response) =>
	                response.text()).then((res) => JSON.parse(res))
	                .then((json) => {
	                	setFavouriteData(json)
	                	}).catch((err) => console.log({ err }));
           };
           fetch_favourite();
			let localStorageData = localStorage.getItem('userDetail');
			localStorageData = JSON.parse(localStorageData).favourite_properties
			if (localStorageData && userDetails.length!==localStorageData.length){
				setUserDetails(localStorageData);
			}
       },[]);
    const handleFavApiCall =  (reqBody) => {
		let localStorageData = localStorage.getItem('userDetail');
		localStorageData = JSON.parse(localStorageData).favourite_properties
		setUserDetails(localStorageData);
		setFlag(!flag);
	}
	return(
			<div>
				<div className="accountsetting">
					<div className="row">

						{favouriteData.map((item,index) => {
							if (userDetails.indexOf(item.ListingId) === -1){
								return null;
							}
							return (
								<div className="col-lg-4 mb-2" key={index}>
									<MapCard item={item} key={index} showIsFav={true}
											 openUserPopup={true}
											 openLoginCb={props.togglePopUp}
											 isLogin={props.isLogin}
											 checkFavApiCall={handleFavApiCall}
									/>
								</div>
							)
						})}

					</div>
				</div>
			</div>
		);
}
export default Favourite;