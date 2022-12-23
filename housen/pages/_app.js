import React, { useState, useEffect } from "react";
import dynamic from "next/dynamic";
// import Layout from "../components/Layout/Layout";
import { useRouter } from "next/router";
import "../styles/global.css";
import { ToastContainer, toast } from "react-toastify";
import detect from "../ReactCommon/utility/detect";
// import MetaDecorator from "../pages/MetaDecorator";
const MetaDecorator = dynamic(() => import("../pages/MetaDecorator"), {
  loading: () => <>Loading.......</>,
  ssr: true,
});
const Layout = dynamic(() => import("../components/Layout/Layout"), {
  loading: () => <>Loading.......</>,
  ssr: true,
});
function MyApp({ Component, pageProps }) {
  const router = useRouter();
  const [pageLoading, setPageLoading] = React.useState(false);
  const [isOpen, setIsOpen] = useState(false);
  const [isLogin, setLogin] = useState(false);
  const [emailIsVerified, setEmailIsVerified] = useState(false);

  const [isLoginRequired, setIsLoginRequired] = useState(false);
  const [pageTtile, setPageTitle] = useState("");
  const [webSettingDetail, setWebSettingDetail] = useState({});
  const [metaInfo, setMetaInfoState] = useState({});
  const [userDetails, setUserdata] = useState("");
  const [webSettingCheck, setWebsettingCheck] = useState(false);
  const [popularSearchCheck, setpopularSearchCheck] = useState(false);
  const [checkCity, setCheckCity] = useState("");
  const [metaThumbnail, setMetaThumbnail] = useState(
    "/images/search_background.jpg"
  );
  const [showFooterBtn, setShowFooterBtn] = useState(false);
  let content = {
    pageTitle: "Housen - Home",
    pageDescription:
      "Housen Is Your Best Choice for Real Estate Search in Canada. Find Homes for Sale, New Developments, Rental Homes, Real Estate Agents, and Property Insights.",
    metaImageAlt: "Homes for Sale and Real Estate Get Listings",
    keyword: "Homes for sale, real estate get listings",
  };
  const toggleLoginPopup = () => {
    if (isLoginRequired && !isLogin) {
      if (localStorage.getItem("login_token")) {
        setIsOpen(false);
        return;
      }
      if (window.location.href.includes("propertydetails")) {
        setIsOpen(true);
        return;
      }
    }
    setIsOpen(!isOpen);
  };
  React.useEffect(() => {
    const handleStart = () => {
      setPageLoading(true);
    };
    const handleComplete = () => {
      setPageLoading(false);
    };

    if (window.location.href.includes("propertydetails") && detect.isMobile()) {
      setShowFooterBtn(true);
    } else {
      setShowFooterBtn(false);
    }
    router.events.on("routeChangeStart", handleStart);
    router.events.on("routeChangeComplete", handleComplete);
    router.events.on("routeChangeError", handleComplete);
  }, [router]);

  React.useEffect(() => {
    let userData = localStorage.getItem("userDetail");
    let websetting = localStorage.getItem("websetting");
    let estimatedTimeToken =
      Number(localStorage.getItem("estimatedTokenTime")) * 1000;
    if (estimatedTimeToken && new Date(estimatedTimeToken) < new Date()) {
      localStorage.removeItem("login_token");
      localStorage.removeItem("userDetail");
      localStorage.removeItem("estimatedTokenTime");
      try {
        toast.error("Logging you out");
      } catch (error) {}
      window.location.href = "/";
    }
    if (
      websetting !== null &&
      websetting !== "undefined" &&
      websetting !== undefined
    ) {
      if (!webSettingDetail) {
        setWebSettingDetail(JSON.parse(websetting));
      }
    }
    userData =
      userData && userData !== "undefined"
        ? JSON.parse(localStorage.getItem("userDetail"))
        : null;
    if (
      userData !== null &&
      userData !== "undefined" &&
      userData !== undefined
    ) {
      if (!userDetails) {
        setUserdata(userData);
      }
      if (userData.EmailIsVerified) {
        setEmailIsVerified(true);
      }
    }
    if (userData !== null && !isLogin) {
      setLogin(true);
    } else {
      if (window.location.href.includes("propertydetails")) {
        let limit = 3;
        if (localStorage.getItem("detailPageSetting")) {
          let setting = JSON.parse(localStorage.getItem("detailPageSetting"));
          console.log("count", setting.pagevisitsSection);
          limit = setting.pagevisitsSection ? setting.pagevisitsSection : 3;
        }
        let urls = [];
        if (localStorage.getItem("propertyView")) {
          let countInfo = localStorage.getItem("propertyView");
          if (
            countInfo !== null &&
            countInfo !== "undefined" &&
            countInfo !== undefined
          ) {
            countInfo = JSON.parse(countInfo);
            urls = countInfo.urls;
            // console.log("",countInfo.count);
            if (countInfo.count > limit) {
              setIsLoginRequired(true);

              return true;
            }
            if (!urls.includes(window.location.href)) {
              urls.push(window.location.href);
              let obj = {
                count: ++countInfo.count,
                urls: urls,
              };
              localStorage.setItem("propertyView", JSON.stringify(obj));
              setIsLoginRequired(false);
            }
          }
        } else {
          urls.push(window.location.href);
          let obj = {
            count: 1,
            urls: urls,
          };
          localStorage.setItem("propertyView", JSON.stringify(obj));
        }
      }
    }
  });

  React.useEffect(() => {
    const color = getComputedStyle(document.documentElement).getPropertyValue(
      "--primary"
    );
    document.documentElement.style.setProperty("--primary", "#ff5f64");

    // console.log(`--color-logo: ${color}`);
  }, []);
  function getMetaInfo(e) {
    setMetaInfoState(e);
  }
  function getPageName(pageNameValue) {
    setPageTitle(pageNameValue);
  }
  function setWebSettings(str) {
    setWebsettingCheck(str);
  }
  function popularSearch(str) {
    setpopularSearchCheck(!popularSearchCheck);
    // console.log("popularSearch",str);
  }
  function checkCityChange(str) {
    setCheckCity(str);
  }
  return (
    <>
      {/* <MetaDecorator
        description={content.pageDescription}
        title={content.pageTitle}
        imageUrl={metaThumbnail}
        imageAlt={content.metaImageAlt}
      /> */}
      <Layout
        title={pageTtile}
        togglePopUp={toggleLoginPopup}
        cb={setWebSettings}
        isLoginRequired={isLoginRequired}
        isOpen={isOpen}
        metaInfo={metaInfo}
        popularSearch={popularSearch}
        popularSearchCheck={popularSearchCheck}
        checkCity={checkCity}
        checkCityChange={checkCityChange}
        showFooterBtn={showFooterBtn}
      >
        <Component
          {...pageProps}
          Routers={router}
          togglePopUp={toggleLoginPopup}
          pageName={getPageName}
          isLogin={isLogin}
          emailIsVerified={emailIsVerified}
          setMetaInfo={getMetaInfo}
          webSetting={webSettingDetail}
          userDetails={userDetails}
          isLoginRequired={isLoginRequired}
          webSettingCheck={webSettingCheck}
          popularSearch={popularSearch}
          popularSearchCheck={popularSearchCheck}
          checkCity={checkCity}
          checkCityChange={checkCityChange}
        />
      </Layout>
    </>
  );
}
export default MyApp;
