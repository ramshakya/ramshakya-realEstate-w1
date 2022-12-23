import React, { useState, useEffect, useRef } from "react";
import Constant from "./../../../constants/Global";
import bathImg from "./../../../public/images/icon/bath.svg";
import garImg from "./../../../public/images/icon/garage.png";
import downArrow from "./../../../public/images/icon/down-arrow-3.png";
import { ToastContainer, toast } from "react-toastify";
import PropertyCarousel from "./PropertyCarousel";
import ReactCarousel from "../ReactCarousel";
import Slider from "@material-ui/core/Slider";
import detect from "../../utility/detect";
import dynamic from "next/dynamic";
import { useRouter } from "next/router";
import { Modal, Tabs, Tab, Form } from "react-bootstrap";
import {
  shareEmailApi,
  agentId,
  favUrl,
  metaJson,
  saveSearchApi,
  getWatchProperty,
} from "../../../constants/Global";
import API from "./../../utility/api";
import Schedule from "./../ScheduleShowing/index-v2";
import Listings from "./../../../components/Home/Listings";
import Autocomplete from "./../AutoSuggestion";
import Notifications from "./../../../public/images/icon/notification.svg";
import { getDomainLocale } from "next/dist/shared/lib/router/router";
import Model from "./../Model";
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
const Card = dynamic(() => import("./../../../components/Cards/PropertyCard"), {
  loading: () => <>Loading.......</>,
  ssr: false,
});

var defaultImages = [];
var defaultDwnPercent = 20;
var defaultRate = 2.7;
var defaultTerm = 25;
var homePrice = 0;
// properties_imges
const DetailsSection = (props) => {
  const { similarProperty } = props.props;
  let prop = props.props;

  const router = useRouter();
  let walkscoreApi = props.webSetting
    ? props.webSetting.WalkScoreApiKey
    : "95xVLjhNZ82RKeWqkU2gr8TfoOexrIYc4LU9SyKE";
  let walkscores = `https://www.walkscore.com/serve-walkscore-tile.php?wsid='${walkscoreApi}'&s="${prop.slug}"&o=h&c=f&h=200&fh=0&w=690`;
  const [changeMeasure, setMeasure] = useState(true);
  const [showModalState, setModal] = useState(false);
  const [propTypeState, setPropTypeState] = useState(true);
  const [comparableState, setComparableState] = useState(true);
  const inputReference = useRef(null);
  const [clicked, setClicked] = useState(false);
  const [term, setTerm] = useState(defaultTerm);
  const [downPaymentPercent, setDownPaymentPercentState] =
    useState(defaultDwnPercent);
  const [rate, setRateState] = useState(defaultRate);
  const [downPaymentPrice, setDownPaymentState] = useState(1);
  const [similarData, setSimilar] = useState(
    similarProperty ? similarProperty.similar : []
  );
  const [detailData, setDetailData] = useState(
    prop.details.details ? prop.details.details : {}
  );
  const [nearest, setNearest] = useState([]);
  const details = prop.details ? prop.details.details : {};
  const [carouselImages, setCarousel] = useState(
    details ? details.properties_images : defaultImages
  );

  const [totalMonthlyPayment, setTotalMonthlyPayment] = useState("");
  const [favIconImg, setfavIconImg] = useState("fa fa-heart-o");
  const [ShowShare, setShowShare] = useState(false);
  const [modalShow, setModalState] = useState(false);
  const [virtualTour, setVirtualTour] = useState(false);
  const [listingPriceData, setListingHistoryData] = useState(
    prop.listingHistoryData
  );
  const [watchList, setWatchList] = useState(false);
  const [checkBoxes, setCheckBoxes] = useState(false);

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
  const [emailIsVerified, setEmailVerified] = useState(props.emailIsVerified);
  const [schoolsStatus, setSchoolsStatus] = useState("elementory");
  const [descriptionText, setDescriptionText] = useState("");
  const [step, setstep] = useState(1);
  const [sliderValue, setSliderValue] = useState(0);
  const [insideDetails, setInsideDetails] = useState({});
  const [watchListLAerts, setWatchListLAerts] = useState(false);
  const [seeMore, setSeeMore] = useState(false);
  const [watchListingsAlerts, setWatchListingsAlerts] = useState({});
  const [watchedListingsForSold, setWatchedListingsForSold] = useState(false);
  const [watchedListingsForComm, setWatchedListingsForComm] = useState(false);
  const [watchedListData, setWatchedListData] = useState(false);

  var formatter = new Intl.NumberFormat("en-US", {
    style: "currency",
    currency: "USD",
    minimumFractionDigits: 0,
  });
  useEffect(() => {
    let detailData = details ? details : false;
    if (detailData) {
      let ob = {
        s3_image_url: Constant.defaultImage,
        listingI: "12565656",
        isDefault: true,
      };
      let images = [ob];
      if (detailData.properties_images.length) {
        images = detailData ? detailData.properties_images : [ob];
      }

      setDetailData(detailData);
      setCarousel(images);
      homePriceFocus();
    }
  }, [props.props]);
  useEffect(() => {
    if (props.emailIsVerified) {
      setEmailVerified(props.emailIsVerified);
    }
  }, [props.emailIsVerified]);

  useEffect(() => {
    setListingHistoryData(prop.listingHistoryData);
  }, [prop.listingHistoryData]);

  useEffect(() => {
    getWatchedList();
    let Kitchen = 0;
    let Breakfast = 0;
    let Living = 0;
    let Dining = 0;
    let rooms = 0;
    if (detailData && Array.isArray(detailData.RoomsDescription)) {
      detailData.RoomsDescription.map((data, index) => {
        if (data.name === "Kitchen") {
          Kitchen++;
        }
        if (data.name === "Breakfast") {
          Breakfast++;
        }
        if (data.name === "Living") {
          Living++;
        }
        if (data.name === "Dining") {
          Dining++;
        }
        if (data.name.includes("Br")) {
          rooms++;
        }
      });
    }
    let obj = {
      Kitchen: Kitchen,
      Breakfast: Breakfast,
      Living: Living,
      Dining: Dining,
      rooms: rooms,
    };
    setInsideDetails(obj);
    calculateMortgage();
    initSDK();
    if (detailData && detailData.Ad_text) {
      let desc = detailData.Ad_text;
      let Extras = detailData.Extras;
      let fullText = desc.concat(Extras);
      setDescriptionText(
        fullText.length > 300 ? detailData.Ad_text.substr(0, 300) + "..." : ""
      );
    }
  }, []);

  useEffect(() => {
    if (similarProperty && similarProperty.similar) {
      setNearest(similarProperty.similar.nearest);
      setSimilar(similarProperty.similar);
    }
  }, [similarProperty.similar]);
  useEffect(() => {
    if (localStorage.getItem("detailPageSetting")) {
      let setting = JSON.parse(localStorage.getItem("detailPageSetting"));
      setPageSetting(setting);
    }
    // console.clear();
  }, [props]);

  function enabledDrop() {
    let el = document.getElementById("year_select");
    setClicked(!clicked);
    // inputReference.current.focus();
  }
  const switchToggel = (e) => {
    if (changeMeasure) {
      setMeasure(false);
    } else {
      setMeasure(true);
    }
  };
  function showMoreText() {
    setSeeMore(!seeMore);
    if (seeMore) {
      if (detailData && detailData.Ad_text) {
        let desc = detailData.Ad_text;
        let Extras = detailData.Extras;
        let fullText = desc.concat(Extras);
        setDescriptionText(
          fullText.length > 300 ? detailData.Ad_text.substr(0, 300) + "..." : ""
        );
      }
    } else {
      setDescriptionText(detailData.Ad_text);
    }
  }
  function setListingsAlert() {
    setWatchListLAerts(true);
    setTimeout(() => {
      if (watchedListData && watchedListData.length) {
        watchedListData.map((item, k) => {
          let watched = JSON.parse(item.watchListings);
          if (watched.isSold) {
            setWatchedListingsForSold(watched);
          } else {
            setCheckBoxesChecked(watched);
            setWatchedListingsForComm(watched);
          }
        });
      }
    }, 200);
  }

  function rangeSelector(event, newValue) {
    setSliderValue(newValue);
    defaultDwnPercent = newValue;
    calculateMortgage();
    setDownPaymentPercentState(defaultDwnPercent);
  }

  const handleChangeCommitted = (event, newValue) => {};
  function changeSchoolStatus(e) {
    if (e.target.attributes.dataset.value == 1) {
      setSchoolsStatus("elementory");
    } else {
      setSchoolsStatus("secondary");
    }
  }
  const showModal = (e) => {
    setModal(!showModalState);
  };
  const getRound = (val, log = false) => {
    let res = Math.ceil(val);
    if (res) {
      return res;
    }
    return false;
  };
  homePrice = getRound(details.Lp_dol);
  // const streetViewConfig = {
  //   type: "street",
  //   mapOptions: {
  //     position: {
  //       lat: detailData.Latitude,
  //       lng: detailData.Longitude,
  //     },
  //     pov: {
  //       heading: 200,
  //       pitch: 0,
  //     },
  //     scrollwheel: true,
  //   },
  // };
  const streetViewConfig = {
    type: "street",
    mapOptions: {
      position: {
        lat: parseFloat(detailData ? detailData.Latitude : 0),
        lng: parseFloat(detailData ? detailData.Longitude : 0),
      },
      pov: {
        heading: 200,
        pitch: 0,
      },
      scrollwheel: true,
    },
  };

  function verifyEmail() {
    localStorage.setItem("verifyemail", true);
    router.push("/profile");
  }
  function amortization(e) {
    defaultTerm = e.value;
    setTerm(defaultTerm);
    calculateMortgage();
  }
  // used
  function downPayment(e) {
    defaultDwnPercent = e.target.value;
    setSliderValue(defaultDwnPercent);
    calculateMortgage();
    setDownPaymentPercentState(defaultDwnPercent);
  }
  // used
  function downPaymentUsd(e) {
    let ddefaultDwnPercent = e.value;
    e.target.value = e.target.value;

    setDownPaymentState(e.target.value);
    calculateMortgage();
    // setDownPaymentPercentState(defaultDwnPercent);
  }
  function mortageRateFocus(e) {
    e.target.value = defaultRate;
  }
  function mortageRateChange(e) {
    defaultRate = e.target.value;
    e.target.value = defaultRate;
    calculateMortgage();
  }
  function homePriceFocus(e) {
    try {
      if (e) {
        e.target.value = homePrice;
      } else {
        document.getElementById("homeprice").value = homePrice;
      }
    } catch (error) {}
    calculateMortgage();
  }
  function homePriceChange(e) {
    homePrice = e.target.value;
    e.target.value = homePrice;
    calculateMortgage();
  }

  function getDom() {
    let d = getRound(detailData.Dom);
    if (d) {
      if (d > 1) {
        return "Listed " + d + " Days Ago";
      }
      if (d == 1) {
        return "Listed " + d + " Day Ago";
      }
    } else {
      return "Just Listed";
    }
  }

  function calculateMortgage() {
    let main_price = homePrice;
    let dp = (defaultDwnPercent / 100) * main_price;
    let timeTerm = defaultTerm;
    let loanAmount = main_price - dp;
    let numberOfMonths = timeTerm * 12;
    let rateOfInterest = defaultRate ? defaultRate : 2.7;
    let monthlyInterestRatio = rateOfInterest / 100 / 12;
    let top = Math.pow(1 + monthlyInterestRatio, numberOfMonths);
    let bottom = top - 1;
    let sp = top / bottom;
    let emi = loanAmount * monthlyInterestRatio * sp;
    let final = emi;
    let newPrice = formatter.format(parseInt(final));
    setDownPaymentState(dp);
    setSliderValue(defaultDwnPercent);
    setTotalMonthlyPayment(newPrice);
  }
  // favourite
  const favorite = (e) => {
    let userData = localStorage.getItem("userDetail");
    if (!userData) {
      props.togglePopUp();
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
    const indexArr = userData.favourite_properties.indexOf(details.Ml_num);
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
        userData.favourite_properties.push(details.Ml_num);
        // setfavIconImg(fillHeart)
        setfavIconImg("fa fa-heart");
      } else {
        const favArr = userData.favourite_properties;
        favArr.splice(indexArr, 1);
        userData.favourite_properties = favArr;
        setfavIconImg("fa fa-heart-o");
        // setfavIconImg(emptyHeart)
      }
      localStorage.setItem("userDetail", JSON.stringify(userData));
      if (props.checkFavApiCall) {
        props.checkFavApiCall(reqBody);
      }
    });
  };
  useEffect(() => {
    let userData = localStorage.getItem("userDetail");
    userData = userData ? JSON.parse(localStorage.getItem("userDetail")) : null;
    if (
      userData &&
      userData !== null &&
      userData !== "undefined" &&
      userData.favourite_properties.indexOf(details.Ml_num) !== -1
    ) {
      setfavIconImg("fa fa-heart");
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
          <div key={index}>
            <div style={{ padding: 2 }}>
              <img
                src={Constant.image_base_url + data.s3_image_url}
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
  function initSDK() {
    try {
      let slug = prop.slug;
      let addr = "";
      slug = slug.split("-");
      slug.map((el, k) => {
        if (k < slug.length - 2) {
          addr = addr + " " + el;
        }
      });
      var hq = new HQ.NeighbourhoodHighlights({
        el: "neighbourhood-highlights",
        address: addr,
      });
      removeIframSearch();
    } catch (error) {}
  }
  function removeIframSearch() {
    var frame = document.getElementById("ws-street");
    return;
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
  function hideVirtualTour(state) {
    setVirtualTour(!virtualTour);
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
      emails = emails.split(",");
      let newEmails = [];
      emails.forEach((mails) => {
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
        setEmailsVal(emails);
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
    return /^(([^<>()[\]\\.,;:\s@\"]+(\.[^<>()[\]\\.,;:\s@\"]+)*)|(\".+\"))@((\[[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\])|(([a-zA-Z\-0-9]+\.)+[a-zA-Z]{2,}))$/.test(
      e
    );
  }
  function signInToggle() {
    if (props.isLogin) {
      verifyEmail();
    } else {
      props.togglePopUp();
    }
  }
  function joinToggle() {
    if (props.isLogin) {
      verifyEmail();
    } else {
      localStorage.setItem("isjoin", true);
      props.togglePopUp();
    }
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
      agentId: agentId,
    };
    let uri = "http://127.0.0.1:8000/api/v1/services/shareEmail";
    uri = shareEmailApi;
    API.jsonApiCall(uri, data, "POST", null, {
      "Content-Type": "application/json",
    })
      .then((res) => {
        if (res.status == 200) {
          try {
            toast.success("Submit Successfully");
          } catch (error) {}
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
        } catch (error) {}
        this.setState({
          dataFlag: false,
        });
      });
  }
  const toggleComparable = (e) => {
    if (e.target.value == "Sold") {
      setComparableState(true);
    }
    if (e.target.value == "Rent") {
      setComparableState(false);
    }
  };

  function handleInputChanges(e) {
    let name = e.target.name;
    let obj = {};
    if (checkBoxes && !Array.isArray(checkBoxes)) {
      obj = checkBoxes ? checkBoxes : {};
    }
    if (!e.target.checked) {
      e.target.value = false;
    }
    obj[name] = e.target.checked;
    // let tem =  { watchListings: { AlertsOn: obj?obj:{} } };
    // let AlertsOn = watchList.watchListings
    //   ? watchList.watchListings.AlertsOn
    //   : [];
    // tem.watchListings.AlertsOn[name] = !AlertsOn[name];
    // e.target.checked = !AlertsOn[name];
    // setWatchList(tem);

    // watchList, setWatchList
    setWatchListingsAlerts(obj);
  }
  function saveListingsAlert() {
    if (!props.isLogin) {
      props.togglePopUp();
      return;
    }
    let userDetails = props.userDetails;
    let sold = {
      isSold: false,
      ListingId: detailData.Ml_num,
      Community: detailData.Community,
      PropertySubType: detailData.Type_own1_out,
      City: detailData.Municipality,
      AlertsOn: watchListingsAlerts,
    };
    setCheckBoxes(watchListingsAlerts);
    let ob = {
      isWatchListings: true,
      agentId: agentId,
      userId: userDetails.login_user_id,
      watchListings: sold,
    };
    let urls = saveSearchApi;
    API.jsonApiCall(urls, ob, "POST", null, {
      "Content-Type": "application/json",
    })
      .then((res) => {
        try {
          toast.success("Listing Watched");
          getWatchedList();
        } catch (error) {}
      })
      .catch((e) => {
        try {
          toast.error("Something went wrong try later!");
        } catch (error) {}
      });
    setWatchListLAerts(!watchListLAerts);
  }

  function getWatchedList() {
    // return;
    if (!props.isLogin) {
      return true;
    }
    let ob = {
      ListingId: detailData.Ml_num,
      userId: props.userDetails.login_user_id,
    };
    let urls = getWatchProperty;
    API.jsonApiCall(urls, ob, "POST", null, {
      "Content-Type": "application/json",
    })
      .then((res) => {
        try {
          let obj = res.watchList;
          if (res.error) {
            return;
          }
          try {
            setWatchedListData(obj);
            setWatchListLAerts(false);

            if (obj && obj.length) {
              obj.map((item, k) => {
                let watched = JSON.parse(item.watchListings);
                if (watched.isSold) {
                  setWatchedListingsForSold(watched);
                } else {
                  setCheckBoxesChecked(watched);
                  setWatchedListingsForComm(watched);
                }
              });
            }
          } catch (error) {
            console.log("gets data error", error);
          }
        } catch (error) {
          console.log("gets data error", error);
        }
      })
      .catch((e) => {});
  }
  function setCheckBoxesChecked(watched) {
    let AlertsOn = watched.AlertsOn;
    if (
      AlertsOn &&
      AlertsOn !== null &&
      AlertsOn !== "" &&
      AlertsOn !== undefined
    ) {
      let ob = {
        watchListings: {
          AlertsOn: AlertsOn,
        },
      };
      if (AlertsOn.DelistedListings) {
        let el = document.getElementById("DelistedListings");
        if (el) {
          el.checked = true;
        }
      }
      if (AlertsOn.NewListings) {
        let el = document.getElementById("NewListings");
        if (el) {
          el.checked = true;
        }
      }
      if (AlertsOn.SoldListings) {
        let el = document.getElementById("SoldListings");
        if (el) {
          el.checked = true;
        }
      }
      setCheckBoxes(AlertsOn);
      setWatchList(ob);
    }
  }

  // done
  function watchListingsSold() {
    if (!props.isLogin) {
      props.togglePopUp();
      return;
    }
    let userDetails = props.userDetails;
    let flag = true;
    if (watchedListingsForSold && watchedListingsForSold.isSold) {
      flag = false;
    }
    // return;
    let sold = {
      isSold: flag,
      ListingId: detailData.Ml_num,
    };
    let ob = {
      isWatchListings: true,
      agentId: agentId,
      userId: userDetails.login_user_id,
      watchListings: sold,
    };
    let urls = saveSearchApi;
    API.jsonApiCall(urls, ob, "POST", null, {
      "Content-Type": "application/json",
    })
      .then((res) => {
        try {
          toast.success(
            flag ? "Listing Watched" : "Watched Listing is removed "
          );
          getWatchedList();
          setWatchedListingsForSold(sold);
        } catch (error) {}
      })
      .catch((e) => {
        try {
          toast.error("Something went wrong try later!");
        } catch (error) {}
      });
  }
  function goToMap() {
    let mls = detailData.Ml_num;
    let filters = {
      searchFilter: {},
      preField: {},
    };
    let field = {
      text: mls,
      value: mls,
      category: "text_search",
      group: "ListingId",
    };
    filters.searchFilter.text_search = mls;
    filters.preField.text_search = field;
    localStorage.setItem("filters", JSON.stringify(filters));
    router.push("/map");
  }
  return (
    <>
      {prop.checkDetailData ? (
        <>
          <div className={`propertiesCls py-5 `} id="propertiesSection">
            <p className="text-center">
              Sorry ! No details found for this property please check another !{" "}
            </p>
          </div>
        </>
      ) : (
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
            />
          )}
          <div>
            <div
              className={` ${props.emailIsVerified ? "sliderBackground" : ""}`}
            >
              <div
                style={{
                  marginTop: detect.isMobile() ? "15%" : "-31px",
                }}
                id="sliderOff"
              >
                {!detect.isMobile() ? (
                  <PropertyCarousel
                    imageToShow={6}
                    inRowImage={3}
                    propertyImage={carouselImages}
                    showSingleFirstSlideImage={true}
                    firstSliderRow={4}
                    sliderHeight={"600px"}
                    signInToggle={signInToggle}
                    {...props}
                  />
                ) : (
                  <>
                    {detailData && (
                      <ReactCarousel show={1}>{sliderImages()}</ReactCarousel>
                    )}
                  </>
                )}
              </div>
            </div>
            <div className="container-fluid px-4 listing-box-container ">
              <div className={"row mt-3 p-1"}>
                <div className="col-md-8 col-lg-8 col-sm-8 col-xs-8 listing-content p-4">
                  <div
                    className={`${
                      detect.isMobile() ? "view-full-map2" : "view-full-map"
                    }`}
                  >
                    <button
                      className="primary-btn-cls btn mb-5 gray-color-cls"
                      onClick={goToMap}
                    >
                      View Listing In Full Map
                    </button>
                  </div>
                  <div className="row px-1">
                    <div className="col-md-6 col-lg-6 col-6">
                      <div className="listing-day-info">
                        <div
                          className="listing-detail-status1"
                          style={{ paddingTop: "5px" }}
                        >
                          <div className="for-sale-tag medium-tag"></div>
                        </div>
                        <div className="listing-DOM regular-tag fnt-16  ">
                          <p className="txt-left exlarge-tag text-black mt-1 mb-1">
                            {detailData.Addr}{" "}
                          </p>
                          <p className="txt-left text-gray mb-1">
                            {detailData.Municipality}
                            {" - "}
                            {detailData.Community}
                          </p>
                          <ul className="listingTypeDom">
                            <li className="second-list ">
                              <span className="subtypeText">
                                <b>{detailData.Type_own1_out}</b>
                              </span>
                            </li>
                          </ul>
                        </div>
                      </div>
                    </div>
                    <div className="col-md-6 col-lg-6 col-6 details-head-right">
                      <div className="row">
                        <div
                          className={`${
                            (detailData.S_r === "Sale" &&
                              detailData.Status === "U") ||
                            detailData.Status == "D"
                              ? "col-md-4"
                              : "col-md-6"
                          }`}
                        >
                          <div className="listing-detail-status textEnd">
                            {detailData.Lsc === "Sld" &&
                              detailData.Status === "U" && (
                                <div className="for-sale-tag medium-tag forSold  ">
                                  {" "}
                                  Sold
                                </div>
                              )}
                            {detailData.S_r === "Sale" &&
                              detailData.Status === "A" && (
                                <div className="for-sale-tag medium-tag  forSale-cls">
                                  {" "}
                                  {"For " + detailData.S_r}
                                </div>
                              )}
                            {detailData.S_r === "Lease" &&
                              detailData.Lsc === "Lsd" &&
                              detailData.Status === "U" && (
                                <div className="for-sale-tag medium-tag  forLease">
                                  {" "}
                                  {"Leased "}
                                </div>
                              )}
                            {detailData.S_r === "Lease" &&
                              detailData.Status === "A" && (
                                <div className="for-sale-tag medium-tag  forLease">
                                  {" "}
                                  For {detailData.S_r}
                                </div>
                              )}
                            {detailData.Lsc === "Ter" &&
                              detailData.Status === "U" && (
                                <div className="for-sale-tag medium-tag  forTer">
                                  {" "}
                                  Terminated
                                </div>
                              )}
                            {detailData.Lsc === "Ter" &&
                              detailData.Status === "D" &&
                              detailData.Status !== "U" && (
                                <div className="for-sale-tag medium-tag  forTer">
                                  {" "}
                                  Terminated
                                </div>
                              )}
                            {detailData.Lsc === "Exp" &&
                              detailData.Status === "U" && (
                                <div className="for-sale-tag medium-tag  forExpAll">
                                  {" "}
                                  Expired
                                </div>
                              )}
                            {detailData.Lsc === "Dft" &&
                              detailData.Status === "U" && (
                                <div className="for-sale-tag medium-tag  forExpAll">
                                  {" "}
                                  Draft
                                </div>
                              )}
                            {detailData.Lsc === "Sus" &&
                              detailData.Status === "U" && (
                                <div className="for-sale-tag medium-tag  forExpAll">
                                  {" "}
                                  Suspended
                                </div>
                              )}
                            {detailData.Lsc === "Pc" &&
                              detailData.Status === "U" && (
                                <div className="for-sale-tag medium-tag  forTer">
                                  {" "}
                                  Terminated
                                </div>
                              )}
                          </div>
                        </div>
                        <div
                          className={`${
                            detailData.Status === "U" ? "col-md-6" : "col-md-6"
                          }`}
                        >
                          {detailData.Status === "U" ||
                          detailData.Status == "D" ? (
                            <>
                              <ul className="price-listed-sections textEnd">
                                {detailData.Sp_dol !== detailData.Lp_dol && (
                                  <li>
                                    <span className="heading-price-listed ">
                                      Listed for:
                                    </span>{" "}
                                    <span className="original-price">
                                      {formatter.format(
                                        getRound(detailData.Lp_dol)
                                      )}
                                    </span>
                                  </li>
                                )}
                                <li>
                                  <span className="heading-price-listed ">
                                    {detailData.S_r === "Sale" &&
                                    detailData.Lsc === "Sld" &&
                                    detailData.Status === "U"
                                      ? "Sold"
                                      : ""}
                                    {detailData.S_r === "Lease" &&
                                    detailData.Lsc === "Lsd" &&
                                    detailData.Status === "U"
                                      ? "Leased"
                                      : ""}
                                    {detailData.Lsc === "Ter" &&
                                      detailData.Status === "U" &&
                                      "Terminated"}
                                    {detailData.Lsc === "Ter" &&
                                      detailData.Status === "D" &&
                                      detailData.Status !== "U" &&
                                      "Terminated"}
                                    {detailData.Lsc === "Exp" &&
                                      detailData.Status === "U" &&
                                      "Expired"}
                                    {detailData.Lsc === "Dft" &&
                                      detailData.Status === "U" &&
                                      "Draft"}
                                    {detailData.Lsc === "Sus" &&
                                      detailData.Status === "U" &&
                                      "Suspended"}
                                    {detailData.Lsc === "Pc" &&
                                      detailData.Status === "U" &&
                                      "Terminated"}{" "}
                                    for:
                                  </span>{" "}
                                  <span className="sold-price">
                                    {formatter.format(
                                      getRound(
                                        detailData.Sp_dol > 0
                                          ? detailData.Sp_dol
                                          : detailData.Lp_dol
                                      )
                                    )}
                                  </span>
                                </li>
                              </ul>
                            </>
                          ) : (
                            <>
                              <h3 className="exlarge-tag theme-text bold-text  mt-2 textEnd">
                                {formatter.format(getRound(detailData.Lp_dol))}{" "}
                              </h3>
                            </>
                          )}
                        </div>
                      </div>
                      <p
                        className={`
                        ${
                          detailData.S_r === "Sale" &&
                          detailData.Lsc === "Sld" &&
                          detailData.Status === "U"
                            ? "sold-in-tag "
                            : " "
                        }
                        ${
                          detailData.S_r === "Lease" &&
                          detailData.Lsc === "Lsd" &&
                          detailData.Status === "U"
                            ? "sold-in-tag"
                            : " "
                        }
                        ${
                          detailData.Lsc === "Ter" && detailData.Status === "U"
                            ? "sold-in-tag"
                            : " "
                        }
                        listing-DOM regular-tag textEnd mt-0  `}
                      >
                        {detailData.Status === "U" ||
                        detailData.Status === "D" ? (
                          <>
                            <b>
                              {/* {detailData.Status === "D" ? "De-listed" : ""}{" "} */}
                              {detailData.S_r === "Sale" &&
                              detailData.Lsc === "Sld" &&
                              detailData.Status === "U"
                                ? "Sold"
                                : ""}
                              {detailData.S_r === "Lease" &&
                              detailData.Lsc === "Lsd" &&
                              detailData.Status === "U"
                                ? "Leased"
                                : ""}
                              {detailData.Lsc === "Ter" &&
                                detailData.Status === "U" &&
                                "Terminated"}
                              {detailData.Lsc === "Ter" &&
                                detailData.Status === "D" &&
                                detailData.Status !== "U" &&
                                "Terminated"}
                              {detailData.Lsc === "Exp" &&
                                detailData.Status === "U" &&
                                "Expired"}
                              {detailData.Lsc === "Dft" &&
                                detailData.Status === "U" &&
                                "Draft"}
                              {detailData.Lsc === "Sus" &&
                                detailData.Status === "U" &&
                                "Suspended"}
                              {detailData.Lsc === "Pc" &&
                                detailData.Status === "U" &&
                                "Terminated"}{" "}
                              On {detailData.propertyUpdated}
                            </b>
                          </>
                        ) : (
                          <>{getDom()}</>
                        )}
                      </p>

                      <p className="txt-right action_btn listing-detail-share-btn">
                        <span className="">
                          <i
                            onClick={() => setShowShare(!ShowShare)}
                            className="fa fa-share fontSize faviconHover"
                            aria-hidden="true"
                          >
                            {" "}
                            Share
                          </i>

                          {/*<img {...share} className="shareIcons" />
                         <span className="actionIcons">Share</span>*/}
                        </span>{" "}
                        &nbsp;&nbsp;&nbsp;
                        <span className="">
                          {/*<img {...save} className="saveIcons" />*/}
                          <i
                            className={` ${favIconImg} fontSize faviconHover`}
                            onClick={favorite}
                            aria-hidden="true"
                          >
                            {" "}
                            Save
                          </i>
                          {/*<span className="actionIcons">Save</span>*/}
                        </span>
                        <br />
                        {/* Tour_url */}
                        {detailData.Tour_url && (
                          <button
                            className="primary-btn-cls btn mt-1 virtual"
                            onClick={hideVirtualTour}
                          >
                            Virtual Tour
                          </button>
                        )}
                        <br />
                        <button
                          className="primary-btn-cls btn mt-1 nearby"
                          onClick={showModal}
                        >
                          Nearby Amenities
                        </button>
                        {ShowShare && (
                          <div className="position-relative">
                            <div
                              className="dropdown-menu show shareOptions"
                              x-placement="bottom-start"
                            >
                              <a
                                className="dropdown-item fb-share mt-1"
                                onClick={fbook}
                              >
                                <i className="fa fa-facebook share-facebook"></i>{" "}
                                Facebook
                              </a>
                              <a className="dropdown-item" onClick={twitter}>
                                <i className="fa fa-twitter share-twitter"></i>{" "}
                                Twitter
                              </a>
                              <a className="dropdown-item" onClick={pinterest}>
                                <i className="fa fa-pinterest share-pinterest"></i>{" "}
                                Pinterest
                              </a>
                              <a
                                className="dropdown-item mb-1"
                                onClick={shareEmail}
                              >
                                <i className="fa fa-envelope share-email"></i>{" "}
                                Email
                              </a>
                            </div>
                          </div>
                        )}
                      </p>
                    </div>
                    <div className="col-md-12">
                      <ul className="room-details ">
                        {getRound(detailData.Br) && (
                          <li>
                            <img
                              src="/images/icon/bed.svg"
                              className="beds mb-2"
                            />{" "}
                            <br />
                            <p>
                              {getRound(detailData.Br, true)
                                ? getRound(detailData.Br) + " Bed "
                                : ""}{" "}
                              &nbsp;
                            </p>
                          </li>
                        )}
                        {getRound(detailData.Bath_tot) && (
                          <li>
                            <img {...bathImg} className="baths mb-1" />
                            <p>
                              {getRound(detailData.Bath_tot)
                                ? getRound(detailData.Bath_tot) + "  Bath "
                                : ""}{" "}
                              &nbsp;
                            </p>
                          </li>
                        )}
                        {detailData.Sqft && (
                          <li>
                            <img
                              src="/images/icon/sqft.svg"
                              className="gar mb-2"
                            />
                            <p> {detailData.Sqft + "  Sqft  "} </p>
                          </li>
                        )}
                        {getRound(detailData.Gar_spaces) && (
                          <li className="garSpace">
                            <img {...garImg} className="garSpc" />

                            <p className="garSpaceLabel">
                              {" "}
                              {getRound(detailData.Gar_spaces)
                                ? getRound(detailData.Gar_spaces) + " Garage"
                                : ""}
                            </p>
                          </li>
                        )}
                      </ul>
                    </div>
                    <hr />
                    <div className="">
                      <div className="col-md-12 p-1 discription-one pb-0 mb-3">
                        <h3 className="heading-title">Listing History</h3>
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
                                    !props.emailIsVerified &&
                                    !props.isLogin &&
                                    (item.Status === "D" || item.Status === "U")
                                  ) {
                                    blury1 = true;
                                  }
                                  return (
                                    <tr
                                      key={key}
                                      className={` ${blury1 ? "blury1" : ""} ${
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
                                          {item.LastStatus === "New" &&
                                            item.Status === "U" && (
                                              <div className="for-sale-tag medium-tag  forExpAll">
                                                {" "}
                                                New
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
                          {props.emailIsVerified ? (
                            <></>
                          ) : (
                            <div className="joinOrSixzdgnin mt-0  mb-4 font-size15 text-transform joinAndSiasagnInBox">
                              <span className=" ">
                                <svg
                                  viewBox="0 0 24 24"
                                  width="24"
                                  height="24"
                                  className="xs-ml1"
                                  aria-hidden="true"
                                >
                                  <path d="M18 8h-1V6A5 5 0 007 6v2H6a2 2 0 00-2 2v10c0 1.1.9 2 2 2h12a2 2 0 002-2V10a2 2 0 00-2-2zM9 6a3 3 0 116 0v2H9V6zm9 14H6V10h12v10zm-6-3c1.1 0 2-.9 2-2s-.9-2-2-2-2 .9-2 2 .9 2 2 2z"></path>
                                </svg>{" "}
                                {!props.emailIsVerified && props.isLogin ? (
                                  <>
                                    <u
                                      onClick={signInToggle}
                                      className={`join-signIn-toggle theme-color`}
                                    >
                                      {" "}
                                      Verify account now
                                    </u>
                                  </>
                                ) : (
                                  <>
                                    <u
                                      onClick={joinToggle}
                                      className={`join-signIn-toggle theme-color`}
                                    >
                                      Join
                                    </u>{" "}
                                    or{" "}
                                    <u
                                      onClick={signInToggle}
                                      className={`join-signIn-toggle red`}
                                    >
                                      {" "}
                                      Sign In
                                    </u>
                                  </>
                                )}{" "}
                                • See All Photos & Sold Data - Real Estate
                                Boards Require a Verified Account (Free).
                              </span>
                            </div>
                          )}
                        </div>
                      </div>

                      <hr className="mt-2" />
                      <div className="col-md-12  p-b-0  tabs-hover-cls">
                        <Tabs
                          defaultActiveKey="key-facts"
                          id="uncontrolled-tab-example"
                          className=" "
                        >
                          <Tab
                            eventKey="key-facts"
                            title="Key Facts"
                            className="border"
                            style={{ borderTop: "0px #fff solid !important" }}
                          >
                            <div className="row discription-one p-3">
                              <div className="col-md-12 fnt-size-14 line-height-35">
                                {/* <p>Key facts for {detailData.Addr} , {detailData.Area} ({detailData.Type_own1_out})</p> */}
                                <div className="row discription-one ">
                                  <div className="col-md-3 col-6">
                                    <span className="detailsLabel">Type:</span>
                                  </div>
                                  <div className="col-md-3 col-6">
                                    <span className="detailsLvalue">
                                      {" "}
                                      {detailData.Type_own1_out}
                                    </span>
                                  </div>
                                  <div className="col-md-3 col-6">
                                    <span className="detailsLabel">
                                      Listing #MLS No.:
                                    </span>
                                  </div>
                                  <div className="col-md-3 col-6">
                                    <span className="detailsLvalue">
                                      {detailData.Ml_num}
                                    </span>
                                  </div>
                                  <div className="col-md-3 col-6">
                                    <span className="detailsLabel">Style:</span>
                                  </div>
                                  <div className="col-md-3 col-6">
                                    <span className="detailsLvalue">
                                      {detailData.Style}
                                    </span>
                                  </div>
                                  <div className="col-md-3 col-6">
                                    <span className="detailsLabel">
                                      Listing Date:
                                    </span>
                                  </div>
                                  <div className="col-md-3 col-6">
                                    <span className="detailsLvalue">
                                      {detailData.property_insert_time}
                                    </span>
                                  </div>
                                  <div className="col-md-3 col-6">
                                    <span className="detailsLabel">Taxes:</span>
                                  </div>
                                  <div className="col-md-3 col-6">
                                    <span className="detailsLvalue">
                                      {detailData.Taxes}
                                    </span>
                                  </div>
                                  <div className="col-md-3 col-6">
                                    <span className="detailsLabel">
                                      Days on Market:
                                    </span>
                                  </div>
                                  <div className="col-md-3 col-6">
                                    <span className="detailsLvalue">
                                      {getRound(detailData.Dom)
                                        ? getRound(detailData.Dom, true) + ""
                                        : "0"}
                                    </span>
                                  </div>
                                  <div className="col-md-3 col-6">
                                    <span className="detailsLabel">
                                      Building Age:
                                    </span>
                                  </div>
                                  <div className="col-md-3 col-6">
                                    <span className="detailsLvalue">
                                      {detailData.Yr_built
                                        ? detailData.Yr_built
                                        : "-"}
                                    </span>
                                  </div>
                                  <div className="col-md-3 col-6">
                                    <span className="detailsLabel">
                                      Updated on:
                                    </span>
                                  </div>
                                  <div className="col-md-3 col-6">
                                    <span className="detailsLvalue">
                                      {detailData.property_last_updated}
                                    </span>
                                  </div>
                                  <div className="col-md-3 col-6">
                                    <span className="detailsLabel">
                                      Lot Size:
                                    </span>
                                  </div>
                                  <div className="col-md-3 col-6">
                                    <span className="detailsLvalue">
                                      {detailData.Sqft + "  Sqft  "}
                                    </span>
                                  </div>
                                  <div className="col-md-3 col-6">
                                    <span className="detailsLabel">
                                      Data Source:
                                    </span>
                                  </div>
                                  <div className="col-md-3 col-6">
                                    <span className="detailsLvalue">
                                      {"TRREB"}
                                    </span>
                                  </div>
                                  <div className="col-md-3 col-6">
                                    <span className="detailsLabel">
                                      Total Parking:
                                    </span>
                                  </div>
                                  <div className="col-md-3 col-6">
                                    <span className="detailsLvalue">
                                      {getRound(detailData.Park_spcs)
                                        ? getRound(detailData.Park_spcs)
                                        : "-"}
                                    </span>
                                  </div>

                                  <div className="col-md-3 col-6">
                                    <span className="detailsLabel">
                                      Listed By:
                                    </span>
                                  </div>
                                  <div className="col-md-3 col-6 line-height-20">
                                    <span className="detailsLvalue ">
                                      {detailData.Rltr}
                                    </span>
                                  </div>

                                  {/* <div className="col-md-3">
                                    <p className="detailsLabel">
                                      Walk Score:
                                    </p>
                                  </div>
                                  <div className="col-md-3">
                                    <p className="detailsLvalue">
                                      <b> --</b>
                                    </p>
                                  </div> */}
                                </div>
                              </div>
                            </div>
                          </Tab>
                          <Tab
                            eventKey="room-details"
                            title="Rooms Details"
                            className="border"
                            style={{ borderTop: "0px #fff solid !important" }}
                          >
                            <div className="row rooms-measure ">
                              <div className="col-md-12">
                                {detailData.RoomsDescription &&
                                detailData.RoomsDescription.length > 0 ? (
                                  <>
                                    <div className="col-md-6 col-sm-6 col-lg-6"></div>
                                    <div className="col-md-1 col-sm-1 col-lg-1 "></div>
                                    <div className="col-md-5 col-sm-5 col-lg-5 text-right rooms-measure-inner ">
                                      <div className="measurement_cal switch_container">
                                        <div className="switch_container_main">
                                          <span className="imper_section ml-2 mt-0 color000">
                                            Imperial
                                          </span>
                                          <ToggleSwitch
                                            callBack={switchToggel}
                                          />
                                          <span className="metric_section mr-2 mt-0 color000">
                                            Metric
                                          </span>
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
                                  </>
                                ) : (
                                  <>
                                    <p>No details founds !</p>
                                  </>
                                )}
                              </div>
                            </div>
                          </Tab>
                        </Tabs>
                      </div>
                      <div className="col-md-12 mt-2">
                        {pageSetting &&
                        pageSetting.descriptionSection === "show" ? (
                          <div className="discription-one col-12 generalDesc">
                            <div className="detailCollapes  ">
                              <div
                                id="generalDesc"
                                className="detailCollapesTitle p-2"
                              >
                                <h3 className="heading-title">
                                  General Description{" "}
                                </h3>
                              </div>
                              {/* <p className="font-family-class m-2">{detailData.Ad_text}</p> */}
                              <div className="p-2 pt-0">
                                <p className="font-family-class ">
                                  {descriptionText}
                                </p>
                                {seeMore && (
                                  <p className="font-family-class">
                                    <b>Extras: </b> {detailData.Extras}
                                  </p>
                                )}
                                {detailData.Ad_text &&
                                  detailData.Ad_text.length > 300 && (
                                    <button
                                      className="btn m-3 mt-1 see-more-btn"
                                      onClick={showMoreText}
                                    >
                                      See {seeMore ? "Less" : "More"}
                                    </button>
                                  )}
                              </div>
                            </div>
                          </div>
                        ) : (
                          ""
                        )}
                      </div>
                      <div className="col-md-12 p-2">
                        <hr className="mt-3" />
                      </div>
                      {/* start */}
                      {/* end */}
                      <div className="row similarProperties discription-one">
                        <div className="col-md-12 mb-0">
                          <h3 className="heading px-3">
                            {detailData.S_r === "Sale" &&
                            detailData.Status === "U"
                              ? "Similar Available Listing"
                              : "Similar Listings"}
                          </h3>
                          <p className="px-3">
                            {" "}
                            {detailData.S_r === "Lease"
                              ? "Leased Homes"
                              : " Homes For Sale"}{" "}
                            Near {detailData.Addr}, {detailData.Municipality}{" "}
                          </p>
                        </div>
                        {similarData && (
                          <div className="mb-0 px-5">
                            <Listings
                              signInToggle={signInToggle}
                              {...props}
                              heading=""
                              isDetails={true}
                              isSimilar={true}
                              propertyData={
                                similarData.sale ? similarData.sale : []
                              }
                              imageData={""}
                            />
                          </div>
                        )}
                      </div>
                      {/* Comparable Properties  Section Start */}
                      <div className="row">
                        <div className="col-md-12">
                          <hr />
                          <div className="row similarPropertiess comparable-cls mt-2 discription-one ">
                            <div className="col-md-12">
                              <h3 className="heading">
                                Comparable Properties{" "}
                              </h3>
                              {detailData.S_r === "Lease" ? (
                                <p className="mr-4">
                                  Leased Homes Near {detailData.Addr},{" "}
                                  {detailData.Municipality}{" "}
                                </p>
                              ) : (
                                <p>
                                  Sold/Leased Homes Near {detailData.Addr},{" "}
                                  {detailData.Municipality}{" "}
                                </p>
                              )}
                            </div>
                            <div className="col-md-12 propTypeBtn  ">
                              {detailData.S_r === "Lease" ? (
                                <></>
                              ) : (
                                <>
                                  <Button
                                    extraProps={{
                                      size: "md",

                                      className: `gridMapView gridMapView-v3 btn  ${
                                        comparableState ? "rentBtn" : ""
                                      }`,
                                      type: "button",
                                      value: "Sold",
                                      text: "Sold Comparables",
                                      onClick: toggleComparable,
                                    }}
                                  />
                                  <Button
                                    extraProps={{
                                      size: "md",
                                      className: `gridMapView gridMapView-v3 btn  ${
                                        comparableState ? "" : "rentBtn"
                                      }`,
                                      type: "button",
                                      value: "Rent",
                                      text: "Rent Comparables",
                                      onClick: toggleComparable,
                                    }}
                                  />
                                </>
                              )}
                            </div>
                            <>
                              {comparableState ? (
                                <>
                                  {nearest && (
                                    <Listings
                                      signInToggle={signInToggle}
                                      {...props}
                                      heading=""
                                      isSold={true}
                                      isDetails={true}
                                      propertyData={
                                        nearest.sold ? nearest.sold : []
                                      }
                                      imageData={""}
                                    />
                                  )}
                                </>
                              ) : (
                                <>
                                  {nearest && (
                                    <Listings
                                      signInToggle={signInToggle}
                                      {...props}
                                      heading=""
                                      isDetails={true}
                                      propertyData={
                                        nearest.rent ? nearest.rent : []
                                      }
                                      showCard={
                                        nearest.rent && nearest.rent.length > 1
                                          ? ""
                                          : 5
                                      }
                                      imageData={""}
                                    />
                                  )}
                                </>
                              )}
                            </>
                          </div>
                        </div>
                      </div>
                      {/* Comparable Properties  Section END */}

                      {/*mortgage  Section START */}
                      {detailData.S_r !== "Lease" && (
                        <div className="row pr-2">
                          <div className="col-md-12">
                            <hr />
                          </div>
                          <div className="col-md-12 cal-section-details">
                            <div className="row border p-2  bg-color-v1">
                              <div className="col-md-8 ">
                                <div className="mortgage-cal-cls-head border-bottom">
                                  <p className="">Mortgage Calculator</p>
                                </div>
                                <div className="row p-3">
                                  <div className="col-md-12"></div>
                                  <div className="col-md-6 pt-4">
                                    <label className="label-mortgage">
                                      Home Price:
                                    </label>
                                  </div>
                                  <div className="col-md-6 p-1">
                                    <div className="form-group">
                                      <span className="p-1 bg-white placeholder-mortage">
                                        $
                                      </span>
                                      <input
                                        type="text"
                                        className="form-control p-l-15 textEnd"
                                        onFocus={homePriceFocus}
                                        onChange={homePriceChange}
                                        id="homeprice"
                                      />
                                    </div>
                                  </div>

                                  <div className="col-md-6 pt-2">
                                    <label className="label-mortgage">
                                      Term:
                                    </label>
                                  </div>
                                  <div className="col-md-6 p-1">
                                    <div className="form-group">
                                      {/* <input type="text" className="form-control" /> */}
                                      <Autocomplete
                                        inputProps={{
                                          id: "year_select",
                                          name: "year_select",
                                          className:
                                            "form-control select-pad bg-white textEnd p-r-10",
                                          placeholder: "",
                                          title: "",
                                          readOnly: "true",
                                        }}
                                        selectedText={
                                          term ? term + " Years" : "25 Years"
                                        }
                                        allList={Constant.yearData}
                                        cb={amortization}
                                        extraProps={{
                                          id: "suggestion-list",
                                        }}
                                        clicked={clicked}
                                      />
                                      <img
                                        {...downArrow}
                                        className="down-array"
                                        htmlFor="year_select"
                                        onClick={enabledDrop}
                                      />
                                      {/* End yearData Sention*/}
                                    </div>
                                  </div>

                                  <div className="col-md-6 pt-2">
                                    <label className="label-mortgage">
                                      Rate:
                                    </label>
                                  </div>
                                  <div className="col-md-6 p-1 pb-4">
                                    <div className="form-group">
                                      <input
                                        type="text"
                                        className="form-control p-r-16 textEnd"
                                        onChange={mortageRateChange}
                                        onFocus={mortageRateFocus}
                                        placeholder={defaultRate}
                                      />
                                      <span className="p-1 bg-white placeholder-mortage-rate textEnd">
                                        %
                                      </span>
                                    </div>
                                  </div>

                                  <div className="col-md-6 border-top pt-4">
                                    <label className="label-mortgage">
                                      Down Payment:
                                    </label>
                                  </div>
                                  <div className="col-md-6 p-1 border-top pt-4">
                                    <div className="form-group ">
                                      <div className="row">
                                        <div className="col-md-7">
                                          <span className="p-1 bg-white placeholder-mortage-v2">
                                            $
                                          </span>
                                          <input
                                            type="text"
                                            className="form-control p-l-15 textEnd inp-mortage-dwn-pmnt"
                                            onChange={downPaymentUsd}
                                            value={downPaymentPrice}
                                          />
                                        </div>
                                        <div className="col-md-5">
                                          <input
                                            type="text"
                                            className={`form-control ${
                                              detect.isMobile()
                                                ? "p-r-10"
                                                : "p-r-30"
                                            }  textEnd`}
                                            onKeyUp={downPayment}
                                            placeholder={defaultDwnPercent}
                                          />
                                          <span className="bg-white placeholder-mortage-percent textEnd">
                                            %
                                          </span>
                                        </div>
                                      </div>
                                    </div>
                                  </div>
                                  <div className="col-md-12 p-1  pt-4">
                                    <Slider
                                      value={sliderValue}
                                      onChange={rangeSelector}
                                      min={0}
                                      max={100}
                                      //step={step}
                                      onChangeCommitted={handleChangeCommitted}
                                      valueLabelDisplay="auto"
                                    />
                                  </div>
                                </div>
                              </div>

                              <div className="col-md-4 border-left padding-top-bottom-15">
                                <div className="mortgage-cal-cls-head text-center">
                                  <p>Mortgage Payment</p>
                                  <p>{totalMonthlyPayment}</p>
                                </div>
                              </div>
                              <div className="col-md-12">
                                <p>
                                  <b>*Disclaimer</b>: Above calculator formula
                                  is for educational purposes only, accuracy is
                                  not guaranteed.
                                </p>
                              </div>
                            </div>
                          </div>
                        </div>
                      )}
                      {/*mortgage  Section END */}

                      {/* Property Details Section Start */}
                      <div className="col-md-12">
                        {/* {pageSetting && pageSetting.propertySection === 'show' ? */}
                        <div className="discription-one col-12 propertyDetails mt-4">
                          <div className="detailCollapes" open>
                            <div
                              id="proprtyDetails"
                              className="detailCollapesTitle p-2"
                            >
                              <h3 className="heading-title">
                                Property Details{" "}
                              </h3>
                              <p className="">
                                Detailed Property Features For {detailData.Addr}{" "}
                                , {detailData.Municipality}
                              </p>
                              <hr />
                            </div>
                            <div className="property-colum row p-2 pt-0">
                              <div className="col-md-6 ">
                                <ul className="property-details">
                                  <li className="text-center">
                                    <span className="detailsLabel m-4 theme-color fnt-size-16">
                                      Property
                                    </span>
                                  </li>
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
                                  {detailData.PropertyType && (
                                    <li>
                                      <span className="detailsLabel">
                                        Type:{" "}
                                      </span>
                                      <span className="detailsLvalue">
                                        {detailData.PropertyType}
                                      </span>
                                    </li>
                                  )}
                                  {detailData.Style && (
                                    <li>
                                      <span className="detailsLabel">
                                        Style:{" "}
                                      </span>
                                      <span className="detailsLvalue">
                                        {detailData.Style}
                                      </span>
                                    </li>
                                  )}
                                  {getRound(detailData.Sqft) && (
                                    <li>
                                      <span className="detailsLabel">
                                        Size(Sq Ft):{" "}
                                      </span>
                                      <span className="detailsLvalue">
                                        {getRound(detailData.Sqft)
                                          ? getRound(detailData.Sqft) + " Sqft"
                                          : ""}
                                      </span>
                                    </li>
                                  )}
                                  {detailData.Yr_built && (
                                    <li>
                                      <span className="detailsLabel">
                                        Age:{" "}
                                      </span>
                                      <span className="detailsLvalue">
                                        {detailData.Yr_built}
                                      </span>
                                    </li>
                                  )}

                                  {detailData.Area && (
                                    <li>
                                      <span className="detailsLabel">
                                        Area:{" "}
                                      </span>
                                      <span className="detailsLvalue">
                                        {detailData.Area}
                                      </span>
                                    </li>
                                  )}

                                  {detailData.Municipality && (
                                    <li>
                                      <span className="detailsLabel">
                                        Municipality:{" "}
                                      </span>
                                      <span className="detailsLvalue">
                                        {detailData.Municipality}
                                      </span>
                                    </li>
                                  )}
                                  {detailData.Community && (
                                    <li>
                                      <span className="detailsLabel">
                                        Community:{" "}
                                      </span>
                                      <span className="detailsLvalue">
                                        {detailData.Community}
                                      </span>
                                    </li>
                                  )}
                                  <li className="text-center">
                                    <span className="detailsLabel m-4 theme-color fnt-size-16">
                                      Inside
                                    </span>
                                  </li>

                                  {getRound(detailData.Br) && (
                                    <li>
                                      <span className="detailsLabel">
                                        Bedrooms:{" "}
                                      </span>
                                      <span className="detailsLvalue">
                                        {getRound(detailData.Br)}
                                      </span>
                                    </li>
                                  )}
                                  {getRound(detailData.Bath_tot) && (
                                    <li>
                                      <span className="detailsLabel">
                                        Bathrooms:{" "}
                                      </span>
                                      <span className="detailsLvalue">
                                        {getRound(detailData.Bath_tot)}
                                      </span>
                                    </li>
                                  )}

                                  {insideDetails.Kitchen !== 0 && (
                                    <li>
                                      <span className="detailsLabel">
                                        {" "}
                                        Kitchens:{" "}
                                      </span>
                                      <span className="detailsLvalue">
                                        {insideDetails.Kitchen}
                                      </span>
                                    </li>
                                  )}
                                  {getRound(detailData.Rms) && (
                                    <li>
                                      <span className="detailsLabel">
                                        {" "}
                                        Rooms:{" "}
                                      </span>
                                      <span className="detailsLvalue">
                                        {} {getRound(detailData.Rms)}
                                      </span>
                                    </li>
                                  )}

                                  <li>
                                    <span className="detailsLabel">
                                      Family Room/Den:{" "}
                                    </span>
                                    <span className="detailsLvalue">
                                      {insideDetails.Dining +
                                        insideDetails.Living}
                                    </span>
                                  </li>

                                  {detailData.Fpl_num && (
                                    <li>
                                      <span className="detailsLabel">
                                        Fireplace:{" "}
                                      </span>
                                      <span className="detailsLvalue">
                                        {detailData.Fpl_num}
                                      </span>
                                    </li>
                                  )}
                                  {/* Unit Exposure: */}
                                  {detailData.Fpl_num && (
                                    <li>
                                      <span className="detailsLabel">
                                        Unit Exposure:{" "}
                                      </span>
                                      <span className="detailsLvalue">
                                        {/* {detailData.Fpl_num} */}
                                        {"--"}
                                      </span>
                                    </li>
                                  )}
                                  {/* Patio Terrace:  */}
                                  {detailData.Fpl_num && (
                                    <li>
                                      <span className="detailsLabel">
                                        Patio Terrace:{" "}
                                      </span>
                                      <span className="detailsLvalue">
                                        {/* {detailData.Fpl_num} */}
                                        {"--"}
                                      </span>
                                    </li>
                                  )}

                                  {detailData.Bsmt1_out && (
                                    <li>
                                      <span className="detailsLabel">
                                        Basement:
                                      </span>
                                      <span className="detailsLvalue">
                                        {detailData.Bsmt1_out}
                                      </span>
                                    </li>
                                  )}
                                  {
                                    <li className="text-center">
                                      <span className="detailsLabel m-4 theme-color fnt-size-16">
                                        Land
                                      </span>
                                    </li>
                                  }
                                  {detailData.Condo_corp && (
                                    <li>
                                      <span className="detailsLabel">
                                        Condo Corporation:{" "}
                                      </span>
                                      <span className="detailsLvalue">
                                        {detailData.Condo_corp}
                                      </span>
                                    </li>
                                  )}
                                  {detailData.Corp_num && (
                                    <li>
                                      <span className="detailsLabel">
                                        Corporation Number:{" "}
                                      </span>
                                      <span className="detailsLvalue">
                                        {detailData.Corp_num}
                                      </span>
                                    </li>
                                  )}
                                  {detailData.Comp_pts && (
                                    <li>
                                      <span className="detailsLabel">
                                        Fronting On:{" "}
                                      </span>
                                      <span className="detailsLvalue">
                                        {detailData.Comp_pts}
                                      </span>
                                    </li>
                                  )}
                                  {detailData.Front_ft && (
                                    <li>
                                      <span className="detailsLabel">
                                        Frontage:{" "}
                                      </span>
                                      <span className="detailsLvalue">
                                        {detailData.Front_ft}
                                      </span>
                                    </li>
                                  )}
                                  {getRound(detailData.Depth) && (
                                    <li>
                                      <span className="detailsLabel">
                                        Depth:{" "}
                                      </span>
                                      <span className="detailsLvalue">
                                        {getRound(detailData.Depth)}
                                      </span>
                                    </li>
                                  )}
                                  {detailData.Lotsz_code && (
                                    <li>
                                      <span className="detailsLabel">
                                        Lot Size Code:{" "}
                                      </span>
                                      <span className="detailsLvalue">
                                        {detailData.Lotsz_code}
                                      </span>
                                    </li>
                                  )}
                                  {detailData.Sewer && (
                                    <li>
                                      <span className="detailsLabel">
                                        Sewer:{" "}
                                      </span>
                                      <span className="detailsLvalue">
                                        {detailData.Sewer}
                                      </span>
                                    </li>
                                  )}
                                  {detailData.Cross_st && (
                                    <li>
                                      <span className="detailsLabel">
                                        Cross Street:{" "}
                                      </span>
                                      <span className="detailsLvalue">
                                        {detailData.Cross_st}
                                      </span>
                                    </li>
                                  )}
                                </ul>
                              </div>
                              <div className="col-md-6">
                                <ul className="property-details">
                                  {detailData.PropertyType === "Condo" ? (
                                    <li className="text-center">
                                      <span className="detailsLabel m-4 theme-color fnt-size-16">
                                        Fees & Utilities:
                                      </span>
                                    </li>
                                  ) : (
                                    <li className="text-center">
                                      {" "}
                                      <span className="detailsLabel m-4 theme-color fnt-size-16 ">
                                        Building & Utilities
                                      </span>
                                    </li>
                                  )}
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

                                  {detailData.Constr1_out && (
                                    <li>
                                      <span className="detailsLabel">
                                        Construction:
                                      </span>
                                      <span className="detailsLvalue">
                                        {" "}
                                        {detailData.Constr1_out}
                                      </span>
                                    </li>
                                  )}
                                  {detailData.Constr1_out && (
                                    <li>
                                      <span className="detailsLabel">
                                        Exterior:
                                      </span>
                                      <span className="detailsLvalue">
                                        {detailData.Constr1_out},{" "}
                                        {detailData.Constr2_out}
                                      </span>
                                    </li>
                                  )}

                                  {detailData.Heating && (
                                    <li>
                                      <span className="detailsLabel">
                                        Heating Type:{" "}
                                      </span>
                                      <span className="detailsLvalue">
                                        {detailData.Heating}
                                      </span>
                                    </li>
                                  )}

                                  {detailData.Fuel && (
                                    <li>
                                      <span className="detailsLabel">
                                        Heating Fuel:{" "}
                                      </span>
                                      <span className="detailsLvalue">
                                        {detailData.Fuel}
                                      </span>
                                    </li>
                                  )}

                                  {detailData.A_c && (
                                    <li>
                                      <span className="detailsLabel">
                                        Cooling:{" "}
                                      </span>
                                      <span className="detailsLvalue">
                                        {detailData.A_c}
                                      </span>
                                    </li>
                                  )}
                                  {detailData.Water && (
                                    <li>
                                      <span className="detailsLabel">
                                        Water Supply:{" "}
                                      </span>
                                      <span className="detailsLvalue">
                                        {detailData.Water}
                                      </span>
                                    </li>
                                  )}
                                  {detailData.Bldg_amen1_out && (
                                    <li>
                                      <span className="detailsLabel">
                                        Amenities:
                                      </span>
                                      <span className="detailsLvalue">
                                        {detailData.Bldg_amen1_out}
                                      </span>
                                    </li>
                                  )}

                                  {detailData.Pets && (
                                    <li>
                                      <span className="detailsLabel">
                                        Pets:
                                      </span>
                                      <span className="detailsLvalue">
                                        {detailData.Pets}
                                      </span>
                                    </li>
                                  )}
                                  <li className="text-center">
                                    <span className="detailsLabel m-4 theme-color fnt-size-16">
                                      Parking
                                    </span>
                                  </li>

                                  {detailData.Bsmt1_out && (
                                    <li>
                                      <span className="detailsLabel">
                                        Driveway:
                                      </span>
                                      <span className="detailsLvalue">
                                        {"-----"}
                                        {/* {detailData.Bsmt1_out} */}
                                      </span>
                                    </li>
                                  )}

                                  {detailData.Gar_type && (
                                    <li>
                                      <span className="detailsLabel">
                                        Garage Type:{" "}
                                      </span>
                                      <span className="detailsLvalue">
                                        {detailData.Gar_type}
                                      </span>
                                    </li>
                                  )}
                                  {detailData.Park_fac && (
                                    <li>
                                      <span className="detailsLabel">
                                        Driveway Parking:{" "}
                                      </span>
                                      <span className="detailsLvalue">
                                        {detailData.Park_fac}
                                      </span>
                                    </li>
                                  )}
                                  {detailData.Gar_type && (
                                    <li>
                                      <span className="detailsLabel">
                                        Garage Parking:
                                      </span>
                                      <span className="detailsLvalue">
                                        {detailData.Gar_type}
                                      </span>
                                    </li>
                                  )}
                                  {detailData.Tot_park_spcs && (
                                    <li>
                                      <span className="detailsLabel">
                                        Total Parking:{" "}
                                      </span>
                                      <span className="detailsLvalue">
                                        {getRound(detailData.Tot_park_spcs)}
                                      </span>
                                    </li>
                                  )}
                                  <li className="text-center">
                                    <span className="detailsLabel m-4 theme-color fnt-size-16">
                                      Fees & Utilities
                                    </span>
                                  </li>

                                  {detailData.Maint && (
                                    <li>
                                      <span className="detailsLabel">
                                        Maintenance:
                                      </span>
                                      <span className="detailsLvalue">
                                        {detailData.Maint}
                                      </span>
                                    </li>
                                  )}

                                  {detailData.Taxes && (
                                    <li>
                                      <span className="detailsLabel">
                                        Taxes:
                                      </span>
                                      <span className="detailsLvalue">
                                        {"  "}
                                        {formatter.format(
                                          parseInt(detailData.Taxes)
                                        )}
                                      </span>
                                    </li>
                                  )}
                                  {detailData.Yr && (
                                    <li>
                                      <span className="detailsLabel">
                                        Tax Year:{" "}
                                      </span>
                                      <span className="detailsLvalue">
                                        {getRound(detailData.Yr)}
                                      </span>
                                    </li>
                                  )}

                                  {detailData.Cond_txinc && (
                                    <li>
                                      <span className="detailsLabel">
                                        Taxes Included:{" "}
                                      </span>
                                      <span className="detailsLvalue">
                                        {detailData.Cond_txinc}
                                      </span>
                                    </li>
                                  )}

                                  {detailData.Insur_bldg && (
                                    <li>
                                      <span className="detailsLabel">
                                        Building Insurance Included:{" "}
                                      </span>
                                      <span className="detailsLvalue">
                                        {detailData.Insur_bldg}
                                      </span>
                                    </li>
                                  )}

                                  {detailData.Comel_inc && (
                                    <li>
                                      <span className="detailsLabel">
                                        Common Elements Included:{" "}
                                      </span>
                                      <span className="detailsLvalue">
                                        {detailData.Comel_inc}
                                      </span>
                                    </li>
                                  )}

                                  {detailData.Cable && (
                                    <li>
                                      <span className="detailsLabel">
                                        Cable Included:{" "}
                                      </span>
                                      <span className="detailsLvalue">
                                        {detailData.Cable}
                                      </span>
                                    </li>
                                  )}

                                  {detailData.Heat_inc && (
                                    <li>
                                      <span className="detailsLabel">
                                        Heating Included:{" "}
                                      </span>
                                      <span className="detailsLvalue">
                                        {detailData.Heat_inc}
                                      </span>
                                    </li>
                                  )}

                                  {detailData.Cac_inc && (
                                    <li>
                                      <span className="detailsLabel">
                                        Central A/C Included:{" "}
                                      </span>
                                      <span className="detailsLvalue">
                                        {detailData.Cac_inc}
                                      </span>
                                    </li>
                                  )}

                                  {detailData.Hydro_inc && (
                                    <li>
                                      <span className="detailsLabel">
                                        Hydro Included:{" "}
                                      </span>
                                      <span className="detailsLvalue">
                                        {detailData.Hydro_inc}
                                      </span>
                                    </li>
                                  )}

                                  {detailData.Water_inc && (
                                    <li>
                                      <span className="detailsLabel">
                                        Water Included:{" "}
                                      </span>
                                      <span className="detailsLvalue">
                                        {detailData.Water_inc}
                                      </span>
                                    </li>
                                  )}
                                  {detailData.Prkg_inc && (
                                    <li>
                                      <span className="detailsLabel">
                                        Parking Included:{" "}
                                      </span>
                                      <span className="detailsLvalue">
                                        {detailData.Prkg_inc}
                                      </span>
                                    </li>
                                  )}
                                </ul>
                              </div>
                            </div>
                          </div>
                        </div>
                        {/* : ''} */}
                      </div>
                      {/* Property details Section end */}
                      <hr />
                    </div>
                  </div>
                  <div className="col-md-12 p-2">
                    <h3 className="heading-title">
                      Schools & Neighborhood Amenities
                    </h3>
                    <div id="neighbourhood-highlights" className="mt-4"></div>
                  </div>
                  <hr />
                  <div className="row">
                    <div>
                      <div className="discription-one roomsDetails  p-2">
                        <div id="walkScoreSection">
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
                              className="walkScore"
                            ></iframe>
                          )}
                          <div id="overLap" className="py-2"></div>
                        </div>
                      </div>
                    </div>
                  </div>
                  {/*  */}
                </div>
                <div className="col-md-4 col-lg-4 col-sm-4 col-xs-4">
                  <div className="sidebar-section" id="sidebar">
                    <div className="form-placeholers-color">
                      <div className="row mobile_row">
                        {!watchListLAerts && (
                          <>
                            <div
                              className="col-md-6 p-3 pt-0 pb-1 watch-listings custom-p2"
                              onClick={watchListingsSold}
                            >
                              <div className="row border-radius pb-1 listing-content watch-alerts-bg-color watch-alert-hover h-100">
                                <div
                                  className={`col-md-12 text-center watch-list-heading-section ${
                                    watchedListingsForSold &&
                                    watchedListingsForSold.isSold
                                      ? " watched-listing "
                                      : "  "
                                  } `}
                                >
                                  <img
                                    {...Notifications}
                                    className="watch-notification-cls  mb-0"
                                  />
                                  <span className="watch-list-heading mt-5">
                                    {watchedListingsForSold &&
                                    watchedListingsForSold.isSold ? (
                                      <b> Listing Watched</b>
                                    ) : (
                                      <b>Watch Listing </b>
                                    )}
                                  </span>
                                </div>
                                <div className="col-md-12 mt-2">
                                  <p>
                                    Watch this listing and get notified when
                                    it's sold
                                  </p>
                                </div>
                              </div>
                            </div>

                            <div
                              className="col-md-6  p-3 pt-0 pb-1 watch-listings custom-p3 "
                              onClick={setListingsAlert}
                            >
                              <div className="row border-radius pb-1 listing-content watch-alerts-bg-color h-100 watch-alert-hover">
                                {/* <div className="col-md-12 text-center "> */}
                                <div
                                  className={`col-md-12 text-center watch-list-heading-section ${
                                    watchedListingsForComm &&
                                    watchedListingsForComm.AlertsOn
                                      ? "  watched-listing "
                                      : "   "
                                  } `}
                                >
                                  <img
                                    {...Notifications}
                                    className="watch-notification-cls  mb-0"
                                  />
                                  <span className="watch-list-heading">
                                    <b>Watch Community</b>
                                  </span>
                                </div>
                                <div className="col-md-12 mt-2">
                                  <p>
                                    Receive updates for{" "}
                                    {detailData.Type_own1_out} homes in{" "}
                                    {detailData.Municipality}
                                    {" - "}
                                    {detailData.Community}
                                  </p>
                                </div>
                              </div>
                            </div>
                          </>
                        )}
                        {watchListLAerts && (
                          <div className="col-md-12  mb-2">
                            <div className="row custom-padding-3">
                              <div className="col-md-12 watch-listing-alert-section listing-content">
                                <div className="">
                                  <h5>Watch Community</h5>
                                  <div className="form-group">
                                    <Form.Check
                                      className="form-control"
                                      type={"checkbox"}
                                      id={`NewListings`}
                                      name="NewListings"
                                      // checked={watchList.watchListings &&watchList.watchListings.AlertsOn && watchList.watchListings.AlertsOn.NewListings ? true : false}
                                      onClick={handleInputChanges}
                                      label={`New Listings`}
                                    />
                                  </div>
                                  <div className="form-group">
                                    <Form.Check
                                      className="form-control"
                                      type={"checkbox"}
                                      id={`SoldListings`}
                                      name="SoldListings"
                                      // checked={watchList.watchListings &&watchList.watchListings.AlertsOn && watchList.watchListings.AlertsOn.SoldListings ? true : false}
                                      onClick={handleInputChanges}
                                      label={`Sold Listings`}
                                    />
                                  </div>
                                  <div className="form-group">
                                    <Form.Check
                                      className="form-control"
                                      type={"checkbox"}
                                      name="DelistedListings"
                                      id={`DelistedListings`}
                                      onClick={handleInputChanges}
                                      label={`Delisted Listings`}
                                    />
                                  </div>
                                </div>
                                <div className="col-md-12">
                                  <button
                                    className="primary-btn-cls btn mt-1 text-transform w-100"
                                    onClick={saveListingsAlert}
                                  >
                                    Save
                                  </button>
                                </div>
                              </div>
                            </div>
                          </div>
                        )}
                        <div className="col-md-12">
                          {detailData.Status === "U" ? (
                            <></>
                          ) : (
                            <Schedule props={props} bookAShowing={true} />
                          )}
                          <ScheduleQuery {...detailData} {...props} />
                        </div>
                      </div>
                    </div>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </>
      )}
      <div className=""></div>

      <div className="">
        <Modal
          show={modalShow}
          onHide={() => shareEmail(false)}
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
            <div className="popup_propform" id="modalEmailForm">
              <div className="row" id="2">
                <div className="col-md-6 col-sm-6 col-lg-6 form-group ">
                  <label className="">Your Name*</label>
                  <input
                    type="text"
                    onChange={handleChanges}
                    onBlur={handleChanges}
                    className=" form-control senderName input-box"
                    placeholder="Name*"
                    name="sender_name"
                    required=""
                  />
                  <span className={`validateError  ${name ? "" : "hide"}`}>
                    Name is required.
                  </span>
                </div>
                <div className="form-group col-md-6 col-sm-6 col-lg-6">
                  <label className="">Your Email*</label>
                  <input
                    type="email"
                    onChange={handleChanges}
                    onBlur={handleChanges}
                    className="  form-control senderEmail input-box"
                    placeholder="Email*"
                    name="sender_email"
                    required=""
                  />
                  <span className={`validateError  ${email ? "" : "hide"}`}>
                    Email is required.
                  </span>
                </div>
                <div className="form-group col-md-12 col-sm-12 col-lg-12 mt-3">
                  <label className="">Your Friends Emails*</label>
                  <input
                    type="email"
                    onChange={handleChanges}
                    onBlur={handleChanges}
                    className=" form-control recipentEmail input-box"
                    placeholder="Emails*"
                    name="email"
                    required=""
                  />
                  <span className={`validateError  ${emails ? "" : "hide"}`}>
                    Emails are required.
                  </span>
                  <span
                    className={`validateError  ${
                      duplicateEmails ? "" : "hide"
                    }`}
                  >
                    Remove duplicate emails
                  </span>
                </div>

                <div className="form-group col-lg-12 mt-3">
                  <label className="">Message*</label>
                  <textarea
                    onChange={handleChanges}
                    onBlur={handleChanges}
                    className="form-control input-box"
                    name="message"
                    style={{ height: "66px" }}
                    placeholder={`I would like to get more info about ${details.Addr}`}
                  ></textarea>
                  <span className={`validateError  ${message ? "" : "hide"}`}>
                    Message is required.
                  </span>
                </div>
                <div className="col-md-4"></div>
                <div className="col-md-4">
                  <div className="shareEmail">
                    {!loaderState ? (
                      <>
                        <button
                          id="shareEmailBtn"
                          disabled={showBtn || message}
                          type="btn"
                          className="btn showSchedule btn-sm mt-4"
                          onClick={sendDetails}
                        >
                          {" "}
                          <i className="fa fa-spinner fa-spin"> </i>Submit
                        </button>
                      </>
                    ) : (
                      <>
                        <button
                          id="shareEmailBtn"
                          disabled={true}
                          type="btn"
                          className="btn showSchedule btn-sm mt-4"
                          onClick={sendDetails}
                        >
                          {" "}
                          <i className="fa fa-spinner fa-spin"> </i>
                          Submiting......
                        </button>
                      </>
                    )}
                  </div>
                </div>
                <div className="col-md-4"></div>
              </div>
            </div>
          </Modal.Body>
        </Modal>
      </div>

      <div className="">
        <Modal
          show={virtualTour}
          onHide={() => hideVirtualTour(false)}
          className="emailShareModel"
          size="lg"
          aria-labelledby="contained-modal-title-vcenter"
        >
          <Modal.Header closeButton>
            <Modal.Title id="contained-modal-title-vcenter ">
              Virtual Tour
            </Modal.Title>
          </Modal.Header>
          <Modal.Body>
            <div className="popup_propform" id="modalEmailForm">
              <div className="row">
                <div className="col-md-12">
                  <iframe
                    src={detailData.Tour_url}
                    title=""
                    style={{ height: "500px", width: "100%" }}
                  ></iframe>
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
