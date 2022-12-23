import React, { useState, useEffect } from "react";
// import RedButton from "../Button/RedButton";
import { useRouter } from "next/router";
import { toast } from "react-toastify";
import "react-toastify/dist/ReactToastify.css";
import { Row, Col, Modal, Form } from "react-bootstrap";
import MapCard from "./../components/Cards/PropertyCard";
import API from "../ReactCommon/utility/api";
import Pagination from "../ReactCommon/Components/Pagination";
import ShimmerEffect from "../ReactCommon/Components/ShimmerEffect";
import MapLoadv2 from "../ReactCommon/Components/MapLoadv2";
import MapHeader from "./../components/PropertyMap/MapHeader.js";
import detect from "../ReactCommon/utility/detect";
import Constants from "../constants/Global";
import { arraysEqual } from "../constants/CommanFunctions";
let showMenuList = "";
import data1 from "../public/json/data.json";
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
} from "../constants/Global";
class ListingsV2 extends React.Component {
  constructor(props) {
    super(props);
    this.props.setMetaInfo
      ? this.props.setMetaInfo({
          title: "map",
        })
      : "";
    this.props.pageName("map");
    this.state = {
      center: [-79.39493841352393, 43.617343105067334],
      zoom: 6,
      mapData: props.mapdata,
      allData: props.alldata,
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
      features: [],
      propertySubType: [],
      basementKey: [],
      selectedFeatures: [],
      prevOver: "",
      shape: "",
      curr_path_query: "",
      cardGrid: false,
      isReset: false,
      propertyType: "",
      beds: "",
      baths: "",
      statusState: "Sale",

      propertyTypeState: "",
      bathsState: "",
      bedsState: "",
      totalpage: 0,
      activeSold: "A",
      isPopularSearched: false,
      cityMarker: "",
      isDraged: false,
      isFocus: false,
      group: "",
      isSearched: "",
      isZoomed:false,      
      isAddress: false,
      isDefaultSubTypes: false,
      showMapInMobile: false,
      open: false,
      isPopular: false,
      mapLoading: true,
      defaultPropertySubTypes: [
        "Detached",
        "Semi-Detached",
        "Freehold Townhouse",
        "Condo Townhouse",
        "Condo Apt",
      ],
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
    this.setCardGrid = this.setCardGrid.bind(this);
    this.handleStatusType = this.handleStatusType.bind(this);
    this.gotoDetailPage = this.gotoDetailPage.bind(this);
    this.showmenu = this.showmenu.bind(this);
    this.handleMoreFilter = this.handleMoreFilter.bind(this);
    this.ActiveSold = this.ActiveSold.bind(this);
    this.getPageData = this.getPageData.bind(this);
    this.getListdata = this.getListdata.bind(this);
    this.getBoundary = this.getBoundary.bind(this);
    // this.getListdata = this.getListdata.bind(this);
    // this.getListdata = this.getListdata.bind(this);
    this.getPriceFilter = this.getPriceFilter.bind(this);
    // this.handleTypeHeadClick = this.handleTypeHeadClick.bind(this);
    this.PopularSearch = this.PopularSearch.bind(this);
    this.searchOnFocus = this.searchOnFocus.bind(this);
    this.searchOutFocus = this.searchOutFocus.bind(this);
    this.setParam = this.setParam.bind(this);
    this.getAllParams = this.getAllParams.bind(this);
    this.setInCardGrid = this.setInCardGrid.bind(this);
    this.openModal = this.openModal.bind(this);
  }

  componentDidMount() {
    const propertyNames = Object.keys(this.props.Routers.query);
    if (localStorage.getItem("isPopular")) {
      this.setState({
        isPopular: true,
      });
      setTimeout(() => {
        localStorage.removeItem("isPopular");
      }, 200);
    }
    this.getAllParams();
    this.getFilterData();
    this.fetchMoreFilterData();
    setTimeout(() => {
      this.setState({
        cardGrid: true,
      });
    }, 2000);
    // this.props.pageName("advance search");
  }
  searchOnFocus() {
    this.setState({
      isFocus: true,
    });
  }
  searchOutFocus() {
    this.setState({
      isFocus: false,
    });
  }
  PopularSearch() {
    let isSearchedFlag = "";
    let isAddressFlag = "";
    this.setState({
      currentPage: 1,
    });
    let status = "Sale";
    let localStatus = localStorage.getItem("status");
    if (localStatus) {
      if (localStatus == "Sold") {
        // this.setState({
        //   activeSold: "U",
        // });
      } else {
        status = localStatus;
      }
      localStorage.removeItem("status");
    }
    // this.setState({
    //   statusState: status,
    // });
    // if (localStorage.getItem("propertytype")) {
    // 	let propertytype = "Residential";
    // 	if(localStorage.getItem("propertytype")!==null){
    // 		propertytype = localStorage.getItem("propertytype");
    // 	}

    // 	this.state.propertySearchFilter.propertyType = propertytype;
    // }
    const filterData = JSON.parse(localStorage.getItem("filters"));
    const prop_subtype = localStorage.getItem("subtype");
    localStorage.removeItem("subtype");
    if (prop_subtype && prop_subtype !== null) {
      // this.setState({
      //   propertySubType: prop_subtype.split(","),
      // });
    }
    localStorage.removeItem("filters");
    // getting city name
    let filterObj = this.state.propertySearchFilter;
    if (filterData && filterData !== null) {
      let { searchFilter, preField } = filterData;
      const { text_search } = preField;
      // getting city name
      if (preField.text_search.category == "Cities") {
        // this.setState({
        //   cityMarker: preField.text_search.value,
        //   isAddress: true,
        // });
      } else if (text_search.group === "StandardAddress") {
        searchFilter.group = "ListingId"; //ListingId
        searchFilter.text_search = text_search.ListingId;
      } else {
        searchFilter.group = text_search.group;
        searchFilter.text_search = text_search.value;
      }
      filterObj = { ...filterObj, ...searchFilter };
      this.setState(
        {
          isSearched: isSearchedFlag,
          isAddress: isAddressFlag,
        },
        () => {
          // this.handleTypeHead(
          //   preField.text_search ? preField.text_search : null,
          //   "text_search"
          // );
        }
      );
    } else {
      // this.resetBtn();
    }
    this.getAllParams();
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
    localStorage.setItem("moreFilters", JSON.stringify(getFilterData));
    this.setState({
      ...getFilterData,
    });
  }
  setCardGrid(state) {
    this.setState({
      cardGrid: state,
    });
  }
  setInCardGrid(state) {
    this.setState({
      showMapInMobile: state,
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
        <ShimmerEffect type="cardView" columnCls={"col-lg-12"} count={10} />
      );
    if (!this.state.allData || this.state.allData.length <= 0) return null;
    const renderData = this.state.allData.map((res, index) => {
      return (
        <div
          className={`${
            this.state.cardGrid ? "col-lg-6" : "col-xl-3 col-lg-4 col-md-6"
          } mb-2 p-1`}
        >
          <MapCard
            item={res}
            key={index}
            showIsFav={true}
            openUserPopup={true}
            openLoginCb={this.props.togglePopUp}
            isLogin={this.props.isLogin}
            emailIsVerified={this.props.emailIsVerified}
            highLightCb={this.highLight}
          />
        </div>
      );
    });
    return renderData;
  }
  gotoDetailPage(e) {}

  fetchAutoSuggestion(fieldValue, fieldName, cb) {
    let payload = {
      query: "default",
      type: "",
    };
    let dataList = [];
    if (fieldValue) {
      let matches = data1.filter((findValue) => {
        const regex = new RegExp(`^${fieldValue}`, "gi");
        if (findValue.value !== null) {
          return findValue.value.match(regex);
        }
      });
      if (fieldValue.length === 0) {
        matches = [];
      }
      if (matches.length > 0) {
        let temp_list = [];
        let temp_city = "";
        let temp_community = false;

        matches.map((item, key) => {
          if (key == 0) {
            if (item.category == "Cities") {
              let obj = {
                isHeading: true,
                text: "Cities",
                value: "Cities",
                category: "Cities",
                group: "City",
              };
              temp_list.push(obj);
              temp_list.push(item);
            }
            if (item.category === "Neighborhood") {
              let obj = {
                isHeading: true,
                text: "Neighborhood",
                value: "Neighborhood",
                category: "Neighborhood",
                group: "Community",
              };
              temp_list.push(obj);
              temp_list.push(item);
            }
          } else {
            if (item.category === "Neighborhood") {
              if (!temp_community) {
                let obj = {
                  isHeading: true,
                  text: "Neighborhood",
                  value: "Neighborhood",
                  category: "Neighborhood",
                  group: "Community",
                };
                temp_list.push(obj);
                temp_community = true;
              }
            }
            temp_list.push(item);
          }
        });
        cb({ allList: temp_list });
      } else {
        let requestOptions = {};

        payload.query = fieldValue;
        payload.type = "address";
        requestOptions = {
          method: "POST",
          headers: { "Content-Type": "application/json" },
          body: JSON.stringify(payload),
        };
        fetch(autoSuggestionApi, requestOptions)
          .then((response) => response.text())
          .then((res) => JSON.parse(res))
          .then((json) => {
            if (json.length) {
              dataList = dataList.concat(json);
              cb({ allList: dataList });
            }
          })
          .catch((e) => {
            console.log("error", e);
          });
        if (fieldValue.indexOf(" ") <= 0) {
          payload.query = fieldValue;
          payload.type = "listingId";
          requestOptions = {
            method: "POST",
            headers: { "Content-Type": "application/json" },
            body: JSON.stringify(payload),
          };
          fetch(autoSuggestionApi, requestOptions)
            .then((response) => response.text())
            .then((res) => JSON.parse(res))
            .then((json) => {
              if (json.length) {
                dataList = dataList.concat(json);
                cb({ allList: dataList });
              }
            })
            .catch((e) => {
              console.log("error", e);
            });
        }
      }
    } else {
      localStorage.removeItem("suggestionList");
      cb({ allList: [] });
    }
  }

  mapdragenCb(obj) {
    const { propertySearchFilter } = this.state;
    if (propertySearchFilter.shape == "polygon") {
      return;
    }
    // this.mapRef.current.updateTrash();
    propertySearchFilter.shape = "rectangle";
    if (propertySearchFilter.text_search) {
      if (!propertySearchFilter.text_search.group == "City") {
        propertySearchFilter.text_search = "";
      }
    }
    propertySearchFilter.curr_bounds = obj.bndstr;
    this.setState({
      isReset: false,
      isDraged: true,
      isZoomed:true
      
    });
    this.setState(propertySearchFilter, () => {
      this.handleTypeHead();
    });
  }
  async fetchMoreFilterData() {
    let uri = filterDataApi;
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
    ).then((getMoreFilterData) => {
      this.setState({
        moreFilterData: getMoreFilterData,
      });
    });
  }
  featuresData(e) {
    let val = e.target.value;
    let prev = this.state.selectedFeatures;
    if (prev.includes(val)) {
      let index = prev.indexOf(val);
      prev.splice(index, 1);
    } else {
      prev.push(val);
    }
    this.setState({
      selectedFeatures: prev,
      currentPage: 1,
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
      prev.push(val);
    }
    this.setState({
      basementKey: prev,
      currentPage: 1,
    });

    this.handleTypeHead();
    // basement: [],
  }
  propertySubData(e) {
    let val = e.target.value;
    let prev = this.state.propertySubType;
    if (prev.length < 1 && !e.target.checked) {
      const queryParams = new URLSearchParams(window.location.search);
      prev = JSON.parse(queryParams.get("propertySubType"));
    }
    let others = [];
    if (val == "All") {
      prev = [];
    } else if (val == "others") {
      let subtype = this.state.subtype;
      let PredefinedSubType = Constants.SubType;
      for (var i = 0; i < subtype.length; i++) {
        if (!PredefinedSubType.includes(subtype[i].value)) {
          if (prev.includes(subtype[i].value)) {
            let index = prev.indexOf(subtype[i].value);
            prev.splice(index, 1);
          } else {
            prev.push(subtype[i].value);
          }
        }
      }
    } else {
      if (prev.includes(val) && !e.target.checked) {
        let index = prev.indexOf(val);
        prev.splice(index, 1);
      } else {
        if (e.target.checked) {
          prev.push(val);
        } else {
          if (prev.includes(val)) {
            let index = prev.indexOf(val);
            prev.splice(index, 1);
          }
        }
      }
    }
    this.setState(
      {
        propertySubType: prev,
        currentPage: 1,
      },
      () => {
        this.handleTypeHead();
      }
    );
  }
  highLight(e) {
    let prev = localStorage.getItem("prevOver");
    if (e.target.attributes.dataset) {
      let obj = JSON.parse(e.target.attributes.dataset.value);
      if (obj) {
        localStorage.setItem("prevOver", obj.id);

        if (prev) {
          let preMarkElm = document.getElementById("marker-" + prev);
          let preCardElm = document.getElementById("propCard" + prev);
          if (preMarkElm) {
            preMarkElm.classList.remove("propHoverMarkers");
          }
          if (preCardElm) {
            preCardElm.classList.remove("markersHoverBorder");
          }
        }
        if (obj.ismap) {
          let cardElm = document.getElementById("propCard" + obj.id);
          if (cardElm) {
            cardElm.classList.add("markersHoverBorder");
          }
        } else {
          let markElm = document.getElementById("marker-" + obj.id);
          if (markElm) {
            markElm.classList.add("propHoverMarkers");
          }
        }
      }
    }
  }
  handleTypeHeadSearch(obj = null, name = null, e, inp) {
    if (name !== undefined) {
      initialPropertySearchFilter[name] = obj.value;
      this.setState({
        text_search: obj.value,
      });
    }
  }
  // // handleTypeHead
  // handleTypeHeadClick(e) {
  //     setCurrentPage(1);
  //     setFlag(true);
  // propertySearchFilter[name]
  // }
  handleStatusType(e) {
    let status = e.target.value;
    this.setState({
      statusState: status,
      currentPage: 1,
    });

    setTimeout(() => {
      this.handleTypeHead();
    }, 150);
  }
  ActiveSold(e) {
    let status = e.target.value;
    const { propertySearchFilter } = this.state;
    this.setState(
      {
        propertySearchFilter: JSON.parse(JSON.stringify(propertySearchFilter)),
        Dom: "",
      },
      () => {
        this.handleTypeHead();
      }
    );
    this.setState({
      activeSold: status,
      currentPage: 1,
    });

    // setTimeout(() => {
    // 	this.handleTypeHead();
    // }, 150);
  }
  showmenu(e) {
    try {
      let cl = document.getElementsByClassName("checkbox-opt-container");
      for (var i = 0; i < cl.length; i++) {
        cl[i].classList.remove("checkbox-opt-showing");
      }
      if (e !== undefined) {
        if (e !== "close") {
          if (e.target.classList[0] == "theme-text") {
            let panel = e.target.nextElementSibling;
            panel.classList.add("checkbox-opt-showing");
          }
        }
      }
    } catch (error) {}
  }
  handleMoreFilter(e) {
    let value = e.target.value;
    let name = e.target.name;
    const _self = this;
    if (name === "propertyType") {
      this.setState({
        propertyTypeState: value,
      });
    }
    if (name === "beds") {
      this.setState({
        bedsState: value,
      });
    }
    if (name === "baths") {
      this.setState({
        bathsState: value,
      });
    }
    this.setState({
      currentPage: 1,
    });
    setTimeout(() => {
      this.handleTypeHead();
    }, 150);
  }
  handleTypeHead(obj = null, name = null) {
    if (localStorage.getItem("calledSearches")) {
      return;
    }
    setTimeout(() => {
      localStorage.removeItem("calledSearches");
    }, 250);
    this.setState({
      mapLoading: true,
    });
    localStorage.setItem("calledSearches",true);
    const { propertySearchFilter } = this.state;
    if (this.state.isReset) {
      delete propertySearchFilter["group"];
      this.setState({
        isZoomed:false
      });
    }
    
    let boundary = false;
    let isNavSearch = false;
    let isSearchedFlag = "";
    let realtEstate = "";
    let isAddressFlag = false;
    let objs = { totalData: 0 };

    if (obj) {
      this.setState({
        isZoomed:false
      });
      if (obj.category == "Cities") {
        this.setState({
          cityMarker: obj.value,
        });
        boundary = true;
      }
      isNavSearch = obj.isNavSearch;
    }
    this.setState(objs);
    const stateData = {
      mapData: [],
      allData: [],
      cityData: {},
      areaData: {},
      apiCall: false,
      showTxt: "Oops! sorry No exact matches Found",
      showCountWords: "0 Listings",
      totalpage: 0,
      city_content: "",
    };
    propertySearchFilter["features"] = [];
    propertySearchFilter["basement"] = [];
    if (this.state.propertySubType && this.state.propertySubType.length) {
      if (!localStorage.getItem("navmapClicked")) {
        localStorage.removeItem("navmapClicked");
      }
      propertySearchFilter["propertySubType"] = this.state.propertySubType;
      this.setState({
        isDefaultSubTypes: false,
      });
    } else {
      this.setState({
        isDefaultSubTypes: true,
      });
      propertySearchFilter["propertySubType"] =
        this.state.defaultPropertySubTypes;
    }
    if (this.state.basementKey) {
      propertySearchFilter["basement"] = this.state.basementKey;
    }
    if (this.state.selectedFeatures) {
      propertySearchFilter["features"] = this.state.selectedFeatures;
    }
    if (this.state.statusState) {
      propertySearchFilter["status"] = this.state.statusState;
    } else propertySearchFilter["status"] = "";

    if (this.state.propertyTypeState) {
      propertySearchFilter["propertyType"] = this.state.propertyTypeState;
    }
    if (this.state.bathsState) {
      propertySearchFilter["baths"] = this.state.bathsState;
    }
    if (this.state.bathsState === "Any") {
      propertySearchFilter["baths"] = "";
    }
    if (this.state.bedsState) {
      propertySearchFilter["beds"] = this.state.bedsState;
    }
    if (this.state.bedsState === "Any") {
      propertySearchFilter["beds"] = "";
    }
    if (this.state.activeSold) {
      propertySearchFilter["soldStatus"] = this.state.activeSold;
    }
    if (obj !== null && name !== null) {
      propertySearchFilter["curr_page"] = 0;
      propertySearchFilter[name] = obj.value;
      this.setState({
        currentPage: 1,
        isAddress: false,
      });
      this.setState({
        [name]: obj,
      });
      if (obj.group) {
        propertySearchFilter["group"] = obj.group;
        if (
          obj.group === "Community" ||
          obj.group === "City" ||
          obj.group === "Cities"
        ) {
          boundary = true;
        }
      }
      if(name==="text_search"){
        propertySearchFilter["curr_bounds"] = "";
        propertySearchFilter["shape"] = "";
        this.setState({
          isZoomed:false
        });
      }

      if (obj.category === "ListingId") {
        propertySearchFilter["group"] = "ListingId";
        propertySearchFilter["text_search"] = obj.value;
        propertySearchFilter["shape"] = "";
        propertySearchFilter["curr_bounds"] = "";
        propertySearchFilter["status"] = "";
        propertySearchFilter["propertyType"] = "";
        delete propertySearchFilter["propertyType"];
        delete propertySearchFilter["soldStatus"];
        realtEstate = obj.value;
        this.setState({
          shape: "",
          curr_path_query: "",
          isSearched: " Properties in " + obj.value,
          isAddress: true,
        });
      } else if (obj.category === "StandardAddress") {
        propertySearchFilter["group"] = "ListingId";
        propertySearchFilter["text_search"] = obj.ListingId;
        propertySearchFilter["shape"] = "";
        propertySearchFilter["curr_bounds"] = "";
        propertySearchFilter["status"] = "";
        delete propertySearchFilter["propertyType"];
        delete propertySearchFilter["soldStatus"];
        realtEstate = obj.value;
        this.setState({
          shape: "",
          curr_path_query: "",
          isSearched: " Properties in " + obj.value,
          isAddress: true,
        });
      } else {
        let propertytypes = localStorage.getItem("propertytype");
        if (propertytypes) {
          propertySearchFilter["propertyType"] = propertytypes;
          localStorage.removeItem("propertytype");
        }
        propertySearchFilter[name] = obj.value;
        delete propertySearchFilter["ListingId"];
        if (obj.group) {
          propertySearchFilter["group"] = obj.group;
        }
        if (name === "text_search") {
          propertySearchFilter["group"] = obj.group ? obj.group : "";
          this.setState({
            shape: "",
            curr_path_query: "",
            isAddress: false,
            isSearched: "",
          });
        }
        let searchElm = document.getElementById("searchByText");
        if (searchElm) {
          if (searchElm.value) {
            isSearchedFlag =
              " Properties in " + propertySearchFilter.text_search;
            isAddressFlag = true;
            realtEstate = searchElm.value;
          }
          this.setState({
            isSearched: isSearchedFlag,
            isAddress: isAddressFlag,
          });
        }
      }
    }
    let advanceSearch = propertySearchFilter;
    advanceSearch["City"] = "";
    localStorage.setItem("advanceSearch", JSON.stringify(advanceSearch));
    this.setState({ apiCall: true, isReset: false });
    if (propertySearchFilter["group"] === "ListingId") {
      propertySearchFilter["shape"] = "";
      propertySearchFilter["curr_bounds"] = "";
      propertySearchFilter["propertyType"] = "";
      propertySearchFilter["Dom"] = "";
      propertySearchFilter["propertySubType"] = [];
      delete propertySearchFilter["propertyType"];
      delete propertySearchFilter["soldStatus"];
    }

    if (propertySearchFilter.fromHome) {
      // propertySearchFilter["text_search"] = propertySearchFilter.value;
      propertySearchFilter["shape"] = "";
      propertySearchFilter["curr_bounds"] = "";
      propertySearchFilter["group"] = "";
      propertySearchFilter["Dom"] = "";
      propertySearchFilter["propertySubType"] = [];
      delete propertySearchFilter["propertyType"];
      delete propertySearchFilter["soldStatus"];
      delete propertySearchFilter["fromHome"];
      if (localStorage.getItem("fromHome")) {
        propertySearchFilter["status"] = propertySearchFilter.status
          ? propertySearchFilter.status
          : "Sale";
      } else {
        delete propertySearchFilter["status"];
      }
    }

    if (propertySearchFilter.group === "Cities") {
      propertySearchFilter["group"] = "City";
    }
    if (
      propertySearchFilter.status === "Sold"  
    ) {
      propertySearchFilter["status"] = "Sale";
      // propertySearchFilter["soldStatus"] = "U";
    }
    if (!boundary)
      boundary = propertySearchFilter.group === "City" ? true : false;
    if (boundary && !isNavSearch) {
     
      this.setState({
        cityMarker: propertySearchFilter.text_search,
      });
      this.getBoundary({ text_search: propertySearchFilter.text_search });
    }
    this.getPageData(propertySearchFilter);
    let filtersData = localStorage.getItem("moreFilters");
    if (filtersData) {
      filtersData = JSON.parse(filtersData);
      this.setState({
        ...filtersData,
        textSearch: realtEstate ? realtEstate : this.state.textSearch,
      });
    }
    let searchFilterValue = this.state.propertySearchFilter.text_search;
    let meta = {
      title: "Advance Search",
      slug: searchFilterValue
        ? "Sale Properties in " + searchFilterValue
        : "Sale Properties in Toronto",
      metaTitle: "Map Details",
      metaDesc: "Advance search in Toronto",
      metaKeyword: "Wedu For Sale , Wedu Finder , Wedu for Rent",
    };
    this.props.setMetaInfo(meta);
  }
  getPageData(propertySearchFilter) {
    let dom = propertySearchFilter.Dom;
    this.getMarkers(propertySearchFilter);
    this.getListdata(propertySearchFilter);
    if (dom && dom.includes("+")) {
      propertySearchFilter["morethan"] = 1;
    }
    if (this.state.allData !== undefined) {
      this.setParam(propertySearchFilter);
    }

    if (this.state.isPopular) {
      this.setParam(propertySearchFilter);
    }
  }
  getListdata(propertySearchFilter) {
    const stateData = {
      apiCall: false,
      showTxt: "Oops! sorry No exact matches Found",
      showCountWords: "0 Listings",
      allData: [],
      totalData: 0,
      limitCount: 0,
      totalpage: 0,
    };
    if (!detect.isMobile() && this.state.isDraged) {
      window.scrollTo({ behavior: "smooth", top: "10px" });
    }
    this.setState({ apiCall: true });
    API.jsonApiCall(mapSearchListApi, propertySearchFilter, "POST", null, {
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
          stateData.totalpage = Math.floor(res.total / 12) + 1;
          stateData.city_content = res.city_content;
          if (this.state.isAddress === true) {
            stateData.showTxt = this.state.isSearched;
          }
        }
        let searchElm = document.getElementById("searchByText");
        if (searchElm) {
          if (searchElm.value) {
            let isSearchedFlag = " Properties in " + searchElm.value;
            let isAddressFlag = true;
            let realtEstate = searchElm.value;
            this.setState({
              isSearched: isSearchedFlag,
              isAddress: isAddressFlag,
              textSearch: realtEstate,
            });
          }
        }
        this.setState(stateData);
        // console.clear();
      })
      .catch(() => {
        this.setState(stateData);
      });
  }
  getMarkers(propertySearchFilter) {
    const stateData = {
      mapData: [],
      apiCall: false,
    };
    let uri = mapSearchMarkersApi;
    this.setState({ apiCall: true });
    API.jsonApiCall(uri, propertySearchFilter, "POST", null, {
      "Content-Type": "application/json",
    })
      .then((res) => {
        let center = this.state.center;
        stateData.shimmer = false;
        if (res.mapdata) {
          const centerLat = res.mapdata
            ? res.mapdata[res.mapdata.length - 1].Latitude
            : 565;
          const centeralLong = res.mapdata
            ? res.mapdata[res.mapdata.length - 1].Longitude
            : -656;
          center = [centeralLong, centerLat];
          stateData.mapData = res.mapdata;
          stateData.center = center;
        }
        this.setState(stateData);
        this.setState({
          mapLoading: false,
        });
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
        stateData.shimmer = false;
        if (res.cityData) {
          stateData.cityData = res.cityData;
          stateData.center = res.cityData
            ? res.cityData[res.cityData.length - 1]
            : [-656, 565];
        }
        if (res.areaData) {
          stateData.areaData = res.areaData;
          stateData.center = res.areaData
            ? res.areaData[res.areaData.length - 1]
            : [-656, 565];
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
      modalShow: state,
    });
  }
  changeHendler(e) {
    if (e.target.name === "filter_name") {
      //
      this.setState({
        searchName: e.target.value ? e.target.value : "",
      });
    }
    if (e.target.name === "frequency") {
      // btnShow
      this.setState({
        frequency: e.target.value ? e.target.value : "",
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
      agentId: agentId,
    };
    let urls = saveSearchApi;
    // urls ="http://127.0.0.1:8000/api/v1/services/saveSearch";
    API.jsonApiCall(urls, data, "POST", null, {
      "Content-Type": "application/json",
    })
      .then((res) => {
        try {
          toast.success("Saved Successfully");
        } catch (error) {}

        this.SavedSearch(false);
        this.setState({
          frequency: "",
          searchName: "",
          filtersData: "",
          btnShow: true,
          loaderState: false,
        });
      })
      .catch((e) => {
        try {
          toast.error("Something went wrong try later!");
        } catch (error) {}
        this.setState({
          dataFlag: false,
        });
      });
  }
  resetBtn() {
    // let params = "/map?City=&PropertyType=&price_min=&price_max=&Sqft=&baths=&beds=&curr_page=1&sort_by=&status=&text_search=";
    let params = window.location.origin + "/map";
    window.history.pushState("", "", params);
    params = "/map?status=Sale&soldStatus=A&Dom=90";
    window.history.pushState("", "", params);
    localStorage.removeItem("fromHome");
    const element = document.getElementsByClassName("checkboxState"); //.checked = false;
    for (let index = 0; index < element.length; index++) {
      const currElm = element[index];
      currElm.checked = false;
    }
    var btn = document.getElementsByClassName("mapbox-gl-draw_trash");
    for (let i = 0; i < btn.length; i++) {
      if (btn[i]) {
        btn[i].click();
      }
    }
    const stateData = { apiCall: false };
    window.localStorage.removeItem("filters");
    localStorage.removeItem("status");
    localStorage.removeItem("propertytype");
    // propertySearchFilter
    const filters = Object.keys(initialPropertySearchFilter);

    if (filters && filters.length) {
      filters.map((item, k) => {
        if (item === "text_search") {
          initialPropertySearchFilter[item] = "";
        } else if (item === "Dom") {
          initialPropertySearchFilter[item] = "90";
        } else if (item === "soldStatus") {
          initialPropertySearchFilter[item] = "A";
        } else {
          initialPropertySearchFilter[item] = "";
        }
      });
    }
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
        isReset: true,
        isDefaultSubTypes: true,
        Sqft: "",
        Dom: "",
        apiCall: true,
        basementKey: [],
        selectedFeatures: [],
        statusState: "Sale",
        propertyTypeState: "",
        bathsState: "",
        bedsState: "",
        textSearch: "",
        showTxt: "",
        showCountWords: "",
        isSearched: "",
        isAddress: false,
        currentPage: "",
        totalpage: 0,
        activeSold: "A",
        cityMarker: "",
        cityData: {},
        areaData: {},
        currentPage: 1,
        isDraged:false
      },
      () => {
        this.handleTypeHead();
      }
    );
    
  }

  pageChange(e) {
    let propertyPayLoad = this.state.propertySearchFilter;
    propertyPayLoad.curr_page = e;
    this.setState(
      {
        currentPage: e,
        propertySearchFilter: propertyPayLoad,
      },
      () => {
        this.handleTypeHead();
      }
    );
  }

  changeDrawState(e) {
    let propertyPayLoad = this.state.propertySearchFilter;
    propertyPayLoad.curr_path_query = "";
    propertyPayLoad.shape = "";
    this.setState(
      {
        propertySearchFilter: propertyPayLoad,
        currentPage: 1,
      },
      () => {
        this.handleTypeHead();
      }
    );
  }
  // setting url peremeters
  setParam(obj) {
    let params = "/map";
    // window.history.pushState("", "", params);
    const propertyNames = Object.keys(obj);
    let flag = false;
    propertyNames.map((item, k) => {
      if (obj[item] === [] || obj[item] == "" || obj[item] === null) {
        return false;
      }
      if (item === "propertySubType") {
        flag = true;
      } else {
        flag = false;
      }
      if (!params.includes("?")) {
        if (flag) {
          params = params + "?" + item + "=" + JSON.stringify(obj[item]);
        } else {
          params = params + "?" + item + "=" + obj[item];
        }
      } else {
        if (flag) {
          params = params + "&" + item + "=" + JSON.stringify(obj[item]);
        } else {
          params = params + "&" + item + "=" + obj[item];
        }
      }
    });
    // window.history.pushState("", "", params);
    this.props.Routers.push(params);
  }
  getAllParams() {
    if (this.props.Routers.asPath == "/map") {
      let params = "/map?";
      params += `propertySubType=["Detached","Semi-Detached","Freehold Townhouse","Condo Townhouse","Condo Apt"]&propertyType=Residential&status=Sale&Dom=90&soldStatus=A`;
      this.props.Routers.push(params);
      setTimeout(() => {
        location.reload();
      }, 200);
    }
    const queryParams2 = new URLSearchParams(window.location.href);
    const queryParams = new URLSearchParams(window.location.search);
    initialPropertySearchFilter["City"] = queryParams.get("City");
    let dom = queryParams.get("Dom");
    let morethan = queryParams.get("morethan");
    if (morethan && dom) {
      dom = dom.trim();
      dom = dom + "+";
    }
    initialPropertySearchFilter["Dom"] = dom;
    initialPropertySearchFilter["Sqft"] = queryParams.get("Sqft");
    let fromHome = queryParams.get("fromHome");
    initialPropertySearchFilter["fromHome"] = fromHome;
    if (fromHome) {
      localStorage.setItem("fromHome", true);
      this.setState({
        defaultPropertySubTypes: "",
        activeSold: "",
        propertySearchFilter: {
          propertyType: "",
          Dom: "",
        },
      });
    }
    let groups = queryParams.get("group");
    if (groups) {
      initialPropertySearchFilter["group"] = groups;
    }
    let baths = queryParams.get("baths");
    let beds = queryParams.get("beds");
    if (baths) {
      baths = baths.replace(/[^\w\s]/gi, "");
      this.setState({
        bathsState: baths,
      });
      initialPropertySearchFilter["baths"] = baths;
    }
    if (beds) {
      beds = beds.replace(/[^\w\s]/gi, "");
      this.setState({
        bedsState: beds,
      });
      initialPropertySearchFilter["beds"] = beds;
    }
    let propertySubType = queryParams.get("propertySubType");
    try {
      propertySubType = JSON.parse(propertySubType);
    } catch (error) {}
    if (propertySubType && propertySubType.length) {
      let res = arraysEqual(
        this.state.defaultPropertySubTypes,
        propertySubType
      );
      if (res && groups) {
        this.setState({
          isDefaultSubTypes: true,
        });
      } else {
        this.setState({
          isDefaultSubTypes: false,
          propertySubType: propertySubType,
        });
      }
    }
    if(localStorage.getItem('isPopularCity')){
      this.setState({
        isDefaultSubTypes: true,
        propertySubType: propertySubType,
      });
    }

    initialPropertySearchFilter["curr_bounds"] =
      queryParams2.get("curr_bounds");
    initialPropertySearchFilter["curr_page"] = queryParams.get("curr_page");
    initialPropertySearchFilter["curr_path_query"] =
      queryParams.get("curr_path_query");
    initialPropertySearchFilter["features"] = queryParams.get("features");
    initialPropertySearchFilter["multiplePropType"] =
      queryParams.get("multiplePropType");
    initialPropertySearchFilter["openhouse"] = queryParams.get("openhouse");
    initialPropertySearchFilter["price_max"] = queryParams.get("price_max");
    initialPropertySearchFilter["price_min"] = queryParams.get("price_min");
    initialPropertySearchFilter["shape"] = queryParams.get("shape");
    let sort = queryParams.get("sort_by");
    if (sort) {
      Constants.sortStatus.map((item, k) => {
        if (sort === item.value) {
          this.setState({
            sort_by: item,
          });
        }
      });
      initialPropertySearchFilter["sort_by"] = sort;
    }
    initialPropertySearchFilter["PropertyType"] =
      queryParams.get("PropertyType");
    initialPropertySearchFilter["soldStatus"] = queryParams.get("soldStatus");
    let subType = Constants.SubType;
    let subArr = [];
    for (let i = 0; i < subType.length; i++) {
      let sub = subType[i];
      if (subType[i] != "Semi-Detached") {
        sub = subType[i].replace(" ", "-");
      }
      if (queryParams.get(sub)) {
        subArr.push(subType[i]);
      }
    }
    let status = queryParams.get("status") ? queryParams.get("status") : "Sale";
    let localStatus = localStorage.getItem("status");
    if (localStatus) {
      if (localStatus == "Sold") {
        this.setState({
          activeSold: "U",
        });
      } else {
        status = localStatus;
      }
      localStorage.removeItem("status");
    }
    this.setState({
      statusState: status,
    });
    // initialPropertySearchFilter["text_search"] = queryParams.get("text_search");

    let textSearches = queryParams.get("text_search");
    if (textSearches) {
      initialPropertySearchFilter["text_search"] = textSearches;
      let ob = {
        text: textSearches,
        value: textSearches,
      };
      this.setState({
        text_search: ob,
      });
    }
    initialPropertySearchFilter["propertySubType"] = subArr;
    const filterData = JSON.parse(localStorage.getItem("filters"));
    let filterObj = this.state.propertySearchFilter;

    if (filterData && filterData !== null) {
      let { searchFilter, preField } = filterData;
      const { text_search } = preField;
      if (preField.text_search.category == "Cities") {
        this.setState({
          cityMarker: preField.text_search.value,
          isAddress: true,
        });
      } else if (text_search.group === "StandardAddress") {
        initialPropertySearchFilter["group"] = "ListingId"; //ListingId
        initialPropertySearchFilter["text_search"] = text_search.ListingId;
      } else {
        if (text_search.group) {
          initialPropertySearchFilter["group"] = text_search.group;
        }
        initialPropertySearchFilter["text_search"] = text_search.value;
      }
      filterObj = { ...filterObj, ...searchFilter };
      this.setState(
        {
          // propertySearchFilter: filterObj,
          textSearch: text_search.value,
          ...preField,
          isSearched: "",
          isAddress: "",
          propertySearchFilter: JSON.parse(
            JSON.stringify(initialPropertySearchFilter)
          ),
        },
        () => {
          this.handleTypeHead();
        }
      );
      localStorage.removeItem("filters");
    } else {
      this.setState(
        {
          propertySearchFilter: JSON.parse(
            JSON.stringify(initialPropertySearchFilter)
          ),
        },
        () => {
          this.handleTypeHead();
        }
      );
    }
    // City=&PropertyType=&price_min=&price_max=&Sqft=&baths=&beds=&curr_page=1&sort_by=&status=&text_search=
  }
  // end
  componentDidUpdate(prevProps, prevState) {
    if (prevState.btnShow !== this.state.btnShow) {
      this.setState({
        btnShow: this.state.btnShow,
      });
    }
    if (this.props.Routers.asPath !== prevProps.Routers.asPath) {
      this.PopularSearch();
    }
    // if (this.props.popularSearchCheck !== prevProps.popularSearchCheck) {
    //   if (this.props.popularSearchCheck === true) {
    //     window.scrollTo({ behavior: "smooth", top: "10px" });
    //     this.props.popularSearch();
    //   }
    //   // this.handleTypeHead()
    // }
  }
  getPriceFilter(minPrice, maxPrice) {
    this.state.propertySearchFilter["price_min"] = minPrice;
    this.state.propertySearchFilter["price_max"] = maxPrice;
    this.handleTypeHead();
  }
  openModal(status) {
    this.setState({
      open: status,
    });
  }
  render() {
    if (this.state.shimmer) {
      return <ShimmerEffect count={2} />;
    }
    return (
      <>
        <div className="savedSearch">
          <Modal
            show={this.state.modalShow}
            onHide={() => this.SavedSearch(false)}
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
              <input
                id="name_search"
                type="text"
                onChange={this.changeHendler}
                onBlur={this.changeHendler}
                className="form-control form-input-border"
                name="filter_name"
                placeholder="Give A Name to your search"
                required="required"
              />
              <br />
              <h4>Setup New Listing Alert</h4>
              <p className="mt-3">Receive Alerts Frequency</p>

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
                label="Weekly"
                name="frequency"
                type="radio"
                value="weekly"
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
                {this.state.loaderState ? (
                  <>
                    <input
                      id="modalSaveFilter"
                      className="button btndisabled  submitButton mt-3 pt-1 pb-2 col-xs-12 col-md-12"
                      type="button"
                      value="Submiting....."
                      disabled={true}
                    ></input>
                  </>
                ) : (
                  <>
                    <input
                      id="modalSaveFilter"
                      onClick={this.handleSaveSubmit}
                      className="button  submitButton mt-3 pt-1 pb-2 col-xs-12 col-md-12"
                      type="button"
                      value="Save Filter"
                      disabled={this.state.btnShow}
                    ></input>
                  </>
                )}
              </div>
            </Modal.Body>
          </Modal>
        </div>
        <div className="p-3">
          <MapHeader
            autoCompleteSuggestion={this.fetchAutoSuggestion}
            handleTypeHead={this.handleTypeHead}
            resetBtn={this.resetBtn}
            savedSearch={this.SavedSearch}
            featuresData={this.featuresData}
            basementData={this.basementData}
            handleStatusType={this.handleStatusType}
            showmenu={this.showmenu}
            handleMoreFilter={this.handleMoreFilter}
            openLoginCb={this.props.togglePopUp}
            // handleTypeHeadClick={this.handleTypeHeadClick}
            propertySubData={this.propertySubData}
            ActiveSold={this.ActiveSold}
            getPriceFilter={this.getPriceFilter}
            searchOnFocus={this.searchOnFocus}
            searchOutFocus={this.searchOutFocus}
            {...this.state}
            openModal={this.openModal}
          />
          <div className="container-fluid">
            <div className="row">
              <div className="col-md-12 col-lg-12 mt-1">
                <strong className="mobileHidse">
                  <ul className="map-heading-cls">
                    {this.state.textSearch && (
                      <li className="mapheading-cls-li">
                        <h1 className="">
                          {this.state.textSearch} Real Estate {"	"}
                        </h1>
                      </li>
                    )}
                    <li>
                      <span>
                        {" "}
                        {"	"} {this.state.totalData}{" "}
                        {this.state.showTxt
                          ? this.state.showTxt.replace(
                              "Residential Properties",
                              "Homes"
                            )
                          : ""}{" "}
                        | Page{" "}
                        {this.state.totalpage == 0 ? 0 : this.state.currentPage}{" "}
                        of {this.state.totalpage}
                      </span>
                    </li>
                  </ul>
                </strong>
                <div className="toggle-btn">
                  <button
                    className={`${this.state.cardGrid ? "" : " active"} btn`}
                    onClick={() => this.setCardGrid(false)}
                  >
                    <img src="../images/icon/grid.png" className="icon-size" />
                  </button>
                  <button
                    className={`${this.state.cardGrid ? "active" : " "} btn`}
                    onClick={() => this.setCardGrid(true)}
                  >
                    <img src="../images/icon/map.png" className="icon-size" />
                  </button>
                </div>
              </div>
            </div>
            {this.state.flag && (
              <ShimmerEffect
                type="cardView"
                columnCls={"col-lg-3"}
                count={10}
              />
            )}
            {!this.state.flag && !this.state.cardGrid && (
              <div className="row in_mobile_grid">
                {this.renderPropertyData()}
              </div>
            )}
          </div>
          <div className="">
            {this.state.showTxt && (
              <p className="text-center">
                {this.state.allData.length ? "" : this.state.showTxt}
              </p>
            )}
          </div>
          {!this.state.flag && this.state.cardGrid && (
            <>
              <div className="mobile_map_toggle">
                <button
                  className={`searchBtn btn pe-0`}
                  onClick={() => this.openModal(true)}
                >
                  <span className="grid-map-shadow">
                    <i className="fa fa-search" aria-hidden="true"></i>
                  </span>
                </button>
                {this.state.showMapInMobile && (
                  <button
                    className={`${
                      this.state.showMapInMobile ? "" : " active"
                    } btn gridViewbtn`}
                    onClick={() => this.setInCardGrid(false)}
                  >
                    <span className="grid-map-shadow">
                      <i className="fa fa-th" aria-hidden="true"></i> Grid
                    </span>
                  </button>
                )}

                {!this.state.showMapInMobile && (
                  <button
                    className={`${
                      this.state.showMapInMobile ? "active" : " "
                    } btn`}
                    onClick={() => this.setInCardGrid(true)}
                  >
                    <span className="grid-map-shadow">
                      <i className="fa fa-map-marker" aria-hidden="true"></i>{" "}
                      Map
                    </span>
                  </button>
                )}
              </div>
              <div className="row ">
                <div className="col-md-6 col-lg-6 mapScroll mobileOrder2">
                  <div className="mobileShow">
                    {this.state.textSearch && (
                      <li className="mapheading-cls-li">
                        <h1 className="">
                          {this.state.textSearch} Real Estate {"  "}
                        </h1>
                      </li>
                    )}
                    <strong>
                      {this.state.totalData}{" "}
                      {this.state.showTxt
                        ? this.state.showTxt.replace(
                            "Residential Properties",
                            "Homes"
                          )
                        : ""}{" "}
                      | Page{" "}
                      {this.state.totalpage == 0 ? 0 : this.state.currentPage}{" "}
                      of {this.state.totalpage}
                    </strong>
                  </div>
                  <div className="row">
                    {this.renderPropertyData()}
                    {/* {this.renderPropertyData()} */}
                  </div>
                </div>
                <div
                  className={`col-md-6 col-lg-6 mobileOrder1 ${
                    this.state.showMapInMobile ? "" : "map_view"
                  }`}
                >
                  {!detect.isMobile() && (
                    <MapLoadv2
                      togglePopUp={this.props.togglePopUp}
                      isLogin={this.props.isLogin}
                      handlePropertyCall={this.handlePropertyCall}
                      mapData={this.state.mapData}
                      handleTypeHead={this.handleTypeHead}
                      changeDrawState={this.changeDrawState}
                      // isReset={this.state.isReset}
                      // cityproperty={this.cityproperty}
                      highLightCb={this.highLight}
                      mapdragenCb={this.mapdragenCb}
                      {...this.state}
                      gotoDetailPage={this.gotoDetailPage}
                      cityMarker={this.state.cityMarker}
                      soldOrActive={this.state.activeSold}
                      mobileView={false}
                    />
                  )}
                  {detect.isMobile() && this.state.showMapInMobile && (
                    <MapLoadv2
                      togglePopUp={this.props.togglePopUp}
                      isLogin={this.props.isLogin}
                      handlePropertyCall={this.handlePropertyCall}
                      mapData={this.state.mapData}
                      handleTypeHead={this.handleTypeHead}
                      changeDrawState={this.changeDrawState}
                      isReset={this.state.isReset}
                      isZoomed={this.state.isZoomed}
                      // cityproperty={this.cityproperty}
                      highLightCb={this.highLight}
                      mapdragenCb={this.mapdragenCb}
                      {...this.state}
                      gotoDetailPage={this.gotoDetailPage}
                      cityMarker={this.state.cityMarker}
                      soldOrActive={this.state.activeSold}
                      mobileView={true}
                    />
                  )}
                </div>
              </div>
              <br />
            </>
          )}

          {/* <div className="row">{this.renderPropertyData()}</div> */}
          <div className="d-flex justify-content-center ">
            {this.state.totalData > 1 && (
              <Pagination
                itemsCount={this.state.totalData}
                itemsPerPage={this.state.limitCount}
                currentPage={this.state.currentPage}
                setCurrentPage={this.pageChange}
                alwaysShown={false}
              />
            )}
          </div>
          <div className="row mt-4">
            <div className="col-md-3 col-lg-3"></div>
            <div className="col-md-6 col-lg-6">
              <div
                dangerouslySetInnerHTML={{ __html: this.state.city_content }}
              ></div>
            </div>
            <div className="col-md-3 col-lg-3"></div>
          </div>
        </div>
      </>
    );
  }
}
export default ListingsV2;
