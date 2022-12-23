import React, { Component } from 'react';
import { Container, Row, Col } from "react-bootstrap";
import Link from "next/link";
class Cookies extends Component {
    constructor(props) {
        super(props);
        this.state = {
        };
    }
    render() {
        return (
            <section className="term_condition">
                <Container className="">
                    <Row>
                        <Col sm={12} className="" >
                            <h1 className="term_condition_heading">
                                <span>Cookie Policy</span>
                            </h1>
                        </Col>
                    </Row>

                    <Row>
                        <Col sm={12} className="mt-2">
                            <h4 className="term_condition_sub_heading mt-1 text-bold">
                               Welcome to Housen.ca!
                            </h4>
                            <p className="terms-content">
                                This cookie policy explains how and why cookies and other 
                                similar technologies may be stored on
                                and accessed from your device when you use or visit:
                                
                            </p>
                            <ul className="">
                                    <li><Link href="/"><a> https://www.housen.ca</a></Link></li>
                                </ul>
                            <p>(Hereinafter referred to as “Housen.ca”).</p>
                            <p>The information collected through cookies will be under responsibility and in charge of:</p>
                             <ul className="">
                                    <li><Link href="/"><a className="text-dark text-bold"> Housen.ca</a></Link></li>
                                    <li><a href="mailto:info@housen.ca" className="text-dark text-bold"> Email: info@housen.ca</a></li>
                                </ul>
                            <p>
                                This cookie policy should be read together with our privacy policy and our terms and conditions.    
                            </p>
                            <p>By using the website, you accept the use of cookies by Housen.ca, in the terms contained in this
                            policy.</p>
                        </Col>
                        <Col sm={12} className="mt-2">
                            <h4 className="term_condition_sub_heading mt-1 text-bold">
                               1. WHAT ARE COOKIES?
                            </h4>
                            <p>
                                Cookies are small text files that are stored on your computer or mobile device when you visit a
                                website. They allow the website to recognise your device and remember if you have been to the
                                website before. Cookies are a very common web technology; most websites use cookies and have
                                done so for years. Cookies are widely used to make the website work more efficiently. Cookies are
                                used to measure which parts of the website users visit and to personalise their experience. Cookies
                                also provide information that helps us monitor and improve the performance of the website.
                            </p>
                        </Col>
                        <Col sm={12} className="mt-2">
                            <h4 className="term_condition_sub_heading mt-1 text-bold">
                              2. REFUSING OR WITHDRAWING CONSENT TO THE USE OF COOKIES
                            </h4>
                            <p>
                            If you do not want cookies to be dropped on your device, you can adjust the setting of your Internet
                            browser to reject the setting of all or some cookies and to alert you when a cookie is placed on your
                            device. For further information about how to do so, please refer to your browser ‘help’, ‘tool’, or
                            ‘edit’ section. Please note that if you use your browser settings to block all cookies, including strictly
                            necessary cookies, you may not be able to access or use all or parts of the functionalities of
                            Housen.ca.
                                                        </p>
                            <p>
                            If you want to remove previously-stored cookies, you can manually delete the cookies at any time.
                            However, this will not prevent Housen.ca from placing further cookies on your device unless and
                            until you adjust your Internet browser setting as described above.
                            </p>
                            <p>
                            We provide the links for the management and blocking of cookies depending on the browser you
                            use:
                            </p>
                            <ul>
                                <li className="text-bold">Microsoft Edge: <a href="https://support.microsoft.com/en-us/office/delete-cookies-in-microsoft-
                                edge-63947406-40ac-c3b8-57b9-2a946a29ae09?ui=en-us&rs=en-us&ad=us" target="_blank">https://support.microsoft.com/en-us/office/delete-cookies-in-microsoft-
                                edge-63947406-40ac-c3b8-57b9-2a946a29ae09?ui=en-us&rs=en-us&ad=us</a></li>
                                <li className="text-bold">Firefox: <a href="https://support.mozilla.org/en-US/kb/clear-cookies-and-site-data-firefox" target="_blank">https://support.mozilla.org/en-US/kb/clear-cookies-and-site-data-firefox</a></li>
                                <li className="text-bold">Chrome: <a href="https://support.google.com/chrome/answer/95647?hl=en" target="_blank">https://support.google.com/chrome/answer/95647?hl=en</a></li>
                                <li className="text-bold">Safari: <a href="https://support.apple.com/guide/safari/manage-cookies-and-website-data-
                                sfri11471/mac" target="_blank">https://support.apple.com/guide/safari/manage-cookies-and-website-data-
                                sfri11471/mac</a></li>
                               
                            </ul>
                        </Col>
                        <Col sm={12} className="mt-2">
                            <h4 className="term_condition_sub_heading mt-1 text-bold">
                               3. FIRST-PARTY COOKIES
                            </h4>
                            <p>
                            We use cookies to enhance the performance of our website and personalise your online Housen.ca
                            experience. Cookies help us to collect information on how people use our website and which pages
                            they visit. They enable us to monitor the number of visitors and to analyse website usage patterns
                            and trends. We collect this information anonymously, so it does not identify anyone as an individual
                            and no personal information is stored in our cookies. We always use cookie data in a responsible
                            way.
                            </p>
                        </Col>
                        <Col sm={12} className="mt-2">
                            <h4 className="term_condition_sub_heading mt-1 text-bold">
                               4. THIRD-PARTY COOKIES
                            </h4>
                            <p>
                            Third-party cookies may come from partners or third-party companies that provide functional web
                            services or tools for our website and the optimal functioning and operation of our services. We use
                            third party cookies responsibly and for the sole purpose of providing optimal functioning of the
                            platform and services. You may opt out of these cookies by following the cookie removal
                            information contained in this document or the technical information of the browser from which you
                            access our website and services.
                            </p>
                        </Col>
                        <Col sm={12} className="mt-2">
                            <h4 className="term_condition_sub_heading mt-1 text-bold">
                              5. SESSION COOKIES
                            </h4>
                            <p>
                            Session cookies are used to keep users' session open when they log in to the website with their
                            credentials and password. Session cookies are temporary and are deleted from your device when
                            you log out and close your browser. We use session cookies to keep your session open when you
                            use our website and to identify you as a user on our website each time you log in. Session cookies
                            will not be retained on your device for longer than necessary and will only be used for the purposes
                            mentioned above.
                            </p>
                        </Col>
                        <Col sm={12} className="mt-2">
                            <h4 className="term_condition_sub_heading mt-1 text-bold">
                               6. SOCIAL COOKIES
                            </h4>
                            <p>
                                These cookies allow you to share our website and click “Like” on social networks like Instagram,
                                Facebook, Twitter, etc. They also allow you to interact with each distinct platform’s contents. The
                                way these cookies are used and the information gathered is governed by the privacy policy of each
                                social platform.
                            </p>
                        </Col>

                        <Col sm={12} className="mt-2">
                            <h4 className="term_condition_sub_heading mt-1 text-bold">
                               7. PURPOSES OF OUR COOKIES
                            </h4>
                            <p>Our cookies are used for the following purposes:</p>
                            <p><strong>Strictly Necessary:</strong> These cookies are essential for Housen.ca to perform its basic functions.</p>
                            <p><strong>Security:</strong> We use these cookies to help identify and prevent potential security risks.</p>
                            <p><strong>Analytics and Performance:</strong> Performance cookies collect information on how users interact with
                            our website, including what pages are visited most, as well as other analytical data. We use these
                            details to improve how our website functions and to understand how users interact with them.</p>
                            <p><strong>Advertising:</strong> These cookies are used to display relevant advertising to visitors who use our services
                            or visit websites we host or provide, as well as to understand and report on the efficacy of ads served
                            on our website. They track details such as the number of unique visitors, the number of times
                            particular ads have been displayed, and the number of clicks the ads have received. They are also
                            used to build user profiles, including showing you ads based on products you’ve viewed on our
                            website. These are set by Housen.ca and trusted third party networks and are generally persistent
                            in nature.</p>
                            <p><strong>GOOGLE Analytics.</strong> We use Google Analytics provided by Google, Inc., USA (“Google”). These tool
                            and technologies collect and analyse certain types of information, including IP addresses, device and
                            software identifiers, referring and exit URLs, feature use metrics and statistics, usage and purchase
                            history, media access control address (MAC Address), mobile unique device identifiers, and other
                            similar information via the use of cookies. The information generated by Google Analytics (including
                            your IP address) may be transmitted to and stored by Google on servers in the United States. We
                            use the GOOGLE Analytics collection of data to enhance the website and improve our service.</p>
                        </Col>
                        <Col sm={12} className="mt-2">
                            <h4 className="term_condition_sub_heading mt-1 text-bold">
                               8. CONTACT US
                            </h4>
                            <p>If you have questions or concerns about this cookie policy and the handling and security of your
                            data, please contact us through our contact page or via the contact information below:</p>
                            <ul className="">
                                    <li><Link href="/"><a className="text-dark text-bold"> Housen.ca</a></Link></li>
                                    <li><a href="mailto:info@housen.ca" className="text-dark text-bold"> Email: info@housen.ca</a></li>
                            </ul>
                        </Col>
                        

                    </Row>
                    
                </Container>
            </section>
        );
    };
}
export default Cookies;