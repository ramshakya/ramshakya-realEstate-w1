// Library
import React, { useState, useEffect } from 'react';
// import RedButton from "../Button/RedButton";
import { withRouter } from "next/router";
import { requestToAPI } from "../../pages/api/api";
import { useRouter } from 'next/router';
import { Container, Row, Col } from "react-bootstrap";
import CardRow3 from "../Cards/PropertyCard";
import API from "../../ReactCommon/utility/api";
import Pagination from "../../ReactCommon/Components/Pagination";
import ShimmerEffect from "../../ReactCommon/Components/ShimmerEffect";

import Link from "next/link";
import {
    propertySearchApi,
    autoSuggestionApi,
    initialPropertySearchFilter,
    filterDataApi,
} from "../../constants/Global";

import MapHeader from "./MapHeader"
import Map from "./city_map";
const CityData = withRouter((props) => {
    const router = useRouter();
    const City_name = router.query.slug;

    const [sort_by, setSort_by] = useState('');
    const [cardGrid, setCardGrid] = useState(false);
    const [cityproperty, setCityproperty] = useState([]);
    const [total, setTotal] = useState('');
    const [pagination, setPagination] = useState('');
    const [currentPage, setCurrentPage] = useState(1);
    const [total_page, setTotal_page] = useState('');
    const [limitCount, setLimitCount] = useState('');
    const [mapData, setMapData] = useState('');
    const [flag, setFlag] = useState(true);
    const [notFound, setNotFound] = useState(false);
    const [status, setStatus] = useState('Sale');
    const [reset, setReset] = useState(true);
    useEffect(() => {
        const fetchcity = async () => {
            window.scrollTo({ behavior: 'smooth', top: '0px' });
            showmenu('close');
            if(!initialPropertySearchFilter.propertySubType){
                initialPropertySearchFilter["propertySubType"] = [];
            }
            if(!initialPropertySearchFilter.basement){
                 initialPropertySearchFilter["basement"] = [];
            }
            if(!initialPropertySearchFilter.features){
                initialPropertySearchFilter["features"] = [];
            }

            initialPropertySearchFilter['curr_page'] = currentPage;
            setStatus(initialPropertySearchFilter['status']);
            const body = JSON.stringify(initialPropertySearchFilter)

            const json = await requestToAPI(body, "api/v1/services/search/propertiesSearch", "POST");
            if (json.alldata) {
                setCityproperty(json.alldata);
                setPagination(json.pagination);
                setTotal(json.total);
                setMapData(json.mapdata);
                setTotal_page((Math.floor(json.total / 10)) + 1);
                setLimitCount(json.limit)
                setNotFound(false);

            }
            else {
                setCityproperty([]);
                setPagination('');
                setTotal(0);
                setMapData([]);
                setTotal_page(0);
                setLimitCount(0)
                // window.scrollTo({ behavior: 'smooth', top: '0px' });
                setNotFound(json.textShow)
            }

            setFlag(false);
            setReset(true);


        };
        if (flag) {
            fetchcity();
        }


    }, [currentPage, flag]);


    const handleChange = (event, value) => {
        if (event !== 0) {
            setCurrentPage(event);
            setFlag(true);
        }
    };
     function mapdragenCb(obj) {
         console.log("mapdragenCb",obj);
        // const { propertySearchFilter } = this.state;
        
        let name="shape";
        let curr_bounds="curr_bounds";
        let text_search="text_search";
        if(initialPropertySearchFilter.shape=="polygon"){
            return;
        }
        initialPropertySearchFilter[name] = "rectangle";
        initialPropertySearchFilter[curr_bounds] = obj.bndstr;
        initialPropertySearchFilter[text_search] = "";
        setCurrentPage(1);
        setFlag(true);
        
        // this.mapRef.current.updateTrash();
        // propertySearchFilter.shape = "rectangle";
        // propertySearchFilter.text_search = "";
        // propertySearchFilter.curr_bounds = obj.bndstr;
        // this.setState(propertySearchFilter, () => {
        //     this.handleTypeHead();
        // });
    }

    function handleTypeHead(obj = null, name = null, e, inp) {
        if (obj.target) {
            if (obj.target.name !== undefined) {
                initialPropertySearchFilter[obj.target.name] = obj.target.value;
                setCurrentPage(1);
                setFlag(true);
            }
        }
        else{
            if(name==="propertySubType"){
                let subArr=[];
                subArr.push(obj.value);
                initialPropertySearchFilter["propertySubType"] = subArr;
                console.log("propertySubType",subArr);
            }else{
                initialPropertySearchFilter[name] = obj.value;
            }
            setCurrentPage(1);
            setFlag(true);
        }
    }
    function handleTypeHeadSearch(obj = null, name = null, e, inp) {
        if (name !== undefined) {
            initialPropertySearchFilter[name] = obj.value;
        }
    }
    // handleTypeHead
    function handleTypeHeadClick(e) {
        setCurrentPage(1);
        setFlag(true);
    }

    function resetBtn() {
        window.localStorage.removeItem("filters");
        // document.getElementById('propertyType').value="";
        initialPropertySearchFilter['text_search'] = ""
        initialPropertySearchFilter['propertyType'] = ""
        initialPropertySearchFilter['propertySubType'] = []
        initialPropertySearchFilter['price_min'] = ""
        initialPropertySearchFilter['price_max'] = ""
        initialPropertySearchFilter['baths'] = ""
        initialPropertySearchFilter['beds'] = ""
        initialPropertySearchFilter['status'] = "Sale"
        initialPropertySearchFilter['sort_by'] = ""
        initialPropertySearchFilter['curr_path_query'] = ""
        initialPropertySearchFilter['shape'] = ""
        delete initialPropertySearchFilter.Park_spcs;
        setFlag(true);
        setExtraFlag(true);
        setReset(false);

    }
    function fetchAutoSuggestion(fieldValue, fieldName, cb) {
        let payload = {
            query: "default",
        };
        if (fieldValue) {
            payload.query = fieldValue;
        }
        API.jsonApiCall(autoSuggestionApi, payload, "POST", null, {
            "Content-Type": "application/json",
        }).then((res) => {
            let arr = [];
            let index = 0;
            for (var i = 0; i < res.length; i++) {
                var loc = window.location.href;
                var targetValue = loc.substr(loc.lastIndexOf('/') + 1);
                if (targetValue === "map") {
                    arr[index] = res[i];
                    index++;
                } else {
                    if (res[i].category !== "Cities" && res[i].category !== "Municipality") {
                        arr[index] = res[i];
                        index++;
                    }
                }


            }

            cb({ allList: arr });
        });
    }
    function handlePropertyCall(coordinates, geometryType) {
        const { propertySearchFilter } = '';
        if (!coordinates || coordinates.length <= 0) return null;
        let shapeStr = "";
        for (let i = 0; i < coordinates.length; i++) {
            if (i !== 0) {
                shapeStr += ", ";
            }
            shapeStr += `${coordinates[i][1]} ${coordinates[i][0]}`;
        }
        initialPropertySearchFilter.curr_path_query = shapeStr;
        initialPropertySearchFilter.shape = geometryType.toLowerCase();
        initialPropertySearchFilter.text_search = "";
        setFlag(true);
    }
    // const [open, setOpen] = useState(false);
    const [extraFilters, setExtraFilters] = useState([]);
    const [extraFlag, setExtraFlag] = useState(true);
    useEffect(() => {
        const getFilterData = async () => {

            const getFilterData = await API.jsonApiCall(
                filterDataApi,
                {},
                "GET",
                null,
                {
                    "Content-Type": "application/json",
                },
                { is_search: 1 }
            );
            setExtraFilters(getFilterData);
            setExtraFlag(false)
        }
        if (extraFlag) {
            getFilterData();
        }
    }, [extraFlag]);
    // console.log("search",filterDataApi);//
    const [show, setShow] = useState(false);
    useEffect(() => {
        if (screen.width < 650) {
            setShow(true);
        }
    }, []);
    
    function showmenu(e) {
        let cl = document.getElementsByClassName('checkbox-opt-container');
        for (var i = 0; i < cl.length; i++) {
            cl[i].classList.remove("checkbox-opt-showing");
        }
        if (e !== 'close') {
            if (e.target.classList[0] == 'theme-text') {
                let panel = e.target.nextElementSibling;
                panel.classList.add('checkbox-opt-showing');
            }
        }
    }
    return (
        <div>
            <div className="mb-5">
                <div className="container-fluid">
                    <div className="row">
                        <div className="col-md-12 col-lg-12 cityPageWrapper">
                        </div>
                        <div className="col-md-12">
                            {reset &&
                                <MapHeader
                                    autoCompleteSuggestion={fetchAutoSuggestion}
                                    handleTypeHeadSearch={handleTypeHeadSearch}
                                    handleTypeHead={handleTypeHead}
                                    resetBtn={resetBtn}
                                    Status={status}
                                    {...extraFilters}
                                    showmenu={showmenu}
                                    handleTypeHeadClick={handleTypeHeadClick}
                                    {...initialPropertySearchFilter}
                                />
                            }
                        </div>
                    </div>
                </div>
                <div className="container-fluid pt-3">
                    <div className="container-fluid city">
                        <div className="row">
                            <div className="col-md-12 col-lg-12 pb-2"><strong>{total} Results | Page {currentPage} of {total_page}</strong>
                                <div className="toggle-btn">
                                    <button className={`${cardGrid ? '' : ' active'} btn`} onClick={() => setCardGrid(false)}><img src="../images/icon/grid.png" className="icon-size" /></button>
                                    <button className={`${cardGrid ? 'active' : ' '} btn`} onClick={() => setCardGrid(true)}><img src="../images/icon/map.png" className="icon-size" /></button>
                                </div>
                            </div>
                            {flag &&
                                <ShimmerEffect type="cardView" columnCls={"col-lg-3"} count={10} />
                            }
                            <div className="col-md-12 col-lg-12">
                                {notFound && <p className="text-center">{notFound}</p>}
                                {!flag && cardGrid &&
                                    <Map
                                        togglePopUp={''}
                                        isLogin={''}
                                        handlePropertyCall={handlePropertyCall}
                                        mapData={mapData}
                                        handleTypeHead={handleTypeHead}
                                        changeDrawState={''}
                                        cityproperty={cityproperty}
                                        mapdragenCb={mapdragenCb}
                                    />
                                }
                                {!flag && !cardGrid &&
                                    <div className="row" id="tableItem">
                                        {cityproperty.map((item) => {
                                            const {
                                                id,
                                                PropertyStatus,
                                                isOpenHouse,
                                                PropertySubType,
                                                ListPrice,
                                                StandardAddress,
                                                City,
                                                ImageUrl,
                                                BedroomsTotal,
                                                BathroomsFull,
                                                Sqft,
                                                SlugUrl,
                                                ListingId

                                            } = item;
                                            return (
                                                <Col md={3} key={id + 'city'} className="mb-4">
                                                    <CardRow3
                                                        key={id}
                                                        forBadge={PropertyStatus}
                                                        isOpenHouse={isOpenHouse}
                                                        PropertySubType={PropertySubType}
                                                        BedroomsTotal={BedroomsTotal}
                                                        BathroomsFull={BathroomsFull}
                                                        price={ListPrice}
                                                        StandardAddress={StandardAddress}
                                                        province={City}
                                                        ImageUrl={ImageUrl}
                                                        Sqft={Sqft}
                                                        SlugUrl={SlugUrl}
                                                        ListingId={ListingId}
                                                        showIsFav={true}
                                                        openUserPopup={true}
                                                        openLoginCb={props.togglePopUp}
                                                        isLogin={props.isLogin}
                                                        item={item}
                                                    />
                                                </Col>
                                            );
                                        })}
                                    </div>
                                }
                            </div>
                            <div className="col-md-12 col-lg-12 justify-content-center text-center mt-4">
                                <div className="d-flex justify-content-center">
                                    <Pagination
                                        itemsCount={Number(total)}
                                        itemsPerPage={10}
                                        currentPage={Number(currentPage)}
                                        setCurrentPage={handleChange}
                                        alwaysShown={false}
                                    />
                                </div>
                            </div>

                        </div>
                    </div>
                </div>
            </div>
        </div>
    );
});


export default CityData;
