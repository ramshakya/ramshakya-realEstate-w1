import react, { useState, useEffect } from "react";
import { useRouter } from "next/router";
import Link from "next/link";
import Loader from "../loader/loader";
// /var/www/html/Current-projects/wedu.ca/wedufront/
const Calculator1 = (props) => {
  const router = useRouter();
  const slugs = router.query.slug;
  // const [slugs,setSlugs] = useState('mortgage-calculator');
  const [calculatorName, setCalculatorName] = useState("");
  const [widgetName, setWidgetName] = useState("");
  const [extraData, setExtraData] = useState("");
  const [LoaderState, setLoaderState] = useState(true);

  useEffect(() => {
    if (slugs !== undefined) {
      addScript();
    }
    setTimeout(() => {
      setLoaderState(false);
    }, 2000);
  }, [slugs]);
  function addScript() {
    if (slugs == "mortgage-calculator") {
      setCalculatorName("Mortgage payment calculator");
      setWidgetName("calc-payment");
    }
    if (slugs == "land-transfer-tax-calculator") {
      setCalculatorName("Land transfer tax calculator");
      setWidgetName("calc-payment");
    }
    if (slugs == "mortgage-affordability-calculator") {
      setCalculatorName("Mortgage Affordability Calculator");
      setWidgetName("calc-affordability");
    }
    const script = document.createElement("script");
    script.src = "https://www.ratehub.ca/assets/js/widget-loader.js";
    script.async = true;
    document.head.appendChild(script);
  }
  return (
    <>
      {LoaderState && <Loader />}
      <section className="section-padding">
        <div className="container">
          <div className="row justify-content-center align-items-center">
            <div className="col-lg-12">
              <ul className="calculator-nav">
                <li className={slugs == "mortgage-calculator" ? "active" : ""}>
                  <Link href="/calculator/mortgage-calculator">
                    <a className="btn">Mortgage payment calculator</a>
                  </Link>
                </li>
                <li
                  className={
                    slugs == "land-transfer-tax-calculator" ? "active" : ""
                  }
                >
                  <Link href="/calculator/land-transfer-tax-calculator">
                    <a className="btn">Land transfer tax calculator</a>
                  </Link>
                </li>
                <li
                  className={
                    slugs == "mortgage-affordability-calculator" ? "active" : ""
                  }
                >
                  <Link href="/calculator/mortgage-affordability-calculator">
                    <a className="btn">Mortgage Affordability Calculator</a>
                  </Link>
                </li>
              </ul>
            </div>
            <div className="col-lg-12">
              <div>
                <h2 className="calculator_head">{calculatorName}</h2>
                {slugs == "mortgage-calculator" && (
                  <div
                    className="widget"
                    data-widget={widgetName}
                    data-lang="en"
                  ></div>
                )}
                {slugs == "land-transfer-tax-calculator" && (
                  <div
                    className="widget"
                    data-widget={widgetName}
                    data-ltt="only"
                    data-lang="en"
                  ></div>
                )}
                {slugs == "mortgage-affordability-calculator" && (
                  <div
                    className="widget"
                    data-widget={widgetName}
                    data-lang="en"
                  ></div>
                )}

                <div className="calculator_right"></div>
              </div>
            </div>
          </div>
        </div>
      </section>
    </>
  );
};
export default Calculator1;
