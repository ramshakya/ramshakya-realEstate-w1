// Library
import { Container, Row, Col } from "react-bootstrap";
import Image from "next/image";
const Footer = () => {
  return (
    <>
      <footer>
        <div className="upbar" style={{ 'background-color': '#fdc689 !important;', 'color': 'gray;' }}>
          <Container>
            <Row>

              <Col lg={4} className="footer-list-wrapper">
                <h3 className=""><strong>Contact Us For More Information</strong></h3>
                {/* Address */}
                <p className="">
                  <span>Email</span>
                </p>
                <p className="">  <strong>info@wedu.com</strong></p>
                {/* Phone */}
                <p className="">
                  <span>Phone</span><br />
                  <strong>+(308) 555-0121</strong>
                </p>
                {/* Email */}
                <p className="">
                  <span>Address</span>
                </p>
                <p className=""><strong>Wedu.com</strong></p>
                <p className="">
                  <strong>2118 Thornidge Cir ,</strong>
                </p>
                <p className="">
                  <strong>Syracouse,Connecticut</strong>
                </p>
                <p className="f">
                  <strong>35624</strong>
                </p>
                <div className="socmed2">
                  <a href="#" className=" social-icon facebook1">
                    <i className="bi bi-facebook"></i>
                  </a>
                  <a href="#" className="social-icon twitter1">
                    <i className="bi bi-twitter"></i>
                  </a>
                  <a href="#" className="social-icon instagram1">
                    <i className="bi bi-instagram"></i>
                  </a>
                </div>
              </Col>
              <Col lg={4} className="footer-list-wrapper">

              </Col>
              <Col lg={4} className="footer-list-wrapper">

                <div className="reviews-wrapper">
                  <div className="reviews">
                    <p className="text-review">
                      <center> <h5>What Do You Want To Ask</h5></center>
                    </p>
                  </div>
                </div>
              </Col>
            </Row>
          </Container>
        </div>
        {/* <div className="botbar">
        <Container>
          <Row>

            <Col lg={4}>
             
            </Col>
            <Col lg={8}>
              <div className="copyright2">
                Copyright © 2021 |
              </div>
            </Col>
          </Row>
        </Container>
      </div> */}
      </footer>
      <style amp-custom jsx>{
        `
        .social-icon{
           padding:15px;
           font-size:15px;
           font-size: 25px;
           color: gray;
        }
        .upbar {
          background-color: #222;
          padding: 3.5rem 0 5rem 0;
        }
        
        .title {
          color: var(--red);
          font-size: 1.125em;
          font-weight: 500;
          padding-bottom: 0.625rem;
          padding-top: 0.625rem;
        }
        
        .foot-text {
          color: var(--white);
        }
        
       
        
        .sub-title {
          color: var(--white);
        }
        
        
        .google-wrapper {
          width: 50%;
        }
        
        .bbb-wrapper {
          margin-top: 1.563rem;
          width: 75%;
        }
        
        .malcare-wrapper {
          width: 40%;
        }
        
        .botbar {
          background-color: #111;
          color: var(--white);
        }
        
        .copyright {
          font-family: var(--font);
          font-size: 0.8rem;
          line-height: 2rem;
          color: #444;
        }
        
         
          .socmed {
            margin-top: 0.625rem;
            text-align: center;
          }
          .facebook,
          .twitter,
          .instagram {
            margin-right: 0.625rem;
          }
          .copyright {
            font-size: 0.8rem;
          }
          .upbar {
            padding: 3.5rem 0.625rem 5rem 0.625rem !important;
          }
        
          .botbar {
            padding: 1rem 0.625rem;
          }
        
          .list-wrapper {
            padding: 0 !important;
          }
        }
        `
      }
      </style>
    </>
  );
};

export default Footer;
