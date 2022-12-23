import React from "react";
import Layout from "../components/Layout/Layout";
import 'react-toastify/dist/ReactToastify.css';
import { ToastContainer, toast } from 'react-toastify';
import MapLoadv2 from "../ReactCommon/Components/MapLoadv3";
import { Row, Col, Modal, Form } from "react-bootstrap";
import Button from "./../ReactCommon/Components/Button";
// import SaveSearch from "./../ReactCommon/Components/SaveSearch"
import {
    propertySearchApi,
    autoSuggestionApi,
    initialPropertySearchFilter,
    filterDataApi,
    saveSearchApi,
    agentId,
    mapSearchListApi,
    mapBoundaryApi,
    mapSearchTotalApi,
    mapSearchMarkersApi,
} from "../constants/GlobalConstants";
import API from "../ReactCommon/utility/api";
import MapCard from "../ReactCommon/Components/MapCard";
import MapHeader from "../components/HomeListSectionMap/MapHeader";
import ShimmerEffect from "../ReactCommon/Components/ShimmerEffect";
import Pagination from "../ReactCommon/Components/Pagination";
import PropertiesList from "../components/HomeListSection/PropertiesList";
import MapLoad from "../ReactCommon/Components/MapLoad";

class ListingsV2 extends React.Component {
    constructor(props) {
        super(props);
        this.props.setMetaInfo({
            title: "map"
        });
        this.state = {
            center: [-79.39493841352393, 43.617343105067334],
            zoom: 10,
            mapData: props.mapdata,
            geojsonData:[],
            allData:[],
            showTxt: props.textShow,
            showCountWords: props.countInWords,
            propertySearchFilter: JSON.parse(
                JSON.stringify(initialPropertySearchFilter)
            ),
            shimmer: true,
            currentPage: 1,
            pageName: "advance search",
            moreFilterData: "",
            modalShow: false,
            searchName: "",
            frequency: "",
            btnShow: true,
            loaderState: false,
            isReset: false,
            features: [],
            propertySubType: [],
            basementKey: [],
            selectedFeatures: [],
            prevOver: "",
            shape: "",
            curr_path_query: "",
            mapview: true,
            resetMapDraw: false
        };
        this.handlePropertyCall = this.handlePropertyCall.bind(this);
        this.fetchAutoSuggestion = this.fetchAutoSuggestion.bind(this);
        this.fetchMoreFilterData = this.fetchMoreFilterData.bind(this);
        this.handleTypeHead = this.handleTypeHead.bind(this);
        this.changeDrawState = this.changeDrawState.bind(this);
        this.resetBtn = this.resetBtn.bind(this);
        this.mapRef = React.createRef();
        this.pageChange = this.pageChange.bind(this);
        this.SavedSearch = this.SavedSearch.bind(this);
        this.changeHendler = this.changeHendler.bind(this);
        this.handleSaveSubmit = this.handleSaveSubmit.bind(this);
        this.featuresData = this.featuresData.bind(this);
        this.basementData = this.basementData.bind(this);
        this.propertySubData = this.propertySubData.bind(this);
        this.mapdragenCb = this.mapdragenCb.bind(this);
        this.highLight = this.highLight.bind(this);
        this.getPageData = this.getPageData.bind(this);

        this.getListdata = this.getListdata.bind(this);
        this.getBoundary = this.getBoundary.bind(this);
        // this.getListdata = this.getListdata.bind(this);
        // this.getListdata = this.getListdata.bind(this);

    }
    componentDidMount() {
        delete initialPropertySearchFilter.Community;
        const filterData = JSON.parse(localStorage.getItem("filters"));
        let filterObj = this.state.propertySearchFilter;
        if (filterData && filterData !== null) {
            const { searchFilter, preField } = filterData;
            filterObj = { ...filterObj, ...searchFilter };
            this.setState(
                {
                    propertySearchFilter: filterObj,
                    ...preField,
                },
                () => {
                    this.handleTypeHead();
                }
            );
        } else {
            this.resetBtn();
        }
        this.getFilterData();
        this.fetchMoreFilterData();
        this.props.pageName("advance search");
    }

    async getFilterData() {
        API.jsonApiCall(
            filterDataApi,
            {},
            "GET",
            null,
            {
                "Content-Type": "application/json",
            },
            { is_search: 1 }
        ).then((res) => {
            localStorage.setItem("moreFilters", JSON.stringify(res));
            this.setState({
                ...res,
            });
        });
    }
    handlePropertyCall(coordinates, geometryType) {
        const { propertySearchFilter } = this.state;
        if (!coordinates || coordinates.length <= 0) return null;
        let shapeStr = "";
        for (let i = 0; i < coordinates.length; i++) {
            if (i !== 0) {
                shapeStr += ", ";
            }
            shapeStr += `${coordinates[i][1]} ${coordinates[i][0]}`;
        }
        propertySearchFilter.curr_path_query = shapeStr;
        propertySearchFilter.shape = geometryType.toLowerCase();
        propertySearchFilter.text_search = "";
        this.setState({
            shape: shapeStr,
            curr_path_query: geometryType.toLowerCase(),
        });

        this.setState(propertySearchFilter, () => {
            this.handleTypeHead();
        });

    }

    renderPropertyData() {
        if (this.state.apiCall)
            return (
                <ShimmerEffect type="cardView" columnCls={"col-lg-6"} count={10} />
            );
        if (!this.state.allData || this.state.allData.length <= 0) {
            
            return (
                <div className="col-lg-6 col-md-3 p-1"></div>);
        }
        const renderData = this.state.allData.map((res, index) => {
            let Vow_exclusive = res.Vow_exclusive ? res.Vow_exclusive : 0;
            return (
                <div className="col-lg-6 col-md-3 p-1">
                    {
                        Vow_exclusive == 0 || this.props.isLogin ? <></> : <> <span className="vow-cls " >Login Required</span></>
                    }
                    <div className={`  ${Vow_exclusive == 0 || this.props.isLogin ? "" : "filter  mt-30"}`}>

                        <MapCard item={res} key={index}
                            showIsFav={true}
                            openUserPopup={true}
                            openLoginCb={this.props.togglePopUp}
                            isLogin={this.props.isLogin}
                            highLightCb={this.highLight}
                        />
                    </div>
                </div>
            );
        });
        return renderData;
    }

    fetchAutoSuggestion(fieldValue, fieldName, cb) {
        let payload = {
            query: "default",
        };
        if (fieldValue) {
            payload.query = fieldValue;
        }
        API.jsonApiCall(autoSuggestionApi, payload, "POST", null, {
            "Content-Type": "application/json",
        }).then((res) => {
            cb({ allList: res });
        });
    }

    mapdragenCb(obj) {
        const { propertySearchFilter } = this.state;
        if (propertySearchFilter.shape == "polygon") {
            return;
        }
        propertySearchFilter.shape = "rectangle";
        propertySearchFilter.text_search = "";
        propertySearchFilter.curr_bounds = obj.bndstr;
        this.setState({
            isReset: false
        })
        this.setState(propertySearchFilter, () => {
            this.handleTypeHead();
        });
    }

    async fetchMoreFilterData() {
        let uri = filterDataApi
        // uri = "http://127.0.0.1:8000/api/v1/services/bootstrap/filterData";
        API.jsonApiCall(
            uri,
            {},
            "GET",
            null,
            {
                "Content-Type": "application/json",
            },
            { is_search: 0 }
        ).then((res) => {
            this.setState({
                moreFilterData: res,
            });
        })
    }
    featuresData(e) {
        let val = e.target.value;
        let prev = this.state.selectedFeatures;
        if (prev.includes(val)) {
            let index = prev.indexOf(val);
            prev.splice(index, 1);
        } else {
            prev.push(val)
        }
        this.setState({
            selectedFeatures: prev
        });
        this.handleTypeHead();
    }
    basementData(e) {
        let val = e.target.value;
        let prev = this.state.basementKey;
        if (prev.includes(val)) {
            let index = prev.indexOf(val);
            prev.splice(index, 1);
        } else {
            prev.push(val)
        }
        this.setState({
            basementKey: prev
        });
        this.handleTypeHead();
        // basement: [],
    }
    propertySubData(e) {
        let val = e.target.value;
        let prev = this.state.propertySubType;
        if (prev.includes(val)) {
            let index = prev.indexOf(val);
            prev.splice(index, 1);
        } else {
            prev.push(val)
        }
        this.setState({
            propertySubType: prev
        });
        this.handleTypeHead();
        // propertySubType: [],
    }
    highLight(e) {
        let prev = this.state.prevOver;
        if (e.target.attributes.dataset) {
            let obj = JSON.parse(e.target.attributes.dataset.value);
            if (obj) {
                this.setState({
                    prevOver: obj.id
                });
                if (prev) {
                    let preMarkElm = document.getElementById('marker-' + prev);
                    let preCardElm = document.getElementById('propCard' + prev);
                    if (preMarkElm) {
                        preMarkElm.classList.remove("propHoverMarkers");
                    }
                    if (preCardElm) {
                        preCardElm.classList.remove("markersHoverBorder");
                    }
                }
                if (obj.ismap) {
                    let cardElm = document.getElementById('propCard' + obj.id);
                    if (cardElm) {
                        cardElm.classList.add("markersHoverBorder");
                    }

                } else {
                    let markElm = document.getElementById('marker-' + obj.id);
                    if (markElm) {
                        markElm.classList.add("propHoverMarkers");
                    }
                }

            }
        }
    }

    handleTypeHead(obj = null, name = null) {
        let objs = { totalData: 0 }
        this.setState(objs);
        let boundary=false;
        const { propertySearchFilter } = this.state;

        if (obj !== null && name !== null) {
            propertySearchFilter[name] = obj.value;
            this.setState({
                [name]: obj,
            });
            if (name === "text_search") {
                this.mapRef.current.updateTrash();
                this.setState({
                    shape: "",
                    curr_path_query: "",
                });
                boundary=true;
               
            }
        }
        propertySearchFilter["features"] = [];
        propertySearchFilter["isMapV2"] = true;
        propertySearchFilter["basement"] = [];
        propertySearchFilter["propertySubType"] = [];
        if (this.state.propertySubType) {
            propertySearchFilter["propertySubType"] = this.state.propertySubType
        }
        if (this.state.basementKey) {
            propertySearchFilter["basement"] = this.state.basementKey
        }
        if (this.state.selectedFeatures) {
            propertySearchFilter["features"] = this.state.selectedFeatures
        }
        let advanceSearch = propertySearchFilter;
        advanceSearch['City'] = "";
        localStorage.setItem("advanceSearch", JSON.stringify(advanceSearch));
        // uri = "http://127.0.0.1:8000/api/v1/services/search/propertiesSearch";
        this.getPageData(propertySearchFilter);
        let filtersData = localStorage.getItem("moreFilters");
        if (filtersData) {
            filtersData = JSON.parse(filtersData);
            this.setState({
                ...filtersData,
            });
        }
        let searchFilterValue = this.state.propertySearchFilter.text_search;
        let meta = {
            title: "Advance Search",
            slug: searchFilterValue ? "Sale Properties in " + searchFilterValue : "Sale Properties in Toronto",
            metaTitle: "Map Details",
            metaDesc: "Advance search in Toronto",
            metaKeyword: "Wedu For Sale , Wedu Finder , Wedu for Rent",
        }
        this.props.setMetaInfo(meta);
        if(boundary){
            this.getBoundary({ text_search: obj.value });
        }
    }

    getPageData(propertySearchFilter) {
        this.getMarkers(propertySearchFilter);
        this.getListdata(propertySearchFilter);
        // this.getListdata(propertySearchFilter);
    }
    getListdata(propertySearchFilter) {
        const stateData = {
            apiCall: false,
            showTxt: "Oops! sorry No exact matches Found",
            showCountWords: "0 Listings",
            allData:[]
           
        };
        let uri = mapSearchListApi;
        this.setState({ apiCall: true });
        API.jsonApiCall(uri, propertySearchFilter, "POST", null, {
            "Content-Type": "application/json",
        })
            .then((res) => {
                let center = this.state.center;
                stateData.shimmer = false;
                if (res.alldata) {
                    stateData.allData = res.alldata;
                    stateData.totalData = res.total;
                    stateData.showCountWords = res.countInWords;
                    stateData.showTxt = res.textShow;
                    stateData.offset = res.offset;
                    stateData.limitCount = res.limit;
                }
                this.setState(stateData);
            })
            .catch(() => {
                this.setState(stateData);
            });
    }
    getMarkers(propertySearchFilter) {
        const stateData = {
            mapData: [],
            apiCall: false,
            geojsonData:[]
           
        };
        let uri = mapSearchMarkersApi;
        this.setState({ apiCall: true });
        API.jsonApiCall(uri, propertySearchFilter, "POST", null, {
            "Content-Type": "application/json",
        })
            .then((res) => {
                let center = this.state.center;
                stateData.shimmer = false;
                // if (res.mapdata) {
                //     const centerLat = res.mapdata ? res.mapdata[res.mapdata.length - 1].Latitude : 565;
                //     const centeralLong = res.mapdata ? res.mapdata[res.mapdata.length - 1].Longitude : -656;
                //     center = [centeralLong, centerLat];
                //     stateData.mapData = res.mapdata;
                // }
                stateData.mapData=[];
                stateData.geojsonData = res.geojson;
                stateData.center =  res.center;
                this.setState(stateData);
            })
            .catch(() => {
                this.setState(stateData);
            });
    }
    getBoundary(propertySearchFilter) {
        const stateData = {
            cityData: {},
            areaData: {},
            apiCall: false,
        };
        let uri = mapBoundaryApi;
        this.setState({ apiCall: true });
        API.jsonApiCall(uri, propertySearchFilter, "POST", null, {
            "Content-Type": "application/json",
        })
            .then((res) => {
                // if ('caches' in window) {
                //// Opening given cache and putting our data into it
                // let url=window.location.origin
                // const data = new Response(JSON.stringify(res))
                // caches.open("test").then((cache) => {
                //   cache.put(url, data);
                //   alert('Data Added into cache!')
                // });
                //}
                stateData.shimmer = false;
                if (res.cityData) {
                    stateData.cityData = res.cityData
                }
                if (res.areaData) {
                    stateData.areaData = res.areaData
                }
                this.setState(stateData);
            })
            .catch((e) => {
                this.setState(stateData);
            });
    }



    SavedSearch(state = true) {
        if (!this.props.isLogin) {
            this.props.togglePopUp();
            return;
        }
        this.setState({
            modalShow: state
        });
    }
    changeHendler(e) {
        if (e.target.name === "filter_name") {
            //
            this.setState({
                searchName: e.target.value ? e.target.value : ""
            });
        }
        if (e.target.name === "frequency") {
            // btnShow
            this.setState({
                frequency: e.target.value ? e.target.value : ""
            });
        }
        setTimeout(() => {
            if (this.state.frequency && this.state.searchName) {
                this.setState({
                    btnShow: false,
                });
            } else {
                this.setState({
                    btnShow: true,
                });
            }
        }, 150);
    }
    handleSaveSubmit() {
        this.setState({
            btnShow: true,
        });
        this.state.frequency && this.state.searchName;
        let data = {
            frequency: this.state.frequency,
            searchName: this.state.searchName,
            filtersData: localStorage.getItem("advanceSearch"),
            userId: this.props.userDetails.login_user_id,
            agentId: agentId
        };
        let urls = saveSearchApi;
        // urls ="http://127.0.0.1:8000/api/v1/services/saveSearch";
        API.jsonApiCall(urls, data, "POST", null, {
            "Content-Type": "application/json",
        }).then((res) => {
            toast.success("Submit Successfully");
            this.SavedSearch(false)
            this.setState({
                frequency: "",
                searchName: "",
                filtersData: "",
                btnShow: true,
                loaderState: false,
            });
        })
            .catch((e) => {
                toast.error("Something went wrong try later!");
                this.setState({
                    dataFlag: false
                });
            });
    }
    resetBtn() {
        this.setState({
            isReset: true,
        });
        try {
            var btn = document.getElementById('clear_draw');
            btn.click();
        } catch (error) {
        }
        const element = document.getElementsByClassName("checkboxState");//.checked = false;
        for (let index = 0; index < element.length; index++) {
            const currElm = element[index];
            currElm.checked = false;
        }
        var btn = document.getElementsByClassName('mapbox-gl-draw_trash');
        for (let i = 0; i < btn.length; i++) {
            if (btn[i]) {
                btn[i].click();
            }
        }
        const stateData = { apiCall: false };
        window.localStorage.removeItem("filters");
        this.setState(
            {

                propertySearchFilter: JSON.parse(
                    JSON.stringify(initialPropertySearchFilter)
                ),
                text_search: "",
                propertyType: "",
                propertySubType: [],
                price_min: "",
                price_max: "",
                baths: "",
                beds: "",
                status: "",
                sort_by: "",
                Sqft: "",
                Dom: "",
                apiCall: true,
                
                propertySubType: [],
                basementKey: [],
                selectedFeatures: [],
            },
            () => {
                this.handleTypeHead();
            }
        );
    }

    pageChange(e) {
        let propertyPayLoad = this.state.propertySearchFilter;
        propertyPayLoad.curr_page = e
        this.setState({
            currentPage: e,
            propertySearchFilter: propertyPayLoad
        }, () => {
            this.handleTypeHead()
        })
    }

    changeDrawState(e) {
        let propertyPayLoad = this.state.propertySearchFilter;
        propertyPayLoad.curr_path_query = "";
        propertyPayLoad.shape = "";
        this.setState({
            propertySearchFilter: propertyPayLoad
        }, () => {
            this.handleTypeHead()
        });
    }

    componentDidUpdate(prevProps, prevState) {
        if (prevState.btnShow !== this.state.btnShow) {
            this.setState({
                btnShow: this.state.btnShow
            })
        }
    }

    render() {
        //right place for loader

        return (
            <>
                <div className="savedSearch">
                    <Modal
                        show={this.state.modalShow} onHide={() => this.SavedSearch(false)}
                        className="saveSearchModel"
                        size="md"
                        aria-labelledby="contained-modal-title-vcenter"
                    >
                        <Modal.Header closeButton>
                            <Modal.Title id="contained-modal-title-vcenter">
                                Saved my search
                        </Modal.Title>
                        </Modal.Header>
                        <Modal.Body>
                            <br />
                            <input id="name_search" type="text" onChange={this.changeHendler} onBlur={this.changeHendler} className="form-control form-input-border" name="filter_name" placeholder="Give A Name to your search" required="required" />
                            <br />
                            <h4>Setup New Listing Alert</h4>
                            <p className="mt-3">Receive Alerts Frequency</p>
                            <Form.Check
                                inline
                                label="Instantly"
                                name="frequency"
                                type="radio"
                                value="instantly"
                                onChange={this.changeHendler}
                            />
                            <Form.Check
                                inline
                                label="Daily"
                                name="frequency"
                                type="radio"
                                value="daily"
                                onChange={this.changeHendler}
                            />
                            <Form.Check
                                inline
                                label="Monthly"
                                name="frequency"
                                type="radio"
                                value="monthly"
                                onChange={this.changeHendler}
                            />
                            <Form.Check
                                inline
                                label="Never"
                                name="frequency"
                                type="radio"
                                value="never"
                                onChange={this.changeHendler}
                            />
                            <div className="saveSearchBtn">
                                {this.state.loaderState ?
                                    <>
                                        <input id="modalSaveFilter" className="btn btn-lg btn-block saveSearchForm reset-btn" type="button" value="Submiting....." disabled={true}></input>
                                    </> : <>
                                        <input id="modalSaveFilter" onClick={this.handleSaveSubmit} className="btn btn-lg btn-block saveSearchForm reset-btn" type="button" value="Save Filter" disabled={this.state.btnShow}></input>
                                    </>}

                            </div>
                        </Modal.Body>
                    </Modal>
                </div>
                <div className="paddingInMobile">
                    <MapHeader
                        autoCompleteSuggestion={this.fetchAutoSuggestion}
                        handleTypeHead={this.handleTypeHead}
                        resetBtn={this.resetBtn}
                        savedSearch={this.SavedSearch}
                        featuresData={this.featuresData}
                        basementData={this.basementData}
                        openLoginCb={this.props.togglePopUp}
                        propertySubData={this.propertySubData}
                        {...this.state}
                    />
                    <button className="float-right mapbtn" onClick={() => this.setState({ mapview: !this.state.mapview })}>{this.state.mapview ? <i className="fa fa-list"> Card</i> : <i className="fa fa-map"> Map</i>} view</button>
                    <div className="row">
                        <div className={!this.state.mapview ? 'mapHideShow col-lg-6' : 'col-lg-6'}>
                            <MapLoadv2
                                center={this.state.center}
                                zoom={this.state.zoom}
                                handlePropertyCall={this.handlePropertyCall}
                                mapData={this.state.mapData}
                                geojsonData={this.state.geojsonData}
                                cityData={this.state.cityData}
                                areaData={this.state.areaData}
                                ref={this.mapRef}
                                // handleTypeHead={this.handleTypeHead}
                                changeDrawState={this.changeDrawState}
                                mapdragenCb={this.mapdragenCb}
                                {...this.props}
                                isReset={this.state.isReset}
                                highLight={this.highLight}
                                showIsFav={true}
                                openUserPopup={true}
                                resetMapDraw={this.state.resetMapDraw}
                            // isLogin={this.props.isLogin}
                            />
                        </div>
                        <div className="col-lg-6 cards_wrapper cardInMobile">
                            <div className="col-lg-12">
                                <div className="row">
                                    <div className="col-lg-9 col-6">
                                        <p>{this.state.showTxt}</p>
                                    </div>
                                    <div className="col-lg-3 col-6 text-right">
                                        <p>{this.state.showCountWords}</p>
                                    </div>


                                </div>

                            </div>
                            <div className="row cardInMobile">
                                {
                                    //this.state.allData && this.state.allData.length > 0 &&
                                }
                                {this.renderPropertyData()}</div>
                            <div className="d-flex justify-content-center">
                                {this.state.totalData > 1 &&
                                    <Pagination
                                        itemsCount={this.state.totalData}
                                        itemsPerPage={this.state.limitCount}
                                        currentPage={this.state.currentPage}
                                        setCurrentPage={this.pageChange}
                                        alwaysShown={false}
                                    />
                                }
                            </div>
                        </div>
                    </div>
                </div>
            </>
        );
    }
}

export default ListingsV2;
