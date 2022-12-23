import {Tab,Row,Col,Nav,Form,Button,Table} from "react-bootstrap";
import React, {useEffect,useState} from "react";
// import {requestToAPI} from "../../pages/api/api";
import Constants from '../../constants/Global';
const extra_url = Constants.extra_url;
const base_url = Constants.base_url;
import Pagination from "../../ReactCommon/Components/Pagination";

import Loader from '../../components/loader/loader';

const LoginInfo =()=>{
	const [loginDate, setLoginDate] = useState([]);
	const [loginTime, setLoginTime] = useState([]);
	const [currentPage, setCurrentPage] = useState(1);
	const [total, setTotal] = useState(1);
	const [totalPage, setTotalPage] = useState(1);
	const [preLoader,setPreloader] = useState(false);

	useEffect(() => {        
           const Login_info = async () => {
	           	let localStorageData=localStorage.getItem('userDetail');
				localStorageData = JSON.parse(localStorageData);
				let id = localStorageData.login_user_id;
				let token = localStorage.getItem('login_token');

           	 	const body= JSON.stringify({ id: id,currentPage:currentPage,limit:10});
           		const requestOptions = {
	            		method: 'POST',
	            		headers: { 'Content-Type': 'application/json','Authorization': `Bearer ${token}` },
	            		body: body
	        	};
	        	let page = "loginInfo";
	        	let urls = extra_url+page;
	        	fetch(urls, requestOptions).then((response) =>
	                response.text()).then((res) => JSON.parse(res))
	                .then((json) => {
	                	setLoginDate(json.login_date);
    	                	setLoginTime(json.login_time);
    	                	setTotal(json.total);
                    		setTotalPage(json.totalPages);
                    		setCurrentPage(json.currentPage);
                    		setPreloader(true);
	                }).catch((err) => console.log({ err }));
    //        		let localStorageData=localStorage.getItem('userDetail');
				// localStorageData = JSON.parse(localStorageData);
				// let id = localStorageData.login_user_id;
				// let token = localStorage.getItem('login_token');
		           	
    //                 const body= JSON.stringify({ id: id})
    //                 const json = await requestToAPI(body,"services/loginInfo","POST",token);
    //                 setLoginDate(json.login_date);
    //                 setLoginTime(json.login_time);
           };
           Login_info();
           
       },[currentPage]);
       const pageChange=(e)=> {
       setCurrentPage(e);
       setPreloader(false);
    }
    let srno=1;

	return(
			<div>
				<Table striped bordered hover>
				  <thead>
				    <tr>
				      <th>Sr. no</th>
				      <th>Login Date</th>
				      <th>Login Time</th>
				    </tr>
				  </thead>
				  <tbody>
				 {loginDate.map((item, key)=>{
				 	srno=((currentPage-1)*10)+key+1;
				 	return(
					    <tr key={key}>
					      <td>{srno}</td>
					      <td>{item}</td>
					      <td>{loginTime[key]}</td>
					      
					    </tr>
				 	);
				 })}
				    
				    
				  </tbody>
				</Table>
				<div className="col-md-12 col-lg-12">
                 
                  			<div className="d-flex justify-content-center">
                                <Pagination
                                    itemsCount={total}
                                    itemsPerPage={10}
                                    currentPage={currentPage}
                                    setCurrentPage={pageChange}
                                    alwaysShown={false}
                                />
                            </div>
		                </div>
		                {!preLoader &&
		                	<Loader/>
		                }
		                
			</div>
		);
}
export default LoginInfo;