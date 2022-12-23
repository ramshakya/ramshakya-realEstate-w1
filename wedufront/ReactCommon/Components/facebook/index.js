import React, { useEffect, useState } from "react";
import { agentId, fbAppId } from "../../../constants/GlobalConstants";
import Loader from '../../../components/loader/loader';
import { requestToAPI } from "../../../pages/api/api";
import { ToastContainer, toast } from 'react-toastify';
const Facebook = (props) => {
  console.log("facebook props", props.FbAppId);
  const [fbdData, setFbdData] = useState({});
  const [status, setStatus] = useState(false);
  const [profile, setProfile] = useState({});
  const [loaderState, setLoaderState] = useState(false);
  useEffect(() => {

    let id = 'fbLogin';
    if (document.getElementById(id) === null) {
      const script = document.createElement("script");
      script.setAttribute('src', 'https://connect.facebook.net/en_US/sdk.js')
      script.setAttribute('id', id);
      script.setAttribute('async', true);
      document.body.appendChild(script)
      script.onload = () => {
        if (document.getElementById(id)) {
          console.log('FacebookLogin loaded');
          window.fbAsyncInit = function () {
            FB.init({
              appId: 943838539830151,
              cookie: true,                     // Enable cookies to allow the server to access the session.
              xfbml: true,                     // Parse social plugins on this webpage.
              version: 'v2.2'           // Use this Graph API version for this call.
            });
          }
          // FB.init({
          //   appId: 943838539830151,//props.FbAppId ? props.FbAppId : '943838539830151',
          //   xfbml: true,
          //   version: 'v2.2'
          // });
        }
      }
    }

  }, []);
  function statusChangeCallback(response) {
    console.log('statusChangeCallback');
    console.log(response);
    if (response.status === 'connected') {
      testAPI();
      setStatus(true);
      console.log('connected !', response);
    } else if (response.status === 'not_authorized') {
      FB.login(function (response) {
        statusChangeCallback2(response);
      }, { scope: 'public_profile,email' });
    } else {
      console.log("not connected, not logged into facebook, we don't know1");
      FB.login(function (response) {
        statusChangeCallback2(response);
      }, { scope: 'public_profile,email' });
    }
  }

  function statusChangeCallback2(response) {
    console.log('statusChangeCallback2');
    console.log(response);
    if (response.status === 'connected') {
      testAPI();

    } else if (response.status === 'not_authorized') {
      console.log('still not authorized!');

    } else {
      console.log("not connected, not logged into facebook, we don't know 2");
    }
  }

  function checkLoginState() {
    FB.getLoginStatus(function (response) {
      statusChangeCallback(response);
    });
  }

  const saveFacebookData = async (obj = {}) => {
    setLoaderState(true);
    obj = profile;
    console.log("payload obj ", obj);
    return;
    try {
      let body = JSON.stringify(obj);
      if (!obj.socialId || !obj.Firstname) {
        setLoaderState(false);
        return false;
      }
      let res = await requestToAPI(body, "loginSocial", "POST");
      let json = res;
      console.log("Facebook save data response from server", json.token);
      if (res.token) {
        setLoaderState(false);
        localStorage.setItem('login_token', json.token);
        localStorage.setItem('userDetail', JSON.stringify(json.user_detail));
        localStorage.setItem('estimatedTokenTime', JSON.stringify(json.estimated_token_time));
        localStorage.setItem("userLoggedIn", true);
        toast.success("Signup Successfully");
        document.getElementById('closeBtn').click();
      }
      else {
        toast.error("Something went wrong try again!");
      }
      setLoaderState(false);
    } catch (e) {
      toast.error("Something went wrong try later!");
      console.log("Facebook error ", e);
      setLoaderState(false);
    }
  }
  function testAPI() {
    console.log('Welcome!  Fetching your information.... ');
    FB.api('/me', function (response) {
      console.log('Successful login: ', response);
      console.log('Thanks for logging in, ', response.name + '!');
      let obj = {
        Firstname: response.name,
        Lastname: "",
        Email: response.email?response.email:'wedu'+ response.userID ? response.userID : response.id+"@wedu.com",
        Phone: "",
        isSocialLogin: true,
        socialId: response.userID ? response.userID : response.id,
        AgentId: agentId,
        imageUrl: ""
      }
      setProfile(obj);
      if (!status) {
        saveFacebookData;
      }
    });
  }
  return (
    <>
      {
        loaderState &&
        <Loader />
      }
      <div className="custom-padding text-center mb-2">
        {
          status ? <>
            <button onClick={saveFacebookData} className="loginBtn loginBtn--facebook text-center">Continue as {profile.Firstname}{' '} {profile.Lastname} </button>
          </> : <>
            <button onClick={checkLoginState} className="loginBtn loginBtn--facebook text-center"> Facebook </button>
          </>
        }
      </div>
    </>
  );
}
export default Facebook;