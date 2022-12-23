import React, { useState, useEffect, useRef } from "react";
import Constant from "./../../../constants/Global";
import bedImg from "./../../../public/images/icon/bedroom_icon.png";
import bathImg from "./../../../public/images/icon/bath_icon.png";
import sqftImg from "./../../../public/images/icon/sqft.png";
import garImg from "./../../../public/images/icon/garage1.jpeg";
import PropertyCarousel from "./PropertyCarousel";
import ReactCarousel from "../ReactCarousel";
import ShimmerEffect from "../../Components/ShimmerEffect";
import detect from "../../utility/detect";
import dynamic from 'next/dynamic';
import share from "./../../../public/images/icon/share.svg";
import save from "./../../../public/images/icon/heart1.png";
import { useRouter } from 'next/router'
import { Row, Col, Modal } from "react-bootstrap";
import { shareEmailApi, agentId, favUrl } from "../../../constants/Global";
import API from "./../../utility/api";
import AmenitiesMap from './MapAmenities'
// import ToggleSwitch from "./../ToggleSwitch";
// import RoomsTable from "./../PropertyDetails/RoomsTable";
// import ScheduleQuery from "./RightSection";
// import Card from "./../Card";
// import Model from "./../Model";
// import Accordion from "../Accordion";
// import Button from "./../SimpleButton";
// const garImg= dynamic(()=> import('./../../../public/images/icons/garage1.jpeg'),{
//   loading:()=><>Loading.......</>,
//   ssr:false
// });

// const sqftImg= dynamic(()=> import('./../../../public/images/icons/sqft.png'),{
//   loading:()=><>Loading.......</>,
//   ssr:false
// });

// const bathImg= dynamic(()=> import('./../../../public/images/icons/bath_icon.png'),{
//   loading:()=><>Loading.......</>,
//   ssr:false
// });

// const bedImg= dynamic(()=> import('./../../../public/images/icons/bedroom_icon.png'),{
//   loading:()=><>Loading.......</>,
//   ssr:false
//  });
const Button = dynamic(() => import('./../SimpleButton'), {
  loading: () => <>Loading.......</>,
  ssr: false
});
const ToggleSwitch = dynamic(() => import('./../ToggleSwitch'), {
  loading: () => <>Loading.......</>,
  ssr: false
});

const RoomsTable = dynamic(() => import('./../PropertyDetails/RoomsTable'), {
  loading: () => <>Loading.......</>,
  ssr: false
});
const ScheduleQuery = dynamic(() => import('./RightSection'), {
  loading: () => <>Loading.......</>,
  ssr: false
});
const Card = dynamic(() => import('./../../../components/Cards/PropertyCard'), {
  loading: () => <>Loading.......</>,
  ssr: false
});
const Model = dynamic(() => import('./../Model'), {
  loading: () => <>Loading.......</>,
  ssr: false
});
const Accordion = dynamic(() => import('../Accordion'), {
  loading: () => <>Loading.......</>,
  ssr: false
});
var defaultImages = [
  "https://cdn.realtor.ca/listing/TS637762888676230000/reb19/highres/4/10243844_1.jpg",
  "https://cdn.realtor.ca/listing/TS637762888676230000/reb19/highres/4/10243844_2.jpg",
  "https://cdn.realtor.ca/listing/TS637762888676230000/reb19/highres/4/10243844_3.jpg",
  "https://cdn.realtor.ca/listing/TS637762888676230000/reb19/highres/4/10243844_9.jpg",
  "https://cdn.realtor.ca/listing/TS637762888676230000/reb19/highres/4/10243844_11.jpg",
];
var defaultDwnPercent = 20;
var defaultRate = 6;
var defaultTerm = 25;
// properties_imges
const DetailsSection = (props) => {
  let prop = props.props;
  const router = useRouter()
  // console.log("detail section login", prop);
  let hoodQApi = "95xVLjhNZ82RKeWqkU2gr8TfoOexrIYc4LU9SyKE";
  let walkscores = `https://www.walkscore.com/serve-walkscore-tile.php?wsid='${hoodQApi}'&s="${prop.slug}"&o=h&c=f&h=200&fh=0&w=690`;
  const [changeMeasure, setMeasure] = useState(true);
  const [showModalState, setModal] = useState(false);
  const [propTypeState, setPropTypeState] = useState(true);
  const [term, setTerm] = useState(defaultTerm);
  const [downPaymentPercent, setDownPaymentPercentState] = useState(defaultDwnPercent);
  const [rate, setRateState] = useState(defaultRate);
  const [downPaymentPrice, setDownPaymentState] = useState(1);
  const [similarData, setSimilar] = useState(
    prop.details.similar ? prop.details.similar : []
  );
  const [detailData, setDetailData] = useState(
    prop.details.details ? prop.details.details : {}
  );
  const [taxes, setTaxes] = useState("");
  const [mortgagePayment, setMortgagePayment] = useState("");
  const [mortgageAmount, setMortgageAmount] = useState("");
  const [totalMonthlyPayment, setTotalMonthlyPayment] = useState("");
  const [condoFee, setcondoFee] = useState("");
  const [CMHCMaintaince, setCMHCMaintaince] = useState("");
  const [carouselImages, setCarousel] = useState(prop.details.details ? prop.details.details.properties_images : defaultImages);
  const [walkScore, setWalkScore] = useState("");
  const [hooqD, setHooqd] = useState("");
  const [favIconImg, setfavIconImg] = useState("fa fa-heart-o");
  const details = prop.details ? prop.details.details : {};
  const [ShowShare,setShowShare] = useState(false);
  const [modalShow, setModalState] = useState(false);
 const [name, setName] = useState(false);
  const [email, setEmail] = useState(false);
  const [emails, setEmails] = useState(false);
  const [message, setMessage] = useState(false);
  const [showBtn, setShowBtn] = useState(true);
  const [duplicateEmails, setDuplicateEmails] = useState(false);
   const [loaderState, setLoaderState] = useState(false);
   const [nameVal, setNameVal] = useState();
  const [emailVal, setEmailVal] = useState();
  const [emailsVal, setEmailsVal] = useState();
  const [messageVal, setMessageVal] = useState();
  const [pageSetting, setPageSetting] = useState([]);
  
  var formatter = new Intl.NumberFormat("en-US", {
    style: "currency",
    currency: "USD",
    minimumFractionDigits: 0,
  });
  useEffect(() => {
    calculateMortgage();
    initSDK();

  }, []);
  useEffect(() => {
    if (localStorage.getItem('pageSetting')) {
      let setting = JSON.parse(localStorage.getItem('pageSetting'));
      setPageSetting(setting);
    }
  }, []);
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
    if (showModalState) {
      setModal(false);
    } else {
      setModal(true);
    }
    scrollToTop();
  };
  const scrollToTop = () => {
    window.scrollTo({
      top: 0,
      behavior: "smooth",
    });
  };
  const getRound = (val, log = false) => {
    let res = Math.ceil(val);
    if (res) {
      return res;
    }
    return false;
  };
  const streetViewConfig = {
    type: "street",
    mapOptions: {
      position: {
        lat: detailData.Latitude,
        lng: detailData.Longitude,
      },
      pov: {
        heading: 200,
        pitch: 0,
      },
      scrollwheel: true,
    },
  };
  function amortization(e) {
    defaultTerm = e.value
    setTerm(defaultTerm);
    calculateMortgage();
  }
  function downPayment(e) {
    console.log("downPaymentselect", e.value);
    defaultDwnPercent = e.value;
    calculateMortgage();
    setDownPaymentPercentState(defaultDwnPercent);
  }
  function keyUpHandler(e) {
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
    }, 1);
  }
  // favourite
  const favorite = (e) => {
    let userData = localStorage.getItem("userDetail");
    if (!userData) {
      props.loginPop();
      return true;
    }
    if (
      !localStorage.getItem("login_token") &&
      props.openUserPopup &&
      props.openLoginCb &&
      !userData
    ) {
      props.openLoginCb();
      return true;
    }


    let token = localStorage.getItem("login_token");
    userData = userData ? JSON.parse(localStorage.getItem("userDetail")) : null;
    const indexArr = userData.favourite_properties.indexOf(details.Ml_num)
    const reqBody = {
      LeadId: userData.login_user_id,
      AgentId: agentId,
      ListingId: details.Ml_num,
      Fav: indexArr === -1 ? 1 : 0,
    };
    const headers = {
      "Content-Type": "application/json",
      Authorization: `Bearer ${token}`,
    };
    API.jsonApiCall(favUrl, reqBody, "post", null, headers).then((res) => {
      if (reqBody.Fav === 1) {
        userData.favourite_properties.push(details.Ml_num)
        // setfavIconImg(fillHeart)
        setfavIconImg("fa fa-heart")
      } else {
        const favArr = userData.favourite_properties;
        favArr.splice(indexArr, 1);
        userData.favourite_properties = favArr;
        setfavIconImg("fa fa-heart-o")
        // setfavIconImg(emptyHeart)
      }
      localStorage.setItem("userDetail", JSON.stringify(userData))
      if (props.checkFavApiCall) {
        props.checkFavApiCall(reqBody);
      }
    });
  };
  useEffect(() => {
    let userData = localStorage.getItem("userDetail");
    userData = userData ? JSON.parse(localStorage.getItem("userDetail")) : null;
    if (userData && userData !== null && userData !== "undefined" && userData.favourite_properties.indexOf(details.Ml_num) !== -1) {
      setfavIconImg('fa fa-heart')

    }
  }, [details]);
  const sliderImages = (e) => {
    if (
      carouselImages &&
      Array.isArray(carouselImages) &&
      carouselImages.length > 0
    ) {
      const renderList = carouselImages.map((data, index) => {
        return (
          <div>
            <div style={{ padding: 2 }}>
              <img
                src={data.s3_image_url}
                alt="placeholder"
                style={{ width: "100%", height: "20rem" }}
                className="img-fluid"
              />
            </div>
          </div>
        );
      });
      return renderList;
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
  function actionBtn(action){
    // router.push('/map');
      console.log("yes login");
  }
  function initSDK() {
    let addr = prop.slug.replace("-", " ");
    try {
      var hq = new HQ.NeighbourhoodHighlights({
        el: "neighbourhood-highlights",
        address: addr,
      });
    } catch (error) {

    }
    removeIframSearch();

  }
  function removeIframSearch() {
    // var frame = document.getElementById("walkScoreIfram");
    var frame = document.getElementById('ws-street');
    return;
    var frameDoc = frame.contentDocument || frame.contentWindow.document;

    frameDoc.removeChild(frameDoc.documentElement);
  }

  function fbook() {
    window.open(
      `https://www.facebook.com/share.php?u=${props.shareLink}`,
      "Facebook",
      "width=650,height=500"
    );
  }
  function twitter() {
    window.open(
      `https://twitter.com/intent/tweet?text=${props.shareLink}`,
      "Twitter",
      "width=650,height=500"
    );
  }
  function pinterest() {
    window.open(
      `https://pinterest.com/pin/create/button/?url=${props.shareLink}`,
      "Pinterest",
      "width=650,height=500"
    );
  }
  function shareEmail(state) {
    if (!props.isLogin) {
      props.loginPop();
      return;
    }
    setModalState(state);
  }
  function handleChanges(e) {
    setLoaderState(false);
    if (e.target.name === "sender_name") {
      setNameVal(e.target.value);
      setName(e.target.value ? false : true);
    }
    if (e.target.name === "sender_email") {
      if (!validateEmail(e.target.value)) {
        setShowBtn(false);
        setEmail(true);
        setEmailVal("");
        return;
      } else {
        setEmail(false);
        setEmailVal(e.target.value);
      }
    }
    if (e.target.name === "message") {
      setMessageVal(e.target.value);
      setMessage(e.target.value ? false : true);
    }
    if (e.target.name === "email") {
      let emails = e.target.value;
      let errCount = 0;
      let duplicateCount = 0;
      emails = emails.replace(" ", "");
      emails = emails.split(',');
      let newEmails = [];
      emails.forEach(mails => {
        if (mails) {
          if (!validateEmail(mails)) {
            errCount++;
          }
          if (!newEmails.includes(mails)) {
            newEmails.push(mails);
          } else {
            setShowBtn(true);
            duplicateCount++;

          }
        }
      });
      if (duplicateCount !== 0) {
        setDuplicateEmails(true);
        return;
      } else {
        setDuplicateEmails(false);
      }
      if (errCount !== 0) {
        setShowBtn(true);
        setEmails(true);
        setEmailsVal("");
        return;
      } else {
        setEmails(false);
        setEmailsVal((emails));
      }
    }
    setTimeout(() => {
      if (nameVal && emailVal && emailsVal && messageVal && !duplicateEmails) {

        setShowBtn(false);
      } else {
        setShowBtn(true);

      }
    }, 150);
  }
  function validateEmail(e) {
    return /^(([^<>()[\]\\.,;:\s@\"]+(\.[^<>()[\]\\.,;:\s@\"]+)*)|(\".+\"))@((\[[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\])|(([a-zA-Z\-0-9]+\.)+[a-zA-Z]{2,}))$/.test(e)
  }
  function sendDetails(e) {
    if (nameVal && emailVal && emailsVal && messageVal && !duplicateEmails) {
      setShowBtn(false);
    } else {
      setShowBtn(true);
      return;
    }
    setLoaderState(true);
    setShowBtn(true);
    let data = {
      name: nameVal,
      email: emailVal,
      emails: emailsVal,
      message: messageVal,
      page_from: "property details share email form",
      property_url: props.shareLink,
      property_id: details.id,
      property_mls_no: details.Ml_num,
      details: details,
      agentId: agentId
    };
    let uri = "http://127.0.0.1:8000/api/v1/services/shareEmail"
    uri = shareEmailApi
    API.jsonApiCall(uri, data, "POST", null, {
      "Content-Type": "application/json",
    }).then((res) => {
      if (res.status == 200) {
        try {
          toast.success("Submit Successfully");
        } catch (error) {
          
        }
        shareEmail(false);
        setShowBtn(true);
        setLoaderState(false);
        setNameVal("");
        setEmailVal("");
        setEmailsVal("");
        setMessageVal("");
      }
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
  return (
    <>
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
      />
      {
        prop.checkDetailData ? <>
          <div className={`propertiesCls py-5 `} id="propertiesSection">
            <p className="text-center">Sorry ! No details found for this property please check another ! </p>
          </div>
        </> : <>
          <div>
            <div className="sliderBackground">
              <div
                style={{
                  marginTop: "-40px",
                }}
              >
                {!detect.isMobile() ?
                  <PropertyCarousel
                    imageToShow={6}
                    inRowImage={3}
                    propertyImage={carouselImages}
                    showSingleFirstSlideImage={true}
                    firstSliderRow={4}
                    sliderHeight={"600px"}
                  /> :
                  <>
                    <ReactCarousel show={1}>
                      {sliderImages()}
                    </ReactCarousel>
                  </>
                }

              </div>
            </div>
            <div className="container-fluid px-4 listing-box-container ">
              <div className={"row mt-3 p-1"}>
                <div className="col-md-8 col-lg-8 col-sm-8 col-xs-8 listing-content">
                  <div className="row px-1">
                    <div className="col-md-6 details-head-left">
                      {/* S_r */}
                      <div className="listing-day-info">
                        <div className="listing-detail-status" style={{ paddingTop: "5px" }}>
                          <div className="for-sale-tag medium-tag">For {detailData.S_r}</div>
                        </div>
                        <div className="listing-DOM regular-tag ">
                          Added <>{"Listed " + getRound(detailData.Dom, true) + " Days Ago"} </>
                          <br />
                          <br />
                          <br />
                          <p className="regular-tag">MLS®#:{detailData.Ml_num}</p>

                        </div>
                      </div>
                      <ul className="room-details">
                        {getRound(detailData.Br) && (
                          <li>
                            <img {...bedImg} className="beds mb-2" /> <br />
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
                            <img {...bathImg} className="baths mb-1" />
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
                            <img {...sqftImg} className="gar mb-2" />
                            <br />
                            <span> {detailData.Sqft + "  Sqft  "} </span>
                          </li>
                        )}
                        {getRound(detailData.Gar_spaces) && (
                          <li className="garSpace">
                            <img {...garImg} className="garSpc" />
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
                      {/* <h3 className="property-price">
                        &nbsp;&nbsp;&nbsp;
                        {formatter.format(getRound(detailData.Lp_dol))}{" "}
                      </h3> */}
                      <h3 className="exlarge-tag theme-text bold-text txt-right">
                        {formatter.format(getRound(detailData.Lp_dol))}{" "}
                      </h3>
                      <p className="txt-right" hidden>
                        Estimated Mortgage{" "}
                        <span className="estimate_payment">
                          {totalMonthlyPayment}{" "}
                        </span>
                        /m
                      </p>
                      <p className="txt-right">{detailData.Addr}</p>
                      <p className="txt-right fnt-14">{detailData.Type_own1_out}</p>
                      <p className="txt-right action_btn listing-detail-share-btn">
                        <span  className="">
                         <i onClick={()=>setShowShare(!ShowShare)} className="fa fa-share fontSize faviconHover"
                            
                            aria-hidden="true"> Share</i>
                        
                        {/*<img {...share} className="shareIcons" />
                         <span className="actionIcons">Share</span>*/}</span> &nbsp;&nbsp;&nbsp;
                        <span className="">
                          {/*<img {...save} className="saveIcons" />*/}
                          <i className={` ${favIconImg} fontSize faviconHover`}
                            onClick={favorite}
                            aria-hidden="true"> Save</i>
                          {/*<span className="actionIcons">Save</span>*/}
                        </span>
                        {ShowShare && <div className="position-relative">
                            <div className="dropdown-menu show shareOptions" x-placement="bottom-start">
                              <a className="dropdown-item fb-share mt-1" onClick={fbook}>
                                <i className="fa fa-facebook share-facebook"></i>  Facebook
                              </a>
                              <a className="dropdown-item" onClick={twitter}>
                                <i className="fa fa-twitter share-twitter"></i> Twitter
                              </a>
                              <a className="dropdown-item" onClick={pinterest}>
                                <i className="fa fa-pinterest share-pinterest"></i> Pinterest
                              </a>
                              <a className="dropdown-item mb-1" onClick={shareEmail}>
                                <i className="fa fa-envelope share-email"></i> Email
                              </a>
                            </div>
                          </div>
                        }
                      </p>
                      <br />
                      <label htmlFor="state" className="txt-right">
                        <a
                          className="form-details-btn txt-right color202020 inlineblock mt-2"
                          data-target="#apporal"
                        >
                          Details here
                        </a>
                      </label>
                    </div>
                  </div>
                  <hr />
                  {/* <Accordion
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
                  /> */}
                  <div className="row">
                    {pageSetting && pageSetting.descriptionSection == 'show' ?
                      <div className="discription-one col-12 generalDesc">
                        <details className="detailCollapes" open>
                          <summary id="generalDesc" className="detailCollapesTitle">
                            General Description{" "}
                          </summary>
                          <p className="font-family-class m-2">{detailData.Ad_text}</p>
                        </details>
                      </div> : ''}
                    {pageSetting && pageSetting.extrasSection == 'show' && detailData.Extras &&
                      <div className="discription-one col-12 extras">
                        <details className="detailCollapes" open>
                          <summary id="extras" className="detailCollapesTitle">
                            Extras{" "}
                          </summary>
                          <p className="font-family-class m-2"> {detailData.Extras}</p>
                        </details>
                      </div>
                    }
                    {pageSetting && pageSetting.propertySection == 'show' ?
                      <div className="discription-one col-12 propertyDetails">
                        <details className="detailCollapes" open>
                          <summary id="proprtyDetails" className="detailCollapesTitle">
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
                                    <span className="detailsLabel">Neighborhood: </span>
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
                                    <span className="detailsLabel">Land Size: </span>
                                    <span className="detailsLvalue">
                                      {getRound(detailData.Sqft)
                                        ? getRound(detailData.Sqft) + " Sqft"
                                        : ""}
                                    </span>
                                  </li>
                                )}
                                {getRound(detailData.Park_spcs) && (
                                  <li>
                                    <span className="detailsLabel">Parking: </span>
                                    <span className="detailsLvalue">
                                      {getRound(detailData.Park_spcs)
                                        ? getRound(detailData.Park_spcs) + " "
                                        : ""}{" "}
                                    </span>
                                  </li>
                                )}
                                {getRound(detailData.Br) && (
                                  <li>
                                    <span className="detailsLabel">Bedrooms: </span>
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
                                    <span className="detailsLabel">Basement:</span>
                                    <span className="detailsLvalue">
                                      {" "}
                                      {detailData.Bsmt1_out}
                                    </span>
                                  </li>
                                )}
                                {detailData.Fpl_num && (
                                  <li>
                                    <span className="detailsLabel">Fireplace:</span>{" "}
                                    <span className="detailsLvalue">
                                      {detailData.Fpl_num}
                                    </span>
                                  </li>
                                )}
                                {detailData.Cross_st && (
                                  <li>
                                    <span className="detailsLabel">Cross Street:</span>{" "}
                                    <span className="detailsLvalue">
                                      {detailData.Cross_st}
                                    </span>
                                  </li>
                                )}
                                {detailData.Gar_type && (
                                  <li>
                                    <span className="detailsLabel">Garage Type:</span>
                                    <span className="detailsLvalue">
                                      {" "}
                                      {detailData.Gar_type}
                                    </span>
                                  </li>
                                )}
                                {detailData.S_r && (
                                  <li>
                                    <span className="detailsLabel">Status:</span>{" "}
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
                      </div> : ''}
                    <div>
                      <div className="discription-one roomsDetails">
                        <div id="walkScoreSection">
                          <hr />
                          <div id="ws-walkscore-tile"></div>
                          {walkscores &&
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
                              className="walkScore"
                            ></iframe>
                          }
                          <div id="overLap" className="py-2"></div>
                          <hr />
                        </div>
                        <div id="neighbourhood-highlights" className="mt-4"></div>
                        {detailData.RoomsDescription && detailData.RoomsDescription.length > 0 && (
                          <details className="detailCollapes2 mt-2" open>
                            <summary id="roomsDetailsd" className="detailCollapesTitle">
                              Rooms Details{" "}
                            </summary>
                            <div className="row rooms-measure ">
                              <div className="col-md-6 col-sm-6 col-lg-6">
                                <h4>Rooms</h4>
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
                        <details className=" height-auto mt-2 borders" open>
                          <summary id=" " className="detailCollapesTitle">
                            <span>Neighbourhood </span>
                          </summary>
                          {/*  */}
                          <div className=" mt-4">
                            <div className="row">
                              <div className="col-md-12">
                                <div className="neighbourhood-cls">
                                  <p>Schools, amenities, Restaurant & Bar , Grocery , Service , and Hospitals near {detailData.Addr}</p>
                                  <div className="">
                                    {/* <div className="marker marker-cls" htmlFor="maps" onClick={showModal}>
                                      <div className="marker marker-cls2" htmlFor="maps" onClick={showModal}></div>
                                    </div>
                                    <img src="./../../../images/amenities.png" alt="img" id="maps" className="w-100" onClick={showModal} />
                                   <div className="neigh-menu-list">
                                   {
                                      renderList()
                                    }
                                   </div> */}
                                   <AmenitiesMap mapResize={true}  details={prop.details?prop.details.details:{}} amenities={prop.amenities} getYelpData={props.getYelpData} />
                                  </div>
                                  <br />
                                  <br />
                                </div>
                              </div>
                            </div>
                          </div>
                        </details>
                        <br />

                        <span className="broker-text mt-2">
                          &nbsp; Rltr:
                          <span className="broker-text"> {detailData.Rltr} </span>
                        </span>
                      </div>
                    </div>
                  </div>
                </div>
                <div className="col-md-4 col-lg-4 col-sm-4 col-xs-4">
                  <div className=" listing-content">
                    <div className="row ">
                      <div className="col-md-12 ">
                        <div className="viewmap loadMap">
                          <img src="./../../../images/map-box.png" alt="img" className="w-100" />
                          <a className="loadMap" onClick={showModal}>
                            View map &amp; nearby amenities
                        </a>
                        </div>
                      </div>
                    </div>
                    <div className="row">
                      <div className="col-md-12">
                        <ScheduleQuery {...detailData}
                          {...props}
                        />
                      </div>
                    </div>
                  </div>
                </div>

              </div>
            </div>
          </div>
        </>
      }
      <div className="container-fluid pb-5">
        <div className="row similarProperties">
          <div className="col-md-12">
            <hr />
            <h3 className="heading">Similar Listings </h3>
          </div>
          <div className="col-md-12 mt-1 propTypeBtn mb-3">
            <Button
              extraProps={{
                size: "md",

                className: `gridMapView btn  ${propTypeState ? "rentBtn" : ""
                  }`,
                type: "button",
                value: "Sale",
                text: "Sale",
                onClick: togglePropTypes,
              }}
            />
            <Button
              extraProps={{
                size: "md",
                className: `gridMapView btn  ${propTypeState ? "" : "rentBtn"
                  }`,
                type: "button",
                value: "Rent",
                text: "Rent",
                onClick: togglePropTypes,
              }}
            />
          </div>
          {similarData ? (
            <>
              {propTypeState ? (
                <>
                  {similarData.sale.map((data, key) => {
                    return (
                      <div className="col-md-3 mt6 detailCards" key={key}>
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
                    );
                  })}
                </>
              ) : (
                <>
                  {similarData.rent.map((data, key) => {
                    return (
                      <div className="col-md-3 mt6 detailCards" key={key}>
                        <Card
                          showIsFav={true}
                          openUserPopup={true}
                          openLoginCb={props.togglePopUp}
                          isLogin={props.isLogin}
                          item={data}
                          key={key}
                          defaultImage={Constant.defaultImage}
                          LoginRequired={1}
                        />
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
      </div>

      <div className="">
              <Modal
                show={modalShow} onHide={() => shareEmail(false)}
                className="emailShareModel"
                size="lg"
                aria-labelledby="contained-modal-title-vcenter"
              >
                <Modal.Header closeButton>
                  <Modal.Title id="contained-modal-title-vcenter">
                    Share Listings
              </Modal.Title>
                </Modal.Header>
                <Modal.Body>
                  <div className="popup_propform" id="modalEmailForm" >
                    <div className="row" id="2">
                      <div className="col-md-6 col-sm-6 col-lg-6 form-group ">
                        <label className="">Your Name*</label>
                        <input type="text" onChange={handleChanges} onBlur={handleChanges} className=" form-control senderName input-box" placeholder="Name*" name="sender_name" required="" />
                        <span className={`validateError  ${name ? "" : "hide"}`} >Name is required.</span>
                      </div>
                      <div className="form-group col-md-6 col-sm-6 col-lg-6">
                        <label className="">Your Email*</label>
                        <input type="email" onChange={handleChanges} onBlur={handleChanges} className="  form-control senderEmail input-box" placeholder="Email*" name="sender_email" required="" />
                        <span className={`validateError  ${email ? "" : "hide"}`} >Email is required.</span>
                      </div>
                      <div className="form-group col-md-12 col-sm-12 col-lg-12 mt-3">
                        <label className="">Your Friends Emails*</label>
                        <input type="email" onChange={handleChanges} onBlur={handleChanges} className=" form-control recipentEmail input-box" placeholder="Emails*" name="email" required="" />
                        <span className={`validateError  ${emails ? "" : "hide"}`} >Emails are required.</span>
                        <span className={`validateError  ${duplicateEmails ? "" : "hide"}`} >Remove duplicate emails</span>

                      </div>

                      <div className="form-group col-lg-12 mt-3">
                        <label className="">Message*</label>
                        <textarea onChange={handleChanges} onBlur={handleChanges} className="form-control input-box" name="message" style={{ "height": "66px" }} placeholder={`I would like to get more info about ${details.Addr}`}></textarea>
                        <span className={`validateError  ${message ? "" : "hide"}`} >Message is required.</span>
                      </div>
                      <div className="col-md-4">
                      </div>
                      <div className="col-md-4">
                        <div className="shareEmail">
                          {!loaderState ? <>
                            <button id="shareEmailBtn" disabled={showBtn || message} type="btn" className="btn showSchedule btn-sm mt-4" onClick={sendDetails} > <i className="fa fa-spinner fa-spin"> </i>Submit</button>
                          </> : <>
                            <button id="shareEmailBtn" disabled={true} type="btn" className="btn showSchedule btn-sm mt-4" onClick={sendDetails} > <i className="fa fa-spinner fa-spin"> </i>Submiting......</button>
                          </>
                          }


                        </div>
                      </div>
                      <div className="col-md-4">
                      </div>
                    </div>
                  </div>
                </Modal.Body>
              </Modal>
            </div>
    </>
  );
};

export default DetailsSection;
