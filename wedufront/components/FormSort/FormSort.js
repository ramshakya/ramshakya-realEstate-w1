import { useState } from "react";
import { Form, Col, Row, Container } from "react-bootstrap";
import { filterProp1, filter2 } from "../../data/FilterSelectItems";
import RangeSlider from "react-bootstrap-range-slider";
import RedButton from "../Button/RedButton";

const FormSort = (props) => {
  const { className } = props;
  const [valueBed, setValueBed] = useState(0);
  const [valueBath, setValueBath] = useState(0);
  const [valuePrice, setValuePrice] = useState(0);

  return (
    <div className={className} id="form">
      <Container className="mb-3">
        <Form className="border form-sort">
          {/* Title */}
          <h4 className="form-title mb-4">Search/Filter Properties</h4>
          <Row>
            <Col md={6}>
              {/* Selection 1*/}
              {filterProp1.map((item) => {
                return (
                  <Form.Group controlId="filter" className="mb-3" key={item.id}>
                    <Form.Label className="form-label" for={item.name}>
                      {item.name}
                    </Form.Label>
                    <Form.Select
                      aria-label="Property Type"
                      className="form-box"
                      id={item.name}
                    >
                      {item.values.map((element) => {
                        return (
                          <option value={element.value} key={element.id}>
                            {element.name}
                          </option>
                        );
                      })}
                    </Form.Select>
                  </Form.Group>
                );
              })}
              {/* End of Selection */}

              {/* Bedrooms Slider */}
              <Form.Group>
                <Form.Label for="Bedrooms">Bedrooms</Form.Label>
                <br />
                <Form.Label className="count-label">{valueBed}</Form.Label>
                <RangeSlider
                  id="Bedrooms"
                  value={valueBed}
                  onChange={(e) => setValueBed(e.target.value)}
                  min="0"
                  max="10"
                  variant="danger"
                />
              </Form.Group>
              {/* End of Bedrooms Slider */}

              {/* Bedrooms Slider */}
              <Form.Group>
                <Form.Label for="Bathrooms">Bathrooms</Form.Label>
                <br />
                <Form.Label className="count-label">{valueBath}</Form.Label>
                <RangeSlider
                  id="Bathrooms"
                  value={valueBath}
                  onChange={(e) => setValueBath(e.target.value)}
                  min="0"
                  max="10"
                  variant="danger"
                />
              </Form.Group>
              {/* End of Bedrooms Slider */}

              {/* Price Slider */}
              <Form.Group>
                <Form.Label for="Price">Price</Form.Label>
                <br />
                <Form.Label className="count-label">$ {valuePrice}</Form.Label>
                <RangeSlider
                  id="Price"
                  value={valuePrice}
                  onChange={(e) => setValuePrice(e.target.value)}
                  min="0"
                  max="5000000"
                  step="10000"
                  variant="danger"
                  tooltipLabel={(v) => `$ ${v}`}
                />
              </Form.Group>
              {/* End of Price Slider */}
            </Col>
            <Col md={6}>
              {/* Street Address */}
              <Form.Group className="mb-3" controlId="streetAdress">
                <Form.Label className="form-label">Street Address</Form.Label>
                <Form.Control
                  type="text"
                  placeholder="Enter Street Name"
                  className="form-box"
                />
              </Form.Group>
              {/* Street Address */}

              {/* Selection 2 */}
              {filter2.map((item) => {
                return (
                  <Form.Group controlId="filter" className="mb-3" key={item.id}>
                    <Form.Label className="form-label" for={item.name}>
                      {item.name}
                    </Form.Label>
                    <Form.Select
                      aria-label="Property Type"
                      className="form-box"
                      id={item.name}
                    >
                      {item.values.map((element) => {
                        return (
                          <option value={element.value} key={element.id}>
                            {element.name}
                          </option>
                        );
                      })}
                    </Form.Select>
                  </Form.Group>
                );
              })}
              {/* End of Selection 2 */}

              {/* Postal Code*/}
              <Form.Group className="mb-3" controlId="postalCode">
                <Form.Label className="form-label">Postal Code</Form.Label>
                <Form.Control
                  type="number"
                  placeholder="Enter Postal Code"
                  className="form-box"
                />
              </Form.Group>
              {/* End of Postal Code*/}

              {/* MLS® or RP Number*/}
              <Form.Group className="mb-3" controlId="streetAdress">
                <Form.Label className="form-label">
                  MLS® or RP Number
                </Form.Label>
                <Form.Control
                  type="text"
                  placeholder="Enter MLS® or RP Number"
                  className="form-box"
                />
              </Form.Group>
              {/* End of MLS® or RP Number*/}

              {/* Keyword*/}
              <Form.Group className="mb-3" controlId="streetAdress">
                <Form.Label className="form-label">Keyword</Form.Label>
                <Form.Control
                  type="text"
                  placeholder="Enter Keyword"
                  className="form-box"
                />
              </Form.Group>
              {/*End of Keyword*/}

              {["checkbox"].map((type, index) => (
                <div key={index} className="mb-3 custom-checkbox">
                  <Form.Check
                    label="Condominium"
                    name="Condominium"
                    type={type}
                    id={`${type}-1`}
                    className="custom-control-input"
                  />
                  <Form.Check
                    label="Pool"
                    name="Pool"
                    type={type}
                    id={`${type}-2`}
                    className="custom-control-input"
                  />
                  <Form.Check
                    label="Waterfront"
                    name="Waterfront"
                    type={type}
                    id={`${type}-3`}
                    className="custom-control-input"
                  />
                  <Form.Check
                    label="Open House"
                    name="Open House"
                    type={type}
                    id={`${type}-4`}
                    className="custom-control-input"
                  />
                </div>
              ))}
            </Col>
          </Row>
          <Row>
            <Col className="d-flex justify-content-center mt-3">
              <RedButton className="form-btn " size="sm">
                Search
              </RedButton>
            </Col>
          </Row>
        </Form>
      </Container>
    </div>
  );
};

export default FormSort;
