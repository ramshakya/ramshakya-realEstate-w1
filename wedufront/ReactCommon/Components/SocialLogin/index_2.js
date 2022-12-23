import React, { useEffect, useState } from "react";
 const FacebookButton = () => {
  const handleClick = () => {
    FB.login(function (response) {
      if (response.authResponse) {
        //  console.log('Welcome!  Fetching your information.... ');
        FB.api('/me', function (response) {
          console.log('Good to see you, ', response);
        });
      } else {
        //  console.log('User cancelled login or did not fully authorize.');
      }
    });
  }
  useEffect(() => {
    window.fbAsyncInit = function () {
      window.FB.init({
        appId: '943838539830151',
        xfbml: true,
        version: 'v14.0'
      });
      window.FB.AppEvents.logPageView();
    };

    (function (d, s, id) {
      var js, fjs = d.getElementsByTagName(s)[0];
      if (d.getElementById(id)) { return; }
      js = d.createElement(s); js.id = id;
      js.src = "https://connect.facebook.net/en_US/sdk.js";
      fjs.parentNode.insertBefore(js, fjs);
    }(document, 'script', 'facebook-jssdk'));

  }, [])

  return (
    <button onClick={handleClick}>Login</button>)
}
export default FacebookButton;