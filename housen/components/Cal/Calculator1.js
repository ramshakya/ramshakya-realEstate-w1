import react, { useState, useEffect } from 'react';
import { useRouter } from 'next/router';
import Link from "next/link";
const Calculator1 = (props) => {
	const router = useRouter();
	const slugs = router.query.slug;
	console.log("slugs===>>>", slugs);
	// const [slugs,setSlugs] = useState('mortgage-calculator');
	const [calculatorName, setCalculatorName] = useState('');
	const [widgetName, setWidgetName] = useState('');
	const [extraData, setExtraData] = useState('');
	useEffect(() => {

		function addScript() {
			 

			if (slugs == 'mortgage-calculator') {
				setCalculatorName('Mortgage payment calculator');
				setWidgetName('calc-payment');

			}
			if (slugs == 'land-transfer-tax-calculator') {
				setCalculatorName('Land transfer tax calculator');
				setWidgetName('calc-payment');

			}
			if (slugs == 'mortgage-affordability-calculator') {
				setCalculatorName('Mortgage Affordability Calculator');
				setWidgetName('calc-affordability');

			}
			const script = document.createElement("script");
			script.src = "https://www.ratehub.ca/assets/js/widget-loader.js";
			script.async = true;
			document.head.appendChild(script);
		}
		if (slugs !== undefined) {
			const element = document.getElementById('mortgage-calculator');
            if (element) {
                // element.remove();
            }
			const element2 = document.getElementById('land-transfer-tax-calculator');
            if (element2) {
                // element2.remove();
            }
			const element3 = document.getElementById('mortgage-affordability-calculator');
            if (element3) {
                // element3.remove();
            }

			addScript();
		}

	}, [slugs])

	return (
		<>
			<section className="custom-padding section-padding">
				<div className="container">
					<div className="row justify-content-center align-items-center">
						<div className="col-lg-12">
							<ul className="calculator-nav">
								<li className={(slugs == 'mortgage-calculator' ? 'active' : '')}><a href="/calculator/mortgage-calculator"   className="btn">Mortgage payment calculator</a></li>
								<li className={(slugs == 'land-transfer-tax-calculator' ? 'active' : '')}><a href="/calculator/land-transfer-tax-calculator"  className="btn">Land transfer tax calculator </a></li>
								<li className={(slugs == 'mortgage-affordability-calculator' ? 'active' : '')}><a href="/calculator/mortgage-affordability-calculator"  className="btn">Mortgage Affordability Calculator </a></li>
							</ul>
						</div>
						<div className="col-lg-12">
							<div>
								<h2 className="calculator_head">
									{calculatorName}</h2>
								{slugs == 'mortgage-calculator' &&
									<div className="widget" id='mortgage-calculator' data-widget={widgetName} data-lang="en"></div>
								}
								{slugs == 'land-transfer-tax-calculator' &&
									<div className="widget" id='land-transfer-tax-calculator' data-widget={widgetName} data-ltt="only" data-lang="en"></div>
								}
								{slugs == 'mortgage-affordability-calculator' &&
									<div className="widget"  id='mortgage-affordability-calculator'  data-widget={widgetName} data-lang="en"></div>
								}

								<div className="calculator_right"></div>
							</div>
						</div>
					</div>
				</div>
			</section>
		</>
	);
}
export default Calculator1;