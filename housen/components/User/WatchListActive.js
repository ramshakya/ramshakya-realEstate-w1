import { Tab, Row, Col, Nav, Form, Button, Table } from "react-bootstrap";
import React, { useEffect, useState } from "react";
import { requestToAPI } from "../../pages/api/api";
// import Pagination from '@material-ui/lab/Pagination';
import Pagination from "../../ReactCommon/Components/Pagination";
import Loader from '../../components/loader/loader';
import { agentId } from '../../constants/Global'
import Modal from 'react-bootstrap/Modal';
import { toast } from 'react-toastify';
import trash from "../../public/images/icon/trash.png";
import Listings from "../Home/Listings";
import Card from "../Cards/PropertyCard";
const SavedSearch = (props) => {
	const [currentPage, setCurrentPage] = useState(1);
	const [total, setTotal] = useState(1);
	const [totalPage, setTotalPage] = useState(1);
	const [preLoader, setPreloader] = useState(false);
	const [watchedList, setWatchedList] = useState([]);
	const [smShow, setSmShow] = useState(false);
	const [deleteId, setDeleteId] = useState('');
	const [flag, setFlag] = useState(true);
	const [lgShow, setLgShow] = useState(false);
	const [modelData, setModelData] = useState(0);
	const [lgShowDetail, setLgShowDetail] = useState(false);
	const [alertDetail, setAlertDetail] = useState([]);
	useEffect(() => {
		const getSavedSearch = async () => {
			let localStorageData = localStorage.getItem('userDetail');
			localStorageData = JSON.parse(localStorageData);
			let id = localStorageData.login_user_id;
			let token = localStorage.getItem('login_token');

			const body = JSON.stringify({
				userId: id,
				currentPage: currentPage,
				limit: 10,
				agentId: agentId,
				isWatch: true
			});
			const json = await requestToAPI(body, "api/v1/services/getSavedSearch", "POST", token);
			let prop = [];
			if (json.savedSearch) {
				json.savedSearch.map((item, k) => {
					let watchListings = JSON.parse(item.watchListings);
					if (watchListings.isSold) {
						item.property_list.map((val, ke) => {
							prop.push(val);
						})
					}
				});
			}
			setWatchedList(prop);
			setTotal(json.total);
			setTotalPage(json.totalPages);
			setCurrentPage(json.currentPage);
			setPreloader(true);
			setFlag(false);
		};
		if (flag) {
			getSavedSearch();
		}


	}, [currentPage, flag]);
	const pageChange = (e) => {
		setCurrentPage(e);
		setPreloader(false);
		setFlag(true);
	}

	function showMore(str) {
		setLgShow(true);
		setModelData(str)
	}
	// console.log("modal",modelData);
	function deleteFunction(str) {
		setSmShow(true);
		setDeleteId(str);
	}
	const DeleteConfirm = async (deleteId) => {
		// console.log('data',deleteId);
		if (deleteId !== null) {
			let localStorageData = localStorage.getItem('userDetail');
			localStorageData = JSON.parse(localStorageData);
			let id = localStorageData.login_user_id;
			let token = localStorage.getItem('login_token');

			let payload = {
				delId: false,
				userId: id,
				agentId: agentId,
				ListingId: deleteId,
				isWatchListing:true
			}
			const body = JSON.stringify(payload);
			const json = await requestToAPI(body, "api/v1/services/deleteSavedSearch", "POST", token);
			if (json.success) {
				toast.success(json.success);
				setFlag(true);
				setSmShow(false);
			}
			else {
				toast.error(json.error);
			}
		}
		// else
		// {
		// 	setFlag(true);
		//        setSmShow(false);
		// }
	}

	let srno = 1;
	const getAlertDetail = async (alertId) => {
		setPreloader(false);
		if (alertId !== null) {
			const body = JSON.stringify({ alertId: alertId });
			const json = await requestToAPI(body, "api/v1/services/getSavedSearchDetail", "POST");
			if (json.alertDetail) {
				setAlertDetail(json.alertDetail);
				setLgShowDetail(true);
				setPreloader(true);
			}
			else {
				setAlertDetail([]);
				setLgShowDetail(true);
				setPreloader(true);
			}
		}

	}
	function verifyEmail() {
		localStorage.setItem('verifyemail', true)
		router.push('/profile');
	}
	return (
		<>
			<div className="watchlisting row">
				{/*<Listings {...props} heading="" propertyData={watchedList} imageData={[]} removeCb={deleteFunction} isTrash={true} isWatch={true} isSold={false}  LoginRequired={0} />*/}
				{watchedList.map((item,index)=>{
					return(
							<div className="col-md-6 col-lg-4 mb-1"><Card
												item={item}
												imageData={''}
												showIsFav={true}
												showTrash={true}
												removeCb={deleteFunction}
												openUserPopup={true}
												openLoginCb={props.togglePopUp}
												isLogin={props.isLogin}
												LoginRequired={props.LoginRequired}
												isSold={false}
												emailIsVerified={props.emailIsVerified}
												signInToggle={props.signInToggle}
												verifyEmail={verifyEmail}
											/>
							</div>
						)
				})}
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
					<Loader />
				}
			</div>
			<Modal
				size="sm"
				show={smShow}
				onHide={() => setSmShow(false)}
				aria-labelledby="example-modal-sizes-title-sm"
				className="deletModal"
			>
				<Modal.Header closeButton>
					<Modal.Title id="example-modal-sizes-title-sm">
						Do you want to delete?
					</Modal.Title>
				</Modal.Header>
				<Modal.Body>
					<ul className="delete_btnUl">
						<li><button className="btn btn-yes" onClick={() => DeleteConfirm(deleteId)}>Confirm</button></li>


					</ul>
				</Modal.Body>
			</Modal>

			<Modal
				size="lg"
				show={lgShow}
				onHide={() => setLgShow(false)}
				aria-labelledby="example-modal-sizes-title-lg"
				className="viewMoreModal"
			>
				<Modal.Header closeButton>
					<Modal.Title id="example-modal-sizes-title-lg">
						More Detail
					</Modal.Title>
				</Modal.Header>
				<Modal.Body>
					<div className="row">
						{watchedList.map((item) => {
							if (item.id == modelData) {
								return (
									<div className="col-md-3 col-lg-3">
										<h6 className="savedPoints"> More Details will be here</h6>
									</div>
								)
							}
						})}

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
export default SavedSearch;