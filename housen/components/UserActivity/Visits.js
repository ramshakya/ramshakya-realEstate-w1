import { useEffect, useState } from "react";
import Axios from "axios";
import Constants from "../../constants/Global";
import { useRouter } from "next/router";
import { requestToAPI } from "../../pages/api/api";
function Visits(props) {
  let InterTime = new Date().toLocaleString();
  const router = useRouter();
  let lastRecId = 0;
  const [city, citySet] = useState("");
  const [interval, setIntervalVal] = useState("");
  let prevPageUrl = "";
  useEffect(() => {
    checkVerify();
    if (interval) {
      clearInterval(interval);
    }
  }, [router]);

  useEffect(() => {
    startTrack();
  }, []);
  function startTrack() {
    let setInter = setInterval(() => {
      trackUser();
    }, 10000);
    setIntervalVal(setInter);
  }

  function checkVerify() {
    if (localStorage.getItem("userDetail")) {
      let userDetail = localStorage.getItem("userDetail");
      try {
        userDetail = JSON.parse(userDetail);
        if (!userDetail.EmailIsVerified) {
          try {
            // setModalShow(true);
            let email = userDetail.login_email;
            let body = JSON.stringify({
              email: email,
            });
            requestToAPI(body, "api/v1/services/checkuser", "POST", {})
              .then((json) => {
                if (json.confirmcode) {
                  try {
                    userDetail.EmailIsVerified = true;
                    localStorage.setItem(
                      "userDetail",
                      JSON.stringify(userDetail)
                    );
                  } catch (e) {}
                }
                if (json.error) {
                  console.log("catch error 1", json);
                }
              })
              .catch((e) => {
                console.log("catch error 2", json);
              });
          } catch (e) {
            console.log("catch error 3", e);
          }
        }
      } catch (e) {
        console.log("error", e);
      }
    }
  }
  function trackUser() {
    let IpAddress = "";
    if (localStorage.getItem("ip")) {
      saveData(localStorage.getItem("ip"));
    } else {
      Axios.get("https://ipapi.co/json/").then((res) => {
        IpAddress = res.data.ip;
        localStorage.setItem("ip", IpAddress);
        saveData(IpAddress);
        // let response = requestToAPI(body,"frontend/userActivity/","POST");
      });
    }
  }
  function saveData(IpAddress) {
    try {
      if (!IpAddress) {
        return;
      }
      let InTime = InterTime;
      let StayTime = new Date().toLocaleString();
      let PageUrl = window.location.href;

      const queryString = window.location.search;
      const urlParams = new URLSearchParams(queryString);
      let redirect = false;
      if (urlParams.has("code")) {
        redirect = true;
      }
      if (urlParams.has("state")) {
        redirect = true;
      }
      if (redirect) {
        router.push("/");
      }

      if (localStorage.getItem("PageUrl")) {
        if (localStorage.getItem("PageUrl") !== PageUrl) {
          localStorage.setItem("InTime", InTime);
          localStorage.setItem("PageUrl", PageUrl);
          InTime = StayTime;
        } else {
          InTime = localStorage.getItem("InTime");
        }
      } else {
        localStorage.setItem("PageUrl", PageUrl);
      }
      let body = "";
      let advanceSearch = {};
      let slug = "";
      if (PageUrl.includes("map") || PageUrl.includes("city")) {
        advanceSearch = localStorage.getItem("advanceSearch");
        if (
          advanceSearch &&
          advanceSearch !== null &&
          advanceSearch !== "" &&
          advanceSearch !== undefined
        ) {
          advanceSearch = JSON.parse(advanceSearch);
          var result = Object.entries(advanceSearch);
          let flag = 0;
          let answer = result.map((value, key) => {
            // if (value[0] === 'text_search') {
            //   if (value[1] !== "" && value[1] !== '') {
            //   }
            // }
            // if(value[0] === 'propertySubType'){
            // }
            if (value[0] === "City") {
              if (value[1] !== "" && value[1] !== "") {
                if (localStorage.getItem("city_search") !== value[1]) {
                  InTime = StayTime;
                  citySet(value[1]);
                  localStorage.setItem("city_search", value[1]);
                  flag++;
                }
              }
            }
          });
          if (!flag) {
            // return;
          }
        }
      } else {
        localStorage.removeItem("advanceSearch");
        advanceSearch = {};
      }
      // return;
      if (PageUrl.includes("propertydetails")) {
        slug = PageUrl.replace(
          window.location.origin + "/propertydetails/",
          ""
        );
        advanceSearch.slug = slug;
      }
      if (localStorage.getItem("userDetail")) {
        let localStorageData = JSON.parse(localStorage.getItem("userDetail"));
        let UserId = localStorageData.login_user_id;
        body = JSON.stringify({
          IpAddress: IpAddress,
          StayTime: StayTime,
          InTime: InTime,
          PageUrl: PageUrl,
          AgentId: Constants.agentId,
          UserId: UserId,
          FilteredData: advanceSearch ? advanceSearch : "",
          prevId: lastRecId,
        });
      } else {
        body = JSON.stringify({
          IpAddress: IpAddress,
          StayTime: StayTime,
          InTime: InTime,
          PageUrl: PageUrl,
          AgentId: Constants.agentId,
          FilteredData: advanceSearch ? advanceSearch : "",
          prevId: lastRecId,
        });
      }
      const requestOptions = {
        method: "POST",
        headers: { "Content-Type": "application/json" },
        body: body,
      };
      let urls = Constants.base_url + "api/v1/services/userActivity";
      fetch(urls, requestOptions)
        .then((response) => response.text())
        .then((res) => JSON.parse(res))
        .then((json) => {
          if (json.data) {
            lastRecId = json.data.id;
          }
        })
        .catch((err) => console.log({ err }));
    } catch (e) {
      console.log("error", e);
    }
  }

  return <></>;
}
export default Visits;
