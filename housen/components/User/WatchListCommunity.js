import { Tab, Row, Col, Nav, Form, Button, Table } from "react-bootstrap";
import React, { useEffect, useState } from "react";
import { requestToAPI } from "../../pages/api/api";
// import Pagination from '@material-ui/lab/Pagination';
import Pagination from "../../ReactCommon/Components/Pagination";
import Loader from "../loader/loader";
import { agentId, saveSearchApi } from "../../constants/Global";
import Modal from "react-bootstrap/Modal";
import { toast } from "react-toastify";
import WatchListCard from "./../../components/Cards/watchListCard";
import API from "../../ReactCommon/utility/api";
import trash from "../../public/images/icon/trash.png";
import Listings from "../Home/Listings";
const WatchListCommunity = (props) => {
  const [currentPage, setCurrentPage] = useState(1);
  const [total, setTotal] = useState(0);
  const [totalPage, setTotalPage] = useState(1);
  const [preLoader, setPreloader] = useState(false);
  const [watchedList, setWatchedList] = useState([]);
  const [smShow, setSmShow] = useState(false);
  const [deleteId, setDeleteId] = useState("");
  const [flag, setFlag] = useState(true);
  const [loaded, setLoaded] = useState(false);
  const [lgShow, setLgShow] = useState(false);
  const [modelData, setModelData] = useState(0);
  const [lgShowDetail, setLgShowDetail] = useState(false);
  const [alertDetail, setAlertDetail] = useState([]);
  useEffect(() => {
    const getSavedSearch = async () => {
      let localStorageData = localStorage.getItem("userDetail");
      localStorageData = JSON.parse(localStorageData);
      let id = localStorageData.login_user_id;
      let token = localStorage.getItem("login_token");

      const body = JSON.stringify({
        userId: id,
        currentPage: currentPage,
        limit: 10,
        agentId: agentId,
        isWatch: true,
        isCommunity: true,
      });
      const json = await requestToAPI(
        body,
        "api/v1/services/getSavedSearch",
        "POST",
        token
      );
      let prop = [];

      if (json.savedSearch) {
        // json.savedSearch.map((item, k) => {
        // 	if (item.property_list_sold) {
        // 		item.property_list_sold.map((val, ke) => {
        // 			prop.push(val);
        // 		})
        // 	}
        // });
      }
      setWatchedList(json.savedSearch);
      setTotal(json.total);
      setTotalPage(json.totalPages);
      setCurrentPage(json.currentPage);
      setLoaded(true);
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
  };

  function showMore(str) {
    setLgShow(true);
    setModelData(str);
  }
  function deleteFunction(str) {
    setSmShow(true);
    setDeleteId(str);
  }
  const DeleteConfirm = async (deleteId) => {
    if (deleteId !== null) {
      let localStorageData = localStorage.getItem("userDetail");
      localStorageData = JSON.parse(localStorageData);
      let id = localStorageData.login_user_id;
      let token = localStorage.getItem("login_token");

      let payload = {
        delId: false,
        userId: id,
        agentId: agentId,
        ListingId: deleteId,
        isWatchListing: true,
      };
      const body = JSON.stringify(payload);
      const json = await requestToAPI(
        body,
        "api/v1/services/deleteSavedSearch",
        "POST",
        token
      );
      if (json.success) {
        toast.success(json.success);
        setFlag(true);
        setSmShow(false);
      } else {
        toast.error(json.error);
      }
    }
    // else
    // {
    // 	setFlag(true);
    //        setSmShow(false);
    // }
  };

  let srno = 1;
  const getAlertDetail = async (alertId) => {
    setPreloader(false);
    if (alertId !== null) {
      const body = JSON.stringify({ alertId: alertId });
      const json = await requestToAPI(
        body,
        "api/v1/services/getSavedSearchDetail",
        "POST"
      );
      if (json.alertDetail) {
        setAlertDetail(json.alertDetail);
        setLgShowDetail(true);
        setPreloader(true);
      } else {
        setAlertDetail([]);
        setLgShowDetail(true);
        setPreloader(true);
      }
    }
  };

  function handleInputChanges(e) {
    let name = e.target.name;
    let { AlertsOn, ListingId,Community,PropertySubType ,City} = JSON.parse(e.target.dataset.value);
    let alerts=AlertsOn?AlertsOn:[]
	alerts[name] = e.target.checked;
    let watchObj = {
      isSold: false,
      ListingId: ListingId,
	  Community:Community,
	  PropertySubType:PropertySubType,
	  City:City,
      AlertsOn: alerts,
    };
    let storeObj = JSON.stringify(watchObj);
		let el1=document.getElementById("NewListings"+ListingId);
		if(el1){
		 el1.dataset.value = storeObj;
		}
		let el2=document.getElementById("SoldListings"+ListingId);
		if(el2){
		 el2.dataset.value = storeObj;
		}
	 
		let el3=document.getElementById("DelistedListings"+ListingId);
		if(el3){
		 el3.dataset.value = storeObj;
		}
	 
    let ob = {
      isWatchListings: true,
      agentId: agentId,
      userId: props.userDetails.login_user_id,
      watchListings: watchObj,
    };
    if (!e.target.checked) {
    //   e.target.checked = false;
    }
    let urls = saveSearchApi;
    API.jsonApiCall(urls, ob, "POST", null, {
      "Content-Type": "application/json",
    })
      .then((res) => {
        try {
          toast.success("Status Changed");
        } catch (error) {}
      })
      .catch((e) => {
        try {
          toast.error("Something went wrong try later!");
        } catch (error) {}
      });
  }

  return (
    <>
      <div>
        <div className="row ">
          {loaded &&
            watchedList.map((item, k) => {
              if (item.watchListings && item.watchListings) {
                return (
                  <div className={`col-md-6`} key={k}>
                    <WatchListCard
                      key={k}
                      {...props}
                      item={item}
                      removeCb={deleteFunction}
                      handleInputChanges={handleInputChanges}
                    />
                  </div>
                );
              }
            })}
        </div>
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
        {!preLoader && <Loader />}
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
            <li>
              <button
                className="btn btn-yes"
                onClick={() => DeleteConfirm(deleteId)}
              >
                Confirm
              </button>
            </li>
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
                );
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
                  <td>
                    Search Name :{" "}
                    <span className="text-normal">
                      {alertDetail.filterName}
                    </span>
                  </td>
                  <td>
                    Frequency :{" "}
                    <span className="text-normal">{alertDetail.frequency}</span>
                  </td>
                  <td>
                    Sub type :{" "}
                    <span className="text-normal">{alertDetail.subClass}</span>
                  </td>
                </tr>

                <tr>
                  <td>
                    Bedrooms :{" "}
                    <span className="text-normal">{alertDetail.bedsTotal}</span>
                  </td>
                  <td>
                    Bathrooms :{" "}
                    <span className="text-normal">{alertDetail.bathsFull}</span>
                  </td>
                  <td>
                    Style :{" "}
                    <span className="text-normal">{alertDetail.style}</span>
                  </td>
                </tr>

                <tr>
                  <td>
                    Garage Type :{" "}
                    <span className="text-normal">{alertDetail.GarType}</span>
                  </td>
                  <td>
                    Lot Size Area Max :{" "}
                    <span className="text-normal">
                      {alertDetail.lotSizeAreaMax} -{" "}
                      {alertDetail.lotSizeAreaMax}
                    </span>
                  </td>
                  <td>
                    Text search :{" "}
                    <span className="text-normal">
                      {alertDetail.textSearch}
                    </span>
                  </td>
                </tr>

                <tr>
                  <td>
                    Keywords :{" "}
                    <span className="text-normal">{alertDetail.keywords}</span>
                  </td>
                  <td>
                    City :{" "}
                    <span className="text-normal">{alertDetail.city}</span>
                  </td>
                  <td>
                    Country Name :{" "}
                    <span className="text-normal">
                      {alertDetail.countyName}
                    </span>
                  </td>
                </tr>
                <tr>
                  <td>
                    Price :{" "}
                    <span className="text-normal">
                      {alertDetail.priceMin} - {alertDetail.priceMax}
                    </span>
                  </td>
                  <td>
                    Sqft:{" "}
                    <span className="text-normal">{alertDetail.sqft}</span>
                  </td>
                  <td>
                    Lot :{" "}
                    <span className="text-normal">
                      {alertDetail.lotMin} - {alertDetail.lotMax}
                    </span>
                  </td>
                </tr>

                <tr>
                  <td>
                    Year Built :{" "}
                    <span className="text-normal">
                      {alertDetail.yearBuiltMin} - {alertDetail.yearBuiltMax}
                    </span>
                  </td>
                  <td>
                    AC : <span className="text-normal">{alertDetail.Ac}</span>
                  </td>
                  <td>
                    Basement :{" "}
                    <span className="text-normal">{alertDetail.Bsmt1Out}</span>
                  </td>
                </tr>
                <tr>
                  <td>
                    Property Sub Type :{" "}
                    <span className="text-normal">
                      {alertDetail.propertySubType}
                    </span>
                  </td>
                  <td>
                    Open House :{" "}
                    <span className="text-normal">{alertDetail.openHouse}</span>
                  </td>
                  <td>
                    Class Name :{" "}
                    <span className="text-normal">{alertDetail.className}</span>
                  </td>
                </tr>
              </table>
            </div>
          </div>
        </Modal.Body>
      </Modal>
    </>
  );
};
export default WatchListCommunity;
