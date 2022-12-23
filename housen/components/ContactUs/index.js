import React, { Component } from 'react';
import 'react-toastify/dist/ReactToastify.css';
import { ToastContainer, toast } from 'react-toastify';
import ContactForm from "./../../constants/Forms/ContactUs";
import utilityGlobal from "../../ReactCommon/utility/utilityGlobal";
import Reaptcha from 'reaptcha';
import Autocomplete from "../../ReactCommon/Components/AutoSuggestion";
import API from './../../ReactCommon/utility/api'
import { contactUsApi, agentInfo, googleTestKey,agentId } from "./../../constants/Global";
import Loader from './../loader/loader';
class ContactUs extends Component {
    constructor(props) {
        console.log("contact page", props);
        super(props);
        this.handleChange = this.handleChange.bind(this);
        this.submitForm = this.submitForm.bind(this);
        this.handleQuery = this.handleQuery.bind(this);
        this.handleTimeline = this.handleTimeline.bind(this);
        this.handleComments = this.handleComments.bind(this);
        this.onVerify = this.onVerify.bind(this);
        this.validateFeilds = this.validateFeilds.bind(this);
        this.props.setMetaInfo({
          title:"conatct us",
          slug:'Contact us',
          metaDesc:'housen.ca contact us',
          MetaTags:'contact us'
        });

        const queryData = [
            { text: "Buying Real Estate (Send Me Listings)", value: "Buying Real Estate (Send Me Listings)" },
            { text: "Buy & Sell Consultation (Learn Our Perks)", value: "Selling Real Estate (Home Evaluation)" },
            { text: "I Need Help with a Mortgage", value: "I Need Help with a Mortgage" },
            { text: "Other", value: "Other" },
        ];
        const timeLineData = [
            { text: "Within 3 Months", value: "Within 3 Months" },
            { text: "3 to 6 Months", value: "3 to 6 Months" },
            { text: "6 to 12 Months", value: "6 to 12 Months" },
            { text: "12+ Months", value: "12+ Months" },
        ];

        this.state = {
            queryData: queryData,
            timeLineData: timeLineData,
            formConfig: ContactForm.contactform,
            validateField: ContactForm.validateFields.contactform,
            showBtn: false,
            captcha: false,
            captchaToken: "",
            timeLine: "",
            queryValue: "",
            comments: "",
            timeLineErr: false,
            commentsErr: false,
            queryErr: false,
            dataFlag: false,
            googleKey: ""

        };
    }
    componentDidMount() {
        let webSetting = localStorage.getItem('websetting');
        if (webSetting) {
            try {
                webSetting=JSON.parse(webSetting);
                console.log("webSetting", webSetting);
                this.setState({
                    googleKey: webSetting.GoogleMapApiKey
                })
            } catch (error) {

            }
        }
    }
    onVerify(e) {
        setTimeout(() => {
            this.setState({
                captcha: true,
            });
            this.validateFeilds();
        }, 100);
    }
    handleQuery = (e) => {
        setTimeout(() => {
            this.setState({
                queryValue: e.value,
            });
            this.setState({
                queryErr: e.value ? false : true
            })
            this.validateFeilds();
        }, 100);
    }
    handleTimeline(e) {
        setTimeout(() => {
            this.setState({
                timeLine: e.value,
            });
            this.setState({
                timeLineErr: e.value ? false : true
            })
            this.validateFeilds();
        }, 100);
    }
    handleComments(e) {
        this.setState({ comments: e.target.value });
        this.setState({
            commentsErr: e.target.value ? false : true
        })
        setTimeout(() => {
            this.validateFeilds();
        }, 100);
    }
    handleChange(e) {
        let data = {};

        let inpval = e.target.value;
        if (e.target.name === "user_phone") {
            const { value, maxLength } = e.target;
            var x = e.target.value.replace(/\D/g, '').match(/(\d{0,3})(\d{0,3})(\d{0,4})/);
			inpval = !x[2] ? x[1] : '(' + x[1] + ') ' + x[2] + (x[3] ? '-' + x[3] : '');
        }
        data[e.target.name] = inpval;
        e.target.value = inpval

        this.setState(data, () => {
            this.setState({
                showBtn: utilityGlobal.validateData(this.state.validateField, this).status,
            });
        });
        this.validateFeilds();
    }

    validateFeilds() {
        let flag = false;
        const { validateField, captcha, captchaToken, timeLine, queryValue, comments } = this.state;
        for (let i = 0; i < validateField.length; i++) {
            if (this.state[validateField[i]]) {
                flag = true
            } else {
                flag = false
            }
        }
        if (!flag) {
            setTimeout(() => {
                this.setState({ showBtn: false });
            }, 100);
            return;
        }
        if (captcha && timeLine && queryValue && comments) {
            flag = true;
        } else {
            flag = false
        }
        if (flag) {
            setTimeout(() => {
                this.setState({ showBtn: true });
            }, 100);
        } else {
            setTimeout(() => {
                this.setState({ showBtn: false });
            }, 100);
        }
    }
    submitForm(e) {
        e.preventDefault();
        this.setState({
            dataFlag: true
        });
        setTimeout(() => {
            this.setState({
                dataFlag: false
            });
        }, 2000);
        const { validateField, captcha, captchaToken, timeLine, queryValue, comments } = this.state;
        
        setTimeout(() => {
            this.setState({ showBtn: false });
        }, 100);
        let validateFlage = false;
        const data = {};
        for (let i = 0; i < validateField.length; i++) {
            if (this.state[validateField[i]]) {
                validateFlage = true
            }
            data[validateField[i]] = this.state[validateField[i]]
        }
        this.setState({
            timeLineErr: timeLine === "" ? false : true
        })
        this.setState({
            queryErr: queryValue === "" ? false : true
        })
        if (!captcha) {
            toast.error("Please Verify Captcha !");
        }
        data.user_id = 1;
        data.agentId=agentId;
        data.comments = comments;
        data.queryValue = queryValue;
        data.timeLine = timeLine;
        data.page_from = window.location.href;
        try {
            API.jsonApiCall(contactUsApi, data, "POST", null, {
                "Content-Type": "application/json",
            })
                .then((res) => {
                    // console.log("ressss", res);
                    if (res.status == 200) {
                        toast.success(res.message);
                        document.getElementById("myForm").reset();
                         this.setState({
                            user_name: "",
                            user_email: "",
                            user_phone: "",
                            comments: "",
                            url: "",
                            timeLine: "",
                            queryValue: "",
                            user_id: "",
                            user_location: "",
                            dataFlag: false,
                            showBtn:false,
                            queryErr:false,
                            timeLineErr:false
                        });
                        // setTimeout(() => {
                        //     window.location.href = "/ContactUs";
                        // }, 2000);
                    }
                })
                .catch((e) => {
                    this.setState({
                        dataFlag: false
                    });
                    console.log("error", e);
                    toast.error("Something went wrong try later!");
                });
        } catch (e) {
            this.setState({
                dataFlag: false
            });
        }
    }
    
    render() {
        return (
            <>
                {this.state.dataFlag &&
                    <Loader />
                }
                <section className="contact-wrapper mt-5">
                    <div className="row">
                        <div className="col-sm-12">

                        </div>
                    </div>
                    <div className="container">
                        <div className="row " >
                            <div className="col-sm-12">
                                <h1 className="contactus">
                                    Contact Us
                                <hr className="hr" />
                                </h1>
                            </div>
                            <div className="col-sm-12 img-wrapper img-responsive ">
                                {/* <img {...AboutUs} className="img-responsive contact-us-img" /> */}
                            </div>
                        </div>
                        <div className="row office-section" >
                            <div className="col-sm-6">
                                
                                <h4>Customer Relations</h4>
                                <p className="office-content"><a href="mailto:Info@housen.ca" className="text-secondary">Info@housen.ca</a></p>
                                <p className="office-content"><a href="tel:+1 (647) 500-7777" className="text-secondary">+1 (647) 500-7777</a> </p>
                                 <div className="agentinfo">
                                    
                                    <h3 className="mb-0">{agentInfo.name}</h3>
                                    <small>({agentInfo.title})</small>
                                    <p className="pt-2 mb-1">Phone : <a href={'tel:'+agentInfo.mobileNumber} className="text-secondary">{agentInfo.mobileNumber}</a></p>
                                    <small>{agentInfo.type}</small>
                                    <p className="mb-0">{agentInfo.officeName}</p>
                                    
                                    <p className="pt-1 mb-1">Phone : <a href={'tel:'+agentInfo.brokerageNumber} className="text-secondary">{agentInfo.brokerageNumber}</a></p>

                                    <p className="pt-1">{agentInfo.OfficeAddress}{agentInfo.city},{agentInfo.state}</p>
                                </div>
                                
                            </div>
                            <div className="col-sm-6">
                                {/* <SimpleMap /> */}
                                <iframe
                                    loading="lazy"
                                    width="600"
                                    height="450"
                                    className="iframMobile"
                                    allowfullscreen
                                    referrerpolicy="no-referrer-when-downgrade"
                                    src={`https://www.google.com/maps?key=${this.state.googleKey?this.state.googleKey:googleTestKey}&q=25-81 Zenway Blvd. Woodbridge, ON. L4H 0S5, Canada&output=embed`}>
                                </iframe>
                                <br />
                            </div>
                        </div>
                            <hr/>
                        

                    </div>
                    <div className="contactSection aboutSection">
                        <div className="col-sm-12">
                            
                            <h3 className="contactus">
                                Let’s Talk About It
                            <hr className="hr" />
                            </h3>
                            <br />
                            <br />
                        </div>
                        <div className="col-sm-12 aboutForm position-relative">

                            <form id="myForm" className="letAboutForm" onSubmit={this.submitForm}>
                                {utilityGlobal.renderConfig(this.state.formConfig, this.state, this)}
                                <div className="autoSuggestionCls">
                                    <div className="">
                                        <Autocomplete
                                            inputProps={{
                                                id: "query",
                                                name: "text_search",
                                                className: "auto form-control auto-suggestion-inp inp ",
                                                placeholder: "Select Your Query",
                                                title: "Select Your Query",
                                                readOnly: true,
                                            }}
                                            allList={this.state.queryData}
                                            cb={this.handleQuery}
                                            selectedText={this.state.queryValue?this.state.queryValue:''}
                                            extraProps={{}}
                                        />
                                    </div>

                                    {this.state.queryErr && <span className="err-inp-msg" >Field is required</span>}
                                </div>
                                <div className="autoSuggestionCls mt-4">
                                    <div className="">
                                        <Autocomplete
                                            inputProps={{
                                                id: "query",
                                                name: "text_search",
                                                className: "auto form-control auto-suggestion-inp inp ",
                                                placeholder: "Select Your Timeline",
                                                title: "Select Your Timeline",
                                                readOnly: true,
                                            }}
                                            allList={this.state.timeLineData}
                                            cb={this.handleTimeline}
                                            selectedText={this.state.timeLine?this.state.timeLine:''}
                                            extraProps={{}}
                                        />
                                    </div>
                                    {this.state.timeLineErr && <span className="err-inp-msg" >Field is required</span>}
                                </div>
                                <textarea className="mt-4 form-control comments" value={this.state.comments} onChange={this.handleComments} placeholder="Comments or Questions" cols={72} rows={5} id="comments-section" />
                                {this.state.commentsErr && <span className="err-inp-msg customErr" >Field is required</span>}
                                <div className="col-sm-2 submitBtns mt-4 googleCaptcha">
                                    <Reaptcha sitekey="6LeIxAcTAAAAAJcZVRqyHh71UMIEGNQ_MXjiZKhI" onVerify={this.onVerify} /><br />
                                    <button className="common-btn  nav-button btn  " disabled={this.state.showBtn ? false : true} >Submit</button>
                                </div>
                                <div className="col-sm-10">

                                </div>

                            </form>
                        </div>
                    </div>

                    <ToastContainer />

                </section>
            </>
        );
    };
}
export default ContactUs;






