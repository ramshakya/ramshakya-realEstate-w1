import React, { Component } from 'react';
import { Container, Row, Col } from "react-bootstrap";
import Link from "next/link";
import Constants from '../../../constants/Global';
class Reviews extends Component {
    constructor(props) {
        super(props);
        this.renderCards = this.renderCards.bind(this);
        const cardsData = [
            { id: 1, title: 'The Emerging Brand', content: "Top emerging real estate company. Business built on trust & our reputation is based on excellent customer service.", imgUrl: '/images/icon/about-emerging-brand.png', link: "", linkTitle: "" },
            { id: 2, title: 'Cutting Edge Technology', content: "We stay ahead of the curve. Tech to keep you informed & knowledgeable.", imgUrl: '/images/icon/about-cutting-edge-tech.png', link: "", linkTitle: "" },
            { id: 3, title: 'Highly Trained Agents', content: "Trained from A to Z via special programme. Specializing in Pricing and Negotiating", imgUrl: '/images/icon/about-highly-trained-agents.png', link: "#", linkTitle: "" },
        ];
        this.state = {
            cardsData: cardsData,
            addCls: ""
        };
        this.props.setMetaInfo({
            title: "aboutUs",
            slug: 'aboutUs',
            metaDesc: 'Housen.ca aboutUs',
            MetaTags: 'aboutUs'
        });

    }
    componentDidMount() {
        

    }
    // { id: 4, title: 'Proud Sponsor', content: "We donate a proceed of every sale to support children’s health!", imgUrl: '/images/icon/about-sick-kids-sponsor.png', link: "", linkTitle: "" },
    //             { id: 5, title: 'Search Mortgage Corp.', content: "More options and better deals than your bank. Options of over 100 banks, lenders & brokers. Lowest Rates Guaranteed.", imgUrl: '/images/icon/search-mortgage-logo-100x100.png', link: "#", linkTitle: "Apply For A Mortgage »" },
    //             { id: 6, title: 'Google Partner', content: "Only brokerage in world to be a Google Partner.", imgUrl: '/images/icon/about-google-partner-100x98.png', link: "", linkTitle: "" },
    renderCards() {
        return this.state.cardsData.map((card) => {
            return (
                <Col sm={4} className={``} >
                    <div className="card-holder">
                        <div className="cardList">
                            <div className="card-img">
                                <img src={card.imgUrl}
                                    alt={'Image'} width={100} height={100} />
                            </div>
                            <div className="card-content">
                                <h3 className="titles text-center">{card.title}</h3>
                                <div className="content-holder text-center">
                                    <p className="contents text-center">{card.content}</p>
                                    <Link href={card.link}>{card.linkTitle}</Link>
                                </div>
                            </div>
                        </div>
                    </div>
                </Col>
            )
        })
    }
    render() {
        return (
            <>
                <section className={`contact-wrapper mt-5 about-section ${this.props.isMobile?"custom-padding-top-3":""}`}>
                    <Container className=" ">
                        <Row className="about">
                            <Col sm={12} className="" >

                                <h1 className="contactus">

                                    <h1 className="heading-text text-center">
                                        About Us
                                    </h1>
                                    <hr className="hr" />
                                </h1>
                            </Col>
                            <Col sm={12} className="mt-2">
                                <p className="office-content aboutContent">
                                {Constants.APP_NAME} is a leading real estate website in Toronto, Canada. We feature the latest MLS listings across Ontario, as well as sold data and statistics from the Toronto Regional Real Estate Board (TRREB). {Constants.APP_NAME} also features a mortgage calculator on each property listing page to give prospective home buyers an idea of their mortgage costs. Many modern features to assist home buyers in search of their dream home including saved searches which sends new listings to your inbox daily, favourite homes to view and share later, watch listings and communities, and much more! In addition, a pre-construction section that highlights the newest condo and home developments in the Greater Toronto Area (GTA) and beyond.

{Constants.APP_NAME} is updated multiple times a day to make sure you have access to the most recent and accurate real estate MLS listings, whether you have just begun browsing or are prepared to make that major purchase. You may use features like the mortgage calculator, social sharing, area information, and the opportunity to connect with local Realtors®️ to locate the home of your dreams.</p>
                            </Col>
                            <Col sm={12} className="" >
                                <Row>
                                    <Col sm={2} className=" mt-3" ></Col>
                                    <Col sm={4} className=" mt-3 pt-3" >
                                        <Link href="/map">
                                            <a className="common-btn">
                                                Find Me a Home &nbsp;&nbsp;&nbsp;<i className="fa fa-chevron-right"></i></a>
                                        </Link>
                                    </Col>
                                    <Col sm={4} className=" mt-3 pt-3" >
                                        <Link href="/#gotoHomevaluation">
                                            <a className="common-btn">
                                                Sell My Home &nbsp;&nbsp;&nbsp;<i className="fa fa-chevron-right"></i></a>
                                        </Link>
                                    </Col>
                                    <Col sm={2} className=" mt-3" ></Col>

                                </Row>
                            </Col>
                        </Row>

                    </Container>
                </section>
                <section className="buysection" hidden>
                    <div className="container">
                        <div className="col-lg-12 text-center">
                            <h3 className="heading-text mb-5 pb-4">Need Help? Use our SR Concierge.</h3>
                            <Link href="/homevalue">
                                <a className="common-btn buyFooterButton">
                                    TRY NOW &nbsp;&nbsp;&nbsp;<i className="fa fa-chevron-right"></i></a>
                            </Link>
                        </div>
                    </div>
                </section>

            </>
        );
    };
}
export default Reviews;