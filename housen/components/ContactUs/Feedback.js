import React, { Component } from 'react';
import { Container, Row, Col } from "react-bootstrap";
import 'react-toastify/dist/ReactToastify.css';
import { ToastContainer, toast } from 'react-toastify';
import AboutUs from "./../../public/images/about_us.jpg"
import SimpleMap from "./../Map/SimpleMap";
import ContactForm from "./../../Constants/forms/ContactUs.js";
import utilityGlobal from "../../ReactCommon/utility/utilityGlobal";
import Reaptcha from 'reaptcha';
import Autocomplete from "../../ReactCommon/Components/AutoSuggestion";
import API from './../../ReactCommon/utility/api'
import { contactUsApi } from './../../Constants';
import Link from "next/link";
import Image from "next/image";
import { Router } from 'next/router';

class Feedback extends Component {
    constructor(props) {
        super(props);
        this.renderOfficeCard = this.renderOfficeCard.bind(this);
        this.handleChange = this.handleChange.bind(this);
        this.submitForm = this.submitForm.bind(this);
        this.handleQuery = this.handleQuery.bind(this);
        this.handleTimeline = this.handleTimeline.bind(this);
        this.handleComments = this.handleComments.bind(this);
        this.onVerify = this.onVerify.bind(this);
        this.validateFeilds = this.validateFeilds.bind(this);

        

        this.state = {
             
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
        };
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
        console.log("handleQuery", e.value);

        setTimeout(() => {
            this.setState({
                queryValue: e.value,
            });
            console.log(" handleQuery =>", this.state);
            this.setState({
                queryErr: e.value ? false : true
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
        let inpval=e.target.value;
        if (e.target.name === "phone") {
            const { value, maxLength } = e.target;
            var x = e.target.value.replace(/\D/g, '').match(/(\d{0,3})(\d{0,3})(\d{0,4})/);
            if (String(value).length >= maxLength) {
                e.preventDefault();
                return;
            }
            if (String(value).length === 10) {
                let formated= '(' + x[1] + ') ' + x[2] + ' ' + x[3];
                inpval =formated;
            }
        }
        data[e.target.name] = inpval;
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
        console.log("captcha", captcha);
        console.log("timeLine", timeLine);
        console.log("query", queryValue);
        console.log("comments", comments);
        // console.log("submitForm", this.state);
        for (let i = 0; i < validateField.length; i++) {
            // console.log("submitForm====>>>", this.state[validateField[i]]);
            if (this.state[validateField[i]]) {
                flag = true
            } else {
                flag = false
            }
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
        console.log("this.state", this.state.showBtn);
    }
    submitForm(e) {
        let validateFlage = false;
        const { validateField, captcha, captchaToken, timeLine, queryValue, comments } = this.state;
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
        data.comments = comments;
        data.queryValue = queryValue;
        data.timeLine = timeLine;
        data.url = window.location.href;
        // alert("test");
        // console.log("submitForm", data);
        API.jsonApiCall(contactUsApi, data, "POST", null, {
            "Content-Type": "application/json",
        })
            .then((res) => {
                console.log("ressss", res);
                if (res.status == 200) {
                    toast.success(res.message);
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
                    });
                }

            })
            .catch((e) => {
                console.log("error", e);
                toast.error("Something went wrong try later!");
            });
    }
    renderOfficeCard() {
        return this.state.cardsData.map((card, key) => {
            const mobile = "tel:" + card.mobile;
            const email = "tel:" + card.email;
            return (
                <Col sm={4} key={key}>
                    <div className="card-holder ">
                        <div className="card">
                            <div className="card-imgs">
                                <img src={card.imgUrl}
                                    alt={'Image'} className="" />
                            </div>
                            <div className="card-content">
                                <div className="content-holder">
                                    <h5 className="office-city">{card.Officecity}</h5>
                                    <p className="office-addr">{card.officeAddr}</p>
                                    <p className="cantact-info">
                                        <a href={mobile}>{card.mobile}{card.status}</a><br />
                                        <a href={email}> {card.email}</a><br />
                                    </p>
                                    <p className="cantact-link mt-4">
                                        <Link href={card.link}> Learn More »</Link>
                                    </p>
                                </div>
                            </div>
                        </div>
                    </div>
                    <br />
                    <br />
                    <br />
                </Col>
            )
        });
    }
    renderDepartments() {
        return this.state.depData.map((card, key) => {
            const mobileExt = "tel:" + card.mobileExt;
            const mobileDir = "tel:" + card.mobileDir;
            let subHeadMobileExt = "";
            let subHeadMobileDir = "";
            if (card.subHead) {
                subHeadMobileExt = "tel:" + card.subHeading.mobileExt
                subHeadMobileDir = "tel:" + card.subHeading.mobileDir
            }
            return (
                <Col sm={3} key={key}>
                    <div className="card-holder dept">
                        <div className="card">
                            <div className="card-content">
                                <div className="content-holder">
                                    <p className="office-title">{card.title}</p>
                                    <p className="cantact-info">
                                        {card.mobileExt && <> <a href={mobileExt}>{card.mobileExt}  <span className="sub-title">{card.typeExt}</span></a><br /></>}
                                        {card.mobileDir && <><a href={mobileDir}>{card.mobileDir}  <span className="sub-title">({card.mobileDirExt})</span></a><br /></>}
                                        {card.searchEmail && <> <a href={card.applyEmail}> {card.searchEmail}</a><br /></>}
                                        {card.applyEmail && <> <a href={card.applyEmail}> {card.applyEmail}</a><br /></>}
                                    </p>
                                    <p className="cantact-link mt-4">
                                    </p>
                                    {
                                        card.subHead &&
                                        <>
                                            <p className="office-title">{card.subHeading.title}</p>
                                            <p className="cantact-info">
                                                {card.subHeading.mobileExt && <> <a href={subHeadMobileExt}>{card.subHeading.mobileExt}  <span className="sub-title">{card.subHeading.typeExt}</span></a><br /></>}
                                                {card.subHeading.mobileDir && <><a href={subHeadMobileDir}>{card.subHeading.mobileDir}  <span className="sub-title">({card.subHeading.mobileDirExt})</span></a><br /></>}
                                                {card.subHeading.searchEmail && <> <a href={card.subHeading.applyEmail}> {card.subHeading.searchEmail}</a><br /></>}
                                                {card.subHeading.applyEmail && <> <a href={card.subHeading.applyEmail}> {card.subHeading.applyEmail}</a><br /></>}
                                            </p>
                                            <p className="cantact-link mt-4">
                                            </p>
                                        </>
                                    }
                                </div>
                            </div>
                        </div>
                    </div>
                    <br />
                    <br />
                </Col>
            )
        });
    }
    renderCitydata(item) {
        if (Array.isArray(item) && item.length > 0) {
            return item.map((e, key) => {
                const numbers = "tel:" + e.cityNum;
                return (
                    <>
                        <p ><span className="office-title">{e.cityTitle} -</span> <span className="sub-title additional">{e.cityAddr}</span> </p>
                        <p className="cantact-info"><a href={numbers}> {e.cityNum} </a><span className="sub-title additional"> ({e.cityNumStatus})</span></p>
                    </>
                )
            })

        }
    }
    renderAddtionalNumber() {
        return this.state.addDataList.map((card, key) => {
            const faxNum = "tel:" + card.fax;
            const operator = "tel:" + card.operator;
            const brokerageNum = "tel:" + card.brokerageNum;
            const brokerageLocalNum = "tel:" + card.brokerageLocalNum;

            return (
                <Col sm={6} key={key}>
                    <div className="card-holder dept ">
                        <div className="card">
                            <div className="card-content">
                                <div className="content-holder">
                                    <p className="office-title">{card.title}</p>
                                    <br />
                                    <p className="cantact-info">
                                        {card.fax && <> <span className="sub-title additional">{"Fax >> "}</span><span className="fax"><a href={faxNum}>{card.fax}</a> </span></>} </p>
                                    {card.brokerageNum && <p className="cantact-info"><span className="fax"><a href={brokerageNum}>{card.brokerageNum}</a> </span><span className="sub-title additional">{"(RATE) >> Toll Free"}</span></p>}
                                    {card.brokerageLocalNum && <p className="cantact-info"><span className="fax"><a href={brokerageLocalNum}>{card.brokerageLocalNum}</a> </span><span className="sub-title additional">{"ext. 3 >> Local"}</span></p>}
                                    {card.brokerageWebSite && < p className="cantact-info"><span className="fax"><a href={"http://" + card.brokerageWebSite}>{card.brokerageWebSite}</a> </span></p>}
                                    {/* 416.993.7653  ext. 3 >> Local */}

                                    {/*  */}


                                    <p className="cantact-info">
                                        {card.operator && <><span className="sub-title additional">{"24/7 Operator >>"}<a href={operator}> {card.operator}</a></span></>}
                                    </p>
                                    <p className="cantact-link mt-4 cantact-info">
                                    </p>
                                    <br />
                                    {
                                        card.city &&
                                        this.renderCitydata(card.cityDetails)
                                    }
                                    {
                                        card.details &&
                                        this.renderDetail(card.detailsList)
                                    }
                                </div>
                            </div>
                        </div>
                    </div>
                    <br />

                    <br />
                </Col>
            )
        });
    }
    renderDetail(item) {
        if (Array.isArray(item) && item.length > 0) {
            return item.map((e, key) => {
                // const numbers = "tel:" + e.cityNum;
                return (
                    <>
                        <h5>{e.title}</h5>
                        {e.email && <> <p className="cantact-info"><br /><span className="sub-title additional">{e.email}</span> </p></>}
                        {e.fullFormLink && <><p className="cantact-info"><a href={e.fullFormLink}> {e.fullForm} </a></p></>}<br />
                        {e.subTitle && <> <p className="cantact-info" ><a href={e.subTitleLink} >{e.subTitle}</a><span className="sub-title additional"> {" >> " + e.key}</span> </p></>}

                    </>
                )
            })

        }
    }
    render() {
        return (
            <section className="contact-wrapper">
                <Row>
                    <Col sm={12} className="">
                        <br />
                        <hr />
                        <br />
                    </Col>
                </Row>
                <Row className="mt-3 aboutSection">
                    <Col sm={12}>
                        <h3 className="contactus">
                            Let’s Talk About It
                            <hr className="hr" />
                        </h3>
                        <br />
                        <br />
                    </Col>
                    <Col sm={12} className="aboutForm">
                        <div className="letAboutForm">
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
                                        extraProps={{}}
                                    />
                                </div>
                                {this.state.timeLineErr && <span className="err-inp-msg" >Field is required</span>}
                            </div>
                            <textarea className="mt-4 form-control comments" value={this.state.comments} onChange={this.handleComments} placeholder="Comments or Questions" cols={72} rows={5} />
                            {this.state.commentsErr && <span className="err-inp-msg customErr" >Field is required</span>}
                            <Col sm={2} className="submitBtns mt-4">
                                <Reaptcha sitekey="6LeIxAcTAAAAAJcZVRqyHh71UMIEGNQ_MXjiZKhI" onVerify={this.onVerify} /><br />
                                <button className="custom-button-red nav-button btn btn-primary" disabled={this.state.showBtn ? false : true} onClick={this.submitForm}>Submit</button>
                            </Col>
                            <Col sm={10}>
                            </Col>

                        </div>
                    </Col>
                </Row>

                <ToastContainer />
            </section>
        );
    };
}
export default ContactUs;

