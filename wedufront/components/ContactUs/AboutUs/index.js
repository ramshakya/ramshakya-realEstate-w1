import React, { Component } from 'react';
import { Container, Row, Col } from "react-bootstrap";
import Link from "next/link";
import Constants from '../../../constants/GlobalConstants';
class Reviews extends Component {
    constructor(props) {
        super(props);
        this.renderCards = this.renderCards.bind(this);
        const cardsData = [
            { id: 1, title: 'The Emerging Brand', content: "Top emerging real estate company. Business built on trust & our reputation is based on excellent customer service.", imgUrl: '/images/icons/about-emerging-brand.png', link: "", linkTitle: "" },
            { id: 2, title: 'Cutting Edge Technology', content: "We stay ahead of the curve. Tech to keep you informed & knowledgeable.", imgUrl: '/images/icons/about-cutting-edge-tech.png', link: "", linkTitle: "" },
            { id: 3, title: 'Highly Trained Agents', content: "Trained from A to Z via Search University. Specializing in Pricing and Negotiating.", imgUrl: '/images/icons/about-highly-trained-agents.png', link: "#", linkTitle: "" },
            
        ];
        this.state = {
            cardsData: cardsData
        };
        this.props.setMetaInfo({
          title:"aboutUs",
          slug:'aboutUs',
          metaDesc:'wedu.ca aboutUs',
          MetaTags:'aboutUs'
        });

    }
// { id: 4, title: 'Proud Sponsor', content: "We donate a proceed of every sale to support children’s health!", imgUrl: '/images/icons/about-sick-kids-sponsor.png', link: "", linkTitle: "" },
//             { id: 5, title: 'Search Mortgage Corp.', content: "More options and better deals than your bank. Options of over 100 banks, lenders & brokers. Lowest Rates Guaranteed.", imgUrl: '/images/icons/search-mortgage-logo-100x100.png', link: "#", linkTitle: "Apply For A Mortgage »" },
//             { id: 6, title: 'Google Partner', content: "Only brokerage in world to be a Google Partner.", imgUrl: '/images/icons/about-google-partner-100x98.png', link: "", linkTitle: "" },
    renderCards() {
        return this.state.cardsData.map((card) => {
            return (
                <Col sm={4} className="" >
                    <div className="card-holder">
                        <div className="cardList">
                            <div className="card-img">
                                <img src={card.imgUrl}
                                    alt={'Image'} width={100} height={100}/>
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
                <section className="contact-wrapper mb-5 about-section">
                    
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
                                    Connecting home buyers and sellers with {Constants.APP_NAME} agents in real-time! {Constants.APP_NAME} is a community-focused, future-facing real estate brokerage that is committed to providing the best tools and technology to serve the evolving needs of today’s home buyers, sellers, and REALTORS®. </p>
                                <br />
                                <br />
                            </Col>
                            <Col sm={12} className="" >
                                <Row>
                                    <Col sm={2} className=" mt-3" ></Col>
                                    <Col sm={4} className=" mt-3 pt-3" >
                                       <Link href="/buyinghomes">
                                            <a className="common-btn">
                                           Find Me a Home &nbsp;&nbsp;&nbsp;<i className="fa fa-chevron-right"></i></a>
                                        </Link>
                                    </Col>
                                    <Col sm={4} className=" mt-3 pt-3" >
                                        <Link href="/sellinghomes">
                                            <a className="common-btn">
                                            Sell My Home &nbsp;&nbsp;&nbsp;<i className="fa fa-chevron-right"></i></a>
                                        </Link>
                                    </Col>
                                    <Col sm={2} className=" mt-3" ></Col>
                                   
                                </Row>
                            </Col>
                        </Row>

                        <Row className="mt-5">
                            <Col sm={12}>
                                <br />
                                <br />
                                <br />
                                <h3 className="contactus">
                                    Our 3 Step Selling Process:
                                    <hr className="hr" />
                                </h3>
                            </Col>
                        </Row>
                        <Row className="mt-5 cardsHolders">
                            {
                                this.renderCards()
                            }
                        </Row>
                    </Container>
                </section>
                 <section className="buysection">
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