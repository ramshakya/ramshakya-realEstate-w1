import React, {useEffect,useState} from "react";
import Layout from '../components/Layout/Layout';
import Link from "next/link";
import AgentCard from '../components/Card/AgentCard';
import Constants from '../constants/GlobalConstants';
import SearchStaff from '../components/Forms/SearchStaff';
const extra_url = Constants.extra_url;
import API from "../ReactCommon/utility/api";
import Loader from '../components/loader/loader';
const agentList = (props) =>{
	const [records, setRecords] = useState([]); 
	const [total, setTotal] = useState(''); 
	const [currentPage, setCurrentPage] = useState(1); 
	const [totalPage, setTotalPage] = useState(''); 
	const [agentName, setAgentName] = useState(''); 
	const [dataFlag,steDataFlag] = useState(false);
	const [Staffimages, setStaffimages] = useState([]); 
	useEffect(() => { 
		steDataFlag(true);
			let body = JSON.stringify({
				currentPage:currentPage,
				AgentId:Constants.agentId,
				agentName:agentName
			});
			API.jsonApiCall((extra_url+'GetStaffs'), body, "POST", null, {
            "Content-Type": "application/json",
	        }).then((res) => {
	          	setRecords(res.records);
	          	setStaffimages(res.images);
	          	setTotal(res.total);
	          	setTotalPage(res.totalPages);

	       });
	       steDataFlag(false);
		},[currentPage,agentName]);
	const handleClick =(event)=>{
		if(event.target.id==="prev")
		{
			setCurrentPage(currentPage-1);
		}
		else if(event.target.id==="next")
		{
			setCurrentPage(currentPage+1);
		}
	}
	const handleSubmit=(event)=>{
		setAgentName(event);
		setCurrentPage(1);
	}
	let agentImage = "../images/avatar.jpg";
	return(
			<>
				<section className="section-padding">
				    <div className="container">
				       <div className="row">
				       	
				       		<div className="col-md-4 col-lg-4">
				       		<h2 className="mb-3">Wedu.ca Agents</h2>
				       			<SearchStaff handleSubmitData = {handleSubmit}/>

				       		</div>
				       		<div className="col-lg-8 col-lg-8 pt-2">

				       			<div className="row mb-3">
				       				<div className="col-md-6 col-lg-6">
				       					<h5>Found {total} results</h5>
				       				{(currentPage!==1) &&
				       					<span onClick={handleClick} id="prev" className="next-prev-btn" disabled="true"><i className="fa fa-angle-double-left" aria-hidden="true"></i> Previous</span>
				       				}
				       				</div>
				       				<div className="col-md-6 col-lg-6 right-side-element">
				       					<h5>Page {(totalPage===0) && '0'}{(totalPage!==0) && currentPage} of {totalPage}</h5>
				       				{(currentPage!==totalPage) && (totalPage!==0) &&
				       					<span onClick={handleClick} className="next-prev-btn" id="next">Next <i className="fa fa-angle-double-right" aria-hidden="true"></i></span>
				       				}
				       				</div>
				       			</div>
				       		{records.map((item)=>{
				       			let staffId = item.id
				       			let name = item.name;
				       			if(Staffimages && Staffimages[staffId]!==null){
				       				agentImage = Staffimages[staffId]
				       				
				       			}
				       			return (
				       				<AgentCard 
				       				image={agentImage} 
				       				name={name}
				       				url={'agents/'+staffId}
				       				buttonName={'View Agent'}
				       				/>
				       				);
				       		})}
				       		</div>	
				       </div>
				    </div>
				</section>

				{dataFlag &&
					<Loader />
				}
			</>
		);
}
export default agentList;
// git check