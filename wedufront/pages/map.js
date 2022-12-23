import React from "react";
import "react-toastify/dist/ReactToastify.css";
import { Modal, Form } from "react-bootstrap";
import { toast } from "react-toastify";
import MapLoadv2 from "../ReactCommon/Components/MapLoadv2";
import API from "../ReactCommon/utility/api";
import MapCard from "../ReactCommon/Components/MapCard";
import MapHeader from "../components/HomeListSectionMap/MapHeader";
import Skeleton from "../ReactCommon/Components/skeleton";
import Pagination from "../ReactCommon/Components/Pagination";
import Constants from "../constants/GlobalConstants";
import {
  autoSuggestionApi,
  initialPropertySearchFilter,
  filterDataApi,
  saveSearchApi,
  agentId,
  mapSearchListApi,
  mapBoundaryApi,
  mapSearchMarkersApi,
} from "../constants/GlobalConstants";
import data1 from "../public/json/data.json";
class Map extends React.Component {
  constructor(props) {
    super(props);
    this.state = {
      center: [-79.39493841352393, 43.617343105067334],
      zoom: 8,
      mapData: props.mapdata,
      allData: [],
      showTxt: props.textShow,
      showCountWords: props.countInWords,
      propertySearchFilter: JSON.parse(
        JSON.stringify(initialPropertySearchFilter)
      ),
      shimmer: true,
      currentPage: 1,
      pageName: "advance search",
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
      resetMapDraw: false,
      filterLoaded: false,
      group: "",
      isSearched: "",
      isAddress: false,
      onMarkerClicked:false,
      mapLoader:false,
      firstTimeLoading:true
    };
    this.handlePropertyCall = this.handlePropertyCall.bind(this);
    this.fetchAutoSuggestion = this.fetchAutoSuggestion.bind(this);
    this.handleTypeHead = this.handleTypeHead.bind(this);
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
    this.setOnMarkerClicked = this.setOnMarkerClicked.bind(this);
    this.setParam = this.setParam.bind(this);
    this.getAllParams = this.getAllParams.bind(this);
  }
  componentDidMount() {
    delete initialPropertySearchFilter.Community;
    let isSearchedFlag = "";
    let isAddressFlag = "";
    const filterData = JSON.parse(localStorage.getItem("filters"));
    let filterObj = this.state.propertySearchFilter;
    if (filterData && filterData !== null) {
      let { searchFilter, preField } = filterData;
      const { text_search } = preField;
      if (text_search.group === "StandardAddress") {
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
          propertySearchFilter: filterObj,
          ...preField,
        },
        () => {
          // this.handleTypeHead();
          this.getAllParams()
        }
      );
    } else {
      // this.resetBtn();
      this.getAllParams()
    }
  }
  setOnMarkerClicked(flag){
    // console.log("zoom end  map");
    this.setState({
      onMarkerClicked:flag
    });
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
        filterLoaded: true,
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
    if (this.state.apiCall) return <Skeleton />;
    if (this.state.allData && this.state.allData.length > 0) {
      const renderData = this.state.allData.map((res, index) => {
        // blury
        let Vow_exclusive = res.Vow_exclusive ? res.Vow_exclusive : 0;
        // console.log("Stustisssss", res.Status);
        return (
          <div className={`col-lg-6 col-md-3 p-1 `} key={index}>
            {Vow_exclusive == 0 || this.props.isLogin ? (
              <></>
            ) : (
              <>
                {" "}
                <span className="vow-cls ">Login Required</span>
              </>
            )}
            {res.Status === "U" && !this.props.isLogin ? (
              <span className="vow-cls2 " onClick={this.props.togglePopUp}>
                Login Required{" "}
              </span>
            ) : (
              <> </>
            )}
            <div
              className={`  ${
                Vow_exclusive == 0 || this.props.isLogin ? "" : "filter  mt-30"
              }`}
            >
              <MapCard
                item={res}
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
  }
  fetchAutoSuggestion(fieldValue, fieldName, cb) {
    let payload = {
      query: "default",
      type: "",
    };
    let dataList = [];
    if (fieldValue) {
      let matches = data1.filter((findValue) => {
        const regex = new RegExp(`^${fieldValue}`, "gi");
        return findValue.value.match(regex);
      });
      if (fieldValue.length === 0) {
        matches = [];
      }
      if (matches.length > 0) {
        // console.log("matches", matches[0].category);
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
        // if whitespace then do not call listing id
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
        // payload.query = fieldValue;
        // payload.type = 'city';
        // requestOptions = {
        //     method: 'POST',
        //     headers: { 'Content-Type': 'application/json' },
        //     body: JSON.stringify(payload)
        // };
        // fetch(autoSuggestionApi, requestOptions).then((response) =>
        //     response.text()).then((res) => JSON.parse(res))
        //     .then((json) => {
        //         if (json.length) {
        //             dataList = dataList.concat(
        //                 json
        //             );
        //             cb({ allList: dataList });
        //         }
        //     }).catch((e) => {
        //         console.log("error", e);
        //     });
        // payload.query = fieldValue;
        // payload.type = 'county';
        // requestOptions = {
        //     method: 'POST',
        //     headers: { 'Content-Type': 'application/json' },
        //     body: JSON.stringify(payload)
        // };
        // fetch(autoSuggestionApi, requestOptions).then((response) =>
        //     response.text()).then((res) => JSON.parse(res))
        //     .then((json) => {
        //         if (json.length) {
        //             dataList = dataList.concat(
        //                 json
        //             );
        //             cb({ allList: dataList });
        //         }
        //     }).catch((e) => {
        //         console.log("error", e);
        //     });
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
    delete propertySearchFilter.ListingId;
    // delete propertySearchFilter.group;
    propertySearchFilter.shape = "rectangle";
    // propertySearchFilter.text_search = "";
    propertySearchFilter.curr_bounds = obj.bndstr;
    this.setState({
      isReset: false,
    });
    this.setState(propertySearchFilter, () => {
      this.handleTypeHead();
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
    });
    this.handleTypeHead();
  }
  propertySubData(e) {
    let val = e.target.value;
    let prev = this.state.propertySubType;
    if (prev.includes(val)) {
      let index = prev.indexOf(val);
      prev.splice(index, 1);
    } else {
      prev.push(val);
    }
    this.setState({
      propertySubType: prev,
    });
    this.handleTypeHead();
    // propertySubType: [],
  }
  highLight(e) {
    let prev = localStorage.getItem("prevOver");
    if (e.target.attributes.dataset) {
      let obj = JSON.parse(e.target.attributes.dataset.value);
      if (obj) {
        localStorage.setItem("prevOver", obj.id);
        if (prev) {
          let preMarkElm = document.getElementById("markers-" + prev);
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
          let markElm = document.getElementById("markers-" + obj.id);
          if (markElm) {
            markElm.classList.add("propHoverMarkers");
          }
        }
      }
    }
  }
  handleTypeHead(obj = null, name = null) {

    const { propertySearchFilter } = this.state;
    let boundary = false;
    let isSearchedFlag = "";
    let isAddressFlag = false;
    if (obj !== null && name !== null) {
      
      propertySearchFilter["curr_page"] = 0;
      this.setState({
        currentPage: 1,
        isAddress: false,
        onMarkerClicked:false
      });
      this.setState({
        [name]: obj,
      });
      
      if (obj.category === "ListingId") {
        propertySearchFilter["group"] = "ListingId";
        propertySearchFilter["text_search"] = obj.value;
        propertySearchFilter["shape"] = "";
        propertySearchFilter["curr_bounds"] = "";
        this.setState({
          shape: "",
          curr_path_query: "",
          isSearched: " Properties in " + obj.value,
          isAddress: true,
        });
        boundary = true;
      } else if (obj.category === "StandardAddress") {
        propertySearchFilter["StandardAddress"] = obj.value;
        propertySearchFilter["group"] = "ListingId";
        propertySearchFilter["text_search"] = obj.ListingId;
        propertySearchFilter["shape"] = "";
        propertySearchFilter["curr_bounds"] = "";
        this.setState({
          shape: "",
          curr_path_query: "",
          isSearched: " Properties in " + obj.value,
          isAddress: true,
        });
        boundary = true;
      } else {
        if(obj.category!==undefined){
          delete propertySearchFilter["StandardAddress"];
        }
        propertySearchFilter[name] = obj.value;
        delete propertySearchFilter["ListingId"];
        if (obj.group) {
          propertySearchFilter["group"] = obj.group;
        }
        if (name === "text_search") {
          propertySearchFilter["group"] = obj.group;
          this.setState({
            shape: "",
            curr_path_query: "",
            isAddress: false,
            isSearched: "",
          });
          boundary = true;
        }
        let searchElm = document.getElementById("autoSuggestion");
        if (searchElm) {
          if (searchElm.value) {
            isSearchedFlag = " Properties in " + searchElm.value;
            isAddressFlag = true;
            this.setState({
              isSearched: isSearchedFlag,
              isAddress: isAddressFlag,
            });
          }
        }
      }
    } else {

      const filterData = JSON.parse(localStorage.getItem("filters"));
      if (filterData && filterData !== null) {
        let { preField } = filterData;
        const { text_search } = preField;

        isSearchedFlag = " Properties in " + text_search.value;
        isAddressFlag = true;
        localStorage.removeItem("filters");
      }
      this.setState({
        isSearched: isSearchedFlag,
        isAddress: isAddressFlag,
      });
    }
    propertySearchFilter["propertySubType"] = this.state.propertySubType
      ? this.state.propertySubType
      : [];
    propertySearchFilter["basement"] = this.state.basementKey
      ? this.state.basementKey
      : [];
    propertySearchFilter["features"] = this.state.selectedFeatures
      ? this.state.selectedFeatures
      : [];
    let advanceSearch = propertySearchFilter;
    advanceSearch["City"] = "";
    localStorage.setItem("advanceSearch", JSON.stringify(advanceSearch));
    // this.setParam(propertySearchFilter);  
    this.getPageData(propertySearchFilter);
    // this.getAllParams(propertySearchFilter);

    let meta = {
      title: "advance search",
    };
    
    this.props.setMetaInfo(meta);
    if (propertySearchFilter.text_search) {
      this.getBoundary({ text_search: propertySearchFilter.text_search });
    }
    this.state.filterLoaded ? "" : this.getFilterData();
    this.setState({
        firstTimeLoading:false
      });
  }

  setParam(obj) {
    
    let params ="/map";  
    // window.history.pushState("", "", params);
    const propertyNames = Object.keys(obj);
    let flag = false;
    propertyNames.map((item, k) => {
      if(item!=="Sqlquery"){
        if (obj[item] === [] || obj[item] == "" || obj[item] === null) {
          return false;
        }
        if (item === "propertySubType" || item === "basement" || item === "features") {
          flag = true;
        }
        else{
          flag = false;
        }
        
        if (!params.includes("?")) {

          if (flag) {
            params = params + "?" + item + "=" + JSON.stringify(obj[item]);
          } else {

            let objItem = obj[item];
            if(item=="curr_bounds"){
               
               objItem = obj[item].replace("###", "br");
            }
            params = params + "?" + item + "=" + objItem;
            console.log(params,"filter on home abj")
          }
        } else {
          if (flag) {
            params = params + "&" + item + "=" + JSON.stringify(obj[item]);
          } else {
            let objItem = obj[item];
            if(item=="curr_bounds"){
               
               objItem = obj[item].replace("###", "br");
            }
            params = params + "&" + item + "=" + objItem;
          }
        }
      }
    });

    // window.history.pushState("", "", params);
    // console.log(this.props.Routers,"this.props.Routers")
    if(params!=="/map"){
      history.pushState("", "", params);
      // this.props.Routers.push(params)
    }
    
  }
  getAllParams(){
    const queryParams = new URLSearchParams(window.location.search);
    initialPropertySearchFilter["City"] = queryParams.get("City");
    initialPropertySearchFilter["Dom"] = queryParams.get("Dom");
    initialPropertySearchFilter["Sqft"] = queryParams.get("Sqft");
    let baths = queryParams.get("baths");
    let beds = queryParams.get("beds");
    if (baths) {
      baths = baths.replace(/[^\w\s]/gi, "");
      initialPropertySearchFilter["baths"] = baths;
      this.setState({
        'baths': {text:baths+"+",value:baths},
      });
    }
    if (beds) {
      beds = beds.replace(/[^\w\s]/gi, "");
      initialPropertySearchFilter["beds"] = beds;
      this.setState({
        'beds': {text:beds+"+",value:beds},
      });
    }
    let dom = queryParams.get("Dom");
    if (dom) {
      initialPropertySearchFilter["Dom"] = dom.replace(/[^\w\s]/gi, "");
      this.setState({
        'Dom': {text:dom+" Days",value:dom}
      });
    }
    initialPropertySearchFilter["price_max"] = queryParams.get("price_max");
    initialPropertySearchFilter["price_min"] = queryParams.get("price_min");
    initialPropertySearchFilter["sort_by"] = queryParams.get("sort_by");
    initialPropertySearchFilter["status"] = queryParams.get("status");
    initialPropertySearchFilter["text_search"] =  queryParams.get("text_search");
    initialPropertySearchFilter["group"] =  queryParams.get("group");
    initialPropertySearchFilter["StandardAddress"] =  queryParams.get("StandardAddress");
    initialPropertySearchFilter["propertyType"] =  queryParams.get("propertyType");
    if(queryParams.get("curr_bounds")){
      let curr_bounds = queryParams.get("curr_bounds")
      curr_bounds = curr_bounds.replace("br", "###")
      initialPropertySearchFilter["curr_bounds"] =  curr_bounds;
      initialPropertySearchFilter["shape"] =  queryParams.get("shape");
    }
    if (queryParams.get("status")) {
      this.setState({
        'status': {value:queryParams.get("status"),text:queryParams.get("status")},
      });
    }
    if (queryParams.get("sort_by")) {
      let val  = queryParams.get("sort_by");
      let sortStatus= Constants.sortStatus;
      let sort_by= sortStatus.find(val => val.value === queryParams.get("sort_by"));
      this.setState({
        'sort_by':sort_by
      });
    }
    if (queryParams.get("price_max")) {
      let maxP= Constants.maxPrice;
      let maxPriceObj= maxP.find(val => val.value === queryParams.get("price_max"));
      this.setState({
        'price_max': maxPriceObj
      });
    }
    if (queryParams.get("price_min")) {
      let minP= Constants.minPriceConstant;
      let price_min= minP.find(val => val.value === queryParams.get("price_min"));
      this.setState({
        'price_min': price_min
      });
    }
    if (queryParams.get("Sqft")) {
      let propSqft= Constants.propSqft;
      let Sqft= propSqft.find(val => val.value === queryParams.get("Sqft"));
      this.setState({
        'Sqft': Sqft
      });
    }
    if (queryParams.get("propertyType")) {
     
      this.setState({
        'propertyType': {text:queryParams.get("propertyType"),value:queryParams.get("propertyType")}
      });
    }
    if (queryParams.get("text_search")) {
      if(queryParams.get("StandardAddress")){
        let obj = {
              "text": queryParams.get("StandardAddress"),
              "value": queryParams.get("StandardAddress"),
          }
          this.setState({
          'text_search': obj
        }); 
      }
      else{
        this.setState({
          'text_search': {text:queryParams.get("text_search"),value:queryParams.get("text_search")}
        });
      }
      
    }
    if(queryParams.get("propertySubType")){
        this.setState({
          'propertySubType': JSON.parse(queryParams.get("propertySubType"))
        });
    }
    if(queryParams.get("basement")){
        this.setState({
          'basementKey': JSON.parse(queryParams.get("basement"))
        });
    }
    if(queryParams.get("features")){
        this.setState({
          'selectedFeatures': JSON.parse(queryParams.get("features"))
        });
    }

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
  getPageData(propertySearchFilter) {
    if(!this.state.firstTimeLoading){
      this.setParam(propertySearchFilter);
    }
    this.getMarkers(propertySearchFilter);
    this.getListdata(propertySearchFilter);
  }
  getListdata(propertySearchFilter) {
    const stateData = {
      apiCall: true,
      showTxt: "",
      showCountWords: "0 Listings",
      allData: [],
      totalData: 0,
      offset: 0,
      limitCount: 0,
    };
    this.setState(stateData);
   
    API.jsonApiCall(mapSearchListApi, propertySearchFilter, "POST", null, {
      "Content-Type": "application/json",
    })
      .then((res) => {
        stateData.showTxt="Oops! sorry No exact matches Found";
        stateData.apiCall=false;
        let center = this.state.center;
        stateData.shimmer = false;
        if (res.alldata) {
          stateData.allData = res.alldata;
          stateData.totalData = res.total;
          stateData.showCountWords = res.countInWords;
          stateData.showTxt = res.textShow;
          stateData.offset = res.offset;
          stateData.limitCount = res.limit;
          if(propertySearchFilter.StandardAddress!=='' && propertySearchFilter.group=="ListingId"){

            stateData.showTxt =  stateData.showTxt.replace(propertySearchFilter.text_search, propertySearchFilter.StandardAddress);
      
          }
          if (this.state.isAddress === true) {
             stateData.showTxt = this.state.isSearched;
          }

        }
        this.setState(stateData);
      })
      .catch(() => {
        this.setState(stateData);
      });
  }
  async getMarkers(propertySearchFilter) {
    const stateData = {
      mapData: [],
      apiCall: false,
    };
    
    this.setState({ apiCall: true,mapLoader:true });
    await API.jsonApiCall(
      mapSearchMarkersApi,
      propertySearchFilter,
      "POST",
      null,
      {
        "Content-Type": "application/json",
      }
    )
      .then((res) => {
        let center = this.state.center;
        stateData.shimmer = false;
        stateData.onMarkerClicked = true;
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
            mapLoader:false
          })

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
        stateData.cityData = res.cityData ? res.cityData : {};
        stateData.areaData = res.areaData ? res.areaData : {};
        this.setState(stateData);
        this.setState({
          cityData: res.cityData ? res.cityData : {},
          areaData: res.areaData ? res.areaData : {},
        });
      })
      .catch((e) => {});
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
      this.setState({
        searchName: e.target.value ? e.target.value : "",
      });
    }
    if (e.target.name === "frequency") {
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
        toast.success("Submit Successfully");
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
        toast.error("Something went wrong try later!");
        this.setState({
          dataFlag: false,
        });
      });
  }
  resetBtn() {
    let params ="/map?status=Sale&propertyType=Residential";  
    window.history.pushState("", "", params);
    this.setState({
      isReset: true,
    });
    const element = document.getElementsByClassName("checkboxState"); //.checked = false;
    for (let index = 0; index < element.length; index++) {
      const currElm = element[index];
      currElm.checked = false;
    }
    window.localStorage.removeItem("filters");
    const filters = Object.keys(initialPropertySearchFilter);

    if (filters && filters.length) {
      filters.map((item, k) => {
        // console.log("propertySearchFilter 22",item);
        if (item === "basement") {
          initialPropertySearchFilter[item] = [];
        }
        else if (item === "features") {
          initialPropertySearchFilter[item] = [];
        }
        else if (item === "propertySubType") {
          initialPropertySearchFilter[item] = [];
        }
        else{
          initialPropertySearchFilter[item] = "";
        }
      });
    }
    initialPropertySearchFilter['status']="Sale";
    initialPropertySearchFilter['propertyType']="Residential";
    this.setState(
      {
        propertySearchFilter: JSON.parse(
          JSON.stringify(initialPropertySearchFilter)
        ),
        text_search: "",
        propertyType: {value:"Residential",text:"Residential"},
        propertySubType: [],
        price_min: "",
        price_max: "",
        baths: "",
        beds: "",
        status: {value:"Sale",text:"Sale"},
        sort_by: "",
        Sqft: "",
        Dom: "",
        apiCall: true,
        propertySubType: [],
        basementKey: [],
        selectedFeatures: [],
        cityData: {},
        areaData: {},
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
  componentDidUpdate(prevProps, prevState) {
    if (prevState.btnShow !== this.state.btnShow) {
      this.setState({
        btnShow: this.state.btnShow,
        firstTimeLoading:false
      });
    }
  }
  render() {
    return (
      <div>
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
              <br />
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
                {this.state.loaderState ? (
                  <>
                    <input
                      id="modalSaveFilter"
                      className="btn btn-lg btn-block saveSearchForm reset-btn"
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
                      className="btn btn-lg btn-block saveSearchForm reset-btn"
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
          <button
            className="float-right mapbtn"
            onClick={() => this.setState({ mapview: !this.state.mapview })}
          >
            {this.state.mapview ? (
              <i className="fa fa-list"> Card</i>
            ) : (
              <i className="fa fa-map"> Map</i>
            )}{" "}
            view
          </button>
          <div className="row">
            <div
              className={
                !this.state.mapview ? "mapHideShow col-lg-6" : "col-lg-6"
              }
            >
              <MapLoadv2
                center={this.state.center}
                zoom={this.state.zoom}
                handlePropertyCall={this.handlePropertyCall}
                mapData={this.state.mapData}
                cityData={this.state.cityData}
                areaData={this.state.areaData}
                {...this.state}
                ref={this.mapRef}
                mapdragenCb={this.mapdragenCb}
                {...this.props}
                isReset={this.state.isReset}
                highLight={this.highLight}
                showIsFav={true}
                openUserPopup={true}
                resetMapDraw={this.state.resetMapDraw}
                onMarkerClicked={this.state.onMarkerClicked}
                setOnMarkerClicked={this.setOnMarkerClicked}
              />
            </div>
            <div className="col-lg-6 cards_wrapper cardInMobile">
              <div className="col-lg-12">
                <div className="row">
                  <div className="col-lg-9 col-6">
                    <h1 className="p-tags">{this.state.showTxt}</h1>
                  </div>
                  <div className="col-lg-3 col-6 text-right">
                    <p>{this.state.showCountWords}</p>
                  </div>
                </div>
              </div>
              <div className="row cardInMobile">
                {this.renderPropertyData()}
              </div>
              <div className="d-flex justify-content-center">
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
            </div>
          </div>
        </div>
      </div>
    );
  }
}
export default Map;
