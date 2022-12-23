import React, { useEffect, useState } from "react";
const Test = (props) => {
    return (
        <>
            <html>

                <head>
                    <meta http-equiv="Content-type" content="text/html; charset=utf-8" />
                    <meta http-equiv="X-UA-Compatible" content="IE=Edge" />
                    <meta name="viewport" content="width=device-width, initial-scale=1" />
                    <meta name="robots" content="all, index, follow" />
                    <meta name="msapplication-TileColor" content="#ff5b60" />
                    <meta name="theme-color" content="#ff5b60" />
                    <meta charSet="utf-8" />
                    <meta http-equiv="content-language" content="en" />
                    <title> wedu.ca</title>
                    <meta name="robots" content="all, index, follow" />
                    <meta name="author" content="wedu" />
                    <meta name="description" content="description" />
                    <meta name="keywords" content="keywords" />
                    <meta property="og:locale" content="en_US" />
                    <meta property="og:title" content="og:title" />
                    <meta name="og:type" content="website" />
                    <meta name="og:description" ontent="og:description" />
                    <meta name="og:url" content="https://www.wedu.ca/" />
                    <meta name="og:image" content="https://www.wedu.ca/images/logo/logo.png" />
                    <meta property="og:image:secure_url" content="https://www.wedu.ca/images/logo/logo.png" />
                    <meta property="og:image:type" content="image/jpeg" />
                    <meta property="og:image:width" content="200" />
                    <meta property="og:image:height" content="200" />
                    <meta property="og:image:alt" content="ram not added image here" />
                    <meta property="twitter:card" content="summary_large_image" />
                    <meta property="twitter:url" content="https://www.wedu.ca/" />
                    <meta property="twitter:title" content="twitter title " />
                    <meta property="twitter:description" content="twitter description " />
                    <meta property="twitter:image" content="https://www.wedu.ca/images/logo/logo.png" />
                    <meta property="twitter:image:secure_url" content="https://www.wedu.ca/images/logo/logo.png" />
                    <meta property="twitter:image:type" content="image/jpeg/png" />
                    <meta property="twitter:image:width" content="200" />
                    <meta property="twitter:image:height" content="200" />
                    <meta property="twitter:image:alt" content="ram not added image here" />
                    <link rel="canonical" href="https://www.wedu.ca" />
                    <link rel="icon" href="" />
                    <link rel="apple-touch-icon" sizes="180x180" href="" />
                    <link rel="alternate icon" type="image/png" sizes="32x32" href="" />
                    <link rel="alternate icon" type="image/png" sizes="16x16" href="" />
                    <link rel="icon" href="https://www.wedu.ca/images/logo/logo.png" />
                    <link rel="mask-icon" href="" color="#ff5b60" />
                </head>
                <body>
                    <div id="__next" data-reactroot="">
                        <div>
                            <header class="top-header " id="top-header">
                                <div class="container-fluid nav-container">
                                    <div class="logo"><span
                                        style={{ 'boxSizing': 'borderBox;', 'display': 'block;', 'overflow': 'hidden;', 'width': 'initial;', 'height': 'initial;', 'background': 'none;', 'opacity': '1;', 'border': '0;', 'margin': '0;', 'padding': '0;', 'position': 'relative' }}><span
                                            style={{ 'boxSizing': 'borderBox;', 'display': 'block;', 'width': 'initial;', 'height': 'initial', 'background': 'none;', 'opacity': '1;', 'border': '0;', 'margin': '0;', 'padding': '0;', 'paddingTop': '40%' }}></span><img
                                            alt="" sizes="100vw"
                                            srcSet="/_next/image?url=%2Fimages%2Flogo%2Flogo.png&amp;w=640&amp;q=75 640w, /_next/image?url=%2Fimages%2Flogo%2Flogo.png&amp;w=750&amp;q=75 750w, /_next/image?url=%2Fimages%2Flogo%2Flogo.png&amp;w=828&amp;q=75 828w, /_next/image?url=%2Fimages%2Flogo%2Flogo.png&amp;w=1080&amp;q=75 1080w, /_next/image?url=%2Fimages%2Flogo%2Flogo.png&amp;w=1200&amp;q=75 1200w, /_next/image?url=%2Fimages%2Flogo%2Flogo.png&amp;w=1920&amp;q=75 1920w, /_next/image?url=%2Fimages%2Flogo%2Flogo.png&amp;w=2048&amp;q=75 2048w, /_next/image?url=%2Fimages%2Flogo%2Flogo.png&amp;w=3840&amp;q=75 3840w"
                                            src="/_next/image?url=%2Fimages%2Flogo%2Flogo.png&amp;w=3840&amp;q=75" decoding="async"
                                            data-nimg="responsive" class="img-fluid"
                                            style={{ 'position': 'absolute;', 'top': '0;', 'left': '0;', 'bottom': '0;', 'right': '0;', 'boxSizing': 'borderBox;', 'padding': '0;', 'border': 'none;', 'margin': 'auto;', 'display': 'block;', 'width': '0;', 'height': '0;', 'minWidth': '100%;', 'maxWidth': '100%;', 'minHeight': '100%;', 'maxHeight': '100%;', 'objectFit': 'fill' }} /></span>
                                    </div>
                                    <div class="d-flex align-items-center">
                                        <ul>
                                            <li class="sign-inbtn-mb me-2"><button class="common-btn" id="togglebtn">Login</button></li>
                                        </ul>
                                        <div class="toggle-bar"><span class="toggle-icon"></span></div>
                                    </div>
                                    <nav class="main-navbar">
                                        <ul>
                                            <li class=""><a href="/">Home</a></li>
                                            <li>
                                                <div class="dropdown"><button type="button" id="dropdown-basic2" aria-expanded="false"
                                                    class="dropdown-toggle btn"><span>Properties</span></button></div>
                                            </li>
                                            <li class=""><a href="/blogs">Blog</a></li>
                                            <li class=""><a href="/calculator/mortgage-calculator">Calculator</a></li>
                                            <li class=""><a href="/homevalue">Home Valuation</a></li>
                                            <li>
                                                <div class="dropdown"><button type="button" id="dropdown-basic3" aria-expanded="false"
                                                    class="dropdown-toggle btn"><span>About</span></button></div>
                                            </li>
                                            <li class="sign-inbtn"><button class="common-btn" id="togglebtn">Login</button></li>
                                        </ul>
                                    </nav>
                                </div>
                            </header>
                            <section class="contact-wrapper">
                                <div class="row">
                                    <div class="col-sm-12"></div>
                                </div>
                                <div class="container">
                                    <div class="row ">
                                        <div class="col-sm-12">
                                            <h1 class="contactus">Contact Us
                                                <hr class="hr" />
                                            </h1>
                                        </div>
                                        <div class="col-sm-12 img-wrapper img-responsive "></div>
                                    </div>
                                    <div class="row office-section">
                                        <div class="col-sm-6">
                                            <h4>Customer Relations</h4>
                                            <p class="office-content"><a href="mailto:Info@Wedu.ca"
                                                class="text-secondary">Info@Wedu.ca</a></p>
                                            <p class="office-content"><a href="tel:+1 (647) 243-5349" class="text-secondary">+1 (647)
                                                243-5349</a> </p>
                                            <div class="agentinfo">
                                                <h3 class="mb-0">JUVAN MARIATHASAN</h3><small>
                                                </small>
                                                <p class="pt-2 mb-1">Phone : <a href="tel:+1 (416) 273-4114" class="text-secondary">+1
                                                    (416) 273-4114</a></p><small>Brokerage</small>
                                                <p class="mb-0">AIMHOME REALTY INC.</p>
                                                <p class="pt-1 mb-1">Phone : <a href="tel:+1 (905) 477-5900" class="text-secondary">+1
                                                    (905) 477-5900</a></p>
                                                <p class="pt-1">3601 HWY. 7 E UNIT 513
                                                </p>
                                            </div>
                                        </div>
                                        <div class="col-sm-6"><iframe loading="lazy" width="600" height="450" class="iframMobile"
                                            referrerpolicy="no-referrer-when-downgrade"
                                            src="https://www.google.com/maps?key=AIzaSyBrc6W-HZICQvpA_EYOefkoB66AG3ANAGQ&amp;q=Aimhome Realty Inc, 3601 Hwy 7 E, Markham, Ontario, Canada&amp;output=embed"></iframe><br />
                                        </div>
                                    </div>
                                </div>
                                <div class="mt-3 aboutSection">
                                    <div class="col-sm-12">
                                        <h3 class="contactus">Let’s Talk About It
                                            <hr class="hr" />
                                        </h3><br /><br />
                                    </div>
                                    <div class="col-sm-12 aboutForm position-relative">
                                        <div class="letAboutForm">
                                            <div class="mt-2 font-normal contact_inputs "><input type="text" name="user_name"
                                                placeholder="Your Name" class="form-control" autoComplete="off" id="user_name"
                                                data-inp-error-msg="Name is required" data-inp-validation="required,alpha"
                                                data-gsf-name="user_name" /></div>
                                            <div class=" font-normal contact_inputs "><input type="text" name="user_email"
                                                placeholder="Your Email" class="form-control" autoComplete="off" id="user_email"
                                                data-inp-error-msg="Input valid email" data-inp-validation="required,email"
                                                data-gsf-name="user_email" /></div>
                                            <div class=" font-normal contact_inputs "><input type="text" name="user_phone"
                                                placeholder="Phone Number" class="form-control" autoComplete="off" id="user_phone"
                                                maxLength="15" data-inp-error-msg="Input valid mobile number"
                                                data-inp-validation="required" data-gsf-name="user_phone" /></div>
                                            <div class=" font-normal contact_inputs "><input type="text" name="user_location"
                                                placeholder="City or Location" class="form-control" autoComplete="off"
                                                id="user_location" data-inp-error-msg="City or Location is required"
                                                data-inp-validation="required" data-gsf-name="user_location" /></div>
                                            <div class="autoSuggestionCls">
                                                <div class="">
                                                    <div class="sss   autoSuggestion_autoSuggestionCls__kVGRX"><input type="text"
                                                        id="query" name="text_search"
                                                        class="auto form-control auto-suggestion-inp inp  type-head"
                                                        placeholder="Select Your Query" title="Select Your Query" readonly=""
                                                        autoComplete="off" /></div>
                                                </div>
                                            </div>
                                            <div class="autoSuggestionCls mt-4">
                                                <div class="">
                                                    <div class="sss   autoSuggestion_autoSuggestionCls__kVGRX"><input type="text"
                                                        id="query" name="text_search"
                                                        class="auto form-control auto-suggestion-inp inp  type-head"
                                                        placeholder="Select Your Timeline" title="Select Your Timeline" readonly=""
                                                        autoComplete="off" /></div>
                                                </div>
                                            </div><textarea class="mt-4 form-control comments" placeholder="Comments or Questions"
                                                cols="72" rows="5"></textarea>
                                            <div class="col-sm-2 submitBtns mt-4 googleCaptcha">
                                                <div id="" class="g-recaptcha"></div><br /><button
                                                    class="custom-button-red nav-button btn " disabled="">Submit</button>
                                            </div>
                                            <div class="col-sm-10"></div>
                                        </div>
                                    </div>
                                </div>
                                <div class="Toastify"></div>
                            </section>
                            <main>
                                <section class="contact-section">
                                    <div class="container">
                                        <div class="row">
                                            <div class="col-md-4 col-lg-6 box-col">
                                                <div class="title mb-4 m-left-15">
                                                    <h2 class="text-bold">Contact Us</h2>
                                                </div>
                                                <ul class="footerInfo m-left-15">
                                                    <li><img src="../images/social-icon/mail.png" alt="icons" class="iconImage"
                                                        width="25" height="25" /> <a href="mailto:" class="text-dark"></a></li>
                                                    <li><img src="../images/social-icon/phone.svg" alt="icons" class="iconImage"
                                                        width="25" height="25" /> <a href="tel:" class="text-dark"></a></li>
                                                    <li><img src="../images/social-icon/user.png" alt="icons" class="iconImage"
                                                        width="25" height="25" /> <span class="text-dark">JUVAN MARIATHASAN</span>
                                                        <small style={{ "fontSize": '15px' }}>
                                                        </small></li>
                                                    <li><img src="../images/social-icon/phone.svg" alt="icons" class="iconImage"
                                                        width="25" height="25" /> <span class="text-dark"><a
                                                            href="tel:+1 (416) 273-4114" class="text-dark">+1 (416)
                                                            273-4114</a></span></li>
                                                    <li><img src="../images/social-icon/marker.png" alt="icons" class="iconImage"
                                                        width="25" height="25" /> <span class="contact-addr text-dark"></span></li>
                                                </ul><span class="m-left-15">Social Media</span>
                                                <ul class="mb-0 footer-social-media m-left-15">
                                                    <li><a class="link" href="/ContactUs#"><img src="../images/social-icon/facebook.svg"
                                                        height="30" width="30" alt="facebook-icon" /></a></li>
                                                    <li><a class="link" href="/ContactUs#"><img src="../images/social-icon/twitter.svg"
                                                        height="30" width="30" alt="twitter-icon" /></a></li>
                                                    <li><a class="link" href="/ContactUs#"><img src="../images/social-icon/linkedin.svg"
                                                        height="30" width="30" alt="linkedin-icon" /></a></li>
                                                </ul>
                                            </div>
                                            <div class="col-md-8 col-lg-6 form-col box-col">
                                                <div class="contact-form-wrapper">
                                                    <h2>What Do You Want To Ask</h2>
                                                    <form>
                                                        <div class="mb-4"><input type="name" class="form-control"
                                                            placeholder="Your name" name="name" required="" autoComplete="off" />
                                                        </div>
                                                        <div class="mb-4"><input type="email" class="form-control"
                                                            placeholder="Your Email Address" name="email" required=""
                                                            autoComplete="off" /></div>
                                                        <div class="mb-4"><textarea placeholder="Your message" name="message"
                                                            class="form-control" cols="20" rows="4" required=""></textarea></div>
                                                        <button type="submit" class="common-btn form-btn pt-3 pb-3">Send Now</button>
                                                    </form>
                                                </div>
                                                <div class="form-bg"></div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="container-fluid footer-credits">
                                        <div class="row">
                                            <div class="col-md-4 col-lg-4">
                                                <p class="copyright-website">Wedu.ca
                                                </p>
                                            </div>
                                            <div class="col-md-4 col-lg-4">
                                                <ul class="footer-links">
                                                    <li><a class="text-white" href="/privacy_policy">Privacy Policy</a></li>
                                                    <li><a class="text-white" href="/Term&amp;conditions">Terms &amp; Conditions</a>
                                                    </li>
                                                </ul>
                                            </div>
                                            <div class="col-md-4 col-lg-4">
                                                <ul class="footer-social">
                                                    <li><a class="link" href="/ContactUs#"><img
                                                        src="../images/social-icon/instagram.svg" height="30" width="30"
                                                        alt="instagram-icon" /></a></li>
                                                    <li><a class="link" href="/ContactUs#"><img src="../images/social-icon/youtube.svg"
                                                        height="30" width="30" alt="youtube-icon" /></a></li>
                                                </ul>
                                            </div>
                                        </div>
                                    </div>
                                </section>
                            </main>
                            <div class="Toastify"></div>
                        </div>
                    </div>
                </body>

            </html>
        </>
    );
};
export default Test;
