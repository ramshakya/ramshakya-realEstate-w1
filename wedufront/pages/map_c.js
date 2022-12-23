import React from "react";
import Layout from "../components/Layout/Layout";
import MapLoad from "../ReactCommon/Components/MapLoad";
import {
    propertySearchApi,
    autoSuggestionApi,
    initialPropertySearchFilter,
    filterDataApi,
} from "../constants/GlobalConstants";
import API from "../ReactCommon/utility/api";
import MapCard from "../ReactCommon/Components/MapCard";
import MapHeader from "../components/HomeListSectionMap/MapHeader";
import ShimmerEffect from "../ReactCommon/Components/ShimmerEffect";
import Pagination from "../ReactCommon/Components/Pagination";
import PropertiesList from "../components/HomeListSection/PropertiesList";
import MapboxDraw from "@mapbox/mapbox-gl-draw";
import mapboxgl from "!mapbox-gl"; // eslint-disable-line import/no-webpack-loader-syntax;
import "mapbox-gl/dist/mapbox-gl.css";
import "@mapbox/mapbox-gl-draw/dist/mapbox-gl-draw.css";
class Listings extends React.Component {
    constructor(props) {
        super(props);
        this.props.setMetaInfo({
            title: "map"
        });
        this.state = {
            center: [-79.39493841352393, 43.617343105067334],
            zoom: 9,
            mapData: props.mapdata,
            allData: props.alldata,
            showTxt: props.textShow,
            showCountWords: props.countInWords,
            propertySearchFilter: JSON.parse(
                JSON.stringify(initialPropertySearchFilter)
            ),
            shimmer: true,
            currentPage: 1,
            pageName: "advance search"
        };
        this.handlePropertyCall = this.handlePropertyCall.bind(this);
        this.fetchAutoSuggestion = this.fetchAutoSuggestion.bind(this);
        this.handleTypeHead = this.handleTypeHead.bind(this);
        this.changeDrawState = this.changeDrawState.bind(this);
        this.resetBtn = this.resetBtn.bind(this);
        this.mapRef = React.createRef();
        this.pageChange = this.pageChange.bind(this)

    }

    componentDidMount() {
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
        this.props.pageName("advance search");
    }

    async getFilterData() {
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
        this.setState({
            ...getFilterData,
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
        this.setState(propertySearchFilter, () => {
            this.handleTypeHead();
        });

    }

    renderPropertyData() {
        if (this.state.apiCall)
            return (
                <ShimmerEffect type="cardView" columnCls={"col-lg-6"} count={10} />
            );
        if (!this.state.allData || this.state.allData.length <= 0) return null;
        const renderData = this.state.allData.map((res, index) => {
            return (
                <div className="col-lg-6 mb-2 p-1">
                    <MapCard item={res} key={index} showIsFav={true}
                             openUserPopup={true}
                             openLoginCb={this.props.togglePopUp}
                             isLogin={this.props.isLogin}/>
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

    handleTypeHead(obj = null, name = null) {
        //console.log("this.state",this.state);
        const { propertySearchFilter } = this.state;
        const stateData = {
            mapData: [],
            allData: [],
            apiCall: false,
            showTxt: "Oops! sorry No exact matches Found",
            showCountWords: "0 Listings"
        };
        if (obj !== null && name !== null) {
            propertySearchFilter[name] = obj.value;
            this.setState({
                [name]: obj,
            });
            this.mapRef.current.updateTrash();
        }
        this.setState({ apiCall: true });
        API.jsonApiCall(propertySearchApi, propertySearchFilter, "POST", null, {
            "Content-Type": "application/json",
        })
            .then((res) => {
                let center = this.state.center;
                stateData.shimmer = false;
                if (res.mapdata) {
                    const centerLat = res.mapdata[res.mapdata.length - 1].Latitude;
                    const centeralLong = res.mapdata[res.mapdata.length - 1].Longitude;
                    center = [centeralLong, centerLat];
                    stateData.mapData = res.mapdata;
                    stateData.allData = res.alldata;
                    stateData.center = center;
                    stateData.totalData = res.total;
                    stateData.offset = res.offset;
                    stateData.limitCount = res.limit;
                    stateData.showTxt=res.textShow;
                    stateData.showCountWords=res.countInWords;
                }
                this.setState(stateData);
            })
            .catch(() => {
                this.setState(stateData);
            });
        let searchFilterValue = this.state.propertySearchFilter.text_search;
        let meta = {
            title: "Advance Search",
            slug: searchFilterValue ? "Sale Properties in " + searchFilterValue : "Sale Properties in Toronto",
            metaTitle: "Map Details",
            metaDesc: "Advance search in Toronto",
            metaKeyword: "Wedu For Sale , Wedu Finder , Wedu for Rent",
        }
        this.props.setMetaInfo(meta);
    }

    resetBtn() {
        const stateData = { apiCall: false };
        window.localStorage.removeItem("filters");
        this.setState(
            {
                propertySearchFilter: JSON.parse(
                    JSON.stringify(initialPropertySearchFilter)
                ),
                text_search: "",
                propertyType: "",
                propertySubType: "",
                price_min: "",
                price_max: "",
                baths: "",
                beds: "",
                status: "",
                sort_by: "",
                apiCall: true,
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

    render() {

        if (this.state.shimmer) {
            return <ShimmerEffect count={2} />;
        }
        return (
            <>
                <div className="p-3">
                    <MapHeader
                        autoCompleteSuggestion={this.fetchAutoSuggestion}
                        handleTypeHead={this.handleTypeHead}
                        resetBtn={this.resetBtn}
                        {...this.state}
                    />
                    <div className="row">
                        <div className="col-lg-6" style={{ maxHeight: "100vh" }}>
                            <MapLoad
                                center={this.state.center}
                                zoom={this.state.zoom}
                                handlePropertyCall={this.handlePropertyCall}
                                mapData={this.state.mapData}
                                ref={this.mapRef}
                                handleTypeHead={this.handleTypeHead}
                                changeDrawState={this.changeDrawState}
                                {...this.props}
                            />
                        </div>
                        <div className="col-lg-6" style={{ maxHeight: "100vh", overflowY: "scroll" }}>
                            <div className="col-lg-12">
                                <div className="row">
                                    <div className="col-lg-9">
                                        <p>{this.state.showTxt}</p>
                                    </div>
                                    <div className="col-lg-3 text-right">
                                        <p>{this.state.showCountWords}</p>
                                    </div>
                                </div>

                            </div>
                            <div className="row">{this.renderPropertyData()}</div>
                            <div className="d-flex justify-content-center">
                                <Pagination
                                    itemsCount={this.state.totalData}
                                    itemsPerPage={this.state.limitCount}
                                    currentPage={this.state.currentPage}
                                    setCurrentPage={this.pageChange}
                                    alwaysShown={false}
                                />
                            </div>
                        </div>
                    </div>
                </div>
            </>
        );
    }
}

export default Listings;
