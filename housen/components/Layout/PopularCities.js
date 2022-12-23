import Link from "next/link";
import { useRouter } from "next/router";
const PopularCities = (props) => {
  const router = useRouter();
  function gotosearch(city, status, heading) {
    let Searches = {
      city: city,
      status: status,
      heading: city + " Real Estate",
    };
    let filters = {
      searchFilter: {},
      preField: {},
    };
    let field = { text: city, value: city, category: "Cities", group: "City" };
    filters.searchFilter.text_search = city;
    filters.preField.text_search = field;
    let group = "City";
    let params = "/map?";
    params += `text_search=${city}&propertySubType=["Detached","Semi-Detached","Freehold Townhouse","Condo Townhouse","Condo Apt"]&propertyType=Residential&status=Sale&Dom=90&soldStatus=A&group=${group}`;
    localStorage.setItem("filters", JSON.stringify(filters));
    localStorage.setItem("City", city);
		localStorage.setItem('status', status);
		localStorage.setItem('isPopular', true);
		localStorage.setItem('isPopularCity', true);
    router.push(params);
  }
  return (
    <>
      <p>POPULAR CITIES</p>
      <ul className="footer-link capitalized-text">
        <li>
          <a
            href="javascript:void(0)"
            onClick={() => gotosearch("Ajax", "Sale", "")}
          >
            Ajax Homes for Sale
          </a>
        </li>
        <li>
          <a
            href="javascript:void(0)"
            onClick={() => gotosearch("Brampton", "Sale", "")}
          >
            Brampton Homes for Sale
          </a>
        </li>
        <li>
          <a
            href="javascript:void(0)"
            onClick={() => gotosearch("Burlington", "Sale", "")}
          >
            Burlington Homes for Sale
          </a>
        </li>

        <li>
          <a
            href="javascript:void(0)"
            onClick={() => gotosearch("Markham", "Sale", "")}
          >
            Markham Homes for Sale
          </a>
        </li>
        <li>
          <a
            href="javascript:void(0)"
            onClick={() => gotosearch("Milton", "Sale", "")}
          >
            Milton Homes for Sale
          </a>
        </li>
        <li>
          <a
            href="javascript:void(0)"
            onClick={() => gotosearch("Mississauga", "Sale", "")}
          >
            Mississauga Homes for Sale
          </a>
        </li>
        <li>
          <a
            href="javascript:void(0)"
            onClick={() => gotosearch("Newmarket", "Sale", "")}
          >
            Newmarket Homes for Sale
          </a>
        </li>
        <li>
          <a
            href="javascript:void(0)"
            onClick={() => gotosearch("Oakville", "Sale", "")}
          >
            Oakville Homes for Sale
          </a>
        </li>
        <li>
          <a
            href="javascript:void(0)"
            onClick={() => gotosearch("Oshawa", "Sale", "")}
          >
            Oshawa Homes for Sale
          </a>
        </li>
        <li>
          <a
            href="javascript:void(0)"
            onClick={() => gotosearch("Richmond Hill", "Sale", "")}
          >
            Richmond Hill Homes for Sale
          </a>
        </li>
        <li>
          <a
            href="javascript:void(0)"
            onClick={() => gotosearch("Vaughan", "Sale", "")}
          >
            Vaughan Homes for Sale
          </a>
        </li>
        <li>
          <a
            href="javascript:void(0)"
            onClick={() => gotosearch("Whitby", "Sale", "")}
          >
            Whitby Homes for Sale
          </a>
        </li>
      </ul>
    </>
  );
};
export default PopularCities;
