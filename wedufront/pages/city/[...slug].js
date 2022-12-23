// Library
import { useState, useEffect } from "react";
import { Container, Row, Col, Form } from "react-bootstrap";
import Pagination from "../../ReactCommon/Components/Pagination";
import CardRow from "../../ReactCommon/Components/MapCard";
import API from "../../ReactCommon/utility/api";
import { requestToAPI } from "../../pages/api/api";
import Link from "next/link";
import Map from "../../components/City/city_map";
import MapHeader from "../../components/City/CityMapHeader";
import { useRouter } from 'next/router';
import map from "../../public/images/icons/map.png"
import grid from "../../public/images/icons/grid.png"
import {
  propertySearchApi,
  autoSuggestionApi,
  initialPropertySearchFilter,
  filterDataApi,
} from "../../constants/GlobalConstants";
import ShimmerEffect from "../../ReactCommon/Components/ShimmerEffect";

const City = (props) => {
  // console.log(props);
  const router = useRouter();
  const slugs = router.query.slug;
  let prevOver = "";
  const [showForm, setShowForm] = useState(true);
  const [changeCard, setChangeCard] = useState("col");
  let toggle = !showForm;
  const toggleCardBtn = (value) => {
    setChangeCard(value);
  };
  const [property, setProperty] = useState([]);
  const [total, setTotal] = useState('');
  const [pagination, setPagination] = useState('');
  const [curr_page, setCurr_page] = useState(1);
  const [total_page, setTotal_page] = useState('');
  const [limitCount, setLimitCount] = useState('');
  const [mapData, setMapData] = useState('');
  const [polygonsData, setPolygonsData] = useState('');
  const [flag, setFlag] = useState(true);

  const [shimmer, setShimmer] = useState(true);
  const [notFound, setNotFound] = useState(false);
  const [loader, setLoader] = useState(true);
  const [selectedFeatures, setSelectedFeatures] = useState([]);
  const [basementKey, setBasementKey] = useState([]);
  const [propertySubType, setPropertySubType] = useState([]);
  const [mapDrag, setMapDrag] = useState();
  const [nextUrl, setNextUrl] = useState('');
  const [preState, setPreState] = useState(true);

  useEffect(() => {
    let obj = {
      selectedFeatures: [],
      propertySubType: [],
      basementKey: [],
    }
    localStorage.setItem("morefilters", JSON.stringify(obj));
  }, []);

  useEffect(() => {
    const fetchProperties = async () => {
      window.scrollTo({ behavior: 'smooth', top: '10px' });
      setNextUrl(slugs[0]);
      if (slugs.length == 1) {
        initialPropertySearchFilter['City'] = slugs[0];
        delete initialPropertySearchFilter.Community;
      }
      else if (slugs.length > 1) {
        initialPropertySearchFilter['City'] = slugs[0];

        initialPropertySearchFilter['Community'] = slugs[1];
      }
      else {
      }
      //check
      initialPropertySearchFilter["features"] = [];
      initialPropertySearchFilter["basement"] = [];
      initialPropertySearchFilter["propertySubType"] = [];
      let filtersData = localStorage.getItem("morefilters");
      if (filtersData) {
        filtersData = JSON.parse(filtersData);
        initialPropertySearchFilter["features"] = filtersData.selectedFeatures
        initialPropertySearchFilter["basement"] = filtersData.basementKey
        initialPropertySearchFilter["propertySubType"] = filtersData.propertySubType
      }
      if (mapDrag) {
        initialPropertySearchFilter["shape"] = mapDrag.bndstr ? "rectangle" : "";
        // initialPropertySearchFilter["text_search"] = "";
        initialPropertySearchFilter["curr_bounds"] = mapDrag.bndstr;
      }
      initialPropertySearchFilter['curr_page'] = curr_page;
      let advanceSearch = initialPropertySearchFilter;
      // advanceSearch['City'] = "";
      localStorage.setItem("advanceSearch", JSON.stringify(advanceSearch));
      const body = JSON.stringify(initialPropertySearchFilter)
      const json = await requestToAPI(body, "search/propertiesSearch", "POST");
      if (json.alldata) {
        setProperty(json.alldata);
        setPagination(json.pagination);
        setTotal(json.total);
        setMapData(json.mapdata);
        setPolygonsData(json);
        setTotal_page((Math.floor(json.total / 12)) + 1);
        setLimitCount(json.limit);
        setNotFound(false);
      }
      else {
        setProperty([]);
        setPagination('');
        setTotal(0);
        setMapData([]);
        setTotal_page(0);
        setLimitCount(0)

        setNotFound(json.textShow)
      }
      setLoader(false);
      setFlag(false);

    };
    if (flag && slugs !== undefined) {
      fetchProperties();
    }


  }, [curr_page, flag, slugs]);



  function handleChange(str) {
    if (str !== 0) {
      setCurr_page(str);
      setLoader(true);
      setFlag(true);
    }
    //
    // console.log(loader);
  };
  function deleteArea(e) {
    let data = this.draw.getAll();
    if (this.map.getLayer('maine')) {
      this.map.removeLayer('maine');
    }
  }

  function handleTypeHead(obj = null, name = null) {
    if (name === "text_search") {
      console.log("text search", name);
      var btn = document.getElementsByClassName('mapbox-gl-draw_trash');
      try {
        btn[0].click();
      } catch (error) {

      }
      // deleteArea();
      // this.setState({ isDrawBtnEnabled: true });
      // this.props.changeDrawState();
      setMapDrag({});
    }

    initialPropertySearchFilter[name] = obj.value;
    setFlag(true);
    // setShimmer(true)
    setLoader(true);
  }
  function resetBtn() {
    window.localStorage.removeItem("filters");
    try {
      var btn = document.getElementById('clear_draw');
      btn.click();
    } catch (error) {
    }
    setMapDrag('');
    // document.getElementById('propertyType').value = "";
    // document.getElementById('propertyType').value="";
    // document.getElementById('Sqft').value = "";
    // document.getElementById('Dom').value = "";

    initialPropertySearchFilter['text_search'] = ""
    initialPropertySearchFilter['propertyType'] = ""
    initialPropertySearchFilter['propertySubType'] = ""
    initialPropertySearchFilter['price_min'] = ""
    initialPropertySearchFilter['price_max'] = ""
    initialPropertySearchFilter['baths'] = ""
    initialPropertySearchFilter['beds'] = ""
    initialPropertySearchFilter['status'] = ""
    initialPropertySearchFilter['sort_by'] = ""
    initialPropertySearchFilter['curr_path_query'] = ""
    initialPropertySearchFilter['shape'] = "";
    initialPropertySearchFilter['Sqft'] = "";
    initialPropertySearchFilter['Dom'] = "";
    initialPropertySearchFilter['curr_bounds'] = "";

    let obj = {
      selectedFeatures: [],
      propertySubType: [],
      basementKey: [],
    }
    setPropertySubType([]);
    setBasementKey([]);
    setSelectedFeatures([]);
    localStorage.setItem("morefilters", JSON.stringify(obj));
    setFlag(true);
    // setShimmer(true)
    setExtraFlag(true);
    resetPreState();
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
        if (res[i].category !== "Cities" && res[i].category !== "Municipality") {
          arr[index] = res[i];
          index++;
        }
      }
      cb({ allList: arr });
    });
  }
  function handlePropertyCall(coordinates, geometryType) {
    console.log("handlePropertyCall", coordinates, geometryType);
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
    // initialPropertySearchFilter.text_search = "";
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
    getFilterData();
  }, [extraFlag]);

  const [show, setShow] = useState(false);
  useEffect(() => {
    if (screen.width < 650) {
      setShow(true);
    }
  }, []);
  function mapdragenCb(obj) {
    if (initialPropertySearchFilter.shape == "polygon") {
      return;
    }
    setMapDrag(obj);
    setFlag(true);
    setLoader(true);
    // handleTypeHead();
  }

  function resetPreState() {
    setPreState(!preState);
  }

  function featuresData(e) {
    console.log("featuresData", e);
    resetPreState();
    let val = e.target.value;
    let prev = selectedFeatures;
    if (prev.includes(val)) {
      let index = prev.indexOf(val);
      prev.splice(index, 1);
    } else {
      prev.push(val)
    }
    setSelectedFeatures(prev);
    let filtersData = localStorage.getItem("morefilters");
    if (filtersData) {
      filtersData = JSON.parse(filtersData);
      filtersData = {
        selectedFeatures: prev,
        propertySubType: filtersData.propertySubType,
        basementKey: filtersData.basementKey,
      }
      localStorage.setItem("morefilters", JSON.stringify(filtersData));
    } else {
      let obj = {
        selectedFeatures: prev,

      }
      localStorage.setItem("morefilters", JSON.stringify(obj));
    }
    setFlag(true);
    // setShimmer(true)
    setLoader(true);

  }
  function changeDrawState() {
    initialPropertySearchFilter['curr_path_query'] = ""
    initialPropertySearchFilter['shape'] = "";
    setFlag(true);
    setLoader(true);
  }
  function basementData(e) {
    resetPreState();
    console.log("basementData", e);
    let val = e.target.value;
    let prev = basementKey;
    if (prev.includes(val)) {
      let index = prev.indexOf(val);
      prev.splice(index, 1);
    } else {
      prev.push(val)
    }
    let filtersData = localStorage.getItem("morefilters");
    if (filtersData) {
      filtersData = JSON.parse(filtersData);
      filtersData = {
        selectedFeatures: filtersData.selectedFeatures,
        propertySubType: filtersData.propertySubType,
        basementKey: prev,
      }
      localStorage.setItem("morefilters", JSON.stringify(filtersData));
    } else {
      let obj = {
        basementKey: prev,
      }
      localStorage.setItem("morefilters", JSON.stringify(obj));
    }
    setBasementKey(prev);
    setFlag(true);
    // setShimmer(true)
    setLoader(true);
  }
  function propertySubData(e) {
    resetPreState();
    console.log("propertySubData", e);
    let val = e.target.value;
    let prev = propertySubType;
    if (prev.includes(val)) {
      let index = prev.indexOf(val);
      prev.splice(index, 1);
    } else {
      prev.push(val);
    }
    setPropertySubType(prev);
    let filtersData = localStorage.getItem("morefilters");
    if (filtersData) {
      filtersData = JSON.parse(filtersData);
      filtersData = {
        selectedFeatures: filtersData.selectedFeatures,
        propertySubType: prev,
        basementKey: filtersData.basementKey,
      }
      localStorage.setItem("morefilters", JSON.stringify(filtersData));
    } else {
      let obj = {
        propertySubType: prev,
      }
      localStorage.setItem("morefilters", JSON.stringify(obj));
    }
    setFlag(true);
    // setShimmer(true)
    setLoader(true);

    // this.setState({
    //   setPropertySubType: prev
    // });
    // this.handleTypeHead();
    // propertySubType: [],
  }
  function highLight(e) {
    let prev = prevOver;
    if (e.target.attributes.dataset) {
      let obj = JSON.parse(e.target.attributes.dataset.value);
      if (obj) {
        prevOver = obj.id
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
  let PreviousType = '';
  let PreviousCommunity = '';
  let array1 = [];
  let array2 = [];
  for (var i = 0; i < property.length; i++) {
    if (!array1.includes(property[i].Community)) {
      array1[i] = property[i].Community;
    }
    if (!array2.includes(property[i].PropertySubType)) {
      array2[i] = property[i].PropertySubType;
    }
  }
  return (
    <div className="custom-paddingd mt-3">
      <Container>
        <Row>
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
                      <Link href={nextUrl + '/' + item} key={index + 'community'}>
                        <a className="color-msg custom-anc"><span className="span1">{item}</span><span className="">{VrLine}</span></a></Link>

                    )
                  }
                })}
              </span></p>
              <p><b>Sub Type:</b> <span style={{ 'color': 'var(--grey)' }}>
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
              </span></p>
            </div>
          </div>
          <Col md={6} lg={4} className="mt-3 mt-lg-0">
            <button id="desktopBtn" className="common-btn search-btn py-2 rounded w-100"
              onClick={() => setShowForm(!showForm)}>
              <i className="bi bi-search"></i> Search / Filters
	                </button>
          </Col>
          <Col md={6} lg={4} className="mt-3 mt-lg-0">
            <Link href={'/map'}>
              <button id="desktopBtn" className="common-btn search-btn py-2 rounded w-100">
                Want More Listings ?
	                </button>
            </Link>

          </Col>
          <Col md={6} lg={4} className="mt-3 mt-lg-0">
            <ul className="view_btn" id="viewBtn">
              <li className={changeCard == 'col' ? 'active' : ''} onClick={() => setChangeCard("col")} id="table">
                <img src={grid.src} className="prop-icons" /></li>
              <li className={changeCard == 'loc' ? 'active' : ''} onClick={() => setChangeCard("loc")} id="list">
                <img src={map.src} className="prop-icons" /></li>
            </ul>
          </Col>
        </Row>
        <Row className="mb-2">
          <Col md={6}>
            <p className="page-number">{total} | Page {curr_page} of {total_page}</p>
          </Col>

        </Row>
        <Row>
          {showForm ? (
            <div className="col-md-12 hide">
              <MapHeader
                autoCompleteSuggestion={fetchAutoSuggestion}
                handleTypeHead={handleTypeHead}
                resetBtn={resetBtn}
                featuresData={featuresData}
                propertySubData={propertySubData}
                basementData={basementData}
                selectedFeatures={selectedFeatures}
                basementKey={basementKey}
                propertySubType={propertySubType}
                preState={preState}
                {...initialPropertySearchFilter}
                {...extraFilters}
              />
            </div>

          ) : (
            <div className="col-md-12 show">
              <MapHeader
                preState={preState}
                autoCompleteSuggestion={fetchAutoSuggestion}
                handleTypeHead={handleTypeHead}
                resetBtn={resetBtn}
                featuresData={featuresData}
                propertySubData={propertySubData}
                basementData={basementData}
                selectedFeatures={selectedFeatures}
                basementKey={basementKey}
                propertySubType={propertySubType}
                {...initialPropertySearchFilter}
                {...extraFilters}
              />
            </div>
          )}
        </Row>
        <Row className="mx-md-0">
          {loader &&
            <ShimmerEffect type="cardView" columnCls={"col-lg-3"} count={10} />
          }
          {/* Card Row */}
          {changeCard === "col" &&
            property.map((item) => {
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
                <Col md={4}>
                  <CardRow
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
                    highLightCb={highLight}
                  />
                </Col>
              );
            })}
        </Row>
      </Container>
      <div className="container-fluid">
        <Row>

          {/* map view card */}
          {changeCard === "loc" &&

            <Map curr_page={curr_page}
              togglePopUp={props.togglePopUp}
              isLogin={props.isLogin}
              sort_by={''}
              handlePropertyCall={handlePropertyCall}
              mapData={mapData}
              handleTypeHead={''}
              changeDrawState={changeDrawState}
              mapdragenCb={mapdragenCb}
              polygonsData={polygonsData}
              highLightCb={highLight}
              cityproperty={property}
            />
          }
        </Row>
      </div>
      <Container>
        <Row>
          <div className="col-md-12 col-lg-12 d-flex justify-content-center pt-3">

            <Pagination
              itemsCount={total}
              itemsPerPage={12}
              currentPage={curr_page}
              setCurrentPage={handleChange}
              alwaysShown={false}
            />
          </div>
        </Row>
      </Container>

    </div>
  );
};
export default City;