"use strict";
import React, { useEffect, useState } from "react";
import { agentId } from "../../../constants/GlobalConstants";
import { ToastContainer, toast } from 'react-toastify';
import FacebookLogin from 'react-facebook-login';
import Loader from '../../../components/loader/loader';
import { requestToAPI } from "../../../pages/api/api";
const Facebook = (props) => {


  useEffect(() => {
    try {
      window.FB.logout();
    } catch (e) {
    }
  }, []);
  const [loaderState, setLoaderState] = useState(false);
  const websetting = props.websetting;
  function handleSubmit(obj) {
    try {
      if (!obj.socialId) {
        setLoaderState(false);
        return false;
      }
      let body = JSON.stringify(obj);
      requestToAPI(body, "loginSocial", "POST").then((res) => {
        let json = res;
        if (res.token) {
          toast.success("Signup Successfully");
          localStorage.setItem('login_token', json.token);
          localStorage.setItem('userDetail', JSON.stringify(json.user_detail));
          localStorage.setItem('estimatedTokenTime', JSON.stringify(json.estimated_token_time))
          document.getElementById('closeBtn').click();
        }
        else {
          toast.error("Something went wrong try later!");
        }
        setLoaderState(false);
      });
    } catch (e) {
      // console.log("facebook error", e);
      setLoaderState(false);
    }
  }
  function testbtn() {
    setLoaderState(true);
    setTimeout(() => {
      setLoaderState(false);
    }, 500);
  }
  function facebookCallBack(response) {
    // console.log("facebookCallBack==>>>", response);
    setLoaderState(true);
    let FacbookObj = {
      Firstname: response.name,
      Lastname: "",
      Email: response.email,
      Phone: "",
      isSocialLogin: true,
      socialId: response.id,
      AgentId: agentId,
      imageUrl: response.picture ? response.picture.data.url : ""
    }
    if (response.id) {
      handleSubmit(FacbookObj);
    }
    // setLoaderState(false);
  }
  function responseFacebook(response) {
    // console.log("responseFacebook", response);
  }

  function responseFacebookFail(e) {
    //console.log("responseFacebookFail", e);
    try {
      setLoaderState(false);
    } catch (e) {
    }
  }
  return (
    <>
      {
        loaderState &&
        <Loader />
      }
      <p className="orSize  m-2">Or</p>
      <button onClick={testbtn} hidden>test</button>
      {
        props.FbAppId &&
        <FacebookLogin
          appId={props.FbAppId ? props.FbAppId : 319600363492618}
          //  appId={943838539830151}
          autoLoad={false}
          reAuthenticate={true}
          cookie={false}
          fields="name,email,picture"
          scope="public_profile,user_friends"
          callback={facebookCallBack}
          // onSuccess={responseFacebook}
          onFailure={responseFacebookFail}
          icon="fa-facebook" />
      }
    </>
  );
}
export default Facebook;