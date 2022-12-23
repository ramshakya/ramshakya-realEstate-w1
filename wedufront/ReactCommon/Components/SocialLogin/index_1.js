
import React, { Component } from 'react';
import FacebookLogin from 'react-facebook-login';
import GoogleLogin from 'react-google-login';
import { requestToAPI } from "../../../pages/api/api";
import { agentId, googleClientId, fbAppId } from "../../../constants/GlobalConstants";
import { ToastContainer, toast } from 'react-toastify';
import Loader from '../../../components/loader/loader';
class SocialLogin extends Component {
  constructor(props) {
    super(props);
    this.state = {
      loaderState: false,
      Facbook: {},
      google: {},
      googleClientId: "",
      fbAppId: "",
      authUrl: "",
    };
    // https://accounts.google.com/o/oauth2/auth?response_type=code&access_type=offline&client_id=&redirect_uri=http%3A%2F%2F127.0.0.1%3A8000%2Fauth_success&state&scope=email%20profile&approval_prompt=auto
    this.handleSubmit = this.handleSubmit.bind(this);
    this.responseGoogleFail = this.responseGoogleFail.bind(this);
    this.responseFacebook = this.responseFacebook.bind(this);
    this.responseGoogle = this.responseGoogle.bind(this);
    this.responseFacebookFail = this.responseFacebookFail.bind(this);
    this.googleLoginInt = this.googleLoginInt.bind(this);

  }
  async handleSubmit(obj) {
    let body = JSON.stringify(obj);
    if (!obj.socialId) {
      this.setState({
        loaderState: false,
      });
      return false;
    }
    let res = await requestToAPI(body, "loginSocial", "POST");
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
    this.setState({
      loaderState: false,
    });
  }
  responseFacebook(response) {
    console.log("responseFacebook", response);
    this.setState({
      loaderState: true,
    });
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
      this.handleSubmit(FacbookObj);
    }
    this.setState({
      loaderState: false,
    });
    this.setState({
      Facbook: {
        ...FacbookObj
      }
    })
  }
  responseGoogle(response) {
    console.log("responseGoogle", response);
    this.setState({
      loaderState: true,
    });
    let familyName = response.profileObj ? response.profileObj.familyName : "";
    let givenName = response.profileObj ? response.profileObj.givenName : "";
    let name = response.profileObj ? response.profileObj.name : "";
    let google = {
      Email: response.profileObj ? response.profileObj.email : "",
      Firstname: familyName ? familyName : givenName,
      Lastname: name,
      Phone: "",
      isSocialLogin: true,
      AgentId: agentId,
      socialId: response.profileObj ? response.profileObj.googleId : "",
      imageUrl: response.profileObj ? response.profileObj.imageUrl : "",
    }
    if (response.profileObj.googleId) {
      this.handleSubmit(google);
    }
    this.setState({
      loaderState: false,
    });
  }
  responseGoogleFail(e) {
    console.log("responseGoogleFail", e);
    this.setState({
      loaderState: false,
    });
  }
  responseFacebookFail(e) {
    console.log("responseFacebookFail", e);
    this.setState({
      loaderState: false,
    });
  }

  componentDidMount() {
    try {
      let websetting = localStorage.getItem("websetting");
      if (websetting !== null && websetting !== "undefined" && websetting !== undefined) {
        websetting = JSON.parse(websetting);
        this.setState({
          googleClientId: websetting.GoogleClientId,
          fbAppId: websetting.FbAppId
        })
      }
    } catch (error) {
      this.setState({
        googleClientId: "",
        fbAppId: ""
      })
    }
  }
  googleLoginInt() {
    let url="https://accounts.google.com/o/oauth2/auth?response_type=code&access_type=offline&client_id=672088791066-battt8l4pa8bmosdk48u5368b45cvmkk.apps.googleusercontent.com&redirect_uri=http://localhost:3000&state&scope=email%20profile&approval_prompt=auto";
    // let url="https://accounts.google.com/o/oauth2/auth?response_type=code&access_type=offline&client_id="+this.state.googleClientId+"&redirect_uri=http://localhost:3000&state&scope=email%20profile&approval_prompt=auto";
    window.open(url, '_self')
  }
  
  render() {

    return (
      <>
        {
          this.state.loaderState &&
          <Loader />
        }
        {
          this.state.fbAppId &&
          <FacebookLogin
            btnContent="LOGIN With Facebook"
             appId={943838539830151}
            //appId={this.state.fbAppId ? this.state.fbAppId : fbAppId}
            fields="name,email,picture"
            onSuccess={this.responseFacebook}
            onFailure={this.responseFacebookFail}

          />
        }
        {/* <p className="orSize  m-2">Or</p>
        {
          this.state.googleClientId && <>
            <GoogleLogin
              className="google-login"
              clientId={this.state.googleClientId ? this.state.googleClientId : googleClientId}
              buttonText="Continue with Google"
              onSuccess={this.responseGoogle}
              onFailure={this.responseGoogleFail}
              cookiePolicy={"single_host_origin"}

            />
            <button onClick={this.googleLoginInt}>Google Login Test</button>
          </>
        } */}
      </>
    );
  }
}
export default SocialLogin;
