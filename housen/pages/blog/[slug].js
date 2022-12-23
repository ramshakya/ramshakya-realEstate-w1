import React, { useEffect, useState } from "react";
import Layout from "../../components/Layout/Layout";
import Link from "next/link";
import BlogCard from "../../components/Cards/ArticleCard";
import BlogCard1 from "../../components/Cards/BlogCard1";
import API from "../../ReactCommon/utility/api";
import Constants from "../../constants/Global";
import Autocomplete from "../../ReactCommon/Components/AutoSuggestion";
import { useRouter } from "next/router";
import Container from "react-bootstrap/Container";
import Nav from "react-bootstrap/Nav";
import Navbar from "react-bootstrap/Navbar";
import NavDropdown from "react-bootstrap/NavDropdown";
import detect from "../../ReactCommon/utility/detect";
const extra_url = Constants.extra_url;
const front_url = Constants.front_url;
const Blogs = (props) => {
  const router = useRouter();
  const slug = router.query.slug;
  const [records, setRecords] = useState([]);
  const [total, setTotal] = useState([]);
  const [currentPage, setCurrentPage] = useState(1);
  const [totalPage, setTotalPage] = useState([]);
  const [blogSearch, setBlogSearch] = useState("");
  // const [dataFlag,steDataFlag] = useState(false);
  const [topPost, setTopPost] = useState([]);
  const [MostPopular, setMostPopular] = useState([]);

  const [blogCategory, setBlogCategory] = useState("");
  const [blogSuggestion, setBlogSuggestion] = useState([]);
  const [isMobile, setIsMobile] = useState("");

  const [labelCategory, setLabelCategory] = useState("Most Popular");
  const [category, setCategory] = useState("");
  const [activeCls, setActiveCls] = useState("");
  const [search, setSearch] = useState(false);
  props.pageName("blogs");
  // props.setMetaInfo(Constants.pageMeta.blogs);

  // useEffect(() => {
  // 	const category = localStorage.getItem("category");
  // 	const activeCls = localStorage.getItem("activeCls");
  // 	console.log(activeCls,"activeCls");
  // 	setCategory(category);
  // 	setLabelCategory(category);
  // 	setActiveCls(activeCls);
  // 	props.popularSearch(false);
  // 	setSearch(true);
  // 	if (category == '') {
  // 		setSearch(false);
  // 	}

  // }, [props.popularSearchCheck])
  useEffect(() => {
    let category = slug;
    let activeCls = "";
    setLabelCategory(category);
    setCategory(category);
    setSearch(true);
    switch (category) {
      case "HOME":
        setCategory("");
        setLabelCategory("Most Popular");
        setSearch(false);
        activeCls = 0;
        break;
      case "MARKET NEWS":
        activeCls = 1;
        break;
      case "FOR BUYERS":
        activeCls = 2;
        break;
      case "FOR SELLERS":
        activeCls = 3;
        break;
      case "FOR RENTERS":
        activeCls = 4;
        break;
      case "PRE CONSTRUCTION":
        activeCls = 5;
        break;
      case "FREE GUIDES":
        activeCls = 6;
        break;
      default:
        setCategory("");
        setLabelCategory("Most Popular");
        setSearch(false);
        activeCls = 0;
        break;
    }
    setActiveCls(activeCls);
  }, [slug]);

  useEffect(() => {
    if (detect.isMobile()) {
      setIsMobile(true);
    }
    const getBlogData = () => {
      let body = JSON.stringify({
        currentPage: currentPage,
        agentId: Constants.agentId,
        blogSearch: blogSearch,
        category: category,
      });
      API.jsonApiCall(extra_url + "GetBlogs", body, "POST", null, {
        "Content-Type": "application/json",
      }).then((res) => {
        setTotal(res.total);
        setTotalPage(res.totalPages);
        setRecords(res.records);
        let temp = [];
        if (res.records) {
          let category2 = category ? category.toUpperCase() : "";
          res.records.map((item) => {
            let cat = [];
            if (item.Categories) {
              cat = JSON.parse(item.Categories);
            } else {
              cat.push(item.Title);
            }
            if (cat.includes(category2)) {
              temp.push(item);
            }
          });
        }
        if (category) {
          if (res.topPost) {
            let category1 = category ? category.toLowerCase() : "";
            let category2 = category ? category.toUpperCase() : "";
            res.topPost.map((item) => {
              let cat = [];
              if (item.Categories) {
                cat = JSON.parse(item.Categories);
                if (cat.includes(category2) || cat.includes(category1)) {
                  temp.push(item);
                }
              } else {
                cat.push(item.Title);
              }
            });
          }
        }
        if (!res.records || temp.length) {
          setRecords(temp);
        }
        setTopPost(res.topPost);
        setMostPopular(res.mostSearches);
      });
    };
    getBlogData();
  }, [currentPage, category, blogSearch, labelCategory]);
  useEffect(() => {
    getCat();
    // localStorage.setItem('pushtoblog', JSON.stringify(ob));
    let routing = localStorage.getItem("pushtoblog");
    console.log("routing", routing);
    if (routing) {
      routing = JSON.parse(routing);
      setActiveCls(routing.active);
      setCategory(routing.category);
      setSearch(routing.search);
      setLabelCategory(routing.labelCategory);
      localStorage.removeItem("pushtoblog");
    }
  }, []);
  const getCat = async () => {
    const categoryList = await API.jsonApiCall(
      extra_url + "GetBlogCategory",
      {},
      "POST",
      null,
      {
        "Content-Type": "application/json",
      }
    );
    localStorage.setItem(
      "blogCategory",
      JSON.stringify(categoryList.suggesstionArr)
    );
     
    // const catMap = new Map();
    // blogsCat.forEach(item => catMap.set(item.text, item));
    // categoryList.suggesstionArr.forEach(item => {
    // 	const exists = catMap.has(item.text);
    // 	if (!exists) {
    // 		catMap.set(item.text, item);
    // 	}
    // })
    // let ar = [];
    // catMap.forEach(item =>
    // 	ar.push(item)
    // )

    // setBlogCategory(categoryList.suggesstionArr); // for dynamic
    setBlogCategory(Constants.blogsCat); // for static
  };
  const getBlogAutoSuggest = async (fieldValue, fieldName, cb) => {
    let payload = {
      keyword_search: "",
      agentId: Constants.agentId,
    };
    if (fieldValue) {
      payload.keyword_search = fieldValue;
    }
    const requestOptions = {
      method: "POST",
      headers: { "Content-Type": "application/json" },
      body: JSON.stringify(payload),
    };
    await fetch(extra_url + "GetBlogTitle", requestOptions)
      .then((response) => response.text())
      .then((res) => JSON.parse(res))
      .then((json) => {
        cb({ allList: json.suggesstionArr });
      })
      .catch((err) => console.log({ err }));
  };
  function actionAgainstKeyword(event) {
    // getBlogData(event.value,blogCategory);
    setBlogSearch(event.value);
    setSearch(true);
  }
  function actionAgainstCategory(event) {
    if (event.target && event.target.dataset && event.target.dataset.set) {
      let index = event.target.getAttribute("data-value");
      setActiveCls(index);
      event = JSON.parse(event.target.dataset.set);
      if (event.text === "BLOG HOME" || event.text === "Blog Home") {
        setSearch(false);
        setCategory("");
        setLabelCategory("Most Popular");
      } else {
        setCategory(event.value);
        setSearch(true);
        setLabelCategory(event.value);
      }
    }
  }
  function setUrl(str) {
	console.log("ram blogs",str);
    router.push("/blog/" + str);
  }
  return (
    <>
      <div className="section-padding-v2">
        <Navbar className={`back-ground-color `} expand="lg">
          <div className={`container blogs-navs ${isMobile ? "mt-3" : ""}`}>
            <Navbar.Toggle
              aria-controls="basic-navbar-nav"
              className={`  ${isMobile ? "mt-4" : ""}`}
            />
            <Navbar.Collapse id="basic-navbar-nav">
              <Nav className="me-auto">
                {blogCategory &&
                  blogCategory.map((item, key) => {
                    let l = blogCategory.length;

                    return (
                      <Nav.Link
                        key={key}
                        data-value={key}
                        className={`${
                          activeCls == key ? "active-blog-nav" : ""
                        }`}
                        data-set={JSON.stringify(item)}
                        onClick={() =>
                          setUrl(
                            item.value == "BLOG HOME" ? "HOME" : item.value
                          )
                        }
                      >
                        {item.text} {l - 1 > key ? "  | " : " "}
                      </Nav.Link>
                    );
                  })}
              </Nav>
            </Navbar.Collapse>
          </div>
        </Navbar>
      </div>
      <section className="">
        <div className="container">
          <div className="containerz">
            <div className="row">
              {!search && (
                <div className="col-md-12 col-lg-12">
                  <h4 className="text-bold">Most Popular</h4>
                </div>
              )}
              {!search &&
                MostPopular &&
                MostPopular.map((item, index) => {
                  // blogCategory
                  if (index === 0) {
                    let d = new Date(item.created_at).toDateString();
                    return (
                      <div
                        className="col-md-8 col-lg-8 image1"
                        key={index + "b"}
                      >
                        <BlogCard
                          image={item.MainImg}
                          title={item.Title}
                          label={"Most Popular"}
                          date={d}
                          url={"/blogs/" + item.Url}
                          content={item.Content}
                          isOne={true}
                        />
                      </div>
                    );
                  }
                })}

              {!search && (
                <div className="col-md-4 col-lg-4 image2 ">
                  <div className="row">
                    <div className="col-md-12 mb-4">
                      <label>Search Blog</label>
                      <Autocomplete
                        inputProps={{
                          id: "autoSuggestion",
                          name: "purpose",
                          className: "auto form-control auto-suggestion-inp",
                          placeholder: "search by keyword",
                          title: "search by keyword",
                          readOnly: false,
                          required: true,
                          autoComplete: "off",
                        }}
                        allList={[]}
                        autoCompleteCb={getBlogAutoSuggest}
                        cb={actionAgainstKeyword}
                        extraProps={{}}
                      />
                    </div>
                    {topPost &&
                      topPost.map((item, index) => {
                        if (index < 2) {
                          let d = new Date(item.created_at).toDateString();
                          let cat = [category];
                          if (item.Categories) {
                            cat = JSON.parse(item.Categories);
                          }
                          return (
                            <div className="col-md-12" key={index + "c"}>
                              <BlogCard
                                image={item.MainImg}
                                title={item.Title}
                                label={category ? category : cat[0]}
                                date={d}
                                url={"/blogs/" + item.Url}
                                isSearch={search}
                                content={item.Content}
                                imageDivClass="w-100 position-relative vh-50"
                              />
                            </div>
                          );
                        }
                      })}
                  </div>
                </div>
              )}
            </div>
            <div className="row">
              <div className="col-md-12 col-lg-12">
                <h4 className="text-bold capitalized-text">
                  {category ? category : "THE LATEST"}
                </h4>
              </div>
              {records &&
                records.map((item, index) => {
                  let d = new Date(item.created_at).toDateString();
                  let cat = [];
                  if (item.Categories) {
                    cat = JSON.parse(item.Categories);
                  } else {
                    cat.push(item.Title);
                  }
                  return (
                    <div className="col-md-4 col-lg-4 image3" key={index + "d"}>
                      <BlogCard1
                        image={item.MainImg}
                        title={item.Title}
                        // label={!category || category === "BLOG HOME" ? "Most Popular" : category}
                        label={category ? category : cat[0]}
                        date={d}
                        url={"/blogs/" + item.Url}
                        isLatest={true}
                        content={item.Content}
                        isSearch={search}
                      />
                    </div>
                  );
                })}
            </div>
          </div>
        </div>
      </section>
    </>
  );
};
export default Blogs;
