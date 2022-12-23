// Library
import { Container, Row, Col, Form } from "react-bootstrap";
// import RedButton from "../Button/RedButton";

const MessageSection = () => {
  return (
    <div className="message">
      <Container className="message-wrapper">
        <Row>
          <Col>
            <div className="title-wrapper">
              <h1 className="message-title">Send Us a Message</h1>
              <hr />
            </div>
          </Col>
        </Row>
        <Row>
          <Col>
            <div className="help-wrapper">
              <Form className="help">
                <Form.Group className="mb-3">
                  <Form.Label htmlFor="help" className="option-label ">
                    How Can We Help Today? <p className="required">*</p>
                  </Form.Label>
                  <Form.Select
                    id="help"
                    required
                    className="option-select form-box"
                  >
                    <option>Please Select</option>
                    <option value="buy">
                      Buying Real Estate ( Send Me Listing)
                    </option>
                    <option value="sell">
                      Selling Real Estate ( Free Home Evaluation )
                    </option>
                    <option value="buysell">
                      Buy and Sell Consultation ( Learn Our Perks )
                    </option>
                    <option value="mortgage">
                      I Need a Mortgage Pre-Approval
                    </option>
                    <option value="agents">
                      Real Estate Agents / Careers Opportunities
                    </option>
                    <option value="other">Other</option>
                  </Form.Select>
                </Form.Group>
                <Form.Group className="mb-3">
                  <Form.Label
                    htmlFor="timeline"
                    className="option-label form-box"
                  >
                    Timeline <p className="required">*</p>
                  </Form.Label>
                  <Form.Select
                    id="timeline"
                    required
                    className="option-select form-box"
                  >
                    <option>Please Select</option>
                    <option value="3">Within 3 Months</option>
                    <option value="3-6">3 or 6 Months</option>
                    <option value="6-12">6 or 12 Months</option>
                    <option value="12+">12+ Months</option>
                  </Form.Select>
                </Form.Group>
                <button className="msg-btn" type="submit">
                  Let Us Help »
                </button>
              </Form>
            </div>
          </Col>
        </Row>
      </Container>
    </div>
  );
};

export default MessageSection;
