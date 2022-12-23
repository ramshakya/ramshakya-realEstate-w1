import React, {useEffect,useState} from "react";
import Link from "next/link";
import API from "../ReactCommon/utility/api";

import Card from "../components/Cards/PreConstrcutionCard"
import Constants from "../constants/Global";
import Pagination from "../ReactCommon/Components/Pagination";
const PreConstruction = (props) =>{
	const [porperty,setProperty] = useState([]);
	const [city,setCity] = useState([]);
	const [cityname,setCityname] = useState("");
	const [currentPage,setcurrentPage] = useState(1);
	const [totalData,settotalData] = useState('');
	useEffect(() => {
		const body = { AgentId: Constants.agentId,currentPage:currentPage,limit:12,cityname:cityname}
        const getConstruction = async () => {
            const data = await API.jsonApiCall(Constants.base_url + "api/v1/services/global/preConstruction",
                body, "POST", {}

            );
            if(data){
            	 
		            setProperty(data.result);
		            setCity(data.City);
		            settotalData(data.totalRecord);
            }
            else
            {
            	setProperty([]);
		        setCity([]);
		        settotalData("");
            }
           

        }
        getConstruction();
	},[currentPage,cityname]);
	
	function handleChange(str) {
	    if (str !== 0) {
	      setcurrentPage(str);
	      
	    }
	  };
	function getCityname(e){
		setcurrentPage(1);
		setCityname(e.target.title);
		let el = e.target;
		let all = document.getElementsByTagName('li');
		for (var i = 0; i < all.length; i++) {
			all[i].classList.remove('active');
		}
		el.classList.add('active');
		// console.log(e.target);
	}
	return(
			<>
				
				<div className="term_condition preconHeader projects">
					<h3 className="text-center">New Condo & Houses Developments
					</h3>
				</div>
				<div className="container mb-5">
					<div className="row">
						<div className="col-md-12 col-lg-12 mb-3">
							<ul className="preConstruction">
							<li onClick={getCityname} title={''} className="active">
								All
							</li>
							{city.map((item,key)=>{
								return(
									<>
									<li key={key} onClick={getCityname} title={item.City} className="">
										{item.City}
									</li>
									</>
									)
							})}
								
							
							</ul>
						</div>
						
							
							{porperty.map((item)=>{
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
						
						
						<div className="col-md-12">
							 <div className="d-flex justify-content-center ">
					           <Pagination
					                itemsCount={totalData}
					                itemsPerPage={12}
					                currentPage={currentPage}
					                setCurrentPage={handleChange}
					                alwaysShown={false}
					              />
					          </div>
						</div>
					</div>
				</div>
			</>
		);
	

}
export default PreConstruction;