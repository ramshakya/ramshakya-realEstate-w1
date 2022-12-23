import React, { useEffect, useState } from "react";
const Facebook = (props) => {
  const [fbdData, setFbdData] = useState({});
  useEffect(() => {
    window.fbAsyncInit = function () {
      // FB JavaScript SDK configuration and setup
      FB.init({
        appId: '943838539830151', // FB App ID
        cookie: true,  // enable cookies to allow the server to access the session
        xfbml: true,  // parse social plugins on this page
        version: 'v3.2' // use graph api version 2.8
      });

      // Check whether the user already logged in
      FB.getLoginStatus(function (response) {
        console.log("fb login 1",response);
        if (response.status === 'connected') {
          //display user data
          getFbUserData();
        }
      });
    };
  }, []);
  return (
    <>
      <div className="custom-padding text-center mb-5">
        <h4>First do login to access profile page</h4>
      </div>
    </>
  );
}
export default Facebook;