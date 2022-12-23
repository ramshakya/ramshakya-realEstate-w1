import Link from "next/link";
import { useRouter } from 'next/router'
import { useEffect, useState } from "react";
const PopularSearches = (props) => {
	const router = useRouter()

	function gotosearch(city, subtype, status, propertytype, heading, isAddress = false) {
		// gotosearch(item.city, item.type, item.status, '', item.text)
		status=status?status:"Sale"
		let Searches = {
			'city':city,
			'subtype':subtype,
			'status':status,
			'propertytype':propertytype,
			'heading':heading
		}
		let statusFlag=status==="Sold"?"U":"A";
		let types=subtype==="Condo Apt"?[subtype]:["Detached","Semi-Detached","Freehold Townhouse","Condo Townhouse","Condo Apt"];
		console.log("==========status=====",Searches);
		let filters = {
			searchFilter: {},
			preField: {}
		}
		let field = "";
		let params="/map?";
		let group="";
		if (isAddress) {
			field = { text: city, value: city, category: 'Neighborhood', group: 'Community' }
			group="Community";
		} else {
			field = { text: city, value: city, category: 'Cities', group: 'City' }
			group="City";
		}
		params+=`text_search=${city}&propertySubType=${types}&propertyType=Residential&status=${status}&Dom=90&soldStatus=${statusFlag}&group=${group}`
		filters.preField.text_search = field
		filters.searchFilter.text_search = city
		localStorage.setItem('filters', JSON.stringify(filters));
		localStorage.setItem('status', status);
		localStorage.setItem('isPopular', true);
		localStorage.setItem('propertytype', propertytype);
		localStorage.setItem('subtype', subtype);
		// props.popularSearch(true);
		router.push(params);
	}


	const [cityname, setCityname] = useState("Toronto");
	useEffect(() => {
		setCityname(props.checkCity)
	}, [props.checkCity])
	const list =  [
		{isCommunity:false,text:`${cityname?cityname:'Toronto'} Condos for sale`,type:"Condo Apt",status:"",city:`${cityname?cityname:'Toronto'}`},
		{isCommunity:false,text:`${cityname?cityname:'Toronto'} Townhouses for sale`,type:"Freehold Townhouse,Condo Townhouse",status:"",city:`${cityname?cityname:'Toronto'}`},
		{isCommunity:false,text:`${cityname?cityname:'Toronto'} Sold Prices`,type:"",status:"Sold",city:`${cityname?cityname:'Toronto'}`},
		{isCommunity:true,text:`${cityname?cityname:'Downtown Toronto'} Condos`,type:"Condo Apt",status:"",city:`${cityname?cityname:'Downtown'}`},
		{isCommunity:false,text:`${cityname?cityname:'North York'} Condos for sale`,type:"Condo Apt",status:"",city:`${cityname?cityname:'North York'}`},
		{isCommunity:false,text:`${cityname?cityname:'North York'} houses for sale`,type:"Detached,Semi-Detached,Freehold Townhouse",status:"",city:`${cityname?cityname:'North York'}`},
		{isCommunity:false,text:`${cityname?cityname:'Etobicoke'} homes for sale`,type:"Detached,Semi-Detached,Freehold Townhouse",status:"",city:`${cityname?cityname:'Etobicoke'}`},
		{isCommunity:false,text:`${cityname?cityname:'Scarborough'} homes for sale`,type:"Detached,Semi-Detached,Freehold Townhouse",status:"",city:`${cityname?cityname:'Scarborough'}`},
		{isCommunity:false,text:`Condos for Rent in ${cityname?cityname:'Toronto'}`,type:"Condo Apt",status:"Lease",city:`${cityname?cityname:'Toronto'}`},
		{isCommunity:false,text:`Houses for Rent in ${cityname?cityname:'Toronto'}`,type:"Detached,Semi-Detached,Freehold Townhouse",status:"Lease",city:`${cityname?cityname:'Toronto'}`}
	];
	return (
		<>
			<p>POPULAR SEARCHES</p>
			<ul className="footer-link">
				{list.map((item,k)=>{
					return(
						<li key = {k} className="capitalized-text">
							<a href="javascript:void(0)"
							 onClick={() => gotosearch(item.city, item.type, item.status, '', item.text,item.isCommunity)}>
							 {item.text}
							  
							</a>
						</li>
					)
				})}
			</ul>
		</>
	)
}
export default PopularSearches;


{/*<li><a href="javascript:void(0)" onClick={() => gotosearch(cityname ? cityname : 'Toronto', '', 'Sale', '', cityname ? cityname : 'Toronto' + ' Homes for Sale')}>{cityname ? cityname : 'Toronto'} Homes for Sale</a></li>
				<li><a href="javascript:void(0)" onClick={() => gotosearch(cityname ? cityname : 'Toronto', '', 'Sale', '', cityname ? cityname : 'Toronto' + ' Houses for Sale')}>{cityname ? cityname : 'Toronto'} Houses for Sale</a></li>
				<li><a href="javascript:void(0)" onClick={() => gotosearch(cityname ? cityname : 'Toronto', '', 'Sale', 'Condos', cityname ? cityname : 'Toronto' + ' Condos for Sale')}>{cityname ? cityname : 'Toronto'} Condos for Sale</a></li>
				<li><a href="javascript:void(0)" onClick={() => gotosearch(cityname ? cityname : 'Toronto', '', 'Sale', 'Townhouses', cityname ? cityname : 'Toronto' + ' Townhouses for Sale')}>{cityname ? cityname : 'Toronto'} Townhouses for Sale</a></li>
				<li><a href="javascript:void(0)" onClick={() => gotosearch(cityname ? cityname : 'Toronto', '', 'Sold', '', cityname ? cityname : 'Toronto' + ' Sold Prices Milton')}>{cityname ? cityname : 'Toronto'} Sold Prices Milton</a></li>
				<li><a href="javascript:void(0)" onClick={() => gotosearch(cityname ? cityname : 'Downtown', '', '', 'Condos', cityname ? cityname : 'Downtown toronto' + ' Condos', true)}>{cityname ? cityname : 'Downtown Toronto'} Condos</a></li>
				<li><a href="javascript:void(0)" onClick={() => gotosearch(cityname ? cityname : 'North York', '', 'Sale', 'Condos', cityname ? cityname : 'North York' + 'Condos For Sale')}>{cityname ? cityname : 'North York'} Condos For Sale</a></li>
				<li><a href="javascript:void(0)" onClick={() => gotosearch(cityname ? cityname : 'North York', '', 'Sale', '', cityname ? cityname : 'North York' + ' Houses For Sale')}>{cityname ? cityname : 'North York'} Houses For Sale</a></li>
				<li><a href="javascript:void(0)" onClick={() => gotosearch(cityname ? cityname : 'Etobicoke', '', 'Sale', '', cityname ? cityname : 'North York' + ' Homes For Sale',true)}>{cityname ? cityname : 'Etobicoke'} Homes For Sale</a></li>
				<li><a href="javascript:void(0)" onClick={() => gotosearch(cityname ? cityname : 'Scarborough', '', 'Sale', '', cityname ? cityname : 'North York' + ' Homes for Sale',true)}>{cityname ? cityname : 'Scarborough'} Homes for Sale</a></li>
				<li><a href="javascript:void(0)" onClick={() => gotosearch(cityname ? cityname : 'Toronto', '', 'Lease', 'Condos', 'Condos for Rent in ' + cityname ? cityname : 'Toronto')}>Condos for Rent in {cityname ? cityname : 'Toronto'}</a></li>
				<li><a href="javascript:void(0)" onClick={() => gotosearch(cityname ? cityname : 'Toronto', '', 'Lease', 'Condos', 'Houses for Rent in ' + cityname ? cityname : 'Toronto')}>Houses for Rent in {cityname ? cityname : 'Toronto'}</a></li>
			*/}