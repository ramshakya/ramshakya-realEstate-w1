import React, { useState } from 'react';
import "../styles/globals.css";
import "../styles/css/bootstrap.min.css";
import "../styles/css/default.css";
import "../styles/css/responsive.css";
import "./../styles/css/ReactCommon/Card.css";
import { ToastContainer, toast } from 'react-toastify';
import Layout from "../components/Layout/Layout";
import ShimmerEffect from '../ReactCommon/Components/ShimmerEffect';
import { useRouter } from 'next/router';
import detect from "./../ReactCommon/utility/detect";
import SSRProvider from "react-bootstrap/SSRProvider";

function MyApp({ Component, pageProps }) {
    const router = useRouter();
    const [pageLoading, setPageLoading] = React.useState(false);
    const [isOpen, setIsOpen] = useState(false);
    const [isLogin, setLogin] = useState(false);
    const [isLoginRequired, setIsLoginRequired] = useState(false);
    const [detailsPageLimit, setDetailsPageLimit] = useState(0);

    const [metaInfo, setMetaInfoState] = useState({});
    const [userDetails, setUserdata] = useState("");
    const [pageTtile, setPageTitle] = useState("");
    const [webSettingDetail, setWebSettingDetail] = useState(false);
    const [webSettingCheck, setWebsettingCheck] = useState(false);
    const toggleLoginPopup = () => {
        if (isLoginRequired && !isLogin) {
            if (localStorage.getItem('login_token')) {
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
          // setTimeout(() => {window.scrollTo({ top: 0, behavior: 'smooth' })}, 1000);
        const handleStart = () => {

            setPageLoading(true);
          
        };
        const handleComplete = () => {
            setPageLoading(false);
        };
        router.events.on("routeChangeStart", handleStart);
        router.events.on("routeChangeComplete", handleComplete);
        router.events.on("routeChangeError", handleComplete);
    }, [router]);

    React.useEffect(() => {
        let userData = localStorage.getItem("userDetail");
        let websetting = localStorage.getItem("websetting");
        let estimatedTimeToken = Number(localStorage.getItem("estimatedTokenTime")) * 1000;
        if (estimatedTimeToken && new Date(estimatedTimeToken) < new Date()) {
            localStorage.removeItem('login_token');
            localStorage.removeItem('userDetail');
            localStorage.removeItem('estimatedTokenTime');
            toast.error("Logging you out");
            window.location.href = "/";
        }
        if (websetting !== null && websetting !== "undefined" && websetting !== undefined) {
            if (!webSettingDetail) {
                setWebSettingDetail(JSON.parse(websetting));
            }
        }
        userData = userData && userData !== "undefined" ? JSON.parse(localStorage.getItem("userDetail")) : null;
        if (userData !== null && userData !== "undefined" && userData !== undefined) {
            if (!userDetails) {
                setUserdata(userData);
            }
        }
        if (userData !== null && !isLogin) {
            setLogin(true)
        } else {
            if (window.location.href.includes("propertydetails")) {
                let limit = 3;
                if (localStorage.getItem('detailPageSetting')) {
                    let setting = JSON.parse(localStorage.getItem('detailPageSetting'));
                    limit = setting.pagevisitsSection ? setting.pagevisitsSection : 3;
                }
                let urls = [];
                let propertyView = localStorage.getItem("propertyView");
                if (propertyView) {
                    let countInfo = propertyView;
                    if (countInfo !== null && countInfo !== undefined) {
                        countInfo = JSON.parse(countInfo);
                        urls = countInfo.urls;
                        if (countInfo.count > limit) {
                            setIsLoginRequired(true);
                            // setDetailsPageLimit(limit);
                            return false;
                        }
                        if (!urls.includes(window.location.href)) {
                            urls.push(window.location.href);
                            let obj = {
                                "count": ++countInfo.count,
                                "urls": urls,
                            }
                            localStorage.setItem("propertyView", JSON.stringify(obj))
                        }
                        // setIsLoginRequired(false);
                    }
                }
                else {
                    urls.push(window.location.href);
                    let obj = {
                        "count": 1,
                        "urls": urls,
                    }
                    localStorage.setItem("propertyView", JSON.stringify(obj))
                }
            }
            // else {   // Ram
            //     if (isOpen) {
            //         toggleLoginPopup();
            //     }
            // }
        }
        if (detect.isMobile()) {
            if (window.location.href.includes("code")) {
                if (!userData) {
                    if (!isOpen) {
                        setIsOpen(true);
                        toggleLoginPopup();
                    }
                }
            }
        }
    })
    React.useEffect(() => {
        const color = getComputedStyle(document.documentElement).getPropertyValue('--primary');
    }, [])
    function getMetaInfo(e) {
        setMetaInfoState(e);
        setPageTitle(e.page);
    }

    function getPageName(pageNameValue) {
        setPageTitle(pageNameValue);
    }
    function setWebSettings(str) {
        setWebsettingCheck(str);
    };
    return (
        <><SSRProvider>
            <Layout title={pageTtile} pageLoading={pageLoading} cb={setWebSettings} togglePopUp={toggleLoginPopup} {...pageProps} isLoginRequired={isLoginRequired} isOpen={isOpen} metaInfo={metaInfo} >
                {pageLoading && pageTtile !== "advance search" && pageTtile !== "map" ? (
                    <ShimmerEffect count={3} />
                ) : (<>
                    <Component
                        {...pageProps} togglePopUp={toggleLoginPopup} pageName={getPageName} isLogin={isLogin}
                        setMetaInfo={getMetaInfo}
                        webSetting={webSettingDetail}
                        userDetails={userDetails}
                        isLoginRequired={isLoginRequired}
                        detailsPageLimit={detailsPageLimit}
                        webSettingCheck={webSettingCheck}
                        pageLoading={pageLoading}
                        Routers={router}
                    />
                </>

                )}
            </Layout>
        </SSRProvider>
        </>
    );
}
export default MyApp;