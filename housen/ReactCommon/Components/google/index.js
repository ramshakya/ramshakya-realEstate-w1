"use strict";
import React, { useEffect, useState } from "react";
import jwt_decode from "jwt-decode";
import Loader from '../../../components/loader/loader';
import { requestToAPI } from "../../../pages/api/api";
import { agentId, googleClientId, fbAppId } from "../../../constants/Global";
import { ToastContainer, toast } from 'react-toastify';
const GoogleLogins = (props) => {
    const [loaderState, setLoaderState] = useState(false);
    const websetting = props.websetting;
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
                    initGoogleLogin();
                }
            }
        }
        if (document.getElementById(id)) {
            initGoogleLogin();
        }
    }, []);

    function initGoogleLogin() {
        console.log('googleLogin loaded');
        try {
            
            google.accounts.id.initialize({
                // client_id: websetting.GoogleClientId ? websetting.GoogleClientId : "672088791066-battt8l4pa8bmosdk48u5368b45cvmkk.apps.googleusercontent.com",
                client_id: websetting.GoogleClientId ,
                callback: handleCallback
            });
            google.accounts.id.renderButton(
                document.getElementById('signInBtn'),
                {
                    theme: "outline",
                    size: "medium",//large
                    width:"950px",
            
                }
            );
            google.accounts.id.prompt();
        } catch (e) {
            console.log('error ', e);
        }
    }
    const handleCallback = async (response) => {
        try {
            var user = jwt_decode(response.credential);
            console.log("Google login user", user);
            setLoaderState(true);
            let givenName = user.given_name ? user.given_name : "";
            let familyName = user.family_name ? user.family_name : "";
            let name = user.name;
            let google = {
                Email: user.email ? user.email : "",
                Firstname: givenName ? givenName : name,
                Lastname: familyName ? familyName : "",
                // Phone: "",
                Phone: '(000) 000 - 000',
                isSocialLogin: true,
                AgentId: agentId,
                socialId: user ? user.nbf : "",
                imageUrl: user ? user.picture : "",
            }
            console.log("Google login object", google);
            let body = JSON.stringify(google);
            let res = await requestToAPI(body, "api/v1/services/loginSocial", "POST");
            let json = res;
            if (res.token) {
                toast.success("Signup Successfully");
                localStorage.setItem('login_token', json.token);
                localStorage.setItem('userDetail', JSON.stringify(json.user_detail));
                localStorage.setItem('estimatedTokenTime', JSON.stringify(json.estimated_token_time))
                document.getElementById('closeBtn').click();
                setLoaderState(false);
            }
            else {
                toast.error("Something went wrong try later!");
                setLoaderState(false);
            }
        } catch (e) {
            console.log("Google login error", e);
            setLoaderState(false);
        }
    }
    return (
        <>
            {
                loaderState &&
                <Loader />
            }
            <div id="signInBtn"></div>
        </>
    );
}
export default GoogleLogins;