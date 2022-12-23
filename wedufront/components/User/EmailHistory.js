import {Tab,Row,Col,Nav,Form,Button,Table} from "react-bootstrap";
import React, {useEffect,useState} from "react";
import {requestToAPI} from "../../pages/api/api";
// import Pagination from '@material-ui/lab/Pagination';
import Pagination from "../../ReactCommon/Components/Pagination";
import Loader from '../../components/loader/loader';
import {agentId} from '../../constants/GlobalConstants'
import Modal from 'react-bootstrap/Modal'
import {toast } from 'react-toastify';
import trash from "../../public/images/icons/trash.png";
import question from "../../public/images/icons/question.png";

const EmailHistory =()=>{
	const [currentPage, setCurrentPage] 		= useState(1);
	const [total, setTotal] 					= useState(1);
	const [totalPage, setTotalPage] 			= useState(1);
	const [preLoader,setPreloader] 				= useState(false);
	const [savedData,setSavedData] 				= useState([]);
	const [flag,setFlag] 						= useState(true);
	const [lgShow, setLgShow] 					= useState(false);
	const [modelData, setModelData] 			= useState(0);
	const [content,setContent] 					= useState('');
	const [alertName,setAlertName] 				= useState([]);
	const [lgShowDetail, setLgShowDetail] 		= useState(false);
	const [alertDetail, setAlertDetail] 		= useState([]);

	useEffect(() => {  
	console.log("yes",currentPage);      
           const getSavedSearch = async () => {
           		let localStorageData=localStorage.getItem('userDetail');
				localStorageData = JSON.parse(localStorageData);
				let id = localStorageData.login_user_id;
				let token = localStorage.getItem('login_token');
		           	
                    const body= JSON.stringify({ userId: id,currentPage:currentPage,limit:10,agentId:agentId});
                    try {
						const json = await requestToAPI(body,"sentEmailHistory","POST",token);
						setSavedData(json.emailHistory);
						setTotal(json.total);
						setTotalPage(json.totalPages);
						setCurrentPage(json.currentPage);
						setAlertName(json.alertsName);
						setPreloader(true);
						setFlag(false);
					} catch (error) {
						
					}
           };
           if(flag){
           	 getSavedSearch();
           }
           
           
       },[currentPage,flag]);
	const pageChange=(e)=> {
       setCurrentPage(e);
       setPreloader(false);
       setFlag(true);
    }

    function showMore(str)
    {
    	setLgShow(true);
    	setModelData(str)
    }
    // console.log("modal",modelData);
    function deleteFunction(str){
    	setSmShow(true);
    	setDeleteId(str);
    }
    const DeleteConfirm=async(deleteId)=>{
    	// console.log('data',deleteId);
    	if(deleteId!==null)
    	{
    		let localStorageData=localStorage.getItem('userDetail');
			localStorageData = JSON.parse(localStorageData);
			let id = localStorageData.login_user_id;
			let token = localStorage.getItem('login_token');

    		const body= JSON.stringify({ delId: deleteId,userId:id,agentId:agentId});
            const json = await requestToAPI(body,"deleteSavedSearch","POST",token);
            if(json.success){
            	toast.success(json.success);
            	setFlag(true);
            	
            }
            else
            {
            	toast.error(json.error);
            }
    	}
    	// else
    	// {
    	// 	setFlag(true);
     //        setSmShow(false);
    	// }
    }

    function getDetail(str){
    	setContent(str);
    	setLgShow(true);
    }
   	
   	const getAlertDetail=async(alertId)=>{
   		setPreloader(false);
   		if(alertId!==null)
    	{
    		const body= JSON.stringify({ alertId: alertId});
            const json = await requestToAPI(body,"getSavedSearchDetail","POST");
            if(json.alertDetail){
            	setAlertDetail(json.alertDetail);
            	setLgShowDetail(true);
            	setPreloader(true);
            }
            else
            {
            	setAlertDetail([]);
            	setLgShowDetail(true);
            	setPreloader(true);
            }
   		}
   	
   }
    let srno=1;

	return(
		<>
			<div>
				<Table striped bordered hover>
				  <thead>
				    <tr>
				      <th>Sr. no</th>
				      <th>Search Name</th>
				      <th>Email</th>

				      <th>Alert Frequency</th>
				      
				      <th>Sent date & time</th>
				      <th>Content</th>
				    </tr>
				  </thead>
				  <tbody>
				 {savedData.map((item, key)=>{
				 	srno=((currentPage-1)*10)+key+1;
				 	let sentDate=item.sentAt;
				 	let alertId = item.alertId;
				 	// console.log("alert",alertName[alertId]); 	
				 	return(
					 	<tr key={key}>
					      <td>{srno}</td>
					      <td><span className="hoverAble" onClick={()=>getAlertDetail(alertId)}>{alertName[alertId]}</span></td>
					      <td>{item.toEmail}</td>
					      <td>{item.alertFreq}</td>
					      <td>{sentDate}</td>
					      <td><img src={question.src} alt="view more" className="hoverAble" onClick={()=>getDetail(item.emailContent)}  width="25px"  title="See detail"/></td>
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
		      <Modal
		        size="lg"
		        show={lgShow}
		        onHide={() => setLgShow(false)}
		        aria-labelledby="example-modal-sizes-title-lg"
		        className="viewMoreModal"
		      >
		        <Modal.Header closeButton>
		          <Modal.Title id="example-modal-sizes-title-lg">
		           	Mail body
		          </Modal.Title>
		        </Modal.Header>
		        <Modal.Body>
		        	<div className="row">
		        		<div dangerouslySetInnerHTML={{__html: content}}></div>		        		
		        	</div>
		        </Modal.Body>
		      </Modal>

		      <Modal
		        size="lg"
		        show={lgShowDetail}
		        onHide={() => setLgShowDetail(false)}
		        aria-labelledby="example-modal-sizes-title-lg"
		        className="viewMoreModal"
		      >
		        <Modal.Header closeButton>
		          <Modal.Title id="example-modal-sizes-title-lg">
		           	Search Detail
		          </Modal.Title>
		        </Modal.Header>
		        <Modal.Body>
		        	<div className="row">
		        		<div className="col-md-12">
			        		<table className="detailTable">
			        			<tr>
			        				<td>Search Name : <span className="text-normal">{alertDetail.filterName}</span></td>
			        				<td>Frequency : <span className="text-normal">{alertDetail.frequency}</span></td>
			        				<td>Sub type : <span className="text-normal">{alertDetail.subClass}</span></td>
			        			</tr>

			        			<tr>
			        				<td>Bedrooms : <span className="text-normal">{alertDetail.bedsTotal}</span></td>
			        				<td>Bathrooms : <span className="text-normal">{alertDetail.bathsFull}</span></td>
			        				<td>Style : <span className="text-normal">{alertDetail.style}</span></td>
			        			</tr>

			        			<tr>
			        				<td>Garage Type : <span className="text-normal">{alertDetail.GarType}</span></td>
			        				<td>Lot Size Area Max : <span className="text-normal">{alertDetail.lotSizeAreaMax} - {alertDetail.lotSizeAreaMax}</span></td>
			        				<td>Text search : <span className="text-normal">{alertDetail.textSearch}</span></td>
			        			</tr>

			        			<tr>
			        				<td>Keywords : <span className="text-normal">{alertDetail.keywords}</span></td>
			        				<td>City : <span className="text-normal">{alertDetail.city}</span></td>
			        				<td>Country Name : <span className="text-normal">{alertDetail.countyName}</span></td>
			        			</tr>
			        			<tr>
			        				<td>Price : <span className="text-normal">{alertDetail.priceMin} - {alertDetail.priceMax}</span></td>
			        				<td>Sqft: <span className="text-normal">{alertDetail.sqft}</span></td>
			        				<td>Lot : <span className="text-normal">{alertDetail.lotMin} - {alertDetail.lotMax}</span></td>
			        			</tr>

			        			<tr>
			        				<td>Year Built : <span className="text-normal">{alertDetail.yearBuiltMin} - {alertDetail.yearBuiltMax}</span></td>
			        				<td>AC : <span className="text-normal">{alertDetail.Ac}</span></td>
			        				<td>Basement : <span className="text-normal">{alertDetail.Bsmt1Out}</span></td>
			        			</tr>
			        			<tr>
			        				<td>Property Sub Type : <span className="text-normal">{alertDetail.propertySubType}</span></td>
			        				<td>Open House : <span className="text-normal">{alertDetail.openHouse}</span></td>
			        				<td>Class Name : <span className="text-normal">{alertDetail.className}</span></td>
			        			</tr>
			        			
			        		</table>	
		        		</div>        		
		        	</div>
		        </Modal.Body>
		      </Modal>
		</>
		);
}
export default EmailHistory;