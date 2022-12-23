import React, { useState, useEffect, useRef } from "react";
import Constant from "./../../../constants/GlobalConstants";
import bedImg from "./../../../public/images/icons/bedroom_icon.png";
import bathImg from "./../../../public/images/icons/bath_icon.png";
import sqftImg from "./../../../public/images/icons/sqft.png";
import garImg from "./../../../public/images/icons/garage.png";
import PropertyCarousel from "./PropertyCarousel";
import ReactCarousel from "../ReactCarousel";
import ShimmerEffect from "../../Components/ShimmerEffect";
import detect from "../../utility/detect";
import dynamic from "next/dynamic";
import AmenitiesMap from "./MapAmenities";
const Button = dynamic(() => import("./../SimpleButton"), {
  loading: () => <>Loading.......</>,
  ssr: false,
});
const ToggleSwitch = dynamic(() => import("./../ToggleSwitch"), {
  loading: () => <>Loading.......</>,
  ssr: false,
});

const RoomsTable = dynamic(() => import("./../PropertyDetails/RoomsTable"), {
  loading: () => <>Loading.......</>,
  ssr: false,
});
const ScheduleQuery = dynamic(() => import("./RightSection"), {
  loading: () => <>Loading.......</>,
  ssr: false,
});
const Card = dynamic(() => import("./../MapCard"), {
  loading: () => <></>,
  ssr: false,
});
const Model = dynamic(() => import("./../Model"), {
  loading: () => <>Loading.......</>,
  ssr: false,
});
const Accordion = dynamic(() => import("../Accordion"), {
  loading: () => <>Loading.......</>,
  ssr: false,
});
var defaultDwnPercent = 20;
var defaultRate = 6;
var defaultTerm = 25;
const DetailsSection = React.memo(function DetailsSection(props) {
  let prop = props.props;
  let hoodQApi = props.webSetting.HoodQApiKey
    ? props.webSetting.HoodQApiKey
    : "";
  let WalkScoreApiKey = props.webSetting.WalkScoreApiKey
    ? props.webSetting.WalkScoreApiKey
    : "";
  let walkscores = `https://www.walkscore.com/serve-walkscore-tile.php?wsid='${WalkScoreApiKey}'&s="${prop.slug}"&o=h&c=f&h=200&fh=0&w=690`;
  const [changeMeasure, setMeasure] = useState(true);
  const [showModalState, setModal] = useState(false);
  const [propTypeState, setPropTypeState] = useState(true);
  const [term, setTerm] = useState(defaultTerm);
  const [downPaymentPercent, setDownPaymentPercentState] =
    useState(defaultDwnPercent);
  const [rate, setRateState] = useState(defaultRate);
  const [downPaymentPrice, setDownPaymentState] = useState(1);
  const [similarData, setSimilar] = useState(
    prop.similarProperty ? prop.similarProperty.similar : {}
  );
  const [soldData, setSoldData] = useState(
    props.soldData ? props.soldData : {}
  );
  const [similarSaleProperty, setSimilarSaleProperty] = useState(
    prop.similarSaleProperty ? prop.similarSaleProperty : []
  );
  const [similarRentProperty, setSimilarRentProperty] = useState(
    prop.similarRentProperty ? prop.similarRentProperty : []
  );
  const [mortgagePayment, setMortgagePayment] = useState("");
  const [mortgageAmount, setMortgageAmount] = useState("");
  const [totalMonthlyPayment, setTotalMonthlyPayment] = useState("");
  const [condoFee, setcondoFee] = useState("");
  const [CMHCMaintaince, setCMHCMaintaince] = useState("");
  const [carouselImages, setCarouselImages] = useState([]);
  const [pageSetting, setPageSetting] = useState([]);
  const [listingPriceData, setListingHistoryData] = useState(
    prop.listingHistoryData
  );
  var formatter = new Intl.NumberFormat("en-US", {
    style: "currency",
    currency: "USD",
    minimumFractionDigits: 0,
  });
  let menuList = [
    { text: "Schools", value: "schools" },
    { text: "Restaurant & Bar", value: "restaurant_bar" },
    { text: "Grocery", value: "grocery" },
    { text: "Service", value: "service" },
    { text: "Hospitals", value: "hospitals" },
  ];
  const [detailData, setDetailData] = useState(
    prop.details.details ? prop.details.details : {}
  );
  useEffect(() => {
    let fl = false;
    if (detailData) {
      if (detailData.properties_images) {
        if (detailData.properties_images.length > 0) {
          fl = true;
          setCarouselImages(detailData.properties_images);
        }
      } else if (detailData.propertiesImages) {
        if (detailData.propertiesImages.length && !fl) {
          setCarouselImages(detailData.propertiesImages);
        }
      }
    }
  }, [detailData]);
  useEffect(() => {
    setListingHistoryData(prop.listingHistoryData);
  }, [prop.listingHistoryData]);

  useEffect(() => {
    setSoldData(prop.soldData ? prop.soldData : {});
    setSimilarRentProperty(
      prop.similarRentProperty ? prop.similarRentProperty : {}
    );
    setSimilarSaleProperty(
      prop.similarSaleProperty ? prop.similarSaleProperty : {}
    );
  }, [prop.soldData, prop.similarSaleProperty, prop.similarRentProperty]);
  useEffect(() => {
    calculateMortgage();
    initSDK();
  }, []);
  useEffect(() => {
    if (localStorage.getItem("detailPageSetting")) {
      let setting = JSON.parse(localStorage.getItem("detailPageSetting"));
      setPageSetting(setting);
    }
  }, [props.webSettingCheck]);
  useEffect(() => {
    generateNeighList();
  }, [prop.amenities]);

  useEffect(() => {
    setDownPaymentPercentState(defaultDwnPercent);
  }, [defaultDwnPercent]);
  useEffect(() => {
    setRateState(defaultRate);
  }, [defaultRate]);
  useEffect(() => {
    setTerm(defaultTerm);
  }, [defaultTerm]);
  const switchToggel = (e) => {
    if (changeMeasure) {
      setMeasure(false);
    } else {
      setMeasure(true);
    }
  };
  const showModal = (e) => {
    setModal(!showModalState);
    props.getYelpData();
    scrollToTop();
  };

  let detailsDatas = prop.details;

  const scrollToTop = () => {
    window.scrollTo({
      top: 0,
      behavior: "smooth",
    });
  };
  const getRound = (val, log = false) => {
    let res = Math.round(val);
    if (log) {
    }
    if (res > 0) {
      return res;
    }
    return "";
  };
  const streetViewConfig = {
    type: "street",
    mapOptions: {
      position: {
        lat: parseFloat(detailData.Latitude),
        lng: parseFloat(detailData.Longitude),
      },
      pov: {
        heading: 200,
        pitch: 0,
      },
      scrollwheel: true,
    },
  };
  const getDomStatus = () => {
    let res = Math.round(detailData.Dom);
    if (res === 1) {
      return res + " Day ";
    }
    if (res > 1) {
      return res + " Days ";
    }
    return "Today";
  };
  {
    (" ");
  }
  function signInToggle() {
    if (props.isLogin) {
      verifyEmail();
    } else {
      props.togglePopUp();
    }
  }
  function amortization(e) {
    defaultTerm = e.value;
    setTerm(defaultTerm);
    calculateMortgage();
  }
  function downPayment(e) {
    defaultDwnPercent = e.value;
    calculateMortgage();
    setDownPaymentPercentState(defaultDwnPercent);
  }
  function setUpDown(Orig_dol, ListPrice) {
    Orig_dol = Math.ceil(Orig_dol);
    ListPrice = Math.ceil(ListPrice);
    if (ListPrice > Orig_dol) {
      var diff_up = ListPrice - Orig_dol;
      var diff_up_per = Math.ceil((diff_up / ListPrice) * 100);
      return (
        <span
          className="iconsHolder diff_up font-14"
          data-toggle="tooltip"
          data-placement="top"
          title={formatter.format(diff_up)}
        >
          {" "}
          &nbsp;&nbsp;
          <img
            src="/images/icons/down-red-icon-svg.png"
            className="down-red-icon mb-1"
            alt="icon"
          />
          {diff_up_per + "%"}{" "}
        </span>
      );
    }
    if (Orig_dol > ListPrice) {
      var diff_dwn = Orig_dol - ListPrice;
      var diff_dwn_per = Math.ceil((diff_dwn / Orig_dol) * 100);
      return (
        <span
          className="iconsHolder diff_dwn font-14"
          data-toggle="tooltip"
          data-placement="top"
          title={formatter.format(diff_dwn)}
        >
          <img
            src="/images/icons/up-green-icon-svg.png"
            className="up-green-icon"
            alt="icon"
          />{" "}
          {diff_dwn_per + "%"}{" "}
        </span>
      );
    }
  }

  function keyUpHandler(e) {
    if (isNaN(e.target.value)) {
      e.target.value = "";
      return false;
    }
    if (e.target.value) {
      defaultRate = e.target.value;
      setRateState(defaultRate);
      calculateMortgage();
    } else {
      defaultRate = 6;
      setRateState(defaultRate);
      calculateMortgage();
    }
  }
  function calculateMortgage() {
    let timeTerm = defaultTerm;
    let main_price = detailData.Lp_dol;
    let dp = (defaultDwnPercent / 100) * main_price;
    let maintainance_amt = (dp / 100) * 0;
    let loanAmount = main_price - dp;
    let numberOfMonths = timeTerm * 12;
    let rateOfInterest = defaultRate;
    let monthlyInterestRatio = rateOfInterest / 100 / 12;
    let top = Math.pow(1 + monthlyInterestRatio, numberOfMonths);
    let bottom = top - 1;
    let sp = top / bottom;
    let emi = loanAmount * monthlyInterestRatio * sp;
    let final = emi + maintainance_amt;
    final = emi / 2 + parseInt(maintainance_amt / 2);
    let tx = detailData.Taxes / 12;
    var ra = parseInt(main_price - dp);
    var rate_of_interest_amount = formatter.format(ra);
    let emi_str = formatter.format(parseInt(emi));
    let newPrice = formatter.format(parseInt(final) + parseInt(tx) + 0);
    setTimeout(() => {
      setDownPaymentState(dp);
      setMortgagePayment(emi_str);
      setTotalMonthlyPayment(newPrice);
      setMortgageAmount(rate_of_interest_amount);
      setCMHCMaintaince(maintainance_amt);
    }, 0);
  }
  function isEmpty(obj) {
    for (var key in obj) {
      if (obj.hasOwnProperty(key)) return false;
    }
    return true;
  }
  const sliderImages = (e) => {
    if (
      carouselImages &&
      Array.isArray(carouselImages) &&
      carouselImages.length > 0
    ) {
      const renderList = carouselImages.map((data, index) => {
        return (
          <div key={index}>
            <div style={{ padding: 2 }}>
              <img
                src={Constant.image_base_url + data.s3_image_url}
                alt={`${detailData.Type_own1_out} For ${detailData.S_r} at ${detailData.Addr} - MLS:${detailData.Ml_num} `}
                className="img-fluid img-height-width"
              />
            </div>
          </div>
        );
      });
      return renderList;
    } else {
      return (
        <div key={1}>
          <div style={{ padding: 2 }}>
            <img
              src={Constant.defaultImage}
              alt="placeholder"
              className="img-fluid img-height-width"
            />
          </div>
        </div>
      );
    }
  };
  const togglePropTypes = (e) => {
    if (e.target.value == "Sale") {
      setPropTypeState(true);
    }
    if (e.target.value == "Rent") {
      setPropTypeState(false);
    }
  };
  function initSDK() {
    let addr = prop.slug.replace("-", " ");
    try {
      var hq = new HQ.NeighbourhoodHighlights({
        el: "neighbourhood-highlights",
        address: addr,
      });
    } catch (error) {}
    removeIframSearch();
  }

  function removeIframSearch() {
    var iframe = document.getElementById("walkScoreIfram");
  }
  function hideInnerSections(e) {
    var frame = document.getElementById("ws-footer");
  }
  const generateNeighList = (e) => {
    let amenities = prop.amenities ? prop.amenities : {};
    if (amenities.businesses) {
      const list = amenities.businesses.map((res, key) => {
        return (
          <div className={`map-tab-inner${key}`} key={key}>
            <div className="menu-one">
              <div className="menu-left">
                <img
                  src={Constant.image_base_url + res.image_url}
                  alt={res.categories ? res.categories[0].title : "img"}
                  loading="lazy"
                  className="border"
                />
                <div className="menu-text-left">
                  <a rel="noopener noreferrer" target="_blank" href={res.url}>
                    <h6>{res.name}</h6>
                  </a>
                  <span>{res.alias}</span>
                  <img
                    className="h-20"
                    src="./../../../images/icons/small_2@2x.png"
                    alt="Yelp rating"
                    height="100"
                  />
                  <span> {res.phone}</span>
                  <span>{res.location.display_address}</span>
                  <span>Based on {res.review_count} Reviews</span>
                </div>
              </div>
            </div>
          </div>
        );
      });
      return list;
    }
  };
  return (
    <>
      {!props.pageLoading && (
        <>
          {showModalState && (
            <Model
              show={showModalState}
              handleClose={showModal}
              carouselImages={carouselImages}
              sliderImages={sliderImages()}
              detect={detect.isMobile}
              detailData={detailData}
              amenities={prop.amenities}
              streetViewConfig={streetViewConfig}
              getYelpData={props.getYelpData}
              alt={`${detailData.Type_own1_out} For ${detailData.S_r} at ${detailData.Addr} - MLS:${detailData.Ml_num} `}
            />
          )}
        </>
      )}
      {prop.checkDetailData ? (
          <div className={`propertiesCls py-5 `} id="propertiesSection">
            <p className="text-center">
              Sorry ! No details found for this property please check another !{" "}
            </p>
          </div>
      ) : (
          <>
            <div className="mt-5">
              <div
                style={{
                  marginTop: "-40px",
                }}
                id="sliderOff"
              >
                {!props.pageLoading && (
                  <>
                    {!detect.isMobile() ? (
                      <PropertyCarousel
                        imageToShow={8}
                        inRowImage={4}
                        propertyImage={carouselImages}
                        showSingleFirstSlideImage={true}
                        firstSliderRow={4}
                        sliderHeight={"600px"}
                        alt={`${detailData.Type_own1_out} For ${detailData.S_r} at ${detailData.Addr} - MLS:${detailData.Ml_num} `}
                      />
                    ) : (
                        <ReactCarousel show={1}>{sliderImages()}</ReactCarousel>
                    )}
                  </>
                )}
              </div>
            </div>
            <div className="container-fluid px-4">
              <div className={"row mt-3"}>
                <div className="col-md-8 col-lg-8 col-sm-8 col-xs-8">
                  <div className="row px-1">
                    <div className="col-md-6 details-head-left">
                      <span className="added-on-time">
                        {" "}
                        <span className="doms" style={{ color: "black" }}>
                          DOM : {getDomStatus()}
                        </span>{" "}
                      </span>
                      <h1 className="h3" 
                      style={{
                        "color": "#202020",
                        "fontSize": "20px",
                        "lineHeight": "26px",
                        "fontWeight": "500"
                      }}  
                      >{detailData.Addr}</h1>
                      <p style={{ color: "rgba(33, 37, 41, 0.60) !important" }}>
                        {detailData.Municipality}{" "}
                        {detailData.Community
                          ? ", " + detailData.Community
                          : ""}
                      </p>
                      <ul className="room-details">
                        {getRound(detailData.Br) && (
                          <li>
                            <img {...bedImg} className="beds mb-2" alt="icon" />{" "}
                            <br />
                            <span>
                              {getRound(detailData.Br, true) !== false
                                ? getRound(detailData.Br) + " Beds "
                                : ""}{" "}
                              &nbsp;
                            </span>
                          </li>
                        )}
                        {getRound(detailData.Bath_tot) && (
                          <li>
                            <img
                              {...bathImg}
                              className="baths mb-1"
                              alt="icon"
                            />
                            <br />

                            <span>
                              {getRound(detailData.Bath_tot) != false
                                ? getRound(detailData.Bath_tot) + "  Baths "
                                : ""}{" "}
                              &nbsp;
                            </span>
                          </li>
                        )}
                        {detailData.Sqft && (
                          <li>
                            <img {...sqftImg} className="gar mb-2" alt="icon" />
                            <br />
                            <span> {detailData.Sqft + "  Sqft  "} </span>
                          </li>
                        )}
                        {getRound(detailData.Gar_spaces) && (
                          <li className="garSpace">
                            <img {...garImg} className="garSpc" alt="icon" />
                            <br />
                            <span className="garSpaceLabel">
                              {" "}
                              {getRound(detailData.Gar_spaces) != false
                                ? getRound(detailData.Gar_spaces) + " Garage"
                                : ""}
                            </span>
                          </li>
                        )}
                      </ul>
                    </div>
                    <div className="col-md-6 details-head-right">
                      <h2 className=" h2 property-price h3 text-right">
                        &nbsp;&nbsp;&nbsp;
                        {detailData.Status === "U" ? (
                          <>
                            {formatter.format(getRound(detailData.Sp_dol))}
                            <span
                              className={`
                            ${detailData.Lsc === "Ter" ? "forTer2" : ""}
                            ${detailData.Lsc === "Sld" ? "forSold2" : ""}
                            ${detailData.Lsc === "Lsd" ? "forLease2" : ""}
                            ${detailData.Lsc === "New" ? "forNew2" : ""}
                             status-fontd  `}
                            >
                              {" "}
                              &nbsp;<i className="dot-cls">&#8226;</i>
                            </span>{" "}
                            <span className="status-font">
                              {detailData.LastStatusButton}
                            </span>
                          </>
                        ) : (
                          <>{formatter.format(getRound(detailData.Lp_dol))}</>
                        )}
                        {/* {formatter.format(getRound(detailData.Lp_dol))} */}
                        {setUpDown(detailData.Orig_dol, detailData.Lp_dol)}
                      </h2>
                      {detailData.Status === "U" && (
                        <>
                          {detailData.Sp_dol !== detailData.Orig_dol && (
                            <h3 className=" h6 Listed-font">
                              &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; Listed:{" "}
                              <strike>
                                {formatter.format(
                                  getRound(detailData.Orig_dol)
                                )}
                              </strike>
                            </h3>
                          )}
                          <h6 className=" Listed-font label-status-cls">
                            &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;{" "}
                            {detailData.label}
                          </h6>
                        </>

                        // <span
                        // className={`
                        // ${detailData.Lsc === "Ter" ? "forTer" : ""}
                        // ${detailData.Lsc === "Sld" ? "forSold" : ""}
                        // ${detailData.Lsc === "Lsd" ? "forLease" : ""}
                        // ${detailData.Lsc === "New" ? "forNew" : ""}
                        // medium-tag status-font   `}
                        // >
                        //   {"/ "}
                        //   {" "}
                        //   {detailData.LastStatusButton}
                        // </span>
                      )}
                      <div className="row">
                        <div className="col-md-3"></div>
                        <div className="col-md-2"></div>
                        <div className="col-md-2"></div>
                      </div>

                      <p className="estimate_payment">
                        Estimated Mortgage{" "}
                        <span className="">{totalMonthlyPayment} </span>
                        /m
                      </p>
                      <label htmlFor="state">
                        <a
                          className="form-details-btn  color202020 inlineblock mt-2"
                          data-target="#apporal"
                        >
                          Details Here
                        </a>
                      </label>
                    </div>
                  </div>
                  <hr />
                  <Accordion
                    totalMonthlyPayment={totalMonthlyPayment}
                    mortgagePayment={mortgagePayment}
                    mortgageAmount={mortgageAmount}
                    taxes={formatter.format(detailData.Taxes)}
                    condoFee={condoFee}
                    CMHCMaintaince={CMHCMaintaince}
                    price={formatter.format(detailData.Lp_dol)}
                    term={term ? term : 25}
                    cbDownpayment={downPayment}
                    cbAmortization={amortization}
                    cbkeyUpHandler={keyUpHandler}
                    downPaymentPrice={formatter.format(downPaymentPrice)}
                    downPaymentPercent={downPaymentPercent}
                    rate={rate}
                  />
                  <div className="row">
                    {detailsDatas.table !== "Commercial" && (
                      <>
                        <div className="col-md-12">
                          <h6 className=" h3 heading-title">Listing History</h6>
                          <p>
                            Buy & Sell History for {detailData.Addr} (
                            {detailData.Type_own1_out})
                          </p>
                          <div className="table-res ">
                            <table className="table border responsive  ">
                              <thead>
                                <tr className="">
                                  <th
                                    colspan="1"
                                    rowspan="1"
                                    className="el-table_2_column_6 "
                                  >
                                    <div className="cell">Listing Date</div>
                                  </th>
                                  <th
                                    colspan="1"
                                    rowspan="1"
                                    className="el-table_2_column_7  "
                                  >
                                    <div className="cell">End Date</div>
                                  </th>
                                  <th
                                    colspan="1"
                                    rowspan="1"
                                    className="el-table_2_column_8   "
                                  >
                                    <div className="cell">Price</div>
                                  </th>
                                  <th
                                    colspan="1"
                                    rowspan="1"
                                    className="el-table_2_column_9  "
                                  >
                                    <div className="cell">Status</div>
                                  </th>
                                  <th
                                    colspan="1"
                                    rowspan="1"
                                    className="el-table_2_column_10  txt-end   is-leaf"
                                  >
                                    <div className="cell">MLS No.</div>
                                  </th>
                                </tr>
                              </thead>
                              <tbody>
                                {listingPriceData &&
                                  Array.isArray(listingPriceData) &&
                                  listingPriceData.map((item, key) => {
                                    if (!key) {
                                    }
                                    let blury1 = false;
                                    if (
                                      !props.isLogin &&
                                      (item.Status === "D" ||
                                        item.Status === "U")
                                    ) {
                                      blury1 = true;
                                    }
                                    return (
                                      <tr
                                        key={key}
                                        className={` ${
                                          blury1 ? "blury1" : ""
                                        } ${
                                          key == 0 ? "listHistoryActive" : ""
                                        } el-table__row el-table__row--striped `}
                                      >
                                        <td
                                          rowspan="1"
                                          colspan="1"
                                          className="el-table_2_column_6  "
                                        >
                                          <div className="cell">
                                            <p className="">
                                              {item.property_insert_time}
                                            </p>
                                          </div>
                                        </td>
                                        <td
                                          rowspan="1"
                                          colspan="1"
                                          className="el-table_2_column_7  "
                                        >
                                          <div className="cell">
                                            {item.Status === "U" ||
                                            item.Status === "D" ? (
                                              <p className="">
                                                {" "}
                                                {item.property_last_updated}
                                              </p>
                                            ) : (
                                              <></>
                                            )}
                                          </div>
                                        </td>
                                        <td
                                          rowspan="1"
                                          colspan="1"
                                          className="el-table_2_column_8  "
                                        >
                                          <div className="cell">
                                            <p className="">
                                              {item.Status === "U" &&
                                              item.LastStatus === "Sld"
                                                ? formatter.format(
                                                    parseInt(item.Sp_dol)
                                                  )
                                                : formatter.format(
                                                    parseInt(item.Lp_dol)
                                                  )}
                                            </p>
                                          </div>
                                        </td>
                                        <td
                                          rowspan="1"
                                          colspan="1"
                                          className="el-table_2_column_9  "
                                        >
                                          <div className="cell listing-detail-status">
                                            {item.LastStatus === "Sld" &&
                                              item.Status === "U" && (
                                                <div className="for-sale-tag medium-tag forSold  ">
                                                  {" "}
                                                  Sold
                                                </div>
                                              )}
                                            {item.S_r === "Sale" &&
                                              item.Status === "A" && (
                                                <div className="for-sale-tag medium-tag  forSale-cls">
                                                  {" "}
                                                  {"For " + item.S_r}
                                                </div>
                                              )}
                                            {item.S_r === "Lease" &&
                                              item.LastStatus === "Lsd" &&
                                              item.Status === "U" && (
                                                <div className="for-sale-tag medium-tag  forLease">
                                                  {" "}
                                                  {"Leased "}
                                                </div>
                                              )}
                                            {item.S_r === "Lease" &&
                                              item.Status === "A" && (
                                                <div className="for-sale-tag medium-tag  forLease">
                                                  {" "}
                                                  For {item.S_r}
                                                </div>
                                              )}
                                            {item.LastStatus === "Ter" &&
                                              item.Status === "U" && (
                                                <div className="for-sale-tag medium-tag  forTer">
                                                  {" "}
                                                  Terminated
                                                </div>
                                              )}
                                            {item.LastStatus === "Ter" &&
                                              item.Status === "D" &&
                                              item.Status !== "U" && (
                                                <div className="for-sale-tag medium-tag  forTer">
                                                  {" "}
                                                  Terminated
                                                </div>
                                              )}
                                            {item.LastStatus === "Exp" &&
                                              item.Status === "U" && (
                                                <div className="for-sale-tag medium-tag  forExpAll">
                                                  {" "}
                                                  Expired
                                                </div>
                                              )}
                                            {item.LastStatus === "Dft" &&
                                              item.Status === "U" && (
                                                <div className="for-sale-tag medium-tag  forExpAll">
                                                  {" "}
                                                  Draft
                                                </div>
                                              )}
                                            {item.LastStatus === "Sus" &&
                                              item.Status === "U" && (
                                                <div className="for-sale-tag medium-tag  forExpAll">
                                                  {" "}
                                                  Suspended
                                                </div>
                                              )}
                                            {item.LastStatus === "Pc" &&
                                              item.Status === "U" && (
                                                <div className="for-sale-tag medium-tag  forTer">
                                                  {" "}
                                                  Terminated
                                                </div>
                                              )}
                                          </div>
                                        </td>
                                        <td
                                          rowspan="1"
                                          colspan="1"
                                          className="el-table_2_column_10 txt-end "
                                        >
                                          <div className="cell">
                                            <p>{item.Ml_num}</p>
                                          </div>
                                        </td>
                                      </tr>
                                    );
                                  })}
                              </tbody>
                            </table>
                          </div>
                        </div>
                        <hr />
                      </>
                    )}
                    {pageSetting &&
                    pageSetting.descriptionSection === "show" ? (
                      <div className="discription-one col-12 generalDesc">
                        <details className="detailCollapes borders" open>
                          <summary
                            id="generalDesc"
                            className="detailCollapesTitle"
                          >
                            General Description{" "}
                          </summary>
                          <p className="font-family-class m-2">
                            {detailData.Ad_text}
                          </p>
                        </details>
                      </div>
                    ) : (
                      ""
                    )}
                    {pageSetting &&
                      pageSetting.extrasSection === "show" &&
                      detailData.Extras && (
                        <div className="discription-one col-12 extras">
                          <details className="detailCollapes borders" open>
                            <summary
                              id="extras"
                              className="detailCollapesTitle"
                            >
                              Extras{" "}
                            </summary>
                            <p className="font-family-class m-2">
                              {" "}
                              {detailData.Extras}
                            </p>
                          </details>
                        </div>
                      )}
                    {pageSetting && pageSetting.propertySection === "show" ? (
                      <div className="discription-one col-12 propertyDetails">
                        <details className="detailCollapes borders" open>
                          <summary
                            id="proprtyDetails"
                            className="detailCollapesTitle"
                          >
                            Property Details{" "}
                          </summary>
                          <div className="property-colum row p-2">
                            <div className="col-md-6 ">
                              <ul className="property-details">
                                {detailData.Ml_num && (
                                  <li>
                                    <span className="detailsLabel">Mls#: </span>
                                    <span className="detailsLvalue">
                                      {detailData.Ml_num}
                                    </span>
                                  </li>
                                )}
                                {detailData.PropertyType && (
                                  <li>
                                    <span className="detailsLabel">
                                      Property Type:{" "}
                                    </span>
                                    <span className="detailsLvalue">
                                      {detailData.PropertyType}
                                    </span>
                                  </li>
                                )}
                                {detailData.Community && (
                                  <li>
                                    <span className="detailsLabel">
                                      Neighborhood:{" "}
                                    </span>
                                    <span className="detailsLvalue">
                                      {detailData.Community}
                                    </span>
                                  </li>
                                )}
                                {detailData.Type_own1_out && (
                                  <li>
                                    <span className="detailsLabel">Type: </span>
                                    <span className="detailsLvalue">
                                      {detailData.Type_own1_out}
                                    </span>
                                  </li>
                                )}
                                {detailData.Prop_feat1_out && (
                                  <li>
                                    <span className="detailsLabel">
                                      Property Features:{" "}
                                    </span>
                                    <span className="detailsLvalue">
                                      {detailData.Prop_feat1_out}{" "}
                                      {detailData.Prop_feat2_out
                                        ? " , " + detailData.Prop_feat2_out
                                        : ""}
                                    </span>
                                  </li>
                                )}
                                {getRound(detailData.Sqft) && (
                                  <li>
                                    <span className="detailsLabel">
                                      Land Size:{" "}
                                    </span>
                                    <span className="detailsLvalue">
                                      {getRound(detailData.Sqft)
                                        ? getRound(detailData.Sqft) + " Sqft"
                                        : ""}
                                    </span>
                                  </li>
                                )}
                                {getRound(detailData.Park_spcs) && (
                                  <li>
                                    <span className="detailsLabel">
                                      Parking:{" "}
                                    </span>
                                    <span className="detailsLvalue">
                                      {getRound(detailData.Park_spcs)
                                        ? getRound(detailData.Park_spcs) + " "
                                        : ""}{" "}
                                    </span>
                                  </li>
                                )}
                                {getRound(detailData.Br) && (
                                  <li>
                                    <span className="detailsLabel">
                                      Bedrooms:{" "}
                                    </span>
                                    <span className="detailsLvalue">
                                      {detailData.Br}
                                    </span>
                                  </li>
                                )}
                                {detailData.A_c && (
                                  <li>
                                    <span className="detailsLabel">
                                      Air Conditioning:{" "}
                                    </span>
                                    <span className="detailsLvalue">
                                      {detailData.A_c}
                                    </span>
                                  </li>
                                )}
                                {getRound(detailData.Bath_tot) && (
                                  <li>
                                    <span className="detailsLabel">
                                      Bathrooms Total:{" "}
                                    </span>
                                    <span className="detailsLvalue">
                                      {detailData.Bath_tot}
                                    </span>
                                  </li>
                                )}
                              </ul>
                            </div>
                            <div className="col-md-6">
                              <ul className="property-details">
                                {detailData.Bsmt1_out && (
                                  <li>
                                    <span className="detailsLabel">
                                      Basement:
                                    </span>
                                    <span className="detailsLvalue">
                                      {" "}
                                      {detailData.Bsmt1_out}
                                    </span>
                                  </li>
                                )}
                                {detailData.Fpl_num && (
                                  <li>
                                    <span className="detailsLabel">
                                      Fireplace:
                                    </span>{" "}
                                    <span className="detailsLvalue">
                                      {detailData.Fpl_num}
                                    </span>
                                  </li>
                                )}
                                {detailData.Cross_st && (
                                  <li>
                                    <span className="detailsLabel">
                                      Cross Street:
                                    </span>{" "}
                                    <span className="detailsLvalue">
                                      {detailData.Cross_st}
                                    </span>
                                  </li>
                                )}
                                {detailData.Gar_type && (
                                  <li>
                                    <span className="detailsLabel">
                                      Garage Type:
                                    </span>
                                    <span className="detailsLvalue">
                                      {" "}
                                      {detailData.Gar_type}
                                    </span>
                                  </li>
                                )}
                                {detailData.S_r && (
                                  <li>
                                    <span className="detailsLabel">
                                      Status:
                                    </span>{" "}
                                    <span className="detailsLvalue">
                                      {detailData.S_r}
                                    </span>
                                  </li>
                                )}
                                {detailData.Fuel && (
                                  <li>
                                    <span className="detailsLabel">Fuel:</span>
                                    <span className="detailsLvalue">
                                      {" "}
                                      {detailData.Fuel}
                                    </span>
                                  </li>
                                )}
                                {detailData.Pool && (
                                  <li>
                                    <span className="detailsLabel">Pool:</span>{" "}
                                    <span className="detailsLvalue">
                                      {detailData.Pool}
                                    </span>
                                  </li>
                                )}
                                {detailData.Water && (
                                  <li>
                                    <span className="detailsLabel">Water:</span>
                                    <span className="detailsLvalue">
                                      {" "}
                                      {detailData.Water}
                                    </span>
                                  </li>
                                )}
                                {detailData.Taxes && (
                                  <li>
                                    <span className="detailsLabel">
                                      Annual Property Taxes:
                                    </span>
                                    <span className="detailsLvalue">
                                      {" "}
                                      {formatter.format(detailData.Taxes)}
                                    </span>
                                  </li>
                                )}
                              </ul>
                            </div>
                          </div>
                        </details>
                      </div>
                    ) : (
                      ""
                    )}
                    <>
                      <div className="discription-one roomsDetails">
                        <div id="walkScoreSection">
                          <hr />
                          <div id="ws-walkscore-tile"></div>
                          {walkscores && (
                            <iframe
                              marginHeight="0"
                              marginWidth="0"
                              height="200px"
                              frameBorder="0"
                              scrolling="no"
                              title="Walk Score"
                              width="100%"
                              id="walkScoreIfram"
                              src={walkscores}
                              onLoad={hideInnerSections}
                              className="walkScore"
                            ></iframe>
                          )}
                          {/* <div id="overLap" className="py-2"></div> */}
                          <hr />
                        </div>
                        <div
                          id="neighbourhood-highlights"
                          className="mt-4"
                        ></div>
                        {detailData.RoomsDescription &&
                          detailData.RoomsDescription.length > 0 && (
                            <details
                              className="detailCollapes2 mt-2 height-auto borders"
                              open
                            >
                              <summary
                                id="roomsDetailsd"
                                className="detailCollapesTitle"
                              >
                                Rooms Details{" "}
                              </summary>
                              <div className="row rooms-measure ">
                                <div className="col-md-6 col-sm-6 col-lg-6">
                                  <h3 className="h4">Rooms</h3>
                                </div>
                                <div className="col-md-1 col-sm-1 col-lg-1 "></div>
                                <div className="col-md-5 col-sm-5 col-lg-5 text-right rooms-measure-inner ">
                                  <div className="measurement_cal switch_container">
                                    <div className="switch_container_main">
                                      <span className="imper_section ml-2 mt-1 color000">
                                        Imperial
                                      </span>
                                      <ToggleSwitch callBack={switchToggel} />
                                      <span className="metric_section mr-2 mt-1 color000">
                                        Metric
                                      </span>
                                    </div>
                                  </div>
                                </div>
                              </div>
                              <div className="col-md-12 col-sm-12 col-lg-12 ">
                                <div className="table-responsive">
                                  <RoomsTable
                                    isShow={changeMeasure}
                                    data={detailData.RoomsDescription}
                                  />
                                </div>
                              </div>
                            </details>
                          )}
                        <br />
                        {!isEmpty(prop.amenities) && (
                          <details className=" height-auto mt-2 borders" open>
                            <summary id=" " className="detailCollapesTitle">
                              <span>Neighbourhood </span>
                            </summary>
                            {/*  */}
                            <div className=" mt-4">
                              <div className="row">
                                <div className="col-md-12">
                                  <div className="neighbourhood-cls">
                                    <p>
                                      Schools, amenities, Restaurant & Bar ,
                                      Grocery , Service , and Hospitals near{" "}
                                      {detailData.Addr}
                                    </p>
                                    <div className="">
                                      <AmenitiesMap
                                        mapResize={true}
                                        details={
                                          prop.details
                                            ? prop.details.details
                                            : {}
                                        }
                                        amenities={prop.amenities}
                                        getYelpData={props.getYelpData}
                                      />
                                    </div>
                                    <br />
                                    <br />
                                  </div>
                                </div>
                              </div>
                            </div>
                          </details>
                        )}
                        <br />
                        <details
                          className={`detailCollapes2  borders ${
                            soldData.sold && soldData.sold.length
                              ? ""
                              : "sold-cls-details"
                          }`}
                          open
                        >
                          <summary
                            id="roomsDetailsd"
                            className="detailCollapesTitle"
                          >
                            <span>Sold Property</span>
                          </summary>
                          {prop.apiLoaded ? (
                            <div
                              className={`${
                                soldData && soldData.sold
                                  ? "sold-properties2"
                                  : "sold-properties"
                              }`}
                            >
                              <div className="row">
                                {soldData ? (
                                  <>
                                    {soldData.sold &&
                                    soldData.sold.length > 0 ? (
                                      soldData.sold.map((data, key) => {
                                        return (
                                          <div className="col-md-6" key={key}>
                                            {data.Vow_exclusive == 0 ||
                                            props.isLogin ? (
                                              <>
                                                {data.Vow_exclusive ? (
                                                  <p
                                                    style={{ height: "10px" }}
                                                    className=" mt-2"
                                                  >
                                                    <span hidden>
                                                      {data.Vow_exclusive}
                                                    </span>
                                                  </p>
                                                ) : (
                                                  ""
                                                )}
                                              </>
                                            ) : (
                                                <span className="vow-cls ">
                                                  Login Required
                                                </span>
                                            )}
                                            <div
                                              className={`  ${
                                                data.Vow_exclusive == 0 ||
                                                props.isLogin
                                                  ? ""
                                                  : "filter  mt-2"
                                              } ${""}`}
                                            >
                                              {data.Status === "U" &&
                                              !props.isLogin ? (
                                                <span
                                                  className="vow-cls2  details-vow-cls"
                                                  onClick={props.togglePopUp}
                                                >
                                                  Login Required{" "}
                                                </span>
                                              ) : (
                                                <> </>
                                              )}
                                              <Card
                                                showIsFav={true}
                                                openUserPopup={true}
                                                openLoginCb={props.togglePopUp}
                                                isLogin={props.isLogin}
                                                item={data}
                                                key={key}
                                                isdetail={true}
                                                isSold={true}
                                                defaultImage={
                                                  Constant.defaultImage
                                                }
                                              />
                                            </div>
                                          </div>
                                        );
                                      })
                                    ) : (
                                      <>
                                        <p>No Data Found !</p>
                                      </>
                                    )}
                                  </>
                                ) : (
                                  <>
                                    <p>No Data Found !</p>
                                  </>
                                )}
                              </div>
                            </div>
                          ) : (
                            <div className="row">
                              <div className="col-12">
                                <ShimmerEffect
                                  type="cardView"
                                  columnCls={"col-lg-6"}
                                  count={4}
                                />
                              </div>
                            </div>
                          )}
                        </details>
                        <br />
                        <div className="" id="footers">
                          <span className=" mt-3">
                            &nbsp; Listed By:
                            <span className=""> {detailData.Rltr} </span>
                          </span>
                        </div>
                      </div>
                    </>
                  </div>
                </div>
                <div className="col-md-4 col-lg-4 ">
                  <div id="sidebar">
                    <div className="row ">
                      <div className="col-md-12">
                        <div className="viewmap loadMap">
                          <img
                            src="./../../../images/map-box.png"
                            alt="img"
                            className="w-100"
                            onClick={showModal}
                          />
                          <a className="loadMap" onClick={showModal}>
                            View map &amp; nearby amenities
                          </a>
                        </div>
                      </div>
                    </div>
                    <div className="row">
                      <div className="col-md-12">
                        <ScheduleQuery {...detailData} {...props} />
                      </div>
                    </div>
                  </div>
                </div>
              </div>
            </div>
          </>
      )}
      {pageSetting && pageSetting.listingsSection === "show" ? (
        <div className="row similarProperties">
          <div className="col-md-12">
            <hr />
            <h6 className="heading h3">Similar Listings </h6>
          </div>
          <div className="col-md-12 mt-1 propTypeBtn mb-2">
            <Button
              extraProps={{
                size: "md",

                className: `gridMapView btn  ${propTypeState ? "rentBtn" : ""}`,
                type: "button",
                value: "Sale",
                text: "Sale",
                onClick: togglePropTypes,
              }}
            />
            <Button
              extraProps={{
                size: "md",
                className: `gridMapView btn  ${propTypeState ? "" : "rentBtn"}`,
                type: "button",
                value: "Rent",
                text: "Rent",
                onClick: togglePropTypes,
              }}
            />
          </div>

          {similarSaleProperty ? (
            <>
              {propTypeState ? (
                <>
                  {similarSaleProperty.sale &&
                    similarSaleProperty.sale.map((data, key) => {
                      return (
                        <div className="col-md-3 mt6 detailCards" key={key}>
                          {data.Vow_exclusive == 0 || props.isLogin ? (
                            <>
                              <p></p>
                              <br />
                            </>
                          ) : (
                            <>
                              {" "}
                              <span className="vow-cls ">Login Required</span>
                            </>
                          )}
                          <div
                            className={`  ${
                              data.Vow_exclusive == 0 || props.isLogin
                                ? ""
                                : "filter  mt-2"
                            }`}
                          >
                            <Card
                              showIsFav={true}
                              openUserPopup={true}
                              openLoginCb={props.togglePopUp}
                              isLogin={props.isLogin}
                              item={data}
                              key={key}
                              defaultImage={Constant.defaultImage}
                            />
                          </div>
                        </div>
                      );
                    })}
                </>
              ) : (
                <>
                  {similarRentProperty &&
                    similarRentProperty.rent.map((data, key) => {
                      return (
                        <div className="col-md-3   detailCards" key={key}>
                          {data.Vow_exclusive == 0 || props.isLogin ? (
                            <>
                              <p></p>
                              <br />
                            </>
                          ) : (
                            <span className="vow-cls "> Login Required </span>
                          )}
                          <div
                            className={`  ${
                              data.Vow_exclusive == 0 || props.isLogin
                                ? ""
                                : "filter  mt-2"
                            }`}
                          >
                            <Card
                              showIsFav={true}
                              openUserPopup={true}
                              openLoginCb={props.togglePopUp}
                              isLogin={props.isLogin}
                              item={data}
                              key={key}
                              defaultImage={Constant.defaultImage}
                            />
                          </div>
                        </div>
                      );
                    })}
                </>
              )}
            </>
          ) : (
            <>
              <div className="row">
                <div className="col-12">
                  <ShimmerEffect
                    type="cardView"
                    columnCls={"col-lg-6"}
                    count={4}
                  />
                </div>
              </div>
            </>
          )}
        </div>
      ) : (
        ""
      )}
    </>
  );
});
export default DetailsSection;
