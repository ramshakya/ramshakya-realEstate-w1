import React, { Component, useState, useEffect, useRef } from "react";
import Layout from "../components/Layout/CalculatorLayout";
import Calculator from "./../ReactCommon/Components/Calculator/CalculatorMain";
import Constants from "../constants/GlobalConstants";
class Calculators extends Component {
  constructor(props) {
    super(props);
  }
  componentDidMount() {}

  componentDidUpdate(prevProps, prevState, snapshot) {}
  render() {
    return <Calculator />;
  }
}
export default Calculators;
