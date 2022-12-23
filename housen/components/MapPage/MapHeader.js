const Header=(props)=>{
	 function showmenu(e){
	 	let cl = document.getElementsByClassName('checkbox-opt-container');
	 	for (var i = 0; i < cl.length; i++) {
	 		cl[i].classList.remove("checkbox-opt-showing");
	 	}
	 	if(e.target.classList[0]=='theme-text'){
	 		let panel = e.target.nextElementSibling;
	 		panel.classList.add('checkbox-opt-showing');
	 	}	

	 }
	return(
		<>
			<div className="container-fluid">
			   <div id="filter_section">
			    	<div className="filter-top">
			    		<div className="filter-input-container filter-part-1">
				            <div className="filter-input-area">
				                <input type="text" className="filter-input" id="searchByText" 
				                placeholder="City, neighbourhood, address or MLS#" name="keyword" autocomplete="off"/>
				                <button className="filter-search-btn"><i className="fa fa-search"></i></button>
				                
				            </div>
				            <button className="filter-btn filter-more-opt" id="show_more_filter">
				            <i className="bi bi-filter" ></i>
				            <i className="bi bi-chevron-down"></i>
				             <span className="desktop-content">More </span>Filter</button>
				        </div>
				        <div className="filter-part-2">
				            <div className="filter-buttom-group">
				                <button className="filter-btn filter-btn-left filter-btn-active" id="sale_btn" data-target="lease_btn" data-value="Sale" data-filter="SaleLease" data-lease="false">Sale</button>
				                <button className="filter-btn filter-btn-right" id="lease_btn" data-target="sale_btn" data-value="Lease" data-filter="SaleLease" data-lease="true">Lease</button>
				            </div>
				            <div className="filter-buttom-group" id="sold_data_filter">
				                <button className="filter-btn filter-btn-left" id="active_btn" data-target="sold_btn" data-value="A" data-filter="Status">Active</button>
				                <button className="filter-btn filter-btn-right filter-btn-active" id="sold_btn" data-target="active_btn" data-value="U" data-filter="Status">Sold</button>
				            </div>
				            <div className="filter-buttom-group to-right">
				                <label className="filter-favorite-container">
				                    <i className="fa fa-heart"></i>
				                    <i className="bi bi-heart-fill"></i>
				                    Favorite
				                    <input className="filter-checkbox" type="checkbox" name="favorite" id="filter_favorite_checkbox" disabled=""/>
				                </label>
				            </div>
				        </div>
			    	</div>
			    	<div className="filter-div desktop-content filter-flex-opt">
				        <div className="filter-option-container checkbox-opt">
				            <label for="Type" className="theme-text gray-text" onClick={showmenu} data-suffix=" Property Types" data-default="All Property Type">All Property Type <i className="fa fa-sort-down"></i></label>
				            <div className="checkbox-opt-container">
				                <div className="checkbox-title theme-text">Property Type</div>
				                <div className="checkbox-line">
				                    <div className="checkbox-label">
				                        <input type="checkbox" checked name="Type" value="All" autocomplete="off" />
				                        <span className="checkbox-text">All</span>
				                    </div>
				                    <div className="checkbox-label">
				                        <input type="checkbox" name="Type" value="Condo Apt" autocomplete="off" />
				                        <span className="checkbox-text">Condo Apt</span>
				                    </div>
				                    <div className="checkbox-label">
				                        <input type="checkbox" name="Type" value="Townhouse" autocomplete="off" />
				                        <span className="checkbox-text">Townhouse</span>
				                    </div>
				                    <div className="checkbox-label">
				                        <input type="checkbox" name="Type" value="Detached" autocomplete="off" />
				                        <span className="checkbox-text">Detached</span>
				                    </div>
				                    <div className="checkbox-label">
				                        <input type="checkbox" name="Type" value="Semi-Detached" autocomplete="off" />
				                        <span className="checkbox-text">Semi-Detached</span>
				                    </div>
				                    <div className="checkbox-label">
				                        <input type="checkbox" name="Type" value="Other" autocomplete="off" />
				                        <span className="checkbox-text">Other</span>
				                    </div>
				                </div>
				                
				            </div>
				        </div>
				        <div className="filter-option-container checkbox-opt">
						            <label for="Bedroom" className="theme-text gray-text" onClick={showmenu} data-suffix=" Bedroom" data-default="Any Bedroom">Any Bedroom <i className="fa fa-sort-down"></i></label>
						            <div className="checkbox-opt-container ">
						                <div className="checkbox-title theme-text">Bedroom</div>
						                <div className="checkbox-line">
						                    <div className="checkbox-label">
						                        <input type="checkbox" checked name="Bedroom" value="All" autocomplete="off"/>
						                        <span className="checkbox-text">All</span>
						                    </div>
						                    <div className="checkbox-label">
						                        <input type="checkbox" name="Bedroom" value="0" autocomplete="off" />
						                        <span className="checkbox-text">0</span>
						                    </div>
						                    <div className="checkbox-label">
						                        <input type="checkbox" name="Bedroom" value="1" autocomplete="off" />
						                        <span className="checkbox-text">1</span>
						                    </div>
						                    <div className="checkbox-label">
						                        <input type="checkbox" name="Bedroom" value="2" autocomplete="off" />
						                        <span className="checkbox-text">2</span>
						                    </div>
						                    <div className="checkbox-label">
						                        <input type="checkbox" name="Bedroom" value="3" autocomplete="off" />
						                        <span className="checkbox-text">3</span>
						                    </div>
						                    <div className="checkbox-label">
						                        <input type="checkbox" name="Bedroom" value="4" autocomplete="off" />
						                        <span className="checkbox-text">4</span>
						                    </div>
						                    <div className="checkbox-label">
						                        <input type="checkbox" name="Bedroom" value="5+" autocomplete="off" />
						                        <span className="checkbox-text">5+</span>
						                    </div>
						                </div>
						            </div>
						        </div>
						<div className="filter-range-slider" data-for="price">
				            <div className="slider-title theme-color">Price</div>
				            <div className="slider-text">
				                <span style={{'float':'left'}} className="range-min" data-prefix="$ " data-suffix="">$ 0</span>
				                <span style={{'float':'right'}} className="range-max" data-prefix="$ " data-suffix="">Unlimited</span>
				            </div>
				            <div className="slider-range">
				                <div className="slider-range-min"></div>
				                <div className="slider-btn" data-type="min" data-for="price" data-name="MinPrice" data-value="0"></div>
				                <div className="slider-range-between"></div>
				                <div className="slider-btn" data-type="max" data-for="price" data-name="MaxPrice" data-value="-1"></div>
				                <div className="slider-range-max"></div>
				            </div>
				        </div>
				        <div className="filter-option-container checkbox-opt">
				            <label for="Bathroom" className="theme-text gray-text" onClick={showmenu} data-suffix=" Bathroom" data-default="Any Bathroom">Any Bathroom <i className="fa fa-sort-down"></i></label>
				            <div className="checkbox-opt-container">
				                <div className="checkbox-title theme-text">Bathroom</div>
				                <div className="checkbox-line">
				                    <div className="checkbox-label">
				                        <input type="checkbox" checked name="Bathroom" value="All" autocomplete="off" />
				                        <span className="checkbox-text">All</span>
				                    </div>
				                    <div className="checkbox-label">
				                        <input type="checkbox" name="Bathroom" value="1+" autocomplete="off" />
				                        <span className="checkbox-text">1+</span>
				                    </div>
				                    <div className="checkbox-label">
				                        <input type="checkbox" name="Bathroom" value="2+" autocomplete="off" />
				                        <span className="checkbox-text">2+</span>
				                    </div>
				                    <div className="checkbox-label">
				                        <input type="checkbox" name="Bathroom" value="3+" autocomplete="off" />
				                        <span className="checkbox-text">3+</span>
				                    </div>
				                    <div className="checkbox-label">
				                        <input type="checkbox" name="Bathroom" value="4+" autocomplete="off" />
				                        <span className="checkbox-text">4+</span>
				                    </div>
				                    <div className="checkbox-label">
				                        <input type="checkbox" name="Bathroom" value="5+" autocomplete="off" />
				                        <span className="checkbox-text">5+</span>
				                    </div>
				                </div>
				            </div>
				        </div>
				        <div className="filter-option-container checkbox-opt">
				            <label for="Parking" className="theme-text gray-text" onClick={showmenu} data-suffix=" Parking" data-default="Any Parking">Any Parking <i className="fa fa-sort-down"></i></label>
				            <div className="checkbox-opt-container checkbox-opt-container-right">
				                <div className="checkbox-title theme-text">Parking </div>
				                <div className="checkbox-line">
				                    <div className="checkbox-label">
				                        <input type="checkbox" checked name="Parking" value="All" autocomplete="off" />
				                        <span className="checkbox-text">All</span>
				                    </div>
				                    <div className="checkbox-label">
				                        <input type="checkbox" name="Parking" value="1+" autocomplete="off" />
				                        <span className="checkbox-text">1+</span>
				                    </div>
				                    <div className="checkbox-label">
				                        <input type="checkbox" name="Parking" value="2+" autocomplete="off" />
				                        <span className="checkbox-text">2+</span>
				                    </div>
				                    <div className="checkbox-label">
				                        <input type="checkbox" name="Parking" value="3+" autocomplete="off" />
				                        <span className="checkbox-text">3+</span>
				                    </div>
				                    <div className="checkbox-label">
				                        <input type="checkbox" name="Parking" value="4+" autocomplete="off" />
				                        <span className="checkbox-text">4+</span>
				                    </div>
				                    <div className="checkbox-label">
				                        <input type="checkbox" name="Parking" value="5+" autocomplete="off" />
				                        <span className="checkbox-text">5+</span>
				                    </div>
				                </div>
				            </div>
				        </div>
				        <div className="filter-option-container active-opt">
				            <label for="Days" className="theme-text"><span className="gray-text">Day Listed (Any)</span></label>
				            <select name="Days" id="filter_days" className="filter-days" data-default="Day Listed" data-prefix="" data-suffix="">
				                <option value="-1" selected="">Any</option>
				                <option value="2">Last 1 Days</option>
				                <option value="4">Last 3 Days</option>
				                <option value="8">Last 7 Days</option>
				                <option value="31">Last 30 Days</option>
				                <option value="91">Last 90 Days</option>
				            </select>
				        </div>
				        <div className="filter-option-container sold-opt">
				            <label for="SoldDays" className="theme-text"><span className="bold-text">Last 90 Days <i className="fa fa-sort-down"></i></span></label>
				            <select name="SoldDays" id="filter_sold_days" className="filter-sold-days" data-default="Sold Days" data-prefix="" data-suffix="">
				                <option value="2">Last 1 Days</option>
				                <option value="4">Last 3 Days</option>
				                <option value="8">Last 7 Days</option>
				                <option value="31">Last 30 Days</option>
				                <option value="91" selected="">Last 90 Days</option>
				                <option value="180">Last 180 Days</option>
				                <option value="360">Last 360 Days</option>
				            </select>
				        </div>
				        <button className="filter-menu-btn filter-top-reset-btn theme-btn-primary" type="reset"> Reset</button>
				    </div>
			    	
			    </div>
			</div>
		</>
		);
}
export default Header;