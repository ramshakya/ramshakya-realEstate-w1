
import React, { useState, useEffect } from 'react';
// import RedButton from "../Button/RedButton";
import { withRouter, useRouter } from "next/router";
import { Row, Col, Modal, Form } from "react-bootstrap";
import { requestToAPI } from "../api/api";
import MapCard from "../../components/Cards/PropertyCard";
import API from "../../ReactCommon/utility/api";
import Pagination from "../../ReactCommon/Components/Pagination";
import ShimmerEffect from "../../ReactCommon/Components/ShimmerEffect";
import MapLoadv2 from "../../ReactCommon/Components/MapLoadv2";
import Link from "next/link";
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
} from "../../constants/Global";
import Constants from "../../constants/Global";

import MapHeader from "../../components/PropertyMap/MapHeader.js";

let showMenuList = "";

class ListingsV2 extends React.Component {

  constructor(props) {

    super(props);
    this.props.setMetaInfo ?
      this.props.setMetaInfo({
        title: "map"
      }) : "";
    this.props.pageName('map');
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
      activeSold: 'A',
      cityMarker: '',
      inputCity:true,
      nextUrl:"",
      SlugUrl:false

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
    this.getCityName = this.getCityName.bind(this);
    this.getPageData = this.getPageData.bind(this);

        this.getListdata = this.getListdata.bind(this);
        this.getBoundary = this.getBoundary.bind(this);
        // this.getListdata = this.getListdata.bind(this);
        // this.getListdata = this.getListdata.bind(this);
    this.getPriceFilter = this.getPriceFilter.bind(this);
    // this.handleTypeHeadClick = this.handleTypeHeadClick.bind(this);
    this.PopularSearch = this.PopularSearch.bind(this);
    // this.getCityData = this.getCityData.bind(this);

  }

  getCityName(str,str1){
    let arr = [];
    arr.push("city")
    arr.push(str)
    arr.push(str1)
    console.log("array",arr); 
    this.setState({
      SlugUrl:arr
    })
    // this.props.popularSearchCheck(true);
    
  }
  componentDidMount() {

    // this.getCityName();
    this.PopularSearch();
    
    this.getFilterData();
    this.fetchMoreFilterData();
    // this.props.pageName("advance search");
  }
  PopularSearch(str=null,str1=null){
    this.setState({
      currentPage: 1
    })
    let status = "Sale";
    let slug = url => new URL(url).pathname.match(/[^\/]+/g)
    let slugs = slug(window.location.href);
    if(str!==null){
    // console.log("slugUrl",);
    slugs = str;

       // slugs = str;
    }
   
    console.log("slugs",slugs);
    this.setState({
      nextUrl:slugs[1]
    })
     if (slugs.length == 2) {
        this.state.propertySearchFilter['City'] = slugs[1];
        delete this.state.propertySearchFilter.Community;
      }
      else if (slugs.length > 2) {
        this.state.propertySearchFilter['City'] = slugs[1];

        this.state.propertySearchFilter['Community'] = slugs[2].replaceAll("%20", " ");
      }
      else {
        // console.log('count = sorry');
      }

    this.handleTypeHead();
    // if (localStorage.getItem("status")) {
    //   if(localStorage.getItem("status")=="Sold"){
    //     this.setState(
    //     {
    //       activeSold:"U"
    //     });
    //   }
    //   else
    //   {
    //     status = localStorage.getItem("status");
    //   }
    // }
    // this.setState(
    //     {
    //       statusState: status
    //     }
    //   );

    // // if (localStorage.getItem("propertytype")) {
    // //  let propertytype = "Residential";
    // //  if(localStorage.getItem("propertytype")!==null){
    // //    propertytype = localStorage.getItem("propertytype");
    // //  }
        
    // //  this.state.propertySearchFilter.propertyType = propertytype;
    // // }
    // const filterData = JSON.parse(localStorage.getItem("filters"));
    // // getting city name
    
    // let filterObj = this.state.propertySearchFilter;
    // if (filterData && filterData !== null) {

    //   const { searchFilter, preField } = filterData;
    //   // getting city name
    //   if (preField.text_search.category == 'Cities') {
    //     this.setState(
    //       {
    //         cityMarker: preField.text_search.value
    //       })
    //   }

    //   // end
    //   filterObj = { ...filterObj, ...searchFilter };
    //   this.setState(
    //     {
    //       propertySearchFilter: filterObj,
    //       ...preField,
    //       statusState: status
    //     },
    //     () => {
    //       this.handleTypeHead();
    //     }
    //   );
    // } else {
    //       this.handleTypeHead();

    //   // this.resetBtn();
    // }
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
      cardGrid: state
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
        <div className={`${this.state.cardGrid ? "col-lg-6" : "col-lg-3"} mb-2 p-1`}>
          <MapCard item={res} key={index}
            showIsFav={true}
            openUserPopup={true}
            openLoginCb={this.props.togglePopUp}
            isLogin={this.props.isLogin}
            highLightCb={this.highLight}
          />
        </div>
      );
    });
    return renderData;
  }
  gotoDetailPage(e) {
    console.log("gotoDetailPage", e);
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
      let arr = [];
      let index = 0;
      for (var i = 0; i < res.length; i++) {
        if (res[i].category !== "Cities" && res[i].category !== "Municipality") {
          arr[index] = res[i];
          index++;
        }
      }
      cb({ allList: arr });
    });
  }

  mapdragenCb(obj) {
    const { propertySearchFilter } = this.state;
    console.log("mapdragenCb", propertySearchFilter);
    console.log("mapdragenCb1", obj);
    if (propertySearchFilter.shape == "polygon") {
      return;
    }
    // this.mapRef.current.updateTrash();
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
    const getMoreFilterData = await API.jsonApiCall(
      uri,
      {},
      "GET",
      null,
      {
        "Content-Type": "application/json",
      },
      { is_search: 0 }
    );
    this.setState({
      moreFilterData: getMoreFilterData,
    });
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
      selectedFeatures: prev,
      currentPage: 1
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
      basementKey: prev,
      currentPage: 1
    });

    this.handleTypeHead();
    // basement: [],
  }
  propertySubData(e) {

    let val = e.target.value;
    let prev = this.state.propertySubType;
    let others = [];
    if (val === "All") {
      prev = [];

    }
    else if (val === "others") {
      let subtype = this.state.subtype;

      let PredefinedSubType = Constants.SubType;
      for (var i = 0; i < subtype.length; i++) {
        if (!PredefinedSubType.includes(subtype[i].value)) {
          if (prev.includes(subtype[i].value)) {
            let index = prev.indexOf(subtype[i].value);
            prev.splice(index, 1);
          } else {
            prev.push(subtype[i].value)
          }
        }
      }
    }
    else {
      if (prev.includes(val)) {
        let index = prev.indexOf(val);
        prev.splice(index, 1);
      } else {
        prev.push(val)
      }
    }
    console.log("others", prev);

    this.setState({
      propertySubType: prev,
      currentPage: 1
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
  handleTypeHeadSearch(obj = null, name = null, e, inp) {
    if (name !== undefined) {
      initialPropertySearchFilter[name] = obj.value;
      this.setState({
        text_search: obj.value
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
    console.log("handleStatusType", e.target.value);
    let status = e.target.value;
    this.setState({
      statusState: status,
      currentPage: 1
    });

    setTimeout(() => {
      this.handleTypeHead();
    }, 150);
  }
  ActiveSold(e) {
    let status = e.target.value;
    console.log("handleStatusType", e.target.value);

    this.setState({
      activeSold: status,
      currentPage: 1
    });

    setTimeout(() => {
      this.handleTypeHead();
    }, 150);
  }
  showmenu(e) {
    try {
      console.log("showmenu", e);
      let cl = document.getElementsByClassName('checkbox-opt-container');
      for (var i = 0; i < cl.length; i++) {
        cl[i].classList.remove("checkbox-opt-showing");
      }
      if (e !== undefined) {
        if (e !== 'close') {
          if (e.target.classList[0] == 'theme-text') {
            let panel = e.target.nextElementSibling;
            panel.classList.add('checkbox-opt-showing');
          }
        }
      }
    } catch (error) {

    }
  }
  handleMoreFilter(e) {
    let value = e.target.value;
    let name = e.target.name;
    const _self = this;
    if (name === "propertyType") {
      console.log("===>>>", e.target.name);
      this.setState({
        propertyTypeState: value
      });

    }
    if (name === "beds") {
      console.log("===>>>", e.target.name);
      this.setState({
        bedsState: value
      });
    }
    if (name === "baths") {
      console.log("===>>>", e.target.name);
      this.setState({
        bathsState: value
      });
    }
    this.setState({
      currentPage: 1
    })
    setTimeout(() => {
      this.handleTypeHead();
    }, 150);
  }
  handleTypeHead(obj = null, name = null) {
    if (obj) {
      if (obj.category == "Cities") {
        this.setState(
          {
            cityMarker: obj.value
          })
      }
      console.log("handleTypeHead", this.state.cityMarker);

    }
    let objs = { totalData: 0 }
    this.setState(objs);
    const { propertySearchFilter } = this.state;
    const stateData = {
      mapData: [],
      allData: [],
      cityData: {},
      areaData: {},
      apiCall: false,
      showTxt: "Oops! sorry No exact matches Found",
      showCountWords: "0 Listings",
      totalpage: 0
    };
    if (obj !== null && name !== null) {
      propertySearchFilter[name] = obj.value;
      this.setState({
        [name]: obj,
      });
      if (name === "text_search") {
        // this.mapRef.current.updateTrash();
        try {
          var btn = document.getElementsByClassName('mapbox-gl-draw_trash');
          btn[0].click();
        } catch (error) {

        }
        this.changeDrawState();
        this.setState({
          shape: "",
          curr_path_query: "",
          isDrawBtnEnabled: true
        });
        this.getBoundary({ text_search: obj.value });
      }
    }
    propertySearchFilter["features"] = [];
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
    if (this.state.statusState) {
      propertySearchFilter["status"] = this.state.statusState
    }

    if (this.state.propertyTypeState) {
      propertySearchFilter["propertyType"] = this.state.propertyTypeState
    }
    if (this.state.bathsState) {
      propertySearchFilter["baths"] = this.state.bathsState
    }
    if (this.state.bedsState) {
      propertySearchFilter["beds"] = this.state.bedsState
    }
    if (this.state.activeSold) {
      propertySearchFilter["soldStatus"] = this.state.activeSold
    }
    let advanceSearch = propertySearchFilter;
    // advanceSearch['City'] = "";
    localStorage.setItem("advanceSearch", JSON.stringify(advanceSearch));
    let uri = propertySearchApi;
    // uri = "http://127.0.0.1:8000/api/v1/services/search/propertiesSearch";
    this.setState({ apiCall: true });
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
    // this.props.setMetaInfo(meta);
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
            allData: []

        };
        let uri = mapSearchListApi;
        this.setState({ apiCall: true });
        window.scrollTo({ behavior: 'smooth', top: '10px' });
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
                    stateData.totalpage = (Math.floor(res.total / 12)) + 1;
                    // getCityData();
                }
                else
                {
                  stateData.totalpage=0;
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
                    const centerLat = res.mapdata ? res.mapdata[res.mapdata.length - 1].Latitude : 565;
                    const centeralLong = res.mapdata ? res.mapdata[res.mapdata.length - 1].Longitude : -656;
                    center = [centeralLong, centerLat];
                    stateData.mapData = res.mapdata;
                    stateData.center = center;

                }
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
      try {
        toast.success("Submit Successfully");
      } catch (error) {
      }
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
        try {
          toast.error("Something went wrong try later!");
          
        } catch (error) {
          
        }
        this.setState({
          dataFlag: false
        });
      });
  }
  resetBtn() {
    this.setState({
            isReset: true,
        });
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
    localStorage.removeItem("status")
    localStorage.removeItem("propertytype")
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
        statusState: "Sale",
        propertyTypeState: "",
        bathsState: "",
        bedsState: "",
      },
      () => {
        this.handleTypeHead();
      }
    );
    this.setState({
      currentPage: 1
    })
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
      propertySearchFilter: propertyPayLoad,
      currentPage: 1
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
    // if(this.props.popularSearchCheck!==prevProps.popularSearchCheck){
    //   console.log("popularSearchCheck");
    //   this.PopularSearch();
    //   // this.handleTypeHead()
    //   this.props.popularSearch(false);
    // }
    if(this.state.SlugUrl!==prevState.SlugUrl){

         this.PopularSearch(this.state.SlugUrl);
         this.setState({
          SlugUrl:false
         })
         // this.handleTypeHead()
    }
    
    // console.log("popularSearchCheck",this.props);
    
  }
  getPriceFilter(minPrice,maxPrice)
  {
    this.state.propertySearchFilter['price_min'] = minPrice
    this.state.propertySearchFilter['price_max'] = maxPrice
    this.handleTypeHead();

    // console.log("PriceList",initialPropertySearchFilter);

  }
  render() {

    if (this.state.shimmer) {
      return <ShimmerEffect count={2} />;
    }
    let PreviousType = '';
    let PreviousCommunity = '';
    let array1 = [];
    let array2 = [];
    if(this.state.allData){
    for (var i = 0; i < this.state.allData.length; i++) {
      if (!array1.includes(this.state.allData[i].Community)) {
        array1[i] = this.state.allData[i].Community;
      }
      if (!array2.includes(this.state.allData[i].PropertySubType)) {
        array2[i] = this.state.allData[i].PropertySubType;
      }
    }
  }
    
    // let Slugurl = location.pathname.split('/').slice(1)
    // if(Slugurl.length>2){
    //   let city = Slugurl[2];
    //   let st = city.replaceAll("%20", " ");
    // }
    
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
            {...this.state}
          />
          <div className="row p-3">
        <div className="col-md-12 col-lg-12">
            <div>
              <p className=""><b>Community:</b> <span style={{ 'color': 'var(--grey)' }}>
                {array1.map((item, index) => {
                  let VrLine = " | ";
                  if (item !== '') {
                    if ((array1.length - 1) === index) {
                      VrLine = "";
                    }
                    return (
                      <Link href={this.state.nextUrl + '/' + item} key={index + 'community'}>
                        <a className="color-msg custom-anc"  onClick={()=>this.getCityName(this.state.nextUrl,item)}><span className="span1">{item}</span><span className="">{VrLine}</span></a></Link>

                    )
                  }
                })}
              </span></p>
              {/*<p><b>Sub Type:</b> <span style={{ 'color': 'var(--grey)' }}>
                {array2.map((item, index) => {
                  let VrLine1 = " | ";
                  if ((array2.length - 1) === index) {
                    VrLine1 = "";
                  }
                  return (
                    <Link href={'/propertytype/' + item} key={index + 'subtype'}>
                      <a className="color-msg custom-anc"><span className="span1">{item}</span><span className="">{VrLine1}</span></a></Link>
                  )
                })}
              </span></p>*/}
            </div>
          </div>
        </div>
          <div className="container-fluid">
            <div className="row">
              <div className="col-md-12 col-lg-12 pb-2 mt-2"><strong>{this.state.totalData}   {this.state.showTxt}| Page {this.state.totalpage == 0 ? 0 : this.state.currentPage} of {this.state.totalpage}</strong>
                <div className="toggle-btn">
                  <button className={`${this.state.cardGrid ? '' : ' active'} btn`} onClick={() => this.setCardGrid(false)}><img src="../images/icon/grid.png" className="icon-size" /></button>
                  <button className={`${this.state.cardGrid ? 'active' : ' '} btn`} onClick={() => this.setCardGrid(true)}><img src="../images/icon/map.png" className="icon-size" /></button>
                </div>
              </div>
            </div>
            {this.state.flag &&
              <ShimmerEffect type="cardView" columnCls={"col-lg-3"} count={10} />
            }
            {!this.state.flag && !this.state.cardGrid &&
              <div className="row">{this.renderPropertyData()}</div>
            }
          </div>
          {this.state.showTxt && <p className="text-center">{this.state.allData.length ? "" : this.state.showTxt}</p>}
          {!this.state.flag && this.state.cardGrid && <>
            <div className="row ">
              <div className="col-md-6 col-lg-6 mapScroll" style={{ maxHeight: "100vh", overflowY: "scroll" }}>
                <div className="row">{this.renderPropertyData()}
                  {/* {this.renderPropertyData()} */}

                </div>
              </div>
              <div className="col-md-6 col-lg-6">
                <MapLoadv2
                  togglePopUp={this.props.togglePopUp}
                  isLogin={this.props.isLogin}
                  handlePropertyCall={this.handlePropertyCall}
                  mapData={this.state.mapData}
                  handleTypeHead={this.handleTypeHead}
                  changeDrawState={this.changeDrawState}
                  isReset={this.state.isReset}
                  // cityproperty={this.cityproperty}
                  highLightCb={this.highLight}
                  mapdragenCb={this.mapdragenCb}
                  {...this.state}
                  gotoDetailPage={this.gotoDetailPage}
                  cityMarker={this.state.cityMarker}
                  soldOrActive={this.state.activeSold}
                />

              </div>
            </div>
            <br />
          </>
          }

          {/* <div className="row">{this.renderPropertyData()}</div> */}
          <div className="d-flex justify-content-center ">
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
      </>
    );
  }
}

export default withRouter(ListingsV2);
