import React, { Component } from 'react';
import { Container, Row, Col } from "react-bootstrap";
import Link from "next/link";
class Reviews extends Component {
    constructor(props) {
        super(props);
        this.reviews = this.reviews.bind(this);
        this.state = {
        };
    }
    reviews() {
        window.open(`https://platform.clientsviews.com/searchrealty`, 'SearchRealty', 'width=650,height=500' ,'align=center')
    }
    render() {
        return (
            <section className="contact-wrapper mb-5">
                <Row>
                    <Col sm={12} className="">
                        <br />
                        <hr />
                        <br />
                    </Col>
                </Row>
                <Container className="reviews">
                    <Row>
                        <Col sm={12} className="" >
                            <br />
                            <h1 className="contactus">
                                Search Realty : Reviews & Testimonials
                                <hr className="hr" />
                            </h1>
                        </Col>
                        <Col sm={12} className="mt-2">
                            <p className="office-content">
                                Connecting home buyers and sellers with Search Realty agents in real-time! Search Realty is a community-focused, future-facing real estate brokerage that is committed to providing the best tools and technology to serve the evolving needs of today’s home buyers, sellers, and REALTORS®.
                            </p>
                            <br />
                            <br />
                            <button className="custom-button-red nav-button btn btn-primary rounded" onClick={this.reviews}>REVIEW US</button>
                            
                        </Col>
                    </Row>
                </Container>
            </section>
        );
    };
}
export default Reviews;
//git check 
///