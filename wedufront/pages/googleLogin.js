
import React, { useEffect, useState } from "react";
import { googleClientId } from "../constants/GlobalConstants";
import jwt_decode from "jwt-decode";
const GoogleLogin = (props) => {
    const [checkLoginToken, setLoginToken] = useState(false);
    useEffect(() => {

    }, [checkLoginToken]);


    useEffect(() => {
        let id = 'googleScript';
        if (document.getElementById(id) === null) {
            const script = document.createElement("script");
            script.setAttribute('src', 'https://accounts.google.com/gsi/client')
            script.setAttribute('id', id);
            script.setAttribute('async', 'defer');
            document.body.appendChild(script)
            script.onload = () => {
                if (document.getElementById(id)) {
                    console.log('googleLogin loaded');
                    try {
                        google.accounts.id.initialize({
                            client_id:"672088791066-battt8l4pa8bmosdk48u5368b45cvmkk.apps.googleusercontent.com",
                            callback:handleCallback
                        });
                        google.accounts.id.renderButton(
                            document.getElementById('signInBtn'),
                            {
                                theme:"outline",size:"large"
                            }
                        )
                        google.accounts.id.prompt();
                    } catch (e) {
                        console.log('error ', e);
                    }
                }
            }
        }
    }, []);
    function handleCallback(response){
        console.log("Google login",response.credential);
       var user= jwt_decode(response.credential);
        console.log("Google login user",user);

    }
 
    return (
        <>
         <div id="signInBtn"></div>
        </>
    );
}
export default GoogleLogin;
 