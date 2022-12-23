import Link from "next/link";
const ListingMenu =(props)=>{
	return (
		<>
			<section className="section-padding ">
                <div className="container">
                    <div className="row">
                <div className="col-md-12 col-lg-12">
                    <div className="title-wrapper pb-4">
                        <h6 className="service-title">Listing Packages</h6>
                        <hr/>
                    </div>
                    <p className="text-center pt-3 default-color pb-5">Advertise Beyond MLS® : Sell FASTER and HIGHER Than Your Neighbor!</p>
                </div>
                <div className="col-md-12 col-lg-12">
                     <div className="row">
                        <div className="col-md-3 packages">
                            <h3>SR Listing Bundle</h3>
                            <div className="innerContent">
                                <span className="color-msg">1%</span>
                                <h6 className="text-center mb-5 pt-2 text-bold text-secondary">Included in all packages</h6>
                                <p className="packagelist">
                                   For Sale Sign : Until Sold
                                </p>
                                <p className="packagelist">
                                    Outdoor Lockbox : Until Sold
                                </p>
                                <p className="packagelist">24 Hour Appointment Desk</p>
                                <p className="packagelist">7 Days/Week Support & Advice</p>
                                <p className="packagelist">Local MLS® : Realtor Specific</p>
                                <p className="packagelist">Realtor.ca : Public Specific</p>
                                <p className="packagelist">Weekend Open House</p>
                                <p className="packagelist">
                                SR Buyer Database</p>
                                <p className="packagelist">Property Photos (9)</p>
                            </div>
                        </div>
                         <div className="col-md-3 packages second">
                            <h3>Coach</h3>
                             <div className="innerContent">
                                <span className="color-msg">2.5%</span>
                                <h6 className="text-center mb-5 pt-2 text-bold text-secondary">2X Traffic vs. Realtor.ca
                                    *30 Day Sold Guarantee!</h6>
                                <p className="text-center mb-3"><i>SR Listing Bundle</i></p>
                                <p className="packagelist">
                                Listed on 5000+ Real Estate Sites</p>

                                <p className="packagelist">
                                Listed on 100+ Search Realty Sites</p>

                                <p className="packagelist">HD Photography (30+ Touch-ups)</p>

                                <p className="packagelist">Virtual Tour with Link</p>

                                <p className="packagelist">Video Creation & Distribution</p>

                                <p className="packagelist">Social Media Ads Package</p>

                                <p className="packagelist">Classified Ads Package</p>

                                <p className="packagelist">

                                    Marketing Report</p>
                            </div>
                        </div>
                         <div className="col-md-3 packages third">
                            <h3>Deluxe</h3>
                             <div className="innerContent">
                                <span className="color-msg">3.5%</span>
                                <h6 className="text-center mb-5 pt-2 text-bold text-secondary">5X Traffic vs.Realtor.ca
                                    *30 Day Sold Guarantee!</h6>
                                <p className="text-center mb-3"><i>SR Listing Bundle</i></p>
                                <p className="text-center mb-3"><i>All Items in Coach</i></p>
                                <p className="packagelist">
                                Google Digital Advertising</p>

                                <p className="packagelist">Home Staging Consultation</p>

                                <p className="packagelist">Virtual Home Staging</p>

                                <p className="packagelist">Color Feature Sheets</p>

                                <p className="packagelist">New Floor Plans & Sqft</p>

                                <p className="packagelist">Dedicated Webpage</p>
                            </div>
                        </div>
                         <div className="col-md-3 packages">
                            <h3>VIP</h3>
                             <div className="innerContent">
                                <span className="color-msg">4.5%</span>
                                <h6 className="text-center mb-5 pt-2 text-bold text-secondary">7X Traffic vs. Realtor.ca
                                    *30 Day Sold Guarantee!</h6>
                                <p className="text-center mb-3"><i>SR Listing Bundle</i></p>
                                <p className="text-center mb-3"><i>All Items in Coach & Deluxe</i></p>
                                <p className="packagelist">
                                 Google Digital Advertising+                                
                                </p>

                                <p className="packagelist">
                                Complete Home Staging</p>

                                <p className="packagelist">Complete Home Cleaning</p>

                                <p className="packagelist">Premium Stock Brochures</p>

                                <p className="packagelist">Local Newspaper Ad</p>

                                <p className="packagelist">Just-Listed Flyers</p>
                            </div>
                        </div>

                     </div>
                    
                </div>
            </div>
                </div>
           	</section>
            <section className="pb-5">
                <div className="container">
                    <div className="row">
                        <div className="col-md-12 text-center">
                            <p className="default-color">*After 30-day guarantee, a 1% fee reduction is granted.</p>
                            <p className="default-color">**Suggested listing agent side commission only.</p>
                            <p className="default-color">Commission may differ from agent to agent.</p>
                            <p className="default-color">Does not include Buyer Agent commission.</p>
                            <p className="default-color">***Not intended to solicit any buyers or sellers already in contract.</p>
                         </div>
                                
                    </div>
                    <div className="row pt-5">
                        <div className="col-md-2 col-lg-2 mb-5"></div>
                                <div className="col-md-4 col-lg-4 col-6 mb-5 text-align-right">
                                    <Link href="javascript:void(0)">
                                            <a className="custom-button-red listing-btn justify-content-center mb-0 rounded hover-btn btn btn-lg">Find Your Realtor
                                                
                                            </a>
                                        </Link>
                                </div>
                                <div className="col-md-4 col-lg-4 col-6 mb-5">
                                    <Link href="javascript:void(0)">
                                            <a className="custom-button-red listing-btn justify-content-center mb-0 rounded hover-btn btn btn-lg">Request a Custom Pin
                                                
                                            </a>
                                        </Link>
                                </div>
                    </div>
                </div>
            </section>
           	
		</>
		)
}
export default ListingMenu;